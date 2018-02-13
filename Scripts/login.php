<?php
require_once("config.php");
require_once("db_connect.php");
require_once("authorisation.php");
require_once("postFunctions.php");
checkAuthorised();
$data = json_decode(file_get_contents("php://input"),true);
$con = dbConnect($dbhost, $dbuser, $dbpass, $dbname);
$email = mysqli_real_escape_string($con,getPostValue(0,"email","string","email",true));
if (mysqli_select_db($con,$dbname)) {
	$sql = "SELECT Email FROM User WHERE Email = '$email';";
	if ($result=mysqli_query($con,$sql)) {
		// Return the number of rows in result set
		$rowcount = mysqli_num_rows($result);
		if ($rowcount ==0) {
			die("unknown user");
		}
		else if ($rowcount ==1){
			//User already exists in the DB
			mysqli_close($con);	
			echo "OK";
		}
		mysqli_free_result($result);
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