<?php
session_start();

$id = $_GET['id'];
$pwd = $_GET['pwd'];

if ($id == 'admin' && $pwd == 'password') {
  $_SESSION['login'] = true;
  echo "ログイン成功";
} else {
  echo "IDまたはパスワードが間違っています";
}
?>
