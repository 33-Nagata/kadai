<?php
$title = $_POST['news_title'];
$author = $_POST['author'];
$detail = $_POST['news_detail'];

require_once('../config.php');
$opt = [
  'method' => 'insert',
  'table' => 'news',
  'columns' => [
    'news_id' => NULL,
    'news_title' => $title,
    'news_detail' => $detail,
    'show_flg' => 1,
    'author' => $author,
    'create_date' => 'SYSDATE()',
    'update_date' => 'SYSDATE()'
  ]
];
include('../functions/controlMySQL.php');
echo $result ? 'ニュースを投稿しました' : 'ニュースの投稿に失敗しました'
?>
