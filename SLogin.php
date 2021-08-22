<?php
// @author Albin Eriksson, https://github.com/kezoponk
// @license MIT, https://opensource.org/licenses/MIT

session_start();
// csrf token
if(!isset($_SESSION['token'])) { $_SESSION['token'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 32); }

class SLogin {
  // Database
  private $dbname   = 'slogintest';
  private $tablename = 'users';
  private $hostname = '127.0.0.1';
  private $username = 'root';
  private $password = 'Necini26';
  private $debug = TRUE;

  // Redirects
  public $successRedirect = '../index.html';
  private $registerExceptionRedirect = '../register.html';
  private $loginExceptionRedirect = '../login.html';


  private function PDOConnection() : object {
    try {
      $database = new PDO("mysql:host=$this->hostname;dbname=$this->dbname;charset=utf8", $this->username, $this->password);

      if ($this->debug) $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return $database;

    } catch(PDOException $e) {
      echo 'Database connection failed: <br>'.$e;
    }
  }

  private function validateCsrf() {
    // Validate csrf token before attempting login
    if($_SESSION['token'] != $_POST['token']) {
      $_SESSION['failure'] = 'invalid_csrf';
      header('location: '.$this->loginExceptionRedirect);

    } else {
      // Generate new csrf token
      $_SESSION['token'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 32);
    }
  }

  public function Login($email_username, $password) {
    // Initialize error variable
    $_SESSION['failure'] = NULL;

    $this->validateCsrf();

    // In case the form elements don't have the required attribute
    if (empty($email_username)) {
      $_SESSION['failure'] = 'email_username_empty';
    }
    if (empty($password)) {
      $_SESSION['failure'] = 'password_empty';
    }
    
    $database = $this->PDOConnection();

    $query = "SELECT * FROM $this->tablename WHERE (username=? OR email=?)";

    $stmt = $database->prepare($query);

    $stmt->execute(array($email_username, $email_username));

    $result = $stmt->fetchAll();

    // Get username and the hash+salt password
    foreach($result as $row) {

      // Check if entered password is the same as database password
      if(password_verify($password, $row['password'])) {
        // Assign username variable as the users username contained in database
        $_SESSION['username'] = $row['username'];
      }
      
    }


    if(count($result) < 1) {
      // Check if user even exists
      $_SESSION['failure'] = 'user_does_not_exist';

    } else if(!isset($_SESSION['username'])) {
      // Will trigger only if password was incorrect
      $_SESSION['failure'] = 'wrong_password';
    }


    if (empty($_SESSION['failure'])) {
      header('location: '.$this->successRedirect);

    } else {
      // If anything went wrong then redirect to entered exception redirect
      header('location: '.$this->loginExceptionRedirect);
    }
  }

  public function Register($username, $email, $password_1, $password_2) {
    $_SESSION['failure'] = NULL;

    // Secure fields
    if (empty($username)) { $_SESSION['failure'] = 'username_empty'; }
    if (empty($email)) { $_SESSION['failure'] = 'email_empty'; }
    if (empty($password_1)) { $_SESSION['failure'] = 'password_empty'; }
    if ($password_1 != $password_2) {
      $_SESSION['failure'] = 'password_no_match';
    }

    // Check if username and email is not taken
    $database = $this->PDOConnection();
    
    $query = "SELECT * FROM $this->tablename WHERE username=? OR email=? LIMIT 1";

    $stmt = $database->prepare($query);
    
    $stmt->execute(array($username, $email));

    $result = $stmt->fetchAll();

    foreach($result as $user) {
      if ($user['username'] === $username) {
        $_SESSION['failure'] = 'username_exists';
      }
      // If email already exists*
      if ($user['email'] === $email) {
        $_SESSION['failure'] = 'email_exists';
      }
    }

    if (empty($_SESSION['failure'])) {
      // Encrypt password, more secure than md5
      $password = password_hash($password_1, PASSWORD_BCRYPT);

      $query = "INSERT INTO $this->tablename (`username`, `email`, `password`)
                                                  VALUES(?, ?, ?)";

      // Upload user to database/table
      $stmt = $database->prepare($query);
      $stmt->execute(array($username, $email, $password));

      // Assign username variable to entered username
      $_SESSION['username'] = $username;
      header('location: '.$this->successRedirect);
      
    } else {
      header('location: '.$this->registerExceptionRedirect);
    }
  }
}

// Register user
if (isset($_POST['register_user'])) {
  $SLogin = new SLogin();
  $SLogin->Register($_POST['username'], $_POST['email'], $_POST['password_1'], $_POST['password_2']);
}

// Login user
if (isset($_POST['login_user'])) {
    $SLogin = new SLogin();
    $SLogin->Login($_POST['email_username'], $_POST['password']);
}

// Logout with post form or get
if (isset($_POST['logout']) || isset($_GET['logout'])) {
  unset($_SESSION['token'], $_SESSION['username'], $_SESSION['failure']);

  $SLogin = new SLogin();
  header('location: '.$SLogin->successRedirect);
}
