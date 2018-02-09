<?php
//debug_backtrace() || header("Location:Wind site/index2.html");
	
echo '<?xml version="1.0" encoding="UTF-8"?>';
require_once("db_connect.php");

// Set the active mySQL database
$db_selected = mysqli_select_db($con,$dbname);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysqli_error($con));
}
// Get all the measurements done in the last 5 minutes
$sql = "SELECT * FROM $dbname.Wind_Data, $dbname.Users WHERE (Wind_Data.device_id = Users.device_id)";

$result = mysqli_query($con,$sql);
if (!$result) {
  die('Invalid query: ' . mysqli_error($con));
}

header("Content-type: text/xml");

// Start XML file, echo parent node
echo '<measurements>';

// Iterate through the rows, printing XML nodes for each
while ($row = mysqli_fetch_array($result)){
  // ADD TO XML DOCUMENT NODE
	echo '<measurement';
	echo ' name="' .$row['firstname'].' '.$row['surname'].'" ';
	echo 'timestamp="' .$row['timestamp'].'" ';
	echo 'longitude="' .$row['longitude'].'" ';
	echo 'latitude="' .$row['latitude'].'" ';
	echo 'altitude="' .$row['altitude'].'" ';
	echo 'velocity="' .$row['velocity'].'" ';
	echo 'direction="' .$row['direction'].'" ';
	echo 'accuracy="' .$row['accuracy'].'" ';
	echo 'comment="' .$row['comment'].'" ';
	echo '/>';
}

// End XML file
echo '</measurements>';
mysqli_close($con);
?>