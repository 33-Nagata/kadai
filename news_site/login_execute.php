<?php
session_start();
require_once('functions/control_MySQL.php');

$email = $_POST['email'];
$password = $_POST['password'];

$opt = [
  'method' => 'select',
  'tables' => ['user'],
  'columns' => ['id', 'email', 'password'],
  'where' => "email='{$email}'"
];
$data = controlMySQL($opt);
$_SESSION['message'] = '';
$error_message = '<p class="message failure">メールアドレスまたはパスワードが違います</p>';
if (count($data) == 0) {
  $_SESSION['message'] = $error_message;
} else {
  foreach ($data as $user) {
    if (password_verify($password, $user['password'])) {
      $id = $user['id'];
    } else {
      $_SESSION['message'] = $error_message;
    }
  }
}
if ($error_message == $_SESSION['message']) {
  header('Location: login.php');
  exit;
}
$_SESSION['id'] = $id;
header('Location: index.php');
?>
