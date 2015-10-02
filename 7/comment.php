<?php
require_once('common.php');

// News ID取得
if (!isset($_GET['id'])) {
  $_SESSION['message'] = '<p class="alert alert-danger">記事を指定してください</p>';
  header('Location: index.php');
  exit;
} elseif (!intval($_GET['id'])) {
  $_SESSION['message'] = '<p class="alert alert-danger">記事が存在しません</p>';
  header('Location: index.php');
  exit;
}
$news_id = $_GET['id'];
// POSTデータセット
$lat = $_POST['lat'];
$lon = $_POST['lon'];
$location = $lat != "" && $lon != "" ? "GeomFromText('POINT({$lon} {$lat})')" : NULL;
$text = $_POST['comment'];
// コメント書き込み
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
// ユーザーの関心情報更新
include('update_user_vector.php');
// 元の記事へ転送
header("Location: news.php?id={$news_id}");
?>
