<?php
$title = $_POST['news_title'];
$author = $_POST['author'];
$detail = $_POST['news_detail'];

$pdo = new PDO("mysql:host=localhost;dbname=cs_academy;charset=utf8", "root", "");
$sql = "INSERT INTO news (news_id, news_title, news_detail, show_flg, author, create_date, update_date) VALUES (NULL, :title, :detail, 1, :author, SYSDATE(), SYSDATE())";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':detail', $detail, PDO::PARAM_STR);
$stmt->bindValue(':author', $author, PDO::PARAM_STR);
if ($stmt->execute()) {
  echo "ニュースを投稿しました";
} else {
  echo "ニュースの投稿に失敗しました";
}
?>
