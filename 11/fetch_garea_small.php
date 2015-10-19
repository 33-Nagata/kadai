<?php
require('api_key.php');
$query = http_build_query([
  'keyid' => $api_key,
  'format' => 'json'
]);
$url = 'http://api.gnavi.co.jp/master/GAreaSmallSearchAPI/20150630/';
$url .= '?'.$query;
$json_data = file_get_contents($url);
$data = json_decode($json_data);
echo json_encode($data->garea_small);
?>
