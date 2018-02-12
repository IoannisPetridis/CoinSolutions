<?php
require_once("config.php");
require_once("db_connect.php");
require_once("authorisation.php");
require_once("postFunctions.php");
checkAuthorised();
$data = json_decode(file_get_contents("php://input"),true);
$con = dbConnect($dbhost, $dbuser, $dbpass, $dbname);
$transId = getPostValue(0,"transId","int","transId",true);
if (mysqli_select_db($con,$dbname)) {
	$sql = "SELECT Id, State FROM Transaction WHERE Id=$transId;";
	if ($result=mysqli_query($con,$sql)) {
		// Return the number of rows in result set
		$rowcount = mysqli_num_rows($result);
		if ($rowcount ==0) {
			die("Transaction doesn't exist!");
		}
		else {
			$ar = array();
			while ($row = mysqli_fetch_array($result)) {
				$obj = new stdClass();
				$obj->State = $row['State'];
				
				array_push($ar,$obj);
			}
			echo json_encode($ar);
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