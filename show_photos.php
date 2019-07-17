<!DOCTYPE html>
<html>

<head>
<title>My Photos</title>
</head>

<body>

<?php
include 'config.php';
require 'vendor/autoload.php';
date_default_timezone_set('UTC');
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

session_start();

if(isset($_SESSION['email'])) {
	echo '<P ALIGN=RIGHT><b>';
	echo $_SESSION['email'];
	echo '</b> ';
	echo '<a href="logout.php">Logout</a>';
	echo '</P>';
} 
else {
	echo '<head>';
	echo '<meta http-equiv="Refresh" content="2;url=index.php">';
	echo '</head>';	
	die();
}

echo "<b>Photo share link: </b>";
$public_hostname = file_get_contents("http://169.254.169.254/latest/meta-data/public-hostname");
$dir_name = basename(dirname(__FILE__));
echo $public_hostname."/".$dir_name."/share_photos.php?email=".$_SESSION['email'];
echo "<br><br>";

?>

<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" enctype="multipart/form-data">
    Select photo to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$target_dir = "uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	
	// Check if file already exists
	$link = mysqli_connect($endpoint, $user, $pwd, $DB);

	if (!$link) {
		echo "Error: Unable to connect to MySQL." . PHP_EOL;
		echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
		echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
		exit;
	}
	
	$result = mysqli_query($link, "SELECT COUNT(*) AS photo_count FROM photo WHERE imagename = '".basename($_FILES["fileToUpload"]["name"])."'");
	mysqli_close($link);
	$row = mysqli_fetch_assoc($result);
	if ($row['photo_count']) {
		echo "Photo name already used. ";
		$uploadOk = 0;
	}
	mysqli_free_result($result);
	
	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 10000000) {
		echo "Your photo size exceeds the limit of 10MB.";
		$uploadOk = 0;
	}
	
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Your photo was not uploaded.";
	// if everything is ok, try to upload file
	} 
	else {
		try {
			move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
			$file_Path = $target_file;
			$key = basename($file_Path);

			//Upload to S3
			try {
				//Create a S3Client
				$s3Client = new S3Client([
					'region' => $region,
					'version' => '2006-03-01'
				]);
				$result = $s3Client->putObject([
					'Bucket' => $bucket,
					'Key' => $key,
					'SourceFile' => $file_Path,
				]);
				echo "Upload to S3 was successful.<br>";
				
				//Create an entry in the DB
				$link = mysqli_connect($endpoint, $user, $pwd, $DB);
			
				if (!$link) {
					echo "Error: Unable to connect to MySQL." . PHP_EOL;
					echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
					echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
					exit;
				}
				
				$insert = "INSERT INTO photo (userid, imagename) 
							SELECT ID, '".$key."' FROM user WHERE email = '".$_SESSION['email']."'";
					   
				$insert_result = mysqli_query($link, $insert);
				if ($insert_result) {
					echo 'Entry updated in photo table.<br>';
					unlink($target_file);
				}
				else 
					echo "Failed to update photo table.  Error(for support personnel): ".mysqli_error($link)." <br>";
				
				mysqli_close($link);
								
			} catch (S3Exception $e) {
				echo 'S3 Exception: ' . $e->getMessage() . "\n";
			}

		} catch (Exception $e) {
    		echo 'Move to temporary location failed: ',  $e->getMessage(), "\n";
		} 
					
	}
}
?>

<h2>My Photos</h2>

<table>

<?php

	//Show photos uploaded by user
	$link = mysqli_connect($endpoint, $user, $pwd, $DB);

	if (!$link) {
		echo "Error: Unable to connect to MySQL." . PHP_EOL;
		echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
		echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
		exit;
	}
	 
	$query = "SELECT imagename AS image_name FROM user, photo ";
	$query = $query. "WHERE user.id = photo.userid AND email = '".addslashes($_SESSION['email'])."'";
	$result = mysqli_query($link, $query);
	mysqli_close($link);

    while ($row = mysqli_fetch_assoc($result)) {

		$image_name = $row['image_name'];
		$url_part1 = "https://";
		$url_part2 = ".s3.amazonaws.com/";
		$image_url = $url_part1.$bucket.$url_part2.$image_name;
		echo "<tr>";
		echo "<td>";
		echo '<img src="'.$image_url.'">';
		echo "</td>";
		echo "</tr>";

    }
    mysqli_free_result($result);
	
?>

</table>

</body>
</html>
