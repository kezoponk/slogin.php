<?php
// @author Albin Eriksson, https://github.com/kezoponk
// @license MIT, https://opensource.org/licenses/MIT

class Credentials {

  function __construct() {
    // Database credentials
    $dbname   = "register";
    $this->tablename = "users";
    $hostname = "localhost";
    $username = "root";
    $password = "";

    // Redirects
    $this->successRedirect = "../index.html";
    $this->registrationExceptionRedirect = "../reg.html";
    $this->loginExceptionRedirect = "../login.html";

    // Attempt database connection
    try {
      $this->database = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8", $username, $password);
      $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
      // If database connection failed
      echo "Database connection failed: <br>".$e;
    }
  }
}

session_start();

// Create csrf token
if(!isset($_SESSION['token'])) { $_SESSION['token'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 32); }

// Main class of SLogin
class SLogin {

  function Login($email_username, $password, $credentials) {
    // Initialize error variable
    $_SESSION['failure'] = "none";
    $exists = 0;

    // In case the form elements don't have the required attribute
    if (empty($email_username)) {
      $_SESSION['failure'] = "username_empty";
    }
    if (empty($password)) {
      $_SESSION['failure'] = "password_empty";
    }

    $query = "SELECT * FROM $credentials->tablename WHERE (username='$email_username' OR email='$email_username')";
    $stmt = $credentials->database->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();

    // Get username and the hashed & salted password
    foreach($result as $row) {
      // Check if entered password is the same as database password
      if(password_verify($password, $row['password'])) {
        // Assign username variable as the users username contained in database
        $_SESSION['username'] = $row['username'];
      }
      $exists++;
    }

    if($exists < 1) {
      // Check if user even exists
      $_SESSION['failure'] = "user_does_not_exist";
    } else if(!isset($_SESSION['username'])) {
      // If password not correct
      $_SESSION['failure'] = "wrong";
    }

    if ($_SESSION['failure'] == "none") {
      header('location: '.$credentials->successRedirect);
    } else {
      // If anything went wrong then redirect to entered exception redirect
      header('location: '.$credentials->loginExceptionRedirect);
    }
  }

  function Register($username, $email, $password_1, $password_2, $credentials) {
    $_SESSION['failure'] = "none";

    // Secure fields
    if (empty($username)) { $_SESSION['failure'] = "username_empty"; }
    if (empty($email)) { $_SESSION['failure'] = "email_empty"; }
    if (empty($password_1)) { $_SESSION['failure'] = "password_empty"; }
    if ($password_1 != $password_2) {
      $_SESSION['failure'] = "password_nomatch";
    }
    // Check if username and email is not taken
    $userCheckQuery = "SELECT * FROM $credentials->tablename WHERE username='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($credentials->database, $userCheckQuery);
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

      $query = "INSERT INTO $credentials->tablename (username, email, password)
      VALUES('$username', '$email', '$password')";

      // Upload user to database/table
      $stmt = $credentials->database->prepare($query);
      $stmt->execute();

      // Assign username variable to entered username
      $_SESSION['username'] = $username;
      header('location: '.$credentials->successRedirect);
    }
    header('location: '.$credentials->registerExceptionRedirect);
  }
}
// Register user
if (isset($_POST['register_user'])) {
  // Fetch database credentials
  $credentials = new Credentials();

  $SLogin = new SLogin();
  $SLogin->Register($_POST['username'], $_POST['email'], $_POST['password_1'], $_POST['password_2'], $credentials);

}
// Login user
if (isset($_POST['login_user'])) {
  // Fetch database credentials
  $credentials = new Credentials();
  // Validate csrf token before attempting login
  if($_SESSION['token'] != $_POST['token']) {
    $_SESSION['failure'] = "invalid_csrf";
    header('location: '.$credentials->loginExceptionRedirect);
  } else {

    // Generate new csrf token
    $_SESSION['token'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 32);

    $SLogin = new SLogin();
    $SLogin->Login($_POST['email_username'], $_POST['password'], $credentials);
  }
}
// Logout with post form or get
if (isset($_POST['logout']) || isset($_GET['logout'])) {
  $credentials = new Credentials();
  unset($_SESSION['token'], $_SESSION['username'], $_SESSION['failure']);
  header('location: '.$credentials->successRedirect);
}
?>
