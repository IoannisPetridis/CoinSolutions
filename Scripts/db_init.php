<html>
<body>
<?php
require_once("db_connect.php");
include("generate_data.php");
echo "<table>";
$sql = "CREATE DATABASE $dbname DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci";  //Η MySql εντολή προς εκτέλεση για δημιουργία ΒΔ με όνομα $dbname
if (mysqli_query($con, $sql)) { //Δημιουργία ΒΔ με όνομα $dbname
	echo "<tr><td>Database $dbname created successfully!</td></tr>";
}
else {
	echo "<tr><td>".mysqli_error($con)."</td></tr>";
}
if (mysqli_select_db($con,$dbname)) { //Επιλέγουμε τη ΒΔ με το όνομα $dbname...
	echo "<tr><td>Database $dbname selected successfully!</td></tr>";
	$sql1 = "CREATE TABLE IF NOT EXISTS Users (surname VARCHAR(255), firstname VARCHAR(255), email VARCHAR(255) NOT NULL, device_id VARCHAR(255) NOT NULL, PRIMARY KEY (device_id))DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB;"; //MySql ερώτημα προς εκτέλεση για τη δημιουργία του πίνακα 'Users'
	$sql2 = "CREATE TABLE IF NOT EXISTS Wind_Data (count MEDIUMINT NOT NULL AUTO_INCREMENT, timestamp DATETIME NOT NULL, longitude DOUBLE NOT NULL, latitude DOUBLE NOT NULL, altitude DOUBLE NOT NULL, accuracy DOUBLE NOT NULL, velocity DOUBLE NOT NULL, direction DOUBLE NOT NULL, comment VARCHAR(255), device_id VARCHAR(255) NOT NULL, PRIMARY KEY (count), CONSTRAINT FOREIGN KEY(device_id) REFERENCES $dbname.Users(device_id) ON DELETE CASCADE ON UPDATE CASCADE)DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB;";
	
	if (mysqli_query($con,$sql1)) {
		echo "<tr><td>Table Users created successfully!</td></tr>";
		if (mysqli_query($con,$sql2)) {
			echo "<tr><td>Table Wind_Data created successfully!</td></tr>";
			for ($i=0;$i<10;$i++) {
				$firstname = array_shift($users);
				$surname = array_shift($users);
				$email = array_shift($users);
				$device_id = array_shift($users);
		
				$sql3 = "INSERT INTO $dbname.Users (surname, firstname, email, device_id) VALUES ('$surname', '$firstname', '$email','$device_id')";
				if (mysqli_query($con,$sql3)) {
					$timestamp = array_shift($wind_data);
					$longitude = array_shift($wind_data);
					$latitude = array_shift($wind_data);
					$altitude = array_shift($wind_data);
					$accuracy = array_shift($wind_data);
					$velocity = array_shift($wind_data);
					$direction = array_shift($wind_data);
					$comment = array_shift($wind_data);
			
					$sql4 = "INSERT INTO $dbname.Wind_Data (timestamp, longitude, latitude,altitude,accuracy,velocity,direction,comment,device_id) VALUES ('$timestamp','$longitude','$latitude','$altitude','$accuracy','$velocity','$direction','$comment','$device_id')";
					if (!mysqli_query($con,$sql4)) {
						die(mysqli_error($con));
					}					
				}
				else {
					echo "<tr><td>".mysqli_error($con)."</td></tr>";
				}
			}
			echo "<tr><td>Table Users filled with data!</td></tr>";
			echo "<tr><td>Table Wind_Data filled with data!</td></tr>";
		}
		else {
			echo "<tr><td>".mysqli_error($con)."</td></tr>";
		}
	}
	else {
		echo "<tr><td>".mysqli_error($con)."</td></tr>";
	}
}
else {
	echo "<tr><td>".mysqli_error($con)."</td></tr>";
}	
echo "</table>";
mysqli_close($con); //Κλείνουμε τη σύνδεση με τη ΒΔ
?>
</body>
</html>