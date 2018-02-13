<?php
require_once("config.php");
require_once("db_connect.php");
require_once("authorisation.php");
require_once("postFunctions.php");
checkAuthorised();
//wget -O /dev/null http://giannispetridis.xyz/CoinSolutions/Scripts/transProcessor.php?pass=masterpass
if (mysqli_select_db($con,$dbname)) {
	$sql = "SELECT * FROM Transaction WHERE State=0 ORDER BY Transaction.Timestamp_created ASC";
	if ($result=mysqli_query($con,$sql)) {
		while ($row = mysqli_fetch_array($result)) {
			//TODO: The actual processing
		}
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