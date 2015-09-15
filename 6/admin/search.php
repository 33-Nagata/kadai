<?php
session_start();
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
  header("Location: login.php");
} else {
  $message = $_SESSION != '' ? '<p>'.$_SESSION['message'].'</p>' : '';
  $_SESSION['message'] = '';
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
  <form action="news_list.php" method="get">
    <label for="keyword">キーワード</label>
    <input name="keyword" type="text">
    <label>検索期間</label>
    <input name="date_start" type="date">~<input name="date_end" type="date">
    <input type="submit" value="検索">
  </form>
</body>
</html>
