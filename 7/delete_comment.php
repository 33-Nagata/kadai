<?php
require_once('common.php');
require_once('functions/control_MySQL.php');
require('login_required.php');

if (!isset($_GET['id'])) {
  $_SESSION['message'] = '<p class="message error">削除対象のコメントが選択されていません</p>';
  header('Location: index.php');
}
$comment_id = $_GET['id'];

$opt = [
  'method' => 'select',
  'tables' => ['comment'],
  'columns' => ['user_id', 'news_id'],
  'where' => "id='{$comment_id}' AND show_flg=1"
];
$result = controlMySQL($opt);
if (count($result) == 0) {
  $_SESSION['message'] = '<p class="message error">削除対象のコメントがありません</p>';
  header("Location: index.php");
}
$user_id = $result[0]['user_id'];
$news_id = $result[0]['news_id'];
if ($id != $user_id) {
  $_SESSION['message'] = '<p class="message error">削除権限がありません</p>';
  header("Location: news.php?id={$news_id}");
}

$opt = [
  'method' => 'update',
  'tables' => ['comment'],
  'columns' => ['show_flg' => 0],
  'where' => "id='{$comment_id}'"
];
if (controlMySQL($opt)) {
  $_SESSION['message'] = '<p class="message success">コメントを削除しました</p>';
} else {
  $_SESSION['message'] = '<p class="message success">コメントを削除できませんでした</p>';
}
header("Location: news.php?id={$news_id}");
?>
