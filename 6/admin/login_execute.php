<?php
session_start();

$id = $_GET['id'];
$pwd = $_GET['pwd'];

if ($id == 'admin' && $pwd == 'password') {
  $_SESSION['login'] = true;
  $_SESSION['message'] = 'ログイン成功';
  header("Location: index.php");
} else {
  echo "IDまたはパスワードが間違っています";
}
?>
