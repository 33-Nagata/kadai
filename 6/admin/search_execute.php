<?php
$keyword = $_GET['keyword'];

$pdo = new PDO("mysql:host=localhost;dbname=cs_academy;charset=utf8", "root", "");
$sql = "SELECT * FROM news WHERE news_title LIKE '%{$keyword}%'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pdo = NULL;

echo "<table>";
foreach ($results as $news) {
  echo "<tr>";
  echo "<th>".$news['news_title']."</th>";
  echo "<td>".$news['author']."</td>";
  echo '<td><a href="update.php?id='.$news['news_id'].'">内容を変更する</a></td>';
  echo "</tr>";
}
echo "</table>";
?>
