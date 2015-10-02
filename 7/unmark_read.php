<?php
require_once('common.php');

if (!isset($_POST['news_id'])) {
  $_SESSION['message'] = '<p class="alert alert-danger">対象の記事が選択されていません</p>';
  header('Location: index.php');
  exit;
}
$news_id = $_POST['news_id'];
// 既読の記録確認
$opt = [
  'method' => 'select',
  'tables' => ['mark_read'],
  'columns' => ['COUNT(id) AS count'],
  'where' => "user_id='{$id}' AND news_id='{$news_id}'"
];
$result = controlMySQL($opt);
$count = $result[0]['count'];
// 既読データがない
if ($count == false) {
  $result = true;
// 既読データがある
} else {
  $opt = [
    'method' => 'update',
    'tables' => ['mark_read'],
    'columns' => ['valid' => 0],
    'where' => "user_id='{$id}' AND news_id='{$news_id}'"
  ];
  $result = controlMySQL($opt);
}
if ($result) {
  $_SESSION['message'] = '<p class="alert alert-success">未読にしました</p>';
  header('Location: index.php');
} else {
  $_SESSION['message'] = '<p class="alert alert-danger">未読にできませんでした</p>';
  header("Location: news.php?id={$news_id}");
}
?>
