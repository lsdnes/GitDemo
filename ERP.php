<?php
header("Content-type:text/html;charset=UTF-8");
ini_set('display_errors','off');
require_once('LINEBotTiny.php');
require_once('functiontools.php');
require_once('userinfo.php');
date_default_timezone_set("Asia/Taipei");

$receive = json_decode(file_get_contents("php://input"));

$client = new LINEBotTiny($channelAccessToken, $channelSecret);
$event=$client->parseEvents();
$event=$event[0];
$message = ($event['beacon']) ? $event['beacon']:$event['message'];

$text=$message['text'];
$header = ["Content-Type: application/json", "Authorization: Bearer {" . $channelAccessToken . "}"];

$replyToken=$event['replyToken'];
$from=$event['source']['userId'];
$type = $event['source']['type'];
if ($type == "room"){
	$from = $event['source']['roomId'];	
}else if ($type == "group"){
	$from = $event['source']['groupId'];
}
else{
	$from = $event['source']['userId'];	
}

$str=QueryData(trim($text),$from,$userid,$username,$pregstr);

foreach ($str as $key => $value) {
    $Replymessages[] = Reply($key,$value);
}

if(isset($Replymessages[0]['type']) && !$Replymessages[1]['type'] && $Replymessages[1][0]['type']){
	$a[0] = $Replymessages[0];
	$b = $Replymessages[1];
	$Replymessages[0] = array_merge($a,$b);
}


$Replymessages = (isset($Replymessages[0][1]['type'])) ? $Replymessages[0] : $Replymessages;

if($Replymessages[1]===NULL) unset($Replymessages[1]);


if(!empty($str))
$client->replyMessage([
                        'replyToken' => $replyToken,
                        'messages' => $Replymessages
                    ]);


function Reply($content_type, $message){
	global $client,$replyToken;
    if(!is_array($message) || $content_type=='carousel') $message= array($message);
    $i=0;
    foreach($message as $key => $m){
	
	switch ($content_type) {
                case 'text':                    
                        $Replymessage[$i]=
                            [
                                'type' => 'text',
                                'text' => $m
                            ];
                   
                    break;
                case 'image':                        
                        $Replymessage[$i]= 
                            [
                                'type' => 'image', // 訊息類型 (圖片)
                				'originalContentUrl' => $m, // 回復圖片
                				'previewImageUrl' => $m // 回復的預覽圖片
                            ];
                        

                    break;
                case 'video':                    
                        $Replymessage[$i]= 
                            [
                                'type' => 'video', // 訊息類型 (圖片)
                				'originalContentUrl' => $m, // 回復圖片
                				'previewImageUrl' => $m // 回復的預覽圖片
                            ];                                          
                    break;
                case 'audio':
                    $Replymessage[$i]=
                            [
                                'type' => 'audio', // 訊息類型 (圖片)
                				'originalContentUrl' => $m, // 回復圖片
                				'duration' => 100000 // 回復的預覽圖片
                            ];                        
                   
                    break;
                case 'carousel':
                	$columns =$m;
      	   				$template = array('type'    => 'carousel',
						  	                    'columns' => $columns
                   			           );
					   $Replymessage[$i] = ['type'     => 'template',
			                               'altText'  => '無法在PC端觀看訊息',
            			                   'template' => $template
                			];


                    break;
                case 'template':

                    break;          
                default:
                    error_log('Unsupported message type: ' . $content_type);
                    break;
            }
            $i++;
    }  
            $Replymessage = (count($message)==1)? $Replymessage[0] : $Replymessage;
            return $Replymessage;
}



function QueryData($text,$from,$userid,$username,$pregstr){
	global $channelAccessToken;
	$ortext=$text;	
	//$text=strtoupper($text);
	$text=strtoupper($text);	
	$type='';		

	


	if(preg_match("/^重置AI$/ui", $text)) $type='RestAI'; //重置AI


	switch ($type) {
		case 'RestAI':
			file_put_contents("images/Gemini/$from.contet", '');				
			$str['text']='已重置Gemini AI對話！'.PHP_EOL.'未清空對話時，只保留最近25筆上下文對話紀錄';
			break;

		default:
			$string = Fcall($ortext,'',$from);
			$string = json_decode($string,true); 
			$string = $string['candidates'];
			$string = json_encode($string, JSON_UNESCAPED_UNICODE);       
            $str['text']=mb_substr($string,0,4900);
            break;	
	}
	
	return $str;
}


function FuncCall($string){
	$ask = "G 在下面的句子中，如果出現 品號:{8碼數字} 或 品名:{數字或文字} 或規格:{數字或文字}請幫我直接輸出為 8碼數字 或數字或文字,".PHP.EOL.
			"如果沒有這樣規律,則回覆沒有這個規律,句子如下:".PHP_EOL.$string;
	$ans = GAI($ask,'',$from);

	if($ans=='沒有這個規律') $s = GAI($string,'',$from); 
	else $s=$ans;

	return $s;
}


//https://ai.google.dev/docs/function_calling?hl=zh-tw#multi-turn-example-1

function Fcall($q,$type='',$from){
	global $api_keys;

    $ch = curl_init();
    $api_key = $api_keys[rand(0,1)];
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$api_key";
    $contents = array("role"=> "user",
                "parts"=>array(array("text"=>$q)));

    $post_fields['contents']=array($contents);

    $declarations = new Declarations();
    $p = array(["exchange","","交易所名字,如幣託, MAX, 幣安, Binance...等"],
            	["crypto","","加密貨幣名稱,如 BTC,ETH,XRP,USDT....等"],
            	["pair","","對或VS之後的加密貨幣名稱,如 BTC,USDT,TWD,USD....等"]
        	);
    $declarations->setProperties($p);
    $declarations->addDeclaration("find_cryptoprice", "尋找特定加密貨幣在特定交易所的的價格");

    $p = array(["location","","城市或地區,台北,桃園,大湳...等"],
            	["movie","","任何電影名稱"]            	
        	);
    $declarations->setProperties($p);
    $declarations->addDeclaration("find_theater", "尋找特定電影在特定地區播放的劇院");

    $tools = $declarations->getDeclarations();


    $post_fields['tools']=array("function_declarations"=>$tools);
    $header  = [
        'Content-Type: application/json'
    ];
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

   

    $result = curl_exec($ch);

    curl_close($ch);

    //$result = json_decode($result);

    //PushtoLineNotify('Cbdb4e629fca086b4681075bdfc100a17', var_export($result,true));


    //$r = json_encode()

    return $result;
}







function GAI($q,$type='',$from){
    global $api_keys;
    $ch = curl_init();    
    $api_key = $api_keys[rand(0,1)];
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=$api_key";
    $ask = array("role"=> "user",
                "parts"=>array(array("text"=>$q)));
    $post_fields['contents']=array($ask);
    
    if($type=='Gemini'){
    	$old_contents = file_get_contents("images/Gemini/$from.contet");
    	if(!empty($old_contents)){    		
    		$old_contents =json_decode($old_contents,ture);
    		if(count($old_contents)>=50) $old_contents= array_slice($old_contents,2);
    		array_push($old_contents,$ask);	
    		$post_fields['contents'] = $old_contents;	
    	}   
    }
	
    $header  = [
        'Content-Type: application/json'
    ];
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    }
    curl_close($ch);
    
    $r = json_decode($result,true); 
    $r = $r['candidates'][0]['content']['parts'][0]['text'];    
    
    if($type=='Gemini' && $r){ 
    	$model = array("role"=> "model",
                "parts"=>array(array("text"=>$r)));
    	array_push($post_fields['contents'],$model);
    	    	
    	file_put_contents("images/Gemini/$from.contet",json_encode($post_fields['contents'],JSON_UNESCAPED_UNICODE));
    }
    if(!$r){
    	$r= var_export($result, true);    	
    }
    

    return mb_substr($r,0,4900);
}




function UTF8($lang,$text){
	//https://zhconvert.org 繁化姬
	switch ($lang) {
		case 'S':
			$ConvLang='Simplified';
			break;
		case 'T':
			$ConvLang='Traditional';
			break;
		case 'CN':
			$ConvLang='China';
			break;
		case 'TW':
			$ConvLang='Taiwan';
			break;
		case 'ZY':
			$ConvLang='Bopomofo';
			break;
		case 'WIKIS':
			$ConvLang='WikiSimplified';
			break;
		case 'MARS':
			$ConvLang='Mars';
			break;	
		case 'PY':
			$ConvLang='Pinyin';
			break;					
		case 'WIKIT':
			$ConvLang='WikiTraditional';
			break;
		case 'HK':
			$ConvLang='Hongkong';
			break;		
		default:
			$ConvLang='Simplified';
			break;
	}
	$url="https://api.zhconvert.org/convert?converter=".$ConvLang."&text=".$text."&prettify=1";
	$string=json_decode(file_get_contents($url),1);
	return $string['data']['text'];
}


//用來找零件料號庫存
Function Part($text){
	$text=preg_replace('/^P\s*/i', '', $text);
	if(trim($text)!=''){
		$query= new sqlQuery("sql","TSK","ASSOC");	
 		$Q="SELECT RTRIM(MB001) MB001,MB002,MB003,MB004,MC002,MC007,MB007 FROM INVMB
 		JOIN INVMC ON MC001=MB001 WHERE MB002 LIKE :str OR MB001='".$text."'";
 		$R=$query->getQuery($Q,$text);
 		$string='';
 		for($i=0;$i<count($R);$i++){
 			$string=$string.'品號: '.$R[$i]['MB001'].PHP_EOL.'品名: '.$R[$i]['MB002'].PHP_EOL.'規格: '.$R[$i]['MB003'].PHP_EOL.'庫別: '.
 			$R[$i]['MC002'].PHP_EOL.'分類: '.$R[$i]['MB007'].PHP_EOL.'庫存: '.floor($R[$i]['MC007']).' '.$R[$i]['MB004'].
 			PHP_EOL.'----------------'.PHP_EOL;
 		}
 	}
 	//0518-TM21P-88P
	return $string;
	
}



//查詢暱稱
function WhoAmI($userId){
 global $channelAccessToken;

 $url = 'https://api.line.me/v2/bot/profile/'.$userId;
 $ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Authorization: Bearer {' . $channelAccessToken . '}',
	));
 $json_content = curl_exec($ch);
 curl_close($ch);
 $json = json_decode($json_content,true);

 return $json['pictureUrl'];

}


function PushtoLineNotify($from,$title,$message=''){
	$query = new sqlQuery("mysqlpro","tsk_internal","ASSOC");
	$Q="SELECT FRTOKEN FROM linefrom WHERE FRID= :str";
	$r=$query->getQuery($Q,$from);
	$token=$r[0]['FRTOKEN'];

	$headers = array(
    	'Content-Type: multipart/form-data',
    	'Authorization: Bearer '.$token
	);
	$message = array(
    	'message' => $title,
    	"imageThumbnail" => $message,
    	"imageFullsize" => $message
	);
	$ch = curl_init();
	curl_setopt($ch , CURLOPT_URL , "https://notify-api.line.me/api/notify");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
	$result = curl_exec($ch);
	curl_close($ch);
}






function ToExcel($r){
	require_once ("../Excel/PHPExcel.php");
	require_once("../Excel/PHPExcel/IOFactory.php");

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0);

   
	for($j=0; $j<count($r); $j++){
   		$k=65;
    	foreach ($r[$j] as $key => $value) {
      	$value = ($value== null)? 0 : $value;
	   		$objPHPExcel->getActiveSheet()->setCellValue(chr($k).($j+1), $value);
    		$k++;	
    	}
	}

	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setAutoSize(true);

	$time = time();
	$filename='../Excel/download/'.$time.'.xlsx';
	//echo 'https://lsd.twf.node.tw/Excel/'.$filename;
	$filetype='Excel2007';


	$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, $filetype);
	$writer -> setIncludeCharts(TRUE);
	$writer -> save($filename);
	$url='https://tskds213.synology.me/Excel/download/'.$time.'.xlsx';
	return $url;

}
