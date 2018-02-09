<?php
require_once("config.php");
$con = mysqli_connect($dbhost,$dbuser,$dbpass); //Σύνδεση στο "localhost" με Username "root" και Password κενό.
if (!$con) {  //Αν αποτύχουμε να συνδεθούμε...	
	die(mysqli_connect_error()); //Μήνυμα αποτυχίας
}
if (!mysqli_set_charset($con, "utf8")) {	
	echo mysqli_error($con);
}
?>
