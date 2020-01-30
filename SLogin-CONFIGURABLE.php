<?php
// Created by Albin Eriksson, https://github.com/kezoponk
// MIT License, https://opensource.org/licenses/MIT

class SLogin {

  public $tablename = "";
  public $columns = array();

  public function __construct($columns, $dbname, $tablename, $hostname, $username, $password) {

    // Database credentials
    $this->tablename = $tablename;

    // Attempt database connection
    try {
      $this->database = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8", $username, $password);
      $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {

      // If database connection failed
      $_SESSION['failure'] = 'nodb';
      header('location: index.html');
    }
    // Connect form element to database column
    $this->columns = $columns;
  }
}

session_start();

// Create csrf token
if(!isset($_SESSION['token'])) { $_SESSION['token'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 32); }

// Main class of SLogin
class Main {

  function Login($username_or_email, $password, $USERNAME_COLUMN, $EMAIL_COLUMN, $PASSWORD_COLUMN, $SLogin) {
    // Initialize error variable
    $_SESSION['failure'] = "none";
    $database = $SLogin->database;
    $tablename = $SLogin->tablename;

    // In case the form elements don't have the required attribute
    if (empty($username_or_email)) {
      $_SESSION['failure'] = "username_empty";
    }
    if (empty($password)) {
      $_SESSION['failure'] = "password_empty";
    }

    if ($_SESSION['failure'] == "none") {

      $query = "SELECT * FROM $tablename WHERE ($USERNAME_COLUMN='$username_or_email' OR $EMAIL_COLUMN='$email_username')";

      $stmt = $database->prepare($query);
      $stmt->execute();
      $result = $stmt->fetchAll();

      // Count results
      $exists = 0;

      // Get username and the hashed & salted password
      foreach($result as $row) {
        // Check if entered password is the same as database password
        if(password_verify($password, $row['password'])) {
          // If correct, assign username variable as the users username contained in database
          $_SESSION['username'] = $row['username'];
          header('location: index.html');
          break;
        }
        $exists++;
      }
      // If password not correct
      $_SESSION['failure'] = "wrong";

      // Check if user even exists
      if($exists<1) {
        $_SESSION['failure'] = "user_does_not_exist";
      }
    }
    // Only reached if something went wrong
    header('location: index.html');
  }

  function Register($username, $email, $password, $password_confirm, $USERNAME_COLUMN, $EMAIL_COLUMN, $Query, $SRegister) {
    $_SESSION['failure'] = "none";

    // Fetch data from SLogin class
    $columns = $SRegister->columns;
    $database = $SRegister->database;
    $tablename = $SRegister->tablename;

    // Secure fields
    if (empty($username)) { $_SESSION['failure'] = "username_empty"; }
    if (empty($email)) { $_SESSION['failure'] = "email_empty"; }
    if (empty($password)) { $_SESSION['failure'] = "password_empty"; }
    if ($password != $password_confirm) {
      $_SESSION['failure'] = "password_nomatch";
    }

    // Check if username and email is not taken
    $user_check_query = "SELECT * FROM $tablename WHERE $USERNAME_COLUMN='$username' OR $EMAIL_COLUMN='$email' LIMIT 1";
    $result = mysqli_query($database, $user_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) { // If user already exists*
      if ($user['username'] === $username) {
        $_SESSION['failure'] = "user_exists";
      }
      // If email already exists*
      if ($user['email'] === $email) {
        $_SESSION['failure'] = "email_exists";
      }
    }

    if ($_SESSION['failure'] == "none") {

      // Run the prepared sql query
      $stmt = $database->prepare($Query);
      $stmt->execute();

      // Assign username variable to entered username
      $_SESSION['username'] = $username;
    }
    header('location: index.html');
  }
}

// CONFIGURE HERE

$REGISTER = array(
  "username" => "",
  "email" => "",
  "password" => ""
);

$SRegister = new SLogin($REGISTER, "register", "users", "localhost", "root", "");


$LOGIN = array(
  "", // Username column
  "", // Email column
  ""  // Password column
);

$SLogin = new SLogin($LOGIN, "register", "users", "localhost", "root", "");

// CONFIGURE HERE


// Register user
if (isset($_POST['register_user'])) {

  // Initialize sql query assemble
  $inserts = 'INSERT INTO '.$SRegister->tablename.' (';
  $values = ') VALUES (';

  // Fetch database credentials
  $x = 0;
  foreach($SRegister->columns as $elementName => $columnName) {

    if($x != 0) {
      // If not first iteration then add comma
      $inserts .= ",";
      $values .= ",";
    } else {
      // First row in array has to contain username
      $USERNAME_COLUMN = $columnName;
      $username = $_POST[$elementName];
    }
    if($x == 1) {
      // Second has to contain email
      $EMAIL_COLUMN = $columnName;
      $email = $_POST[$elementName];
    }
    if($x == 2) {
      // Third has to contain password
      // Encrypt password, more secure than md5
      $values .= '\''.password_hash($_POST[$elementName], PASSWORD_DEFAULT).'\'';
    } else {
      // If iteration count is anything else then 2, add pure data to sql query
      $values .= '\''.$_POST[$elementName].'\'';
    }
    // In which table column is the form data is supposed to go into:
    $inserts .= '`'.$columnName.'`';
    $x++;
  }
  // Close the sql query
  $Query = $inserts.$values.')';

  $main = new Main();
  $main->Register($username, $email, $_POST['password'], $_POST['password_confirm'], $USERNAME_COLUMN, $EMAIL_COLUMN, $Query, $SRegister);

}

// Login user
if (isset($_POST['login_user'])) {
  if($_SESSION['token'] != $_POST['token']) {
    $_SESSION['failure'] = "invalid_csrf";
    header('location: index.html');
  } else {

    // Generate new csrf token
    $_SESSION['token'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 32);

    // LOGIN[0] = username, LOGIN[1] = email, LOGIN[2] = password
    $main = new Main();
    $main->Login($_POST['username_or_email'], $_POST['password'], $LOGIN[0], $LOGIN[1], $LOGIN[2], $SLogin);

  }
}

// Logout with post form or get
if (isset($_POST['logout']) || $_GET['logout']) {
  unset($_SESSION['token'], $_SESSION['username'], $_SESSION['failure']);
  header('location: ../index.html');
}
?>
