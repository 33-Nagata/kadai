<?php
session_start();
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
  header("Location: login.php");
} else {
  $message = $_SESSION != '' ? '<p>'.$_SESSION['message'].'</p>' : '';
  $_SESSION['message'] = '';
}

$id = $_GET['id'];

$pdo = new PDO("mysql:host=localhost;dbname=cs_academy;charset=utf8", "root", "");
$sql = "SELECT * FROM news WHERE news_id=".$id;
$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pdo = NULL;

$title = $results[0]['news_title'];
$detail = $results[0]['news_detail'];
$author = $results[0]['author'];
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
  <form action="update_execute.php" method="post">
    <input name="id" type="hidden" value="<?php echo $id; ?>">
    <label for="title">記事タイトル</label>
    <input name="title" type="text" value="<?php echo $title; ?>">
    <label for="author">投稿者</label>
    <input name="author" type="text" value="<?php echo $author; ?>">
    <label for="detail">本文</label>
    <textarea name="detail" cols=40 rows=4>
      <?php echo $detail; ?>
    </textarea>
    <input name="show" type="radio" value="1">表示する
    <input name="show" type="radio" value="0">表示しない
    <input type="submit" value="更新する">
  </form>
</body>
</html>
