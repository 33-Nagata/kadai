<?php
require_once('common.php');
include_once('functions/img.php');

if ($id == 0) {
  $_SESSION['message'] = '<p class="alert alert-danger">ログインしてください</p>';
  header('Location: login.php');
  exit;
}
$request_id = isset($_GET['id']) ? $_GET['id'] : $id;
if ($id != $request_id) {
  $_SESSION['message'] = '<p class="alert alert-danger">編集したいユーザーアカウントでログインしてください</p>';
  header('Location: user.php?id={$request_id}');
  exit;
}
$opt = [
  'method' => 'select',
  'tables' => ['user'],
  'columns' => ['name', 'email'],
  'where' => "id='{$request_id}'"
];
$result = controlMySQL($opt);
if (!$result) {
  $_SESSION['message'] = '<p class="alert alert-danger">ユーザーが存在しません</p>';
  header('Location: login.php');
  exit;
}
$name = $result[0]['name'];
$email = $result[0]['email'];
$img = getImg('user', $request_id);

include('view.php');
?>
