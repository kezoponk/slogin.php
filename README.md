# SLogin.php
<p align="center">
Secure login with csrf token, secure php password hashing, SQL injection and brute force immune
</p>

### Setup
You need to enter these values in the "*Credentials*" class before using

| Variable | Description |
| --- | --- |
| `$dbname` | Database containing user table |
| `$this->tablename` | Table containing all users |
| `$hostname` | Database host url / address |
| `$username and $password` | Entered database login credentials |

<br>

## Login
```html
<?php include('SLogin.php') ?>
<html>
...
<form method="post">
  <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>">
  
  <input type="text" name="email_username" placeholder=" Username ">
  <input type="password" name="password"  placeholder=" Password ">
  <button type="submit" name="login_user"> Login </button>
</form>
```

| Element Name | Used For |
| --- | --- |
| `token` | CSRF Token for brute force protection |
| `email_username` | Input containing the email OR username of user |
| `password` | Input containing user password |
| `login_user` | Submit button for post login |

<br>

## Register
```html
<?php include('SLogin.php') ?>
<html>
...
<form method="post">
  <input type="text" name="username" placeholder=" Username ">
  <input type="text" name="email" placeholder=" Email ">
  <input type="text" name="password_1" placeholder=" Password ">
  <input type="password" name="password_2"  placeholder=" Confirm password ">
  <button type="submit" name="register_user"> Login </button>
</form>
```

| Element Name | Used For |
| --- | --- |
| `          username          ` | Desired username, has to be unique |
| `email` | Users email, has to be unique |
| `password_1 & password_2` | Inputs containing the desired password |
| `register_user` | Submit button for post register |

