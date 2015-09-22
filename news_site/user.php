<?php
require('common.php');
require_once('functions/control_MySQL.php');

$request_id = $_GET['id'];
$opt = [
  'method' => 'select',
  'tables' => ['user'],
  'columns' => ['*'],
  'where' => "id='{$request_id}'"
];
$result = controlMySQL($opt);
if (count($result) == 0) {
  echo "ユーザーが存在しません";
} else {
  $user = $result[0];
}
$is_owner = $id == $request_id;
?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <?php
  echo $message;
  echo '<p>ユーザー名：'.h($user['name']).'</p>';
  if ($is_owner) echo '<p>メールアドレス：'.h($user['email']).'</p>';
  echo "<p>プロフィール写真：<img src='get_img.php?table=user&id={$request_id}' /></p>";
  echo "<興味・関心>：{$user['vector']}";
  ?>
</body>
</html>
