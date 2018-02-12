<?php
require_once("config.php");
require_once("db_connect.php");
require_once("authorisation.php");
require_once("postFunctions.php");
checkAuthorised();
//wget -O /dev/null http://giannispetridis.xyz/CoinSolutions/Scripts/transProcessor.php?pass=masterpass
if (mysqli_select_db($con,$dbname)) {
	$sql = "CREATE EVENT testevent ON SCHEDULE AT CURRENT_TIMESTAMP EVERY 1 SECOND DO (SELECT ) ";
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