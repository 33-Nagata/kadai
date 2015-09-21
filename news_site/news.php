<?php
require('common.php');
require_once('functions/control_MySQL.php');

$news_id = array_key_exists('id', $_GET) ? $_GET['id'] : 0;
$opt = [
  'method' => 'select',
  'tables' => ['news', 'user'],
  'columns' => [
    'news.title',
    'news.article',
    'user.name'
  ],
  'where' => "news.id={$news_id} AND user.id=news.author_id AND news.show_flg=1"
];
$news = controlMySQL($opt);
$title = $news[0]['title'];
$article = $news[0]['article'];
$photo_src = "get_img.php?table=news&id={$news_id}";
$author = $news[0]['name'];
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
  <h1><?php echo $title; ?></h1>
  <img src="<?php echo $photo_src; ?>" />
  <p><?php echo $author; ?></p>
  <article><?php echo $article; ?></article>
  <a href="index.php">ニュース一覧へ戻る</a>
</body>
</html>
