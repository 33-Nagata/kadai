<?php
session_start();
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
  header("Location: login.php");
}

$keyword = $_GET['keyword'];
$start = $_GET['date_start'];
$end = $_GET['date_end'];

require_once('../config.php');
$opt = [
  'method' => 'select',
  'table' => 'news',
  'columns' => ['news_id','news_title','author'],
];
if ($keyword != '') {
  $opt['where'] = "(news_title LIKE '%{$keyword}%' || news_detail LIKE '%{$keyword}%')";
}
if ($start != '') {
  $opt['where'] = array_key_exists('where', $opt) ? $opt['where'].' && ' : '';
  $opt['where'] .= 'create_date>="'.$start.' 00:00:00"';
}
if ($end != '') {
  $opt['where'] = array_key_exists('where', $opt) ? $opt['where'].' && ' : '';
  $opt['where'] .= 'create_date<="'.$end.'23:59:59"';
}
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
