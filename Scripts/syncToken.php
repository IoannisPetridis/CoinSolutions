<?php
require_once("config.php");
require_once("db_connect.php");
require_once("authorisation.php");
require_once("postFunctions.php");
checkAuthorised();
$data = json_decode(file_get_contents("php://input"),true);
$con = dbConnect($dbhost, $dbuser, $dbpass, $dbname);
$email = getPostValue(0,"email","string","email",true);
$token = getPostValue(0,"token","string","token",true);
if (mysqli_select_db($con,$dbname)) {
	$sql = "UPDATE User SET Firebase_token = '$token' WHERE Email = '$email';";
	if (mysqli_query($con,$sql)) {
		echo "OK";
	}
	else {
		echo mysqli_error($con);
	}
}
else {
	echo mysqli_error($con);
}
mysqli_close($con);	
?>