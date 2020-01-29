# SLogin.php
Secure login with csrf token, secure php password hashing, SQL injection and brute force immune

### Setup
| Variable | Description |
| --- | --- |
| `$dbname` | Database containing user table |
| `$this->tablename` | Table containing all users |
| `$hostname` | Database host url / address |
| `$username and $password` | Entered database login credentials |


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
