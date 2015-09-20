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
  <?php echo $message; ?>
  <ul>
  <li><a href="input.php">ニュース新規追加</a></li>
  <li><a href="news_list.php">ニュース一覧（更新はここから）</a></li>
  <li><a href="search_ps.php">ニュース検索</a></li>
  </ul>
</body>
</html>
