# SLogin.php
Secure login with csrf token, secure php password hashing, and SQL injection immune

### Setup
| Variable | Description |
| --- | --- |
| `$dbname` | Help: Display all available flags |
| `-k` | Keep: Keep all captured packet files (deleted at end of session by default) |
| `-a` | Alert: Turn off successful crack alert |
| `-w <wordlist>` | Wordlist: Manually define a wordlist path (the script will prompt you otherwise) |
| `-i <interface>` | Interface: Manually set Wi-Fi interface (script should normally auto-detect the correct interface) |
| `-d <device>` | Device: Manually define 'devices' for hashcat |


       $dbname   = "register";
$this->tablename = "users";
       $hostname = "localhost";
       $username = "root";
       $password = "password";
<br>

## Example:

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
