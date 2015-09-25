<?php
require_once('common.php');
require_once('functions/control_MySQL.php');
require('login_required.php');

if (!isset($_GET['id'])) {
  $_SESSION['message'] = '<p class="message error">削除対象の記事が選択されていません</p>';
  header('Location: index.php');
  exit;
}
$news_id = $_GET['id'];
$opt = [
  'method' => 'select',
  'tables' => 'news',
  'columns' => ['author_id'],
  'where' => "id='{$news_id}'"
];
$result = controlMySQL($opt);
if (count($result) == 0) {
  $_SESSION['message'] = '<p class="message error">削除対象の記事が存在しません</p>';
  header('Location: index.php');
  exit;
}
if ($id != $result[0]['author_id']) {
  $_SESSION['message'] = '<p class="message error">削除権限がありません</p>';
  header("Location: news.php?id={$news_id}");
  exit;
}

$opt = [
  'method' => 'update',
  'tables' => ['news'],
  'columns' => ['show_flg' => 0],
  'where' => "id='{$news_id}'"
];
if (controlMySQL($opt)) {
  $_SESSION['message'] = '<p class="message success">記事を削除しました</p>';
  header('Location: index.php');
} else {
  $_SESSION['message'] = '<p class="message error">記事を削除できませんでした</p>';
  header("Location: news.php?id={$news_id}");
}
?>
