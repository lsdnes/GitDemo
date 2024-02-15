<?php
$string = "";
$j ='
{"style":"AWM 2085","issued":"1959-05-01","revised":"2011-08-04","desc":"Parallel cable with extruded integral insulation and jacket.","Rating":"105 deg C, 300 Vac, FT2 flame, Optional - 60 or 80 deg C Oil.","Conductor":"Two No. 18 or 16 AWG consisting of No. 30 AWG or smaller strands.","Insulation":"Integral with Jacket. PVC, 60 mils minimum average, 54 mils minimum at any point.","Standard":"Appliance Wiring Material UL 758.","Marking":"General.","Use":"Internal wiring of electric refrigerators where exposed to temperatures not exceeding 105 C; or internal wiring of electric refrigerators where exposed to temperatures not exceeding 105 C or where exposed to oil at a temperature not exceeding (60 C or 80 C, whichever is applicable). Polarity identification may be omitted."}';

echo $j."<BR><BR>";
$s = json_decode($j,true);
print_r($s);
foreach($s as $key => $value){
       if($key) $string .= $key." : ".$value.PHP_EOL;
    }


?>