<?php
	$lastname = $_POST['lastname'];
	$firstname = $_POST['firstname'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$conf_password = $_POST['conf_password'];
	
	$fields_valid = 1;
	
	if (trim($email)=="") {
		echo "Email cannot be blank.<br>";
		$fields_valid = 0;
	}
	
	if (trim($password)=="") {
		echo "Password cannot be blank.<br>";
		$fields_valid = 0;
	}
	
	if (trim($conf_password)=="") {
		echo "Confirm Password cannot be blank.<br>";
		$fields_valid = 0;
	}
	
	if ($password != $conf_password) {
		echo "Passwords don't match.<br>";
		$fields_valid = 0;
	}
	
	if ($fields_valid) {
	
		include 'config.php';	
		$link = mysqli_connect($endpoint, $user, $pwd, $DB);
	
		if (!$link) {
			echo "Error: Unable to connect to MySQL." . PHP_EOL;
			echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
			echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
			exit;
		}
		
		$insert = "INSERT INTO user (lastname, firstname, email, password) 
                        	VALUES ('".$lastname."','".$firstname."','".$email."',sha1('".$password."'))";
 			   
   		$insert_result = mysqli_query($link, $insert);
		if ($insert_result) {
		  	echo 'Registration successful.  Redirecting to login page...<br>	';
		  	echo '<meta http-equiv="Refresh" content="2;url=index.php">';
		}
		else {
			echo "Registration unsuccessful due to technical problems.  Error(for support personnel): ".mysqli_error($link)." <br>";
			echo '<meta http-equiv="Refresh" content="5;url=register.php">';
		}
		
		mysqli_close($link);

	}
	
?>
