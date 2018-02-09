<?php
if (isset($_GET['q']) && ($_GET['q'] =='ALL')) { //That means the user requested for all data in DB
	$sql = "SELECT * FROM $dbname.Wind_Data, $dbname.Users WHERE (Wind_Data.device_id = Users.device_id)";
}
else if (isset($_GET['name']) && is_string($_GET['name'])) { //That means the user requested for all data of specified user
	$check = explode(' ',$_GET['name']);
	if (count($check)==2) {
		$sql = "SELECT * FROM $dbname.Wind_Data, $dbname.Users WHERE (((Users.firstname LIKE '$check[0]' AND Users.surname LIKE '$check[1]') OR (Users.surname LIKE '$check[0]' AND Users.firstname LIKE '$check[1]')) AND Wind_Data.device_id = Users.device_id)";
	}
	else if (count($check)==1){
		$sql = "SELECT * FROM $dbname.Wind_Data, $dbname.Users WHERE ((Users.firstname LIKE '$check[0]' OR Users.surname LIKE '$check[0]') AND Wind_Data.device_id = Users.device_id)";
	}
}
else if (isset($_GET['days']) && is_numeric($_GET['days'])) { //That means the user requested for data of x days
	$check = $_GET['days'];
	$sql = "SELECT * FROM $dbname.Wind_Data, $dbname.Users WHERE (Wind_Data.device_id = Users.device_id AND Wind_Data.timestamp >= DATE_SUB(NOW(), INTERVAL $check DAY))";
}
else if (isset($_GET['velocity']) && is_numeric($_GET['velocity'])) {
	$check = $_GET['velocity'];
	$sql = "SELECT * FROM $dbname.Wind_Data, $dbname.Users WHERE (Wind_Data.device_id = Users.device_id AND Wind_Data.velocity >= '$check')";
}
else if (isset($_GET['altitude']) && is_numeric($_GET['altitude'])) {
	$check = $_GET['altitude'];
	$sql = "SELECT * FROM $dbname.Wind_Data, $dbname.Users WHERE (Wind_Data.device_id = Users.device_id AND Wind_Data.altitude >= '$check')";
}
else if (isset($_GET['email']) && is_string($_GET['email'])) {
	$check = $_GET['email'];
	$sql = "SELECT * FROM $dbname.Wind_Data, $dbname.Users WHERE (Users.email = '$check' AND Wind_Data.device_id = Users.device_id)";
}
else { //That means the user didn't specify an API keyword
	$sql = "SELECT * FROM $dbname.Wind_Data, $dbname.Users WHERE (Wind_Data.device_id = Users.device_id AND Wind_Data.timestamp >= DATE_SUB(NOW(), INTERVAL 15 MINUTE))";
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
require_once("db_connect.php");

// Set the active mySQL database
$db_selected = mysqli_select_db($con,$dbname);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysqli_error($con));
}

$result = mysqli_query($con,$sql); //Run the query
if (!$result) {
  die('Invalid query: ' . mysqli_error($con));
}

header("Content-type: text/xml");
header("Access-Control-Allow-Origin: *");

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
}echo '</measurements>';

// End XML file

mysqli_close($con);
?>