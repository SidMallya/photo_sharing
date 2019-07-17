<!DOCTYPE html>
<html>

<?php
echo "Logging out...";
session_start();
if(isset($_SESSION['email'])) {
	session_destroy();
} 
echo '<head>';
echo '<meta http-equiv="Refresh" content="2;url=index.php">';
echo '</head>';	
?>

</html>