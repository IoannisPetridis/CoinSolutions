<html>
<body>
<?php
require_once("config.php");
require_once("db_connect.php");
require_once("authorisation.php");
echo "<table>";
checkAuthorised($dbhost,$dbuser,$dbpass,$dbname);

$con = dbConnect() or die(mysqli_connect_error());
if (mysqli_select_db($con,$dbname)) {
	echo "<tr><td>Database $dbname selected successfully!</td></tr>";
	$sql1 = "CREATE TABLE IF NOT EXISTS User ( Id MEDIUMINT NOT NULL AUTO_INCREMENT, Name VARCHAR(512), Description VARCHAR(1000), Email VARCHAR(1000) NOT NULL, Account_id_btc VARCHAR(35) NOT NULL, Account_balance_btc DOUBLE(8,8) NOT NULL,Account_id_eth VARCHAR(42) NOT NULL, Account_balance_eth DOUBLE(8,8) NOT NULL, Max_trans_amount DOUBLE(8,8), PRIMARY KEY (Id, Account_id_btc, Account_id_eth))DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB;";
	//Create trigger to check for max value of 1 bln in Account_balance_btc
	//Max amount of transaction (Max_trans_amount) ???
	//Source: https://coinmarketcap.com/currencies/bolenum/
	//Source: https://ethereum.stackexchange.com/questions/3542/how-are-ethereum-addresses-generated
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
mysqli_close($con); //��������� �� ������� �� �� ��
?>
</body>
</html>