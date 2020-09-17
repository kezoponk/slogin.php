# SLogin.php
- **Secure php password hashing with salt** 
- **Cross-site Request Forgery immune**
- **Sql Injection**

## Setup
You need to enter these values in the "*Credentials*" class before using

| Variable | Description |
| --- | --- |
| `$dbname` | Database containing user table |
| `$this->tablename` | Table containing all users |
| `$hostname` | Database host url / address |
| `$username and $password` | Entered database login credentials |

## Errors
Errors are stored in <code>$_SESSION['failure']</code>

| Error | Login or Register |
| --- | --- |
| `email_username_empty` | Both |
| `password_empty` | Both |
| `email_empty` | Both |
| `username_empty` | Register |
| `password_no_match` | Register |
| `email_exists` | Register |
| `username_exists` | Register |
| `user_does_not_exist` | Login |
| `wrong_password` | Login |
| `invalid_csrf` | Login |
<br>

## Examples
### Login
```html
<?php include('SLogin.php') ?>
<html>
...
<form method="post">
  <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>">
  <input type="text" name="email_username" placeholder=" Username " required autofocus>
  <input type="password" name="password"  placeholder=" Password " required>
  <button type="submit" name="login_user"> Login </button>
</form>
```

| Element Name | Used For |
| --- | --- |
| `token` | CSRF Token for Cross-site Request Forgery protection |
| `email_username` | Input containing the email **OR** username of user |
| `password` | Input containing user password |
| `login_user` | Submit button for post login |

<br>

### Register
```html
<?php include('SLogin.php') ?>
<html>
...
<form method="post">
  <input type="text" name="username" placeholder=" Username " required autofocus>
  <input type="text" name="email" placeholder=" Email " required>
  <input type="password" name="password_1" placeholder=" Password " required>
  <input type="password" name="password_2"  placeholder=" Confirm password " required>
  <button type="submit" name="register_user"> Login </button>
</form>
```

| Element Name | Used For |
| --- | --- |
| `username` | Desired username, has to be unique |
| `email` | Users email, has to be unique |
| `password_1 & password_2` | Inputs containing the desired password |
| `register_user` | Submit button for post register |
