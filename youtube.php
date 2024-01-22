<?php
date_default_timezone_set("Asia/Taipei");
require_once('userinfo.php');

$api_key = $youtube_key;
//echo $api_key;
$ch = curl_init("https://www.googleapis.com/youtube/v3/captions/2TL3DgIMY1g&key=".$api_key);  

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0 );
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0 );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
 
$result = curl_exec($ch); 
$response = json_decode($result);
 
echo "<pre>";
print_r($response);



?>