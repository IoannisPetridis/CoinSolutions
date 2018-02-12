<?php
function dbConnect($host, $user, $pass, $dbname) {
	return mysqli_connect($host,$user,$pass, $dbname);
}
?>
