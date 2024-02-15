<?php
header("Content-type:text/html;charset=UTF-8");

date_default_timezone_set("Asia/Taipei");
require_once('../sqlquery.php');

$key=isset($_GET['key'])? trim($_GET['key']) :'';
$id=isset($_GET['id'])? trim($_GET['id']) :'';

$query = new sqlQuery("mysqlpro","lsdtwfno_line","ASSOC");
if($key!='' && $id!='')
if(preg_match("/^sk-\S{48}/ui",$key)){    
    $Q = "INSERT INTO apikey VALUES(:str, NOW(), NULL,'$id')";
    $r = $query->getQuery($Q, $key);
    
}


?>