<?php
session_start();
include_once('functions/control_MySQL.php');

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$photo = file_get_contents($_FILES['photo']['tmp_name']);

$opt = [
  'method' => 'select',
  'tables' => ['user'],
  'columns' => ['email'],
  'where' => "email='{$email}'"
];
$result = controlMySQL($opt);
if (count($result) != 0) {
  $_SESSION['message'] = '<p class="message failure">既に登録されているメールアドレスです</p>';
  header('Location: login.php');
  exit;
}

$opt = [
  'method' => 'insert',
  'tables' => ['user'],
  'columns' => [
    'id' => NULL,
    'name' => $name,
    'email' => $email,
    'password' => $password,
    'photo' => $photo
    ]
];
if (controlMySQL($opt)) {
  $_SESSION['login'] = true;
  $_SESSION['message'] = '<p class="message success">登録完了</p>';
  header('index.php');
} else {
  header('Location: register.php?error=0');
  exit;
}
?>
