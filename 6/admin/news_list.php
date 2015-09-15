<?php
session_start();
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
  header("Location: login.php");
} else {
  $message = $_SESSION != '' ? '<p>'.$_SESSION['message'].'</p>' : '';
  $_SESSION['message'] = '';
}

$keyword = array_key_exists('keyword', $_GET) ? $_GET['keyword'] : '';
$start = array_key_exists('date_start', $_GET) ? $_GET['date_start'] : '';
$end = array_key_exists('date_end', $_GET) ? $_GET['date_end'] : '';

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

echo $message;
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
