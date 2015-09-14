<?php
$keyword = $_GET['keyword'];

require_once('../config.php');
$opt = [
  'method' => 'select',
  'table' => 'news',
  'columns' => ['news_id','news_title','author'],
  'where' => "news_title LIKE '%{$keyword}%'"
];
include('../functions/controlMySQL.php');

echo "<table>";
foreach ($result as $news) {
  echo "<tr>";
  echo "<th>".$news['news_title']."</th>";
  echo "<td>".$news['author']."</td>";
  echo '<td><a href="update.php?id='.$news['news_id'].'">内容を変更する</a></td>';
  echo "</tr>";
}
echo "</table>";
?>
