<?php
require_once('common.php');
require_once('functions/control_MySQL.php');
require_once('functions/json_endode.php');
require('login_required.php');
$NEWS_PER_PAGE = 10;

if (!isset($_GET['lat']) || !isset($_GET['lon'])) {
  exit;
}
$lat = $_GET['lat'];
$lon = $_GET['lon'];
$location = $lat != "" && $lon != "" ? "GeomFromText('POINT({$lon} {$lat})')" : NULL;

//既読ニュース取得
$opt = [
  'method' => 'select',
  'tables' => ['mark_read'],
  'columns' => ['news_id'],
  'where' => "user_id='{$id}' AND valid=1"
];
$results = controlMySQL($opt);
$read_news = [0];
foreach ($results as $row) $read_news[] = $row['news_id'];
//近くのニュース取得
$lat1 = 'RADIANS('.$lat.')';
$lon1 = 'RADIANS('.$lon.')';
$lat2 = 'RADIANS(Y(news.location))';
$lon2 = 'RADIANS(X(news.location))';
$distance = "6378.137 * ACOS(SIN({$lat1}) * SIN({$lat2}) + COS({$lat1}) * COS({$lat2}) * COS({$lon2} - {$lon1})) AS distance";
$opt = [
  'method' => 'select',
  'tables' =>['news', 'user'],
  'columns' => [
    'news.id AS news_id',
    'news.title AS title',
    'DATE_FORMAT(create_date, "%Y/%c/%e") AS date',
    'user.name AS author',
    $distance
  ],
  'where' => "user.id=news.author_id AND news.show_flg=1 AND news.id NOT IN (".implode(", ", $read_news).") AND news.location IS NOT NULL",
  'order' => 'distance',
  'limit' => $NEWS_PER_PAGE,
];
$results = controlMySQL($opt);
if ($results != false && count($results) > 0) {
  echo json_safe_encode($results);
}
?>
