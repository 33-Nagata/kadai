<?php
require_once('common.php');
require_once('functions/control_MySQL.php');
require('login_required.php');

//includeで呼び出されているか確認
if (!isset($news_id)) {
  $_SESSION['message'] = '<p class="message error">不正なアクセスです</p>';
  header('Location: login.php');
  exit;
}

////Term Frequency
$opt = [
  'method' => 'select',
  'tables' => ['news_word_frequency'],
  'columns' => ['word_id, frequency'],
  'where' => "news_id='{$news_id}'"
];
$results = controlMySQL($opt);
$total = 0;
foreach ($results as $word) $total += $word['frequency'];
foreach ($results as $word) $tf[$word['word_id']] = $word['frequency'] / $total;

//Inverse Document Frequency
include('idf.php');

//TF-IDF
include('tf_idf.php');

//news.vector更新
$vector = serialize($tf_idf);
$opt = [
  'method' => 'update',
  'tables' => ['news'],
  'columns' => ['vector' => $vector],
  'where' => "id='{$news_id}'"
];
controlMySQL($opt);
?>
