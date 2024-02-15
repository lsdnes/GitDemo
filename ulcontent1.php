<?php
date_default_timezone_set("Asia/Taipei");
require_once('simple_html_dom.php');
//1772 1042
echo getAwmContent('1467');

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



    return json_encode($data,JSON_UNESCAPED_SLASHES);
    //return preg_replace("/\\\\'/ui","'", $json);
}

?>