<?php
session_start();
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
  header("Location: login.php");
}

$title = $_POST['news_title'];
$author = $_POST['author'];
$detail = $_POST['news_detail'];

require_once('../functions/controlMySQL.php');
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
$_SESSION['message'] = controlMySQL($opt) ? 'ニュースを投稿しました' : 'ニュースの投稿に失敗しました';
header("Location: index.php");
?>
