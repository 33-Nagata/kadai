<?php
$id = $_POST['id'];
$title = $_POST['title'];
$author = $_POST['author'];
$detail = $_POST['detail'];
$flg = $_POST['show'];

$pdo = new PDO("mysql:host=localhost;dbname=cs_academy;charset=utf8", "root", "");
$sql = "UPDATE news SET news_title=:title, news_detail=:detail, show_flg=:flg, author=:author, update_date=SYSDATE() WHERE news_id=$id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':detail', $detail, PDO::PARAM_STR);
$stmt->bindValue(':author', $author, PDO::PARAM_STR);
$stmt->bindValue(':flg', $flg, PDO::PARAM_INT);
if ($stmt->execute()) {
  echo "ニュースを更新しました";
} else {
  echo "ニュースの更新に失敗しました";
}
?>
