<html>
<body>
<?php
require_once("config.php");
require_once("db_connect.php");
require_once("authorisation.php");
echo "<table>";
checkAuthorised();

$con = dbConnect($dbhost, $dbuser, $dbpass, $dbname); //or die(mysqli_connect_error());
if (mysqli_select_db($con,$dbname)) {
	echo "<tr><td>Database $dbname selected successfully!</td></tr>";
	$sql1 = "CREATE TABLE IF NOT EXISTS User ( Id MEDIUMINT NOT NULL AUTO_INCREMENT, Name VARCHAR(512) NOT NULL, Description VARCHAR(1000), Email VARCHAR(1000) NOT NULL, Firebase_token VARCHAR(1000) DEFAULT '', Account_id_btc VARCHAR(35), Account_balance_btc FLOAT(8,8) DEFAULT 0.00000000, Account_id_eth VARCHAR(42), Account_balance_eth FLOAT(8,8) DEFAULT 0.00000000, Max_trans_amount DOUBLE(8,8) DEFAULT 0.00000000, PRIMARY KEY (Id))DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
	//Create trigger to check for max value of 1 bln in Account_balance_btc
	//Max amount of transaction (Max_trans_amount) ???
	//Source: https://coinmarketcap.com/currencies/bolenum/
	//Source: https://ethereum.stackexchange.com/questions/3542/how-are-ethereum-addresses-generated
	//Source: https://en.bitcoin.it/wiki/Address
	$sql2 = "CREATE TABLE IF NOT EXISTS Transaction (Id MEDIUMINT NOT NULL AUTO_INCREMENT, Cur_amount DOUBLE(8,8) NOT NULL, Cur_type VARCHAR(25), Source_usr_id MEDIUMINT NOT NULL, Target_usr_id MEDIUMINT NOT NULL, Timestamp_created DATETIME NOT NULL, Timestamp_processed DATETIME, State INT(1) UNSIGNED NOT NULL DEFAULT 0, PRIMARY KEY (Id), CONSTRAINT FOREIGN KEY(Source_usr_id) REFERENCES $dbname.User(Id) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT FOREIGN KEY(Target_usr_id) REFERENCES $dbname.User(Id) ON DELETE CASCADE ON UPDATE CASCADE)DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
	if (mysqli_query($con,$sql1)) {
		echo "<tr><td>Table User created successfully!</td></tr>";
		if (mysqli_query($con,$sql2)) {
			echo "<tr><td>Table Transaction created successfully!</td></tr>";
			
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
mysqli_close($con);
?>
</body>
</html>