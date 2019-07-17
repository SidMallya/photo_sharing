<!DOCTYPE html>
<html>
<head>
<title>Photo Sharing</title>
<h1 align="center">Photo Sharing</h1>
</head>

<?php
	session_start();
	if(isset($_SESSION['email'])) {
		echo 'Already logged in, redirecting...';
		echo '<head>';
		echo '<meta http-equiv="Refresh" content="2;url=show_photos.php">';
		echo '</head>';
		die();
	}
?>

<body>

<table>
<tr>
<td>

<body>
<h2>Login</h2>

<form action="login_action.php" method="post">
  Email:<br>
  <input type="text" name="email">
  <br>
  Password:<br>
  <input type="password" name="password">
  <br><br>
  <input type="submit" value="Submit">
</form> 
<br>
<a href="register.php">New User</a>
</td>
</tr>
<tr>
<td>
<br>
<img src="images/background.png" alt="Background">
</td>
</tr>
</table>

</body>
</html>
