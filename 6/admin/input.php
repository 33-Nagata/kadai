<?php
session_start();
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
  header("Location: login.php");
}
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
  <form action="input_execute.php" method="post">
    <label for="news_title">タイトル</label>
    <input name="news_title" type="text">
    <label for="author">投稿者</label>
    <input name="author" type="text">
    <label for="news_detail">本文</label>
    <textarea name="news_detail" cols=40 rows=4></textarea>
    <input type="submit" value="投稿する">
  </form>
</body>
</html>
