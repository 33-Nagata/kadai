<?php
require('api_key.php');
$query = [
  'keyid' => $api_key,
  'format' => 'json'
];
$param_list = ['area', 'pref', 'areacode_l', 'areacode_m', 'areacode_s', 'category_l', 'category_s', 'name'];
foreach ($param_list as $param) {
  if (isset($_GET[$param])) $query[$param] = $_GET[$param];
}
$query_str = http_build_query($query);
$url = 'http://api.gnavi.co.jp/RestSearchAPI/20150630/';
$url .= '?'.$query_str;
$json_data = file_get_contents($url);
$data = json_decode($json_data);
echo json_encode($data->rest);
?>
