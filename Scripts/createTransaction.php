<?php
require_once("config.php");
require_once("db_connect.php");
require_once("authorisation.php");
require_once("postFunctions.php");
checkAuthorised();
$data = json_decode(file_get_contents("php://input"),true);
$con = dbConnect($dbhost, $dbuser, $dbpass, $dbname);
$type = getPostValue(0,"type","string","type",true);
$source_user = getPostValue(0,"source_user","string","email",true);
$target_user = getPostValue(0,"target_user","string","email",true);
$amount = getPostValue(0,"amount","double","amount",true);
$sql="";
$cur = "";
if ($type=="send_btc") {
	$sql = "SELECT Id, Email, Account_balance_btc FROM $dbname.User WHERE Email = '$source_user' AND Account_balance_btc >= $amount;";
	$cur = "Bitcoin";
}
else if ($type=="send_eth") {
	$sql = "SELECT Id, Email, Account_balance_eth FROM $dbname.User WHERE Email = '$source_user' AND Account_balance_eth >= $amount;";
	$cur = "Ethereum";
}

if (mysqli_select_db($con,$dbname)) {
	if ($result=mysqli_query($con,$sql)) {
		$rowcount = mysqli_num_rows($result);
		if ($rowcount ==0) {
			die("Doesn't exist or insufficient amount");
		}
		else if ($rowcount==1) {
			$row = mysqli_fetch_array($result);
			$sourceId = $row['Id'];
			$sql2 = "SELECT Id, Email FROM User WHERE Email = '$target_user';";
			if ($result = mysqli_query($con,$sql2)) {
				$row = mysqli_fetch_array($result);
				$targetId = $row['Id'];
				$timestamp_created = date("Y-m-d H:i:s");
				$sql3 = "INSERT INTO $dbname.Transaction (Cur_amount, Cur_type, Source_usr_id, Target_usr_id, Timestamp_created, Timestamp_processed, State) VALUES ($amount, '$cur', $sourceId, $targetId, '$timestamp_created', NULL, 0);";
				if (mysqli_query($con,$sql3)) {
					$sql4 = "SELECT Id, Timestamp_created, Source_usr_id, Target_usr_id FROM $dbname.Transaction WHERE Source_usr_id = $sourceId AND Target_usr_id = $targetId AND Timestamp_created = '$timestamp_created';";
					$result = mysqli_query($con,$sql4);
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
			else {
				echo mysqli_error();
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