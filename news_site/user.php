<?php
require('common.php');
require_once('functions/control_MySQL.php');

$id = array_key_exists('id', $_GET) ? $_GET['id'] : 0;
$opt = [
  'method' => 'select',
  'tables' => ['user'],
  'columns' => ['*'],
  'where' => "id='{$_GET['id']}'"
];
$result = controlMySQL($opt);
if (count($result) == 0) {
  echo "ユーザーが存在しません";
} else {
  $user = $result[0];
}
$owner = $id == $user['id'] ? true : false;
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
  echo '<p>ユーザー名：'.$user['name'].'</p>';
  if ($owner) echo '<p>メールアドレス：'.$user['email'].'</p>';
  echo "<p>プロフィール写真：<img src='get_img.php?table=user&id={$id}' /></p>";
  echo "<興味・関心>：{$user['vector']}";
  ?>
</body>
</html>
