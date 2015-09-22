<?php
require('common.php');
require_once('functions/control_MySQL.php');

if (!isset($_POST['name']) || $_POST['name'] == "" || !isset($_POST['email']) || $_POST['email'] == "") {
  header("Location: update_user.php?id={$id}");
  exit;
}

$name = $_POST['name'];
$email = $_POST['email'];
$password = isset($_POST['password']) && $_POST['password'] != "" ? password_hash($_POST['password'], PASSWORD_DEFAULT) : false;
$photo = isset($_FILES['photo']['tmp_name']) && $_FILES['photo']['error'] != UPLOAD_ERR_NO_FILE ? file_get_contents($_FILES['photo']['tmp_name']) : false;

$opt = [
  'method' => 'update',
  'tables' => ['user'],
  'columns' => [
    'name' => $name,
    'email' => $email,
  ],
  'where' => "id='{$id}'"
];
if ($password) $opt['columns']['password'] = $password;
if ($photo) $opt['columns']['photo'] = $photo;
if (controlMySQL($opt)) {
  $_SESSION['message'] = '<p class="message success">データを更新しました</p>';
  header('Location: user.php');
} else {
  $_SESSION['message'] = '<p class="message success">データの更新に失敗しました</p>';
  header('Location: update_user.php');
}
?>
