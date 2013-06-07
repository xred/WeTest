<?php 
function post($url,$post_data){
    $ch = curl_init($url); //初始化
    curl_setopt($ch, CURLOPT_HEADER, 0); //返回header部分
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回字符串，而非直接输出
    //curl_setopt($ch , CURLOPT_COOKIEFILE,  'cookie0.txt' );  //读取   
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/xml"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch , CURLOPT_POST, count ( $post_data ));
    curl_setopt($ch , CURLOPT_POSTFIELDS, $post_data );
    curl_setopt($ch, CURLOPT_POST,1 );

    $result = curl_exec($ch);
    curl_close($ch);
   	return $result;
}

$postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
//echo $postStr;
$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
$url = $postObj->Url;
//echo $url;
echo post($url,$postStr);
 ?>