<?php
date_default_timezone_set("Asia/Taipei");
$dateString = "2040-03-29 03:14:08";

echo date("Y-m-d H:i:s",strtotime("+3 day",strtotime($dateString)));
echo "<BR><BR>";
$date = new DateTime($dateString);
echo $date -> modify("+3 day")->format("Y-m-d H:i:s");

echo "<BR><BR>";
echo '<pre>程式碼php 2040-03-29 +3day
date_default_timezone_set("Asia/Taipei");
$dateString = "2040-03-29 03:14:08";
echo date("Y-m-d H:i:s",strtotime("+3 day",strtotime($dateString)));
new DateTime方法
$date = new DateTime($dateString);
echo $date -> modify("+3 day")->format("Y-m-d H:i:s");
</pre>';

phpinfo();

?>