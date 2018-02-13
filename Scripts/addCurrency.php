<?php
require_once("config.php");
require_once("db_connect.php");
require_once("authorisation.php");
require_once("postFunctions.php");
checkAuthorised();
$data = json_decode(file_get_contents("php://input"),true);
$con = dbConnect($dbhost, $dbuser, $dbpass, $dbname);
$type = getPostValue(0,"type","string","type",true);
$account_id_btc = null;
$account_id_eth = null;
$sql="";
$email = mysqli_real_escape_string($con,getPostValue(0,"email","string","email",true));
if ($type=="add_btc_currency") {
	$account_id_btc = mysqli_real_escape_string($con,getPostValue(0,"account_id_btc","string","account id btc",true));
	$sql = "UPDATE $dbname.User SET Account_id_btc = '$account_id_btc', Account_balance_btc = 1.00000000 WHERE Email = '$email';";
}
else if ($type=="add_eth_currency") {
	$account_id_eth = mysqli_real_escape_string($con,getPostValue(0,"account_id_eth","string","account id eth",true));
	$sql = "UPDATE $dbname.User SET Account_id_eth = '$account_id_eth', Account_balance_eth = 1.00000000 WHERE Email = '$email';";
}

if (mysqli_select_db($con,$dbname)) {
	if (mysqli_query($con,$sql)) {
		echo "Your currency account has been updated successfully!";
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