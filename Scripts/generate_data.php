<html>
<meta charset="UTF-8">
<body>
<?php
mb_internal_encoding("UTF-8");

$male_names = array("Ιωάννης","Γεώργιος","Σπυρίδων","Κωνσταντίνος","Πέτρος","Χρήστος","Μάρκος","Αλέξιος","Αλέξανδρος","Εμμανουήλ","Δημήτριος","Στυλιανός","Γρηγόριος","Σωτήριος","Ανδρέας","Παύλος","Ορέστης");
$male_surnames = array("Πετρίδης","Τσακατσώνης","Μωυσής","Λυκοστράτης","Αποστόλου","Γεωργιάδης","Αυλωνίτης","Σιούτας","Παπαδόπουλος","Λαζαρίδης","Οικονόμου","Σταθόπουλος","Σαλπιγγίδης","Μαρινάκης","Αναστασιάδης");
$female_names = array("Ελένη","Χρυσάνθη","Χριστίνα","Κατερίνα","Μαρία","Αντιγόνη","Μερόπη","Παναγιώτα","Κωνσταντίνα","Αλεξάνδρα","Στυλιανή","Γρηγορία","Χρυσούλα","Σοφία","Άννα");
$female_surnames = array("Παπαδοπούλου","Πετρίδου","Λαζαρίδου","Αποστόλου","Σιούτα","Οικονόμου","Μαρινάκη","Σταυριανίδου","Αναστασιάδου","Λυκοστράτη","Σταθοπούλου");

$mail_providers = array('@gmail.com','@hotmail.com','@windowslive.com','@yahoo.com','@yahoo.gr','@otenet.gr');

$comments = array('Windy today','Not so much wind today', "There\'s a hurricane coming",'This wind is gonna mess up my hair','A sudden gust of wind...','Nice day to fly a kite');

$en_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$gr_letters = 'ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩ';

$users = array();
$wind_data = array();
for ($count = 0; $count<10; $count++) {
	$a = rand(0,2);
	if ($a==0) {
		$firstname = $male_names[array_rand($male_names,1)];
		$surname = $male_surnames[array_rand($male_surnames,1)];
	}
	else if ($a==1){
		$firstname = $female_names[array_rand($female_names,1)];
		$surname = $female_surnames[array_rand($female_surnames,1)];
	}
	else {
		$firstname = "";
		$surname = "";
	}
	echo $firstname.' '.$surname."\n";
	array_push($users,$firstname);
	array_push($users,$surname);

	$email='';
	for ($i=0; $i<rand(2,15); $i++) {
		$email .= $en_letters[rand(0,strlen($en_letters)-1)];
	}
	$email.=$mail_providers[array_rand($mail_providers,1)];
	echo $email."\n";
	array_push($users,$email);
	
	$device_id='';
	for ($i=0; $i<rand(2,15); $i++) {
		$device_id .= $en_letters[rand(0,strlen($en_letters)-1)]; 
	}
	echo $device_id."\n";
	array_push($users,$device_id);
	
	$timestamp = date('Y-m-d H:i:s');
	echo $timestamp."\n";
	array_push($wind_data,$timestamp);

	$longitude = mt_rand(19.5*10, 26.5*10)/10;
	echo $longitude."\n";
	array_push($wind_data,$longitude);

	$latitude = mt_rand(35*10, 41.5*10)/10;
	echo $latitude."\n";
	array_push($wind_data,$latitude);
	
	$altitude = mt_rand(0,1500);
	echo $altitude."\n";
	array_push($wind_data,$altitude);

	$accuracy = mt_rand(50,100);
	echo $accuracy."\n";
	array_push($wind_data,$accuracy);

	$velocity = mt_rand(0,50);
	echo $velocity."\n";
	array_push($wind_data,$velocity);

	$direction = mt_rand(0,360);
	echo $direction."\n";
	array_push($wind_data,$direction);

	$comment = $comments[array_rand($comments,1)];
	echo $comment."\n";
	array_push($wind_data,$comment);
}
?>
</body>
</html>