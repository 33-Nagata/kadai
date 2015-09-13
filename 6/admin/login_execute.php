<?php
session_start();

var_dump($_GET);

$id = $_GET['id'];
$pwd = $_GET['pwd'];

if ($id == 'admin' && $pwd == 'password') {
  $_SESSION['login'] = true;
  echo "ログイン成功";
} else {
  echo "IDまたはパスワードが間違っています";
}
?>
