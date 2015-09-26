<?php
require_once('common.php');
require_once('functions/control_MySQL.php');
require('login_required.php');

if (!isset($_POST['news_id'])) {
  $_SESSION['message'] = '<p class="message error">対象の記事が選択されていません</p>';
  header('Location: index.php');
}
$news_id = $_POST['news_id'];
$opt = [
  'method' => 'select',
  'tables' => ['mark_read'],
  'columns' => ['COUNT(id) AS count'],
  'where' => "user_id='{$id}' AND news_id='{$news_id}'"
];
$result = controlMySQL($opt);
$count = $result[0]['count'];
if ($count == false) {
  $success = true;
} else {
  $opt = [
    'method' => 'update',
    'tables' => ['mark_read'],
    'columns' => ['valid' => 0],
    'where' => "user_id='{$id}' AND news_id='{$news_id}'"
  ];
  $success = controlMySQL($opt);
}
if ($success) {
  $_SESSION['message'] = '<p class="message success">未読にしました</p>';
  header('Location: index.php');
} else {
  $_SESSION['message'] = '<p class="message success">未読にできませんでした</p>';
  header("Location: news.php?id={$news_id}");
}
?>
