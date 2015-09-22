<?php
require('common.php');

if ($id == 0) {
  header('Location: login.php');
  exit;
}

$title = '';
$article = '';
if (isset($_SESSION['title']) && isset($_SESSION['article'])) {
  $title = $_SESSION['title'];
  unset($_SESSION['title']);
  $article = $_SESSION['article'];
  unset($_SESSION['article']);
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
  <form action="post_execute.php" method="post" enctype="multipart/form-data">
    <label for="title" value="<?php echo $tilte; ?>">タイトル</label>
    <input name="title" type="text" required>
    <label for="article" value="<?php echo $article; ?>">記事</label>
    <textarea name="article" required></textarea>
    <label for="photo">写真</label>
    <input name="photo" type="file">
    <input name="lat" type="hidden" value="">
    <input name="lon" type="hidden" value="">
    <input type="submit" value="投稿">
  </form>

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
