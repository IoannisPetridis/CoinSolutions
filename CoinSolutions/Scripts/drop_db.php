<?php
require_once("db_connect.php");
echo "<table>";
$selected_db = mysqli_select_db($con, $dbname); //Selecting $dbname as our Database
if ($selected_db) { // If we can select it...	
	$sql = "DROP DATABASE $dbname";  //MySql command to be executed	
	if (mysqli_query($con, $sql)) { //Delete database with name $dbname		
		echo "<tr><td>Database $dbname deleted successfully</td></tr>";	
	} 	
	else {		
		echo "<tr><td> " . mysqli_error($con)."</td></tr>";	
	} 
}
else {	
	echo "<tr><td> " . mysqli_error($con)."</td></tr>";
}
echo "</table>";
mysqli_close($con);
?>