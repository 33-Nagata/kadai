<?php
session_start();
include_once('functions/control_MySQL.php');

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$opt = [
  'method' => 'select',
  'table' => 'user',
  'columns' => ['email'],
  'where' => "email={$_POST['email']}"
];
$result = controlMySQL($opt);
if (count($result) == 0) {
  header('Location: register.php?error=0');
  exit;
}

$opt = [
  'method' => 'insert',
  'table' => 'user',
  'columns' => [
    'id' => NULL,
    'name' => $name,
    'email' => $email,
    'password' => $password
    ]
];
if (controlMySQL($opt)) {
  $_SESSION['login'] = true;
  $_SESSION['message'] = '<p class="message success">登録完了</p>';
  echo "登録成功";
  // header('index.php');
} else {
  header('Location: register.php?error=1');
  exit;
}
?>
