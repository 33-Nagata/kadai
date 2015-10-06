<?php
const APPID = 'v38ZMz/FKfPBmfeZ7hyxOXe7k+EWCBK3kT0YBFlfwcY';
$text = urlencode($_GET['text']);
$from = urlencode($_GET['from']);
$to = urlencode($_GET['to']);
$url = 'https://api.datamarket.azure.com/Bing/MicrosoftTranslator/v1/Translate?';
$url .= 'Text=%27'.$text.'%27';
$url .= '&From=%27'.$from.'%27';
$url .= '&To=%27'.$to.'%27';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_USERPWD, APPID.':'.APPID);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
curl_close($ch);
$begin_tag = '<d:Text m:type="Edm.String">';
$close_tag = '</d:Text>';
// begin_tagの前後で分割
$result = explode($begin_tag, $result);
// close_tagの前後で分割
$result = explode($close_tag, $result[1]);
echo $result[0];
?>
