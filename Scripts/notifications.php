<?php
$key = "AAAAmeDU7Oc:APA91bFfLxy0iYAwCAcqKBrG3v0pasVjM0JgpaVL5XNKkiMYiPz6f1F4RtrkxJnQEpuWrWMElFirDXF1FiC3BxLCdBDsim_gCHgRhydhriN6YT-KAkoefqixNtMHcWA2d8HzVRQU5Bzv";

function prepareNotification($sourceUserId, $targUserId, $amount, $currType, $timestampProcessed) {
    
    $part_one_token  = getToken($sourceUserId);
    $part_two_token  = getToken($targUserId);
    $part_one_email = getEmail($sourceUserId);
    $part_two_email = getEmail($targUserId);
    $text = "";
    
    $text = "From: $part_one_email\nTo: $part_two_email\nAmount: $amount $currType\n Processed at: $timestampProcessed";
    $msg = array
    (
        "body" 	=> $text,
        "title"	=> "Transaction processed!"
    );

    $data1 = array
    (
        "notification_type"=>"message",
        "source"=>$part_one_email,
        "target"=>$part_two_email,
        "amount"=>$amount,
        "cur_type"=>$currType,
        "processed"=>$timestampProcessed
    );

    //var_dump($msg);

    sendNotification($part_one_token, $msg, $data1);
    sendNotification($part_two_token, $msg, $data1);
}

/* Fetches and returns the user's ($id) Firebase token */
function getToken($id) {
    global $con;
    $sql = "SELECT Id, Firebase_token FROM User WHERE Id = $id";
    if ($result = mysqli_query($con,$sql)) {
        return mysqli_fetch_array($result)['Firebase_token'];
    }
}

/* Fetches and returns the user's ($id) email */
function getEmail($id) {
    global $con;
    $sql = "SELECT Id, Email FROM User WHERE Id = $id";
    if ($result = mysqli_query($con,$sql)) {
        return mysqli_fetch_array($result)['Email'];
    }
}

/* Issues and sends a notification with message ($msg) to the user's with token ($token) */
function sendNotification($token,$msg,$data) {
    global $key;
    $fields = array
    (   "data"=>$data,
        "to"=> $token,
        "notification"=>$msg
    );
    
    $headers = array
    (
      "Authorization: key=" .$key,
      'Content-Type: application/json'
    );

    $ch= curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $result = curl_exec($ch);
    curl_close($ch);
    var_dump($result);
}
?>