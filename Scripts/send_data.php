<?php
mb_internal_encoding("UTF-8");
require_once("db_connect.php");
include("generate_data.php");
for ($i=0;$i<10;$i++) {	
	$firstname = array_shift($users);	
	$surname = array_shift($users);	
	$email = array_shift($users);	
	$device_id = array_shift($users);	
	$timestamp = array_shift($wind_data);	
	$latitude = array_shift($wind_data);	
	$longitude = array_shift($wind_data);	
	$accuracy = array_shift($wind_data);	
	$velocity = array_shift($wind_data);	
	$direction = array_shift($wind_data);	
	$comment = array_shift($wind_data);		
	$ch = curl_init();	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);	
	$url = mb_convert_encoding("http://www.giannispetridis.xyz/windtrack/Scripts/receive_data.php?velocity=$velocity&direction=$direction&longitude=$longitude&latitude=$latitude&device_id=$device_id&timestamp=".(string)$timestamp."&accuracy=".(string)$accuracy."&email=".(string)$email."&firstname=".(string)$firstname."&surname=".(string)$surname."&comment=$comment","UTF-8","auto");
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HEADER, 0);	
	$content = curl_exec($ch);	
	echo $content;
	curl_close($ch);
}
?>