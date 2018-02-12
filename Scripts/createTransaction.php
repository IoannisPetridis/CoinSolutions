<?php
require_once("config.php");
require_once("db_connect.php");
require_once("authorisation.php");
require_once("postFunctions.php");
checkAuthorised();
$data = json_decode(file_get_contents("php://input"),true);
$con = dbConnect($dbhost, $dbuser, $dbpass, $dbname);
$type = getPostValue(0,"type","string","type",true);
$source_user = getPostValue(0,"source_user","int","source_user",true);
$target_user = getPostValue(0,"target_user","int","target_user",true);
$amount = getPostValue(0,"amount","double","amount",true);
$sql="";
$cur = "";
if ($type=="send_btc") {
	$sql = "SELECT Id, Account_balance_btc FROM $dbname.User WHERE Id = '$source_user' AND Account_balance_btc >= $amount;";
	$cur = "Bitcoin";
}
else if ($type=="send_eth") {
	$sql = "SELECT Id, Account_balance_eth FROM $dbname.User WHERE Id = '$source_user' AND Account_balance_eth >= $amount;";
	$cur = "Ethereum";
}

if (mysqli_select_db($con,$dbname)) {
	if ($result=mysqli_query($con,$sql)) {
		$rowcount = mysqli_num_rows($result);
		if ($rowcount ==0) {
			die("Doesn't exist or insufficient amount");
		}
		else if ($rowcount==1) {
			//Todo:
			//$timestamp_created
			//$timestamp_processed
			$timestamp_created = date("Y-m-d H:i:s");
			//$timestamp_processed = null;
			$sql2 = "INSERT INTO $dbname.Transaction (Cur_amount, Cur_type, Source_usr_id, Target_usr_id, Timestamp_created, Timestamp_processed, State) VALUES ($amount, '$cur', $source_user, $target_user, '$timestamp_created', NULL, 0);";
			if (mysqli_query($con,$sql2)) {
				$sql3 = "SELECT Id, Timestamp_created, Source_usr_id, Target_usr_id FROM $dbname.Transaction WHERE Source_usr_id = $source_user AND Target_usr_id = $target_user AND Timestamp_created = '$timestamp_created';";
				$result = mysqli_query($con,$sql3);
				$id;
				while ($row = mysqli_fetch_array($result)) {
					$id = $row['Id'];
				}
				echo "Transaction submitted, ID: $id";
				//Ideally return a json file here
			}
			else {
				echo mysqli_error($con);
			}
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