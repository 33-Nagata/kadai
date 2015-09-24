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
}

$opt = [
  'method' => 'select',
  'tables' => ['dictionary', 'user_vector'],
  'columns' => ['dictionary.word AS word'],
  'where' => "user_vector.word_id=dictionary.id AND user_vector.user_id='{$request_id}'",
  'order' => 'user_vector.tf_idf',
  'limit' => 5
];
$results = controlMySQL($opt);
$interests = [];
foreach ($results as $row) $interests[] = $row['word'];

$opt = [
  'method' => 'select',
  'tables' => ['follow'],
  'columns' => ['valid'],
  'where' => "follower_id='{$id}' AND followed_id='{$request_id}'"
];
$result = controlMySQL($opt);
$valid = count($result) > 0 ? ($result[0]['valid'] + 1) % 2 : 2;
$follow_button = 'フォロー';
if ($valid == 0) $follow_button .= '✓';

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
  <p>関心のあるワード：<?php echo implode(', ', $interests); ?></p>
  <?php if ($is_owner): ?>
    <a href="update_user.php?id=<?php echo $request_id; ?>"><button>変更する</button></a>
  <?php else: ?>
    <form action="follow.php" method="post">
      <input name="id" type="hidden" value="<?php echo $request_id; ?>">
      <input type="submit" value="<?php echo $follow_button; ?>">
    </form>
  <?php endif; ?>
</body>
</html>
