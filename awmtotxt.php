<?php
date_default_timezone_set("Asia/Taipei");
require_once('simple_html_dom.php');
require_once('../sqlquery.php');

$query = new sqlQuery("mysqlpro","lsdtwfno_line","ASSOC");

$Q = "SELECT * FROM stylepage";
$r = $query -> getQuery($Q);
$string="";
for($i=0;$i<count($r);$i++){
//for($i=1623;$i<1624;$i++){
    $q = trim($r[$i]['content']);
    $q = preg_replace("/".PHP_EOL."/ui","\\\\r",$q);
    $q = preg_replace("/\t/ui","\\\\t",$q);
    $s =json_decode($q,true);

    foreach($s as $key => $value){
       if($key) $string .= $key." : ".$value.PHP_EOL;
    }
    $string .= "###".PHP_EOL;
    
}

$string .= '####'.PHP_EOL.$string;
file_put_contents('awm.txt',$string);
?>