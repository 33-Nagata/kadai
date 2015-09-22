<?php
require('common.php');
require_once('functions/control_MySQL.php');

if ($id == 0 && !isset($_GET['id'])) {
  header('Location: login.php');
  exit;
}

$request_id = isset($_GET['id']) ? $_GET['id'] : $id;
$opt = [
  'method' => 'select',
  'tables' => ['user'],
  'columns' => ['name', 'email'],
  'where' => "id='{$request_id}'"
];
$result = controlMySQL($opt);
if (!$result) {
  $_SESSION['message'] = '<p class="message error">ユーザーが存在しません</p>';
  header('Location: login.php');
  exit;
} else {
  $name = $result[0]['name'];
  $email = $result[0]['email'];
  $vector = "";
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
  <?php echo $message; ?>
  <p>ユーザー名：<?php echo h($name); ?></p>
  <?php if ($is_owner): ?>
    <p>メールアドレス：<?php echo h($email); ?></p>
  <?php endif; ?>
  <p>プロフィール写真：<img src="get_img.php?table=user&id=<?php echo $request_id; ?>" /></p>
  <p>興味・関心：<?php echo $vector; ?></p>
  <?php if ($is_owner): ?>
    <a href="update_user.php?id=<?php echo $request_id; ?>"><button>変更する</button></a>
  <?php endif; ?>
</body>
</html>
