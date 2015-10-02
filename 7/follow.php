<?php
require_once('common.php');

// エラー処理
if (!isset($_POST['id'])) {
  $_SESSION['message'] = '<p class="alert alert-danger">フォロー対象が選択されていません</p>';
  header('Location: user.php');
  exit;
}

$followed_id = $_POST['id'];
$opt = [
  'method' => 'select',
  'tables' => ['user'],
  'columns' => ['id'],
  'where' => "id='{$followed_id}'"
];
if (!controlMySQL($opt)) {
  $_SESSION['message'] = '<p class="alert alert-danger">対象ユーザーが存在しません</p>';
  header('Location: user.php');
  exit;
}

// フォローステータス確認
$opt = [
  'method' => 'select',
  'tables' => ['follow'],
  'columns' => ['valid'],
  'where' => "follower_id='{$id}' AND followed_id='{$followed_id}'"
];
$result = controlMySQL($opt);
//データがあればフラグを反転・なければ新規追加
//0:シェアを外す, 1:再度シェアする, 2:新規にシェアする
$valid = count($result) > 0 ? ($result[0]['valid'] + 1) % 2 : 2;

if ($valid < 2) {
  $opt = [
    'method' => 'update',
    'tables' => ['follow'],
    'columns' => ['valid' => $valid],
    'where' => "follower_id='{$id}' AND followed_id='{$followed_id}'"
  ];
} else {
  $opt = [
    'method' => 'insert',
    'tables' => ['follow'],
    'columns' => [
      'follower_id' => $id,
      'followed_id' => $followed_id,
      'valid' => 1
    ]
  ];
}
controlMySQL($opt);
header("Location: user.php?id=$followed_id");
?>
