<?php
require_once("config.php");
require_once("db_connect.php");
require_once("authorisation.php");
require_once("postFunctions.php");
checkAuthorised();
//wget -O /dev/null http://giannispetridis.xyz/CoinSolutions/Scripts/transProcessor.php?pass=masterpass
$con = dbConnect($dbhost, $dbuser, $dbpass, $dbname);
if (mysqli_select_db($con,$dbname)) {
	$sql = "SELECT * FROM Transaction WHERE State=0 ORDER BY Timestamp_created ASC;";
	if ($result=mysqli_query($con,$sql)) {
		while ($row = mysqli_fetch_array($result)) {
			//This is sorted by oldest to newest (basically first come first serve - FIFO)
			//TODO: The actual processing
			//TODO: Log it
			//Check the availability of the amount of this type currency of the source user
			$transId = $row['Id'];
			$sourceUserId = $row['Source_usr_id'];
			$targUserId = $row['Target_usr_id'];
			$currType = $row['Cur_type'];
			$amount = $row['Cur_amount'];
			$timestampCreated = $row['Timestamp_created'];
			$sql = "";
			$sql2 = "";
			if ($currType == "Bitcoin") {
				//Quick way but doesn't let us control the program flow (i.e. we don't know in this script whether there's insufficient balance):
				//$sql = "UPDATE User SET Account_balance_btc = IF(Account_balance_btc >=$amount, Account_balance_btc - $amount, Account_balance_btc) WHERE Id = $sourceUserId;";
				//Instead we will separate it into two subsequent calls
				$sql = "SELECT Account_balance_btc, Id FROM User WHERE Account_balance_btc>=$amount AND Id = $sourceUserId;";
				$sql2 = "UPDATE User SET Account_balance_btc = Account_balance_btc - $amount WHERE Id = $sourceUserId;";
				$sql3 = "UPDATE User SET Account_balance_btc = Account_balance_btc + $amount WHERE Id = $targUserId;";
			}
			else if ($currType == "Ethereum") {
				//Quick way but doesn't let us control the program flow (i.e. we don't know in this script whether there's insufficient balance):
				//$sql = "UPDATE User SET Account_balance_btc = IF(Account_balance_btc >=$amount, Account_balance_btc - $amount, Account_balance_btc) WHERE Id = $sourceUserId;";
				//Instead we will separate it into two subsequent calls
				$sql = "SELECT Account_balance_eth, Id FROM User WHERE Account_balance_eth>=$amount AND Id = $sourceUserId;";
				$sql2 = "UPDATE User SET Account_balance_eth = Account_balance_eth - $amount WHERE Id = $sourceUserId;";
				$sql3 = "UPDATE User SET Account_balance_eth = Account_balance_eth + $amount WHERE Id = $targUserId;";
			}

			if ($result = mysqli_query($con,$sql)) {
				$rowcount = mysqli_num_rows($result);
				if ($rowcount==1) {
					if (mysqli_query($con,$sql2)) {
						//Amount subtracted from source user
						if (mysqli_query($con,$sql3)) {
							//Amount added to target user
							$timestampProcessed = date('Y-m-d H:i:s'); 
							$sql4 = "UPDATE Transaction SET State=1, Timestamp_processed = '$timestampProcessed' WHERE Id = $transId;";
							if (mysqli_query($con,$sql4)) {
								//State updated
								//Logging is essentially the database entry itself but we could also log
								//everything in a json file that's stored in the server
								echo "Transaction completed";
								echo "<br>";
								$file = fopen("transactionLog.json", "r+");
								$size = filesize("transactionLog.json");
								if (flock($file,LOCK_EX)) {
									$contents = fread($file, $size);
									$jsonData = json_decode($contents);
									$obj = new stdClass();
									$obj->Id = $transId;
									$obj->Cur_amount = $amount;
									$obj->Cur_type = $currType;
									$obj->Source_usr_id = $sourceUserId;
									$obj->Target_usr_id = $targUserId;
									$obj->Timestamp_created = $timestampCreated;
									$obj->Timestamp_processed = $timestampProcessed;
									array_push($jsonData,$obj);
									ftruncate($file,0);
									rewind($file);
									fwrite($file,json_encode($jsonData));
									//TODO: It appears to be null, check why this is the case
								}
								else {
									echo "Error locking file!";
								}
								fclose($file);
							}
							else {
								echo mysqli_error($con);
							}
						}
						else {
							echo mysqli_error($con);
						}
					}
					else {
						//TODO: If the second query fails, revert everything back to normal
						//SQL query to handle this
						echo mysqli_error($con);
					}
				}
				else {
					echo "Transaction from userId: $sourceUserId to userId: $targUserId has failed due to insufficient funds!<br>";
				}
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