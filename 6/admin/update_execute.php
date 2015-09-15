<?php
session_start();
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
  header("Location: login.php");
}

$id = $_POST['id'];
$title = $_POST['title'];
$author = $_POST['author'];
$detail = $_POST['detail'];
$flg = $_POST['show'];

require_once('../config.php');
$opt = [
  'method' => 'update',
  'table' => 'news',
  'columns' => [
    'news_title' => $title,
    'news_detail' => $detail,
    'show_flg' => $flg,
    'author' => $author,
    'update_date' => 'SYSDATE()'
  ],
  'where' => "news_id=$id"
];
include('../functions/controlMySQL.php');

$_SESSION['message'] = $result ? 'ニュースを更新しました' : 'ニュースの更新に失敗しました';
header("Location: news_list.php");
?>
