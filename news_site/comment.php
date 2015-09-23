<?php
require('common.php');
require_once('functions/control_MySQL.php');
require('login_required.php');

if (!isset($_GET['id'])) {
  $_SESSION['message'] = '<p class="message error">記事を指定してください</p>';
  header('Location: index.php');
  exit;
} elseif (!intval($_GET['id'])) {
  $_SESSION['message'] = '<p class="message error">記事が存在しません</p>';
  header('Location: index.php');
  exit;
} else {
  $news_id = $_GET['id'];
}
$lat = $_POST['lat'];
$lon = $_POST['lon'];
$location = $lat != "" && $lon != "" ? "GeomFromText('POINT({$lon} {$lat})')" : NULL;
$text = $_POST['comment'];

$opt = [
  'method' => 'insert',
  'tables' => ['comment'],
  'columns' => [
    'news_id' => $news_id,
    'user_id' => $id,
    'text' => $text,
    'location' => $location,
    'create_date' => 'SYSDATE()',
    'show_flg' => 1
  ]
];
controlMySQL($opt);
header("Location: news.php?id={$news_id}");
?>
