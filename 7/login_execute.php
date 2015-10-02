<?php
require_once('common.php');

if (!isset($_POST['email']) || !isset($_POST['password'])) {
  $_SESSION['message'] = '<p class="alert alert-danger>必要事項が記入されていません</p>"';
  header('Location: login.php');
  exit;
}
$email = $_POST['email'];
$password = $_POST['password'];

$opt = [
  'method' => 'select',
  'tables' => ['user'],
  'columns' => ['id', 'email', 'password'],
  'where' => "email='{$email}'"
];
$data = controlMySQL($opt);
if (count($data) != 0) {
  foreach ($data as $user) {
    if (password_verify($password, $user['password'])) {
      $_SESSION['id'] = $user['id'];
      header('Location: index.php');
      exit;
    }
  }
}
$_SESSION['message'] = '<p class="alert alert-danger>メールアドレスまたはパスワードが違います</p>"';
header('Location: login.php');
?>
