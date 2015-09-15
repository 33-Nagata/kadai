<?php
session_start();
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
  header("Location: login.php");
} else {
  $message = $_SESSION != '' ? '<p>'.$_SESSION['message'].'</p>' : '';
  $_SESSION['message'] = '';
}

$id = $_GET['id'];

require_once('../config.php');
$opt = [
  'method' => 'select',
  'table' => 'news',
  'columns' => ['news_title', 'news_detail', 'show_flg', 'author'],
  'where' => "news_id=$id"
];
include('../functions/controlMySQL.php');

$title = $result[0]['news_title'];
$detail = $result[0]['news_detail'];
$flg = $result[0]['show_flg'];
$author = $result[0]['author'];
?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <form action="update_execute.php" method="post">
    <input name="id" type="hidden" value="<?php echo $id; ?>">
    <label for="title">記事タイトル</label>
    <input name="title" type="text" value="<?php echo $title; ?>">
    <label for="author">投稿者</label>
    <input name="author" type="text" value="<?php echo $author; ?>">
    <label for="detail">本文</label>
    <textarea name="detail" cols=40 rows=4><?php echo $detail; ?></textarea>
    <input name="show" type="radio" value="1">表示する
    <input name="show" type="radio" value="0">表示しない
    <input type="submit" value="更新する">
  </form>
</body>
</html>
