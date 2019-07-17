<!DOCTYPE html>
<html>
<head>
<title>Shared Photos</title>
</head>
<body>
<table>

<?php
include 'config.php';
$email = $_GET["email"];
echo "<b>";
echo "Photos shared by ".$email;
echo "</b><br>";


//Show photos uploaded by user
$link = mysqli_connect($endpoint, $user, $pwd, $DB);

if (!$link) {
	echo "Error: Unable to connect to MySQL." . PHP_EOL;
	echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
	echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
	exit;
}
 
$query = "SELECT imagename AS image_name FROM user, photo ";
$query = $query. "WHERE user.id = photo.userid AND email = '".addslashes($email)."'";
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