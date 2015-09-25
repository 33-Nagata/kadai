<?php
require_once('common.php');
require_once('functions/control_MySQL.php');

$news_id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : 0;
$opt = [
  'method' => 'select',
  'tables' => ['news', 'user'],
  'columns' => [
    'news.id',
    'news.title',
    'news.author_id',
    'news.photo IS NOT NULL AS is_photo',
    'news.article',
    'user.name'
  ],
  'where' => "news.id={$news_id} AND user.id=news.author_id AND news.show_flg=1"
];
$news = controlMySQL($opt);
if (count($news) == 0) {
  $_SESSION['message'] = '<p class="message error">記事が存在しません</p>';
  header('Location: index.php');
  exit;
}
$news_id = $news[0]['id'];
$title = $news[0]['title'];
$author_id = $news[0]['author_id'];
$article = $news[0]['article'];
$is_photo = $news[0]['is_photo'];
$photo_src = "get_img.php?table=news&id={$news_id}";
$author = $news[0]['name'];
$is_owner = $id == $author_id;

$opt = [
  'method' => 'select',
  'tables' => ['share'],
  'columns' => ['count(valid) AS cnt'],
  'where' => "news_id='{$news_id}' AND valid=1"
];
$result = controlMySQL($opt);
$share_count = $result[0]['cnt'];

$opt = [
  'method' => 'select',
  'tables' => ['share'],
  'columns' => ['valid'],
  'where' => "user_id='{$id}' AND news_id='{$news_id}'"
];
$result = controlMySQL($opt);
//データがあればフラグを反転・なければ2
//0:シェアを外す, 1:再度シェアする, 2:新規にシェアする
$valid = count($result) != 0 ? (intval($result[0]['valid']) + 1) % 2 : 2;

$share_button = $share_count.'シェア';
if (!$valid) $share_button .= '✓';

$opt = [
  'method' => 'select',
  'tables' => ['comment', 'user'],
  'columns' => ['user.name', 'comment.id', 'comment.user_id', 'comment.text'],
  'where' => "comment.news_id='{$news_id}' AND comment.show_flg=1 AND user.id=comment.user_id",
  'order' => 'create_date'
];
$comments = controlMySQL($opt);
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
  <?php echo $message; ?>
  <h1><?php echo h($title); ?></h1>
  <?php if ($is_photo): ?>
    <img src="<?php echo $photo_src; ?>" />
  <?php endif; ?>
  <p><?php echo h($author); ?></p>
  <article><?php echo h($article); ?></article>
  <?php if ($is_owner): ?>
    <a href="delete_news.php?id=<?php echo $news_id; ?>"><button>削除</button></a>
  <?php else: ?>
    <form action="share.php?id=<?php echo $news_id; ?>" method="post">
      <input name="lat" type="hidden" value="">
      <input name="lon" type="hidden" value="">
      <input name="valid" type="hidden" value="<?php echo $valid; ?>">
      <input type="submit" value="<?php echo $share_button ?>">
    </form>
  <?php endif; ?>
  <form action="comment.php?id=<?php echo $news_id; ?>" method="post">
    <textarea name="comment" required></textarea>
    <input name="lat" type="hidden" value="">
    <input name="lon" type="hidden" value="">
    <input type="submit" value="コメントする">
  </form>
  <?php foreach ($comments as $comment): ?>
    <hr>
    <p class="user_name"><?php echo $comment['name']; ?></p><br>
    <p class="comment"><?php echo $comment['text']; ?></p><br>
    <?php if ($id == $comment['user_id']): ?>
      <a href="delete_comment.php?id=<?php echo $comment['id']; ?>"><button>削除</button></a>
    <?php endif; ?>
  <?php endforeach; ?>
  <a href="index.php">ニュース一覧へ戻る</a>

  <script>
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position){
      $("input[name=lat]").val(position.coords.latitude);
      $("input[name=lon]").val(position.coords.longitude);
    }, function(){
      console.log("位置情報取得不可");
    });
  }
  </script>
</body>
</html>
