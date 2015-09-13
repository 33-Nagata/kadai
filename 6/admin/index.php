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
  <p><a href="news_list.php">ニュース一覧</a></p>
  <p><a href="input.php">ニュース入力ページ</a></p>
</body>
</html>
