<?php
require_once('common.php');

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

if (!isset($_POST['lat']) || !isset($_POST['lon']) || !isset($_POST['valid'])) {
  $_SESSION['message'] = '<p class="alert alert-danger">パラメーターが不正です</p>';
  header("Location: news.php?id={$news_id}");
  exit;
}
$lat = $_POST['lat'];
$lon = $_POST['lon'];
$location = $lat != "" && $lon != "" ? "GeomFromText('POINT({$lon} {$lat})')" : NULL;
$valid = $_POST['valid'];

// $valid -> 0:非表示, 1:表示, 2:表示(行追加)
if ($valid < 2) {
  $opt = [
    'method' => 'update',
    'tables' => 'share',
    'columns' => [
      'location' => $location,
      'update_date' => 'SYSDATE()',
      'valid' => $valid
    ],
    'where' => "user_id='{$id}' AND news_id='{$news_id}'"
  ];
} else {
  $opt = [
    'method' => 'insert',
    'tables' => 'share',
    'columns' => [
      'user_id' => $id,
      'news_id' => $news_id,
      'location' => $location,
      'valid' => 1
    ]
  ];
}
controlMySQL($opt);

//ユーザーベクトル更新
include('update_user_vector.php');

header("Location: news.php?id={$news_id}");
?>
