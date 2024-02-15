<?php
date_default_timezone_set("Asia/Taipei");

//$maxpage = getMAxpage();
setAwmSql();


//field = style,issued, revised, content 
function setAwmSql(){
  $url = "https://iq.ul.com/awm/stylepage.aspx?style=%d";
  $value = [[1000,1999],[2000,2999],[3000,3999],[4000,4659],[12000,12089],[22450,22491],[30000,30124]];
  for($i=0;$i<count($value);$i++){    
    for($j=$value[$i][0];$j<=$value[$i][1];$j++){
        $style = $j;
        $sql[]= "'$style', NULL, NULL, '2024-01-01',''";
    }
  }
  $Q = "INSERT INTO `stylepage` VALUES (".implode("), (", $sql).")";
  echo $Q;
}
 



//"The Style Page you have requested is currently unavailable"

function getMAxpage(){
  $url = "https://iq.ul.com/awm/stylepage.aspx?style=%d";
  $value = [[1000,1999],[2000,2999],[3000,3999],[4000,4659],[12000,12089],[22450,22491],[30000,30124]];
  $i = 0;
  for($v=3;$v<count($value);$v++){
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
  
    $value[$v][1] = $max;
    $i = 0;
  }

  $json = json_encode($value);
  file_put_contents('maxpage.json', $json);
  return $value;
}




?>