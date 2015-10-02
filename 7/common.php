<?php
session_start();
require_once('functions/control_MySQL.php');
require('config.php');
include_once('functions/output.php');


if (isset($_SESSION['id']) && intval($_SESSION['id'])) {
  $id = $_SESSION['id'];
} else {
  $id = 0;
}
// リストにないページならログイン要求
$self = basename($_SERVER['PHP_SELF']);
$login_needless = [
  'get_img.php',
  'index.php',
  'login.php',
  'login_execute.php',
  'login_required.php',
  'mysql_config.php',
  'news.php',
  'register.php',
  'register_execute.php',
  'user.php'
];
if (!in_array($self, $login_needless)) {
  if ($id == 0) {
    $_SESSION['message'] = '<p class="alert alert-danger">ログインしてください</p>';
    header('Location: login.php');
    exit;
  }
}
// エラーメッセージ等セット
$message = '';
if (array_key_exists('message', $_SESSION)) {
  $message = $_SESSION['message'];
  $_SESSION['message'] = '';
}
?>
