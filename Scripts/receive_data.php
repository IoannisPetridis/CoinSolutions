<?php
mb_internal_encoding("UTF-8");

function check_data() {
	$values = array("velocity","direction","longitude","latitude","altitude","device_id","timestamp");
	foreach ($values as $key) {
		if (is_null($_GET[$key])) {
			echo "Null data or empty string".$key;
			return false;
		}
	}
	return true;
}

if (empty($_GET)) {
	die("No data received");
}
else {
	if (!check_data()) {	
		die("Wrong data");
	}
	else {
		if (filter_var($_GET['velocity'],FILTER_VALIDATE_FLOAT)) {
			$velocity = $_GET['velocity'];
		}
		if (filter_var($_GET['direction'],FILTER_VALIDATE_FLOAT)) {
			$direction = $_GET['direction'];
		}
		if (filter_var($_GET['longitude'],FILTER_VALIDATE_FLOAT)) {
			$longitude = $_GET['longitude'];
		}
		if (filter_var($_GET['latitude'],FILTER_VALIDATE_FLOAT)) {
			$latitude = $_GET['latitude'];
		}
		if (filter_var($_GET['altitude'],FILTER_VALIDATE_FLOAT)) {
			$latitude = $_GET['altitude'];
		}
		if (filter_var($_GET['device_id'],FILTER_SANITIZE_STRING)) {
			$device_id = $_GET['device_id'];
		}
		if (filter_var($_GET['accuracy'],FILTER_VALIDATE_FLOAT)) {
			$accuracy = $_GET['accuracy'];
		}
		if (filter_var($_GET['email'],FILTER_VALIDATE_EMAIL)) {
			$email = $_GET['email'];
		}
		if (filter_var($_GET['firstname'],FILTER_SANITIZE_STRING)) {
			$firstname = $_GET['firstname'];
		}
		if (filter_var($_GET['surname'],FILTER_SANITIZE_STRING)) {
			$surname = $_GET['surname'];
		}
		if (filter_var($_GET['comment'],FILTER_SANITIZE_STRING)) {
			$comment = $_GET['comment'];
		}
		if (filter_var($_GET['timestamp'],FILTER_SANITIZE_ENCODED)) {
			$timestamp = $_GET['timestamp'];
		}
		
		require_once("db_connect.php");

		if (mysqli_select_db($con,$dbname)) {
			$sql = "SELECT Users.device_id FROM $dbname.Users WHERE (Users.device_id='$device_id')";
			if ($result=mysqli_query($con,$sql)) {
				// Return the number of rows in result set
				$rowcount=mysqli_num_rows($result);
				if ($rowcount ==0) {
					$sql = "INSERT INTO $dbname.Users (surname, firstname, email, device_id) VALUES ('$surname', '$firstname', '$email','$device_id')";
				}
				else if ($rowcount ==1){
					$sql = "UPDATE $dbname.Users SET surname='$surname', firstname='$firstname' WHERE (Users.device_id='$device_id')";
				}
				mysqli_free_result($result);
			}
			if (mysqli_query($con,$sql)) {
				$sql = "INSERT INTO $dbname.Wind_Data (timestamp, longitude, latitude,altitude,accuracy,velocity,direction,comment,device_id) VALUES ('$timestamp', '$longitude', '$latitude','$altitude','$accuracy','$velocity','$direction','$comment','$device_id')";
				if (!mysqli_query($con,$sql)) {
					die(mysqli_error($con));
				}
				else {
					echo "All good here";
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
	}
}
?>