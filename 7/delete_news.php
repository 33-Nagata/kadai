<?php
require_once('common.php');

$target_str = '記事';
$target_table = 'news';
$target_id = $_GET['id'];
$opt = [
  'method' => 'select',
  'tables' => 'news',
  'columns' => ['id AS news_id', 'author_id'],
  'where' => "id='{$target_id}'"
];

include('delete.php');

if (stristr($_SESSION['message'], 'success')) {
  header('Location: index.php');
} else {
  header("Location: news.php?id={$news_id}");
}
?>
