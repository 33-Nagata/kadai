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

//近くで話題のニュース取得
$lat1 = 'RADIANS('.$lat.')';
$lon1 = 'RADIANS('.$lon.')';
$lat2['comment'] = 'RADIANS(Y(comment.location))';
$lon2['comment'] = 'RADIANS(X(comment.location))';
$lat2['share'] = 'RADIANS(Y(share.location))';
$lon2['share'] = 'RADIANS(X(share.location))';
$comment_distance = "6378.137 * ACOS(SIN({$lat1}) * SIN({$lat2['comment']}) + COS({$lat1}) * COS({$lat2['comment']}) * COS({$lon2['comment']} - {$lon1})) < 20";
$share_distance = "6378.137 * ACOS(SIN({$lat1}) * SIN({$lat2['share']}) + COS({$lat1}) * COS({$lat2['share']}) * COS({$lon2['share']} - {$lon1})) < 20";
$common_conditon = 'news.show_flg=1 AND user.id=news.author_id';
$comment_condition = "news.id=comment.news_id AND comment.show_flg=1 AND {$comment_distance}";
$share_condition = "news.id=share.news_id AND share.valid=1 AND {$share_distance}";
$opt = [
  'method' => 'select',
  'tables' => ['share', 'comment', 'news', 'user'],
  'columns' => [
    'news.id AS news_id',
    'news.title AS title',
    'DATE_FORMAT(news.create_date, "%Y/%c/%e") AS date',
    'user.name AS author',
    'count(comment.id) + count(share.id) AS count'
  ],
  'where' => "(({$comment_condition}) OR ({$share_condition})) AND {$common_conditon}",
  'group' => "news.id"
];
$results = controlMySQL($opt);
if ($results != false && count($results) > 0) {
  echo json_safe_encode($results);
}
?>
