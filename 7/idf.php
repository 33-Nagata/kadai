<?php
require_once('common.php');

//includeで呼び出されているか確認
if (!isset($news_id)) {
  $_SESSION['message'] = '<p class="alert alert-danger">不正なアクセスです</p>';
  header('Location: login.php');
  exit;
}
// 記事総数取得
$opt = [
  'method' => 'select',
  'tables' => ['news'],
  'columns' => ['COUNT(news.id) AS sum'],
  'where' => 'news.show_flg=1'
];
$result = controlMySQL($opt);
$total = $result[0]['sum'];
// 各単語が使用されている記事数取得
$opt = [
  'method' => 'select',
  'tables' => ['news_word_frequency', 'news'],
  'columns' => ['news_word_frequency.word_id AS word_id', 'COUNT(news_word_frequency.news_id) AS count'],
  'where' => 'news_word_frequency.news_id=news.id AND news.show_flg=1 AND news_word_frequency.word_id IN ("'.implode('", "', array_keys($tf)).'")',
  'group' => 'news_word_frequency.word_id'
];
$words = controlMySQL($opt);
foreach ($words as $word) $idf[$word['word_id']] = log($total / $word['count']);
?>
