<?php
require_once("config.php");
require_once("db_connect.php");
require_once("authorisation.php");
require_once("postFunctions.php");
checkAuthorised();
$data = json_decode(file_get_contents("php://input"),true);
$con = dbConnect($dbhost, $dbuser, $dbpass, $dbname);
$name = mysqli_real_escape_string($con,getPostValue(0,"name","string","name",false));
$description = mysqli_real_escape_string($con,getPostValue(0,"description","string","description",false));
$email = mysqli_real_escape_string($con,getPostValue(0,"email","string","email",true));
$account_id_btc = null; //Null before adding currency account
$account_balance_btc = 0.00000000; //Default to 0
$account_id_eth = null; //Null before adding currency account
$account_balance_eth = 0.00000000; //Default to 0
$max_trans_amount = 0.00000000; //Default to 0
if (mysqli_select_db($con,$dbname)) {
	$sql = "SELECT Email FROM User WHERE Email = '$email';";
	if ($result=mysqli_query($con,$sql)) {
		// Return the number of rows in result set
		$rowcount = mysqli_num_rows($result);
		if ($rowcount ==0) {
			//No existing user
			$sql = "INSERT INTO User (Name, Description, Email, Account_id_btc, Account_balance_btc, Account_id_eth, Account_balance_eth, Max_trans_amount) VALUES ('$name', '$description', '$email','$account_id_btc', $account_balance_btc, '$account_id_eth', $account_balance_eth, $max_trans_amount)";
			
			if (mysqli_query($con,$sql)) {
				echo "User created!";
			}
			else {
				echo mysqli_error($con);
			}
		}
		else if ($rowcount >=1){
			//User already exists in the DB
			mysqli_close($con);	
			die("User already exists!");
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