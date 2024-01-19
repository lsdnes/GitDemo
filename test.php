<?php
// Example usage:
require_once('functiontools.php');
require_once('userinfo.php');

$q = "在台北哪裡可以看哪吒之魔童降世";
$declarations = new Declarations();

$declarations -> set_Role("user");
$declarations -> set_Parts(array("text"=>$q));
$contents = $declarations->get_contents();
$post_fields['contents'] = $contents;


//$declarations -> set_declarations_name("find_cryptoprice");
//$declarations -> set_declarations_description("尋找特定加密貨幣在特定交易所的的價格");

$p = array(["exchange","","交易所名字,如幣託, MAX, 幣安, Binance...等"],
            ["crypto","","加密貨幣名稱,如 BTC,ETH,XRP,USDT....等"],
            ["pair","","對或VS之後的加密貨幣名稱,如 BTC,USDT,TWD,USD....等"]
        );
$declarations->set_Properties($p);
$declarations->add_Declaration("find_cryptoprice", "尋找特定加密貨幣在特定交易所的的價格");

$p = array(["location","","城市或地區,台北,桃園,大湳...等"],
            	["movie","","任何電影名稱"]            	
        );
$declarations->set_Properties($p);
$declarations->add_Declaration("find_theater", "尋找特定電影在特定地區播放的電影院");

$tools = $declarations->get_Declarations();
$post_fields['tools']=[array("function_declarations"=>$tools)];



//echo json_encode($post_fields,JSON_UNESCAPED_UNICODE);

$ch = curl_init();
$api_key = $api_keys[rand(0,1)];

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$api_key";


$header  = [
    'Content-Type: application/json'
];
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
}

curl_close($ch);
echo $result;






//echo json_encode($post_fields,JSON_UNESCAPED_UNICODE);
//echo var_dump($post_fields);


//第二輪開始
$declarations -> set_Role("model");
$modelrole = array("functionCall"=>
                    array("name"=>"find_theater",array("args"=>
                        array("location"=>"台北","movie"=>"哪吒之魔童降世")))
                );
$declarations -> set_Parts($modelrole);
$contents[] = $declarations->get_contents();

//echo json_encode($contents,JSON_UNESCAPED_UNICODE);


?>
