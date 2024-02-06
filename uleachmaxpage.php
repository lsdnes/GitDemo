<?php
date_default_timezone_set("Asia/Taipei");
// 定義 $i 變數

// 設定 $url 的格式
$url = "https://iq.ul.com/awm/stylepage.aspx?style=%d";

// 設定 $i 的範圍
//$value = [[10000,19999],[20000,29999],[30000,39999],[40000,49999]];

$value = [[10000,19999],[20000,29999]];

$i = 0;
for($v=0;$v<count($value);$v++){
    $min = $value[$v][0];
    $max = $value[$v][1];
    
    while ($min <= $max) {
    // 計算出中間值
    $mid = floor(($min + $max) / 2);    
    $content = file_get_contents(sprintf($url, $mid)); 

    if (strpos($content, "The Style Page you have requested is currently unavailable") !== false) {
      // 若存在，則將 $max 設定為 $mid - 1
      $max = $mid - 1;
    } else {
      // 若不存在，則將 $min 設定為 $mid + 1
      $min = $mid + 1;
    }
  
    // 遞增 $i 的值
    $i++;
  }
  
  echo "max=$max, min=$min, mid=$mid, i=$i<Br>";
  $i = 0;
}




?>