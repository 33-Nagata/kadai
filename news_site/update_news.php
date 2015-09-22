<?php
require('common.php');
require_once('functions/control_MySQL.php');

if ($id == 0) {
  $_SESSION['message'] = '<p class="message error">ログインしてください</p>';
  header('Location: login.php');
  exit;
}
if (!isset($_GET['id'])) {
  header('Location: index.php');
  exit;
}
$news_id = $_GET['id'];
$opt = [
  'method' => 'select',
  'tables' => ['news'],
  'columns' => ['title', 'author_id', 'article', 'NOT ISNULL("photo") AS photo_exist'],
  'where' => "id='{$news_id}'"
];
$result = controlMySQL($opt);
if (!$result) {
  $_SESSION['message'] = '<p class="message error">ニュースが存在しません</p>';
  header('Location: index.php');
  exit;
}
$title = $result[0]['title'];
$article = $result[0]['article'];
if (isset($_SESSION['title']) && isset($_SESSION['article'])) {
  $title = $_SESSION['title'];
  unset($_SESSION['title']);
  $article = $_SESSION['article'];
  unset($_SESSION['article']);
}
$author_id = $result[0]['author_id'];
$photo_src = $result[0]['photo_exist'] ? "http://127.0.0.1/kadai/news_site/get_img.php?table=news&id={$news_id}" : "";
if ($id != $author_id) {
  $_SESSION['message'] = '<p class="message error">編集権限がありません</p>';
  header("Location: news.php?id={$news_id}");
  exit;
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
  <?php echo $message; ?>
  <form action="update_news_execute.php" method="post" enctype="multipart/form-data">
    <label for="title">タイトル</label>
    <input name="title" type="text" value="<?php echo h($title); ?>" required>
    <label for="article">記事</label>
    <textarea name="article" required><?php echo h($article); ?></textarea>
    <label for="photo">写真</label>
    <img src="<?php echo $photo_src; ?>" />
    <input name="photo" type="file">
    <label for="delete[]">削除</label>
    <input name="delete[]" type="checkbox" value="0">
    <input name="news_id" type="hidden" value="<?php echo $news_id; ?>">
    <input name="lat" type="hidden" value="">
    <input name="lon" type="hidden" value="">
    <input type="submit" value="更新">
  </form>
  <a href="news.php?id=<?php echo $news_id; ?>">ニュースページへ戻る</a>

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
