<?php
session_start();

$_SESSION['login'] = false;
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
  <form action="login_execute.php" method="get">
    <label for="id">ユーザー名</label>
    <input name="id" type="text">
    <label for="pwd">パスワード</label>
    <input name="pwd" type="password">
    <br>
    <input type="submit" value="ログイン">
  </form>
</body>
</html>
