<?php
// Created by Albin Eriksson, https://github.com/kezoponk
// MIT License, https://opensource.org/licenses/MIT

class Credentials {
  function __construct() {

    // Database credentials
       $dbname   = "register";
$this->tablename = "users";
       $hostname = "localhost";
       $username = "root";
       $password = "password";

    // Attempt database connection
    try {
      $this->database = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8", $username, $password);
      $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {

      // If database connection failed
      $_SESSION['failure'] = 'nodb';
      header('location: index.html');
    }
  }
}

session_start();

// Create csrf token
if(!isset($_SESSION['token'])) { $_SESSION['token'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 32); }

// Main class of SLogin
class SLogin {

  function Login($email_username, $password, $database, $tablename) {
    // Initialize error variable
    $_SESSION['failure'] = "none";

    // In case the form elements don't have the required attribute
    if (empty($email_username)) {
      $_SESSION['failure'] = "username_empty";
    }
    if (empty($password)) {
      $_SESSION['failure'] = "password_empty";
    }

    if ($_SESSION['failure'] == "none") {

      $query = "SELECT * FROM $tablename WHERE (username='$email_username' OR email='$email_username')";

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

  function Register($username, $email, $password_1, $password_2, $database, $tablename) {
    $_SESSION['failure'] = "none";

    // Secure fields
    if (empty($username)) { $_SESSION['failure'] = "username_empty"; }
    if (empty($email)) { $_SESSION['failure'] = "email_empty"; }
    if (empty($password_1)) { $_SESSION['failure'] = "password_empty"; }
    if ($password_1 != $password_2) {
      $_SESSION['failure'] = "password_nomatch";
    }

    // Check if username and email is not taken
    $user_check_query = "SELECT * FROM $tablename WHERE username='$username' OR email='$email' LIMIT 1";
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

      // Encrypt password, more secure than md5
      $password = password_hash($password_1, PASSWORD_DEFAULT);

      $query = "INSERT INTO $tablename (username, email, password)
      VALUES('$username', '$email', '$password')";

      // Upload user to database/table
      $stmt = $database->prepare($query);
      $stmt->execute();

      // Assign username variable to entered username
      $_SESSION['username'] = $username;
    }
    header('location: index.html');
  }
}

// Register user
if (isset($_POST['register_user'])) {
  // Fetch database credentials
  $credentials = new Credentials();

  $SLogin = new SLogin();
  $SLogin->Register($_POST['username'], $_POST['email'], $_POST['password_1'], $_POST['password_2'], $credentials->database, $credentials->tablename);

}

// Login user
if (isset($_POST['login_user'])) {
  if($_SESSION['token'] != $_POST['token']) {
    $_SESSION['failure'] = "invalid_csrf";
    header('location: index.html');
  } else {

    // Fetch database credentials
    $credentials = new Credentials();

    // Generate new csrf token
    $_SESSION['token'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 32);

    $SLogin = new SLogin();
    $SLogin->Login($_POST['email_username'], $_POST['password'], $credentials->database, $credentials->tablename);
  }
}
// Logout with post form or get
if (isset($_POST['logout']) || $_GET['logout']) {
  unset($_SESSION['token'], $_SESSION['username'], $_SESSION['failure']);
  header('location: ../index.html');
}
?>
