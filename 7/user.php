<?php
require_once('common.php');

if ($id == 0 && !isset($_GET['id'])) {
  header('Location: login.php');
  exit;
}
//プロフィールを取得
$request_id = isset($_GET['id']) ? $_GET['id'] : $id;
$opt = [
  'method' => 'select',
  'tables' => ['user'],
  'columns' => ['user.name AS name', 'user.email AS email'],
  'where' => "user.id={$request_id}"
];
$result = controlMySQL($opt);
if (!$result) {
  $_SESSION['message'] = '<p class="alert alert-danger">ユーザーが存在しません</p>';
  header('Location: login.php');
  exit;
}
$name = $result[0]['name'];
$email = $result[0]['email'];
$opt = [
  'method' => 'select',
  'tables' => ['img'],
  'columns' => ['file_name'],
  'where' => "table_name='user' AND content_id={$id}"
];
$result = controlMySQL($opt);
$img = $result ? $result[0]['file_name'] : null;
//関心ワード取得
$opt = [
  'method' => 'select',
  'tables' => ['dictionary', 'user_vector'],
  'columns' => ['dictionary.word AS word'],
  'where' => "user_vector.word_id=dictionary.id AND user_vector.user_id={$request_id}",
  'order' => 'user_vector.tf_idf',
  'limit' => 5
];
$results = controlMySQL($opt);
$interests = [];
foreach ($results as $row) $interests[] = $row['word'];
//最近の投稿取得
$opt = [
  'method' => 'select',
  'tables' => ['news'],
  'columns' => ['id', 'title'],
  'where' => "author_id={$request_id} AND show_flg=1",
  'order' => 'create_date',
  'limit' => 5
];
$recent_news = controlMySQL($opt);
//最近のシェア
$opt = [
  'method' => 'select',
  'tables' => ['news', 'share'],
  'columns' => ['news.id AS id', 'news.title AS title'],
  'where' => "news.id=share.news_id AND share.user_id={$request_id} AND news.show_flg=1 AND share.valid=1",
  'order' => 'share.update_date',
  'limit' => 5
];
$recent_share = controlMySQL($opt);
//本人判定用変数セット
$is_owner = $id == $request_id;
//フォローボタン作成
if (!$is_owner) {
  $opt = [
    'method' => 'select',
    'tables' => ['follow'],
    'columns' => ['valid'],
    'where' => "follower_id={$id} AND followed_id={$request_id}"
  ];
  $result = controlMySQL($opt);
  $valid = count($result) > 0 ? ($result[0]['valid'] + 1) % 2 : 2;
  $follow_button = 'フォロー';
  if ($valid == 0) $follow_button .= '✓';
}
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
  <?php if ($img != null): ?>
    <p>プロフィール写真：<img src="img/<?php echo $img; ?>" /></p>
  <?php endif; ?>
  <p>関心のあるワード：<?php echo implode(', ', $interests); ?></p>
  <?php if ($is_owner): ?>
    <a href="update_user.php?id=<?php echo $request_id; ?>"><button>プロフィール変更</button></a>
    <a href="post.php"><button>ニュース投稿</button></a>
  <?php else: ?>
    <form action="follow.php" method="post">
      <input name="id" type="hidden" value="<?php echo $request_id; ?>">
      <input type="submit" value="<?php echo $follow_button; ?>">
    </form>
  <?php endif; ?>
  <hr>
  <p>最近の投稿</p>
  <?php if ($recent_news == false || count($recent_news) == 0): ?>
    <p>なし</p>
  <?php else: ?>
    <?php foreach ($recent_news as $news): ?>
      <p><a href="news.php?id=<?php echo $news['id']; ?>"><?php echo $news['title']; ?></a></p>
    <?php endforeach; ?>
  <?php endif; ?>
  <hr>
  <p>最近のシェア</p>
  <?php if ($recent_share == false || count($recent_share) == 0): ?>
    <p>なし</p>
  <?php else: ?>
    <?php foreach ($recent_share as $news): ?>
      <p><a href="news.php?id=<?php echo $news['id']; ?>"><?php echo $news['title']; ?></a></p>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>
