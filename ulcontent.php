<?php
date_default_timezone_set("Asia/Taipei");
require_once('simple_html_dom.php');
$q = "20222";
$url = "https://iq.ul.com/awm/stylepage.aspx?style=$q";

$htmlString = file_get_contents($url);
//echo $htmlString;

$html = str_get_html($htmlString); // Assuming $htmlString holds the HTML content


$data = [];
// Iterate through table rows with data
foreach ($html->find('table#ObjTable tr') as $row) {
   // echo $row -> attr['style']."<BR>";

    $headerCell = $row->find('td', 0);
    $valueCell = $row-> find('td', 1);
    $v0 ='';
    if ($headerCell && $valueCell) {
        //echo $aa. 
        if($headerCell-> plaintext !=''){
            $header = trim($headerCell->plaintext);
            $value = trim($valueCell->plaintext);
            $data[$header] = $value;
            $h = $header;
        }else{
            $td = $row -> find('td');
            $tdString = "";
            for($i=1;$i<count($td);$i++) $tdString = $tdString.$td[$i] -> plaintext. '|'; 
            $data[$h]=trim($data[$h].trim($tdString)).PHP_EOL; 

        }

   }
}




$json = json_encode($data);

echo $json;
?>