<?php
require_once('common.php');

// POSTデータセット
$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
// 重複登録確認
$opt = [
  'method' => 'select',
  'tables' => ['user'],
  'columns' => ['email'],
  'where' => "email='{$email}'"
];
$result = controlMySQL($opt);
if (count($result) != 0) {
  $_SESSION['message'] = '<p class="alert alert-danger">既に登録されているメールアドレスです</p>';
  header('Location: login.php');
  exit;
}
// ユーザー登録
$opt = [
  'method' => 'insert',
  'tables' => ['user'],
  'columns' => [
    'id' => NULL,
    'name' => $name,
    'email' => $email,
    'password' => $password,
    ]
];
$target_id = controlMySQL($opt);
if ($target_id) {
  // 画像保存
  $table_name = 'user';
  include('save_img.php');
  // user.phpへ転送
  $_SESSION['message'] = '<p class="alert alert-success">登録完了</p>';
  $_SESSION['id'] = $target_id;
  header('Location: user.php');
} else {
  $_SESSION['message'] = '<p class="alert alert-danger">登録に失敗しました</p>';
  header('Location: register.php');
}
?>
