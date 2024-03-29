<?php
date_default_timezone_set("Asia/Taipei");
require_once('simple_html_dom.php');
require_once('../sqlquery.php');

$query = new sqlQuery("mysqlpro","lsdtwfno_line","ASSOC");

$Q = "SELECT style FROM `stylepage` WHERE content ='' ORDER BY modifydate , style LIMIT 15";
//$Q = "SELECT style FROM `stylepage`  ORDER BY modifydate , style LIMIT 3";
$r = $query -> getQuery($Q);

for($i=0;$i<count($r);$i++){
    $q = $r[$i]['style'];
    echo $q."<BR>";
    $content = getAwmContent($q);
    $json = json_decode($content,true);
    $content = preg_replace("/\\\\'/ui","'", $content);
    $issued = $json['issued'];
    $modifydate = date("Y-m-d H:i:s");
    $revised = $json['revised'];
    $Q = "UPDATE `stylepage` SET issued = '$issued', 
        revised = '$revised', modifydate = '$modifydate', content = '$content' WHERE style = '$q'";
    echo $Q."<BR><BR>";
    if($issued!== null)$query -> getQuery($Q);
}


//echo getAwmContent($q);

function getAwmContent($q){
    $url = "https://iq.ul.com/awm/stylepage.aspx?style=$q";
    $htmlString = file_get_contents($url);
    $html = str_get_html($htmlString);
    
    $data = [];
    //$data['UL758'] = 'APPLIANCE WIRING MATERIAL';
    $data['style'] = "AWM $q";
    if($html -> find('span#IssLabel',0))
        $issued = $html -> find('span#IssLabel',0)->plaintext;
    else $issued = $html -> find('span#oldIssLabel',0)->plaintext;
    $data['issued'] = $issued;
    
    if($revised = $html -> find('span#RevLabel',0))
        $revised = $html -> find('span#RevLabel',0)->plaintext;
    else $revised = $html -> find('span#oldIssLabel',0)->plaintext;
    $data['revised'] = $revised;
    
    if($html -> find('span#DescLabel',0))
        $desc = $html -> find('span#DescLabel',0)->plaintext;
    else $desc = $html -> find('span#oldDescLabel',0)->plaintext;
    $data['desc'] = $desc;

     $table = ($html->find('table#ObjTable tr')) ? $html->find('table#ObjTable tr') : $html->find('table[id]') ;

   foreach ($table as $row) {
       $headerCell = $row->find('td', 0);
       $valueCell = $row-> find('td', 1);
       if ($headerCell && $valueCell) {
           if($headerCell-> plaintext !=''){
                $header = trim($headerCell->plaintext);
                $value = trim($valueCell->plaintext);
                $data[$header] = addslashes($value);
                $h = $header;
            }else{
                $td = $row -> find('td');
                $tdString = "";
                for($i=1;$i<count($td);$i++) $tdString = $tdString.$td[$i] -> plaintext. '|'; 
                $data[$h]=trim($data[$h].addslashes(trim($tdString))).PHP_EOL; 
            }

        }
    }

    if(preg_match("/The Style Page you have requested is currently unavailable/ui", $html)){
         $data['issued'] = '0000-00-00 00:00:00';
         $data['revised'] = '0000-00-00 00:00:00';
         $data["desc"]  = 'The Style Page you have requested is currently unavailable';
    }

    return json_encode($data,JSON_UNESCAPED_SLASHES);
    //return preg_replace("/\\\\'/ui","'", $json);
}

?>