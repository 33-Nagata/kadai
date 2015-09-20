<?php
session_start();

$id = $_POST['name'];
$pwd = $_POST['password'];

if ($id == 'admin' && $pwd == 'password') {
  $_SESSION['login'] = true;
  $_SESSION['message'] = 'ログイン成功';
  header("Location: index.php");
} else {
  $_SESSION['message'] =  "IDまたはパスワードが間違っています";
  header("Location: login.php");
}
?>
