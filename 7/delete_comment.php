<?php
require_once('common.php');

$target_str = 'コメント';
$target_table = 'comment';
$target_id = $_GET['id'];
$opt = [
  'method' => 'select',
  'tables' => ['comment'],
  'columns' => ['user_id AS author_id', 'news_id'],
  'where' => "id='{$target_id}' AND show_flg=1"
];

include('delete.php');

header("Location: news.php?id={$news_id}");
?>
