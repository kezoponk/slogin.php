# SLogin.php
<p align="center"><br>
  <strong>Secure login with csrf token, secure php password hashing, SQL injection and brute force immune</strong> <br>
  <a href="#Configurable"> Configurable </a> | <a href="#Static"> Static </a>
</p>
<br>

## Configurable

#### Setup
The login and register are configured in middle of SLogin-CONFIGURABLE.php, line 133 <br>
Login: <code>$SLogin = new SLogin......</code> <br>
Register: <code>$SRegister = new SLogin......</code> <br>

#### Arguments
>Arg 1 <br>
>
>> Variable of the array containing names of the user table columns <br>
>> And in register, containing element names in form
>
>Arg 2 <br>
>Database containing users table
>
>Arg 3 <br>
>Table containing all users
>
>Arg 4 <br>
>Database host ( Example localhost or 127.0.0.1 )
>
>Arg 5 <br>
>Database login ( Example root )
>
>Arg 6 <br>
>Database password




<br>

## Login
```html
<?php include('SLogin.php') ?>
<html>
...
<form method="post">
  <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>"> 
  
  <input type="text" name="username_or_email" placeholder=" Username "> <!-- name has to be username_or_email  -->
  <input type="password" name="password"  placeholder=" Password ">     <!-- name has to be password  -->
  <button type="submit" name="login_user"> Login </button>
</form>
```
```php
$LOGIN = array(
  "username", // Database table column containing username
  "email",    
  "password"  
);

$SLogin = new SLogin($LOGIN, "register", "users", "localhost", "root", "password");
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
  <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>"> <!-- REQUIRED -->
  
  <input type="text" name="username" placeholder=" Username ">
  <input type="text" name="email" placeholder=" Email ">
  
  <input type="text" name="address" placeholder=" Address ">
  <input type="text" name="postcode" placeholder=" Postcode ">
  
  <input type="text" name="password" placeholder=" Password ">
  <input type="password" name="password_confirm" placeholder="Requires this exact name"> <!-- REQUIRED -->
  <button type="submit" name="register_user"> Login </button>
</form>
```
```php
// Element-name => Database-table-column-name

$REGISTER = array(
  "username" => "user", // Element name needs to be username
  "email" => "mail",    // Element name needs to be email
  "password" => "password", // Element name needs to be password
  "address" => "location",
  "postcode" => "postcode"
);

$SRegister = new SLogin($REGISTER, "register", "users", "localhost", "root", "password");
```

| Element Name | Used For |
| --- | --- |
| `username` | Desired username, has to be unique |
| `email` | Users email, has to be unique |
| `password & password_confirm` | Inputs containing the desired password, both are mandatory with these exact names |
| `register_user` | Submit button for post register |

<br>
<br>

## Static

#### Setup
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
`#configurable` 
| Element Name | Used For |
| --- | --- |
| `          username          ` | Desired username, has to be unique |
| `email` | Users email, has to be unique |
| `password_1 & password_2` | Inputs containing the desired password |
| `register_user` | Submit button for post register |
