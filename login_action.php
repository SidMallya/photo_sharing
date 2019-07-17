<!DOCTYPE html>
<html>

<?php
if (isset($_POST['email']) && isset($_POST['password'])) {		
   $fields_ok = 1;
   $email = trim($_POST['email']);
   $password = $_POST['password'];
   
   if ($email == "" or $password == "") {
		$fields_ok = 0;
		echo "All fields are mandatory.";
		echo '<head>';
		echo '<meta http-equiv="Refresh" content="2;url=login.php">';
		echo '</head>';
   }
}
   
if($fields_ok) {
   
	include 'config.php';	
	$link = mysqli_connect($endpoint, $user, $pwd, $DB);

	if (!$link) {
		echo "Error: Unable to connect to MySQL." . PHP_EOL;
		echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
		echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
		exit;
	}
	
	$result = mysqli_query($link, "SELECT COUNT(*) AS row_count FROM user WHERE email = '".addslashes($email)."' AND password = SHA1('".addslashes($password)."')");
	mysqli_close($link);
	$row = mysqli_fetch_assoc($result);
	if ($row['row_count']) {
		echo "Login successful.";
		session_start();
		$_SESSION['email'] = $email;
		echo '<head>';
		echo '<meta http-equiv="Refresh" content="2;url=show_photos.php">';
		echo '</head>';
	}
	else {
		echo "Authorization failed.";  
		echo '<head>';
		echo '<meta http-equiv="Refresh" content="2;url=index.php">';
		echo '</head>'; 
	}
	mysqli_free_result($result);
}  
?>

</html>