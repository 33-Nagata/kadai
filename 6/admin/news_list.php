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
  'columns' => ['count(news_id) AS count']
];
include('../functions/controlMySQL.php');
$cnt = $result[0]['count'];

$opt['columns'] = ['news_id AS id','news_title AS title','author'];
$opt['order'] = 'create_date';
if ($keyword != '') {
  $opt['where'] = "(news_title LIKE '%{$keyword}%' || news_detail LIKE '%{$keyword}%')";
}
if ($start != '') {
  $opt['where'] = array_key_exists('where', $opt) ? $opt['where'].' && ' : '';
  $opt['where'] .= 'create_date>="'.$start.' 00:00:00"';
}
if ($end != '') {
  $opt['where'] = array_key_exists('where', $opt) ? $opt['where'].' && ' : '';
  $opt['where'] .= 'create_date<="'.$end.' 23:59:59"';
}
$opt['limit'] = array_key_exists('limit', $_GET) ? $_GET['limit'] : $sqlConfig['limit'];
$opt['offset'] = array_key_exists('offset', $_GET) ? $_GET['offset'] : 0;
include('../functions/controlMySQL.php');

$table = "<table>";
foreach ($result as $news) {
  $table .= "<tr>";
  $table .= "<th>".$news['title']."</th>";
  $table .= "<td>".$news['author']."</td>";
  $table .= '<td><a href="update.php?id='.$news['id'].'">内容を変更する</a></td>';
  $table .= "</tr>";
}
$table .= "</table>";

$prev = '';
if ($opt['offset'] > 0) {
  $rest = min($sqlConfig['limit'], $opt['offset']);
  $prev = '<a href="news_list.php?';
  $prev .= $keyword != '' ? 'keyword='.$keyword.'&' : '';
  $prev .= $start != '' ? 'date_start='.$start.'&' : '';
  $prev .= $end != '' ? 'date_end='.$end.'&' : '';
  $prev .= 'limit='.$rest.'&';
  $prev .= 'offset='.($opt['offset'] - $rest);
  $prev .= '">前の'.$rest.'件</a>';
}

$next = '';
if ($cnt - $opt['offset'] - $sqlConfig['limit'] > 0) {
  $rest = min($sqlConfig['limit'], $cnt - $opt['offset'] - $sqlConfig['limit']);
  $next = '<a href="news_list.php?';
  $next .= $keyword != '' ? 'keyword='.$keyword.'&' : '';
  $next .= $start != '' ? 'date_start='.$start.'&' : '';
  $next .= $end != '' ? 'date_end='.$end.'&' : '';
  $next .= 'offset='.($opt['offset']+$sqlConfig['limit']);
  $next .= '">次の'.$rest.'件</a>';
}

echo $message;
echo $table;
echo $prev;
echo $next;
?>
