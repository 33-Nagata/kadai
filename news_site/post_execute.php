<?php
require('common.php');
require_once('functions/control_MySQL.php');
include('yahoo_japan_application_id.php');

$title = $_POST['title'];
$article = $_POST['article'];
$photo = array_key_exists('photo', $_POST) ? $_POST['photo'] : NULL;
$lat = $_POST['lat'];
$lon = $_POST['lon'];
$location = $lat != "" && $lon != "" ? "GeomFromText('POINT({$lon} {$lat})')" : NULL;
//テキスト解析
$sentence = mb_convert_encoding($article, 'utf-8', 'auto');
$url = "http://jlp.yahooapis.jp/MAService/V1/parse?appid=".$yahoo_japan_application_id."&results=ma";
$url .= "&sentence=".urlencode($sentence);
$xml  = simplexml_load_file($url);
$used_words = [];
foreach ($xml->ma_result->word_list->word as $data) {
  $word = (string)$data->surface;
  $category = $data->pos;
  if ($category == '名詞') {
    $used_words[$word] = array_key_exists($word, $used_words) ? $used_words[$word] + 1 : 1;
  }
}
//既登録単語取得
$opt = [
  'method' => 'select',
  'tables' => ['dictionary'],
  'columns' => ['word'],
  'where' => 'word="'.implode('" OR word="', array_keys($used_words)).'"'
];
$results = controlMySQL($opt);
$old_words = [];
foreach ($results as $v) $old_words[] = $v['word'];
//未登録単語登録
$new_words = array_diff(array_keys($used_words), $old_words);
$opt = [
  'table' => 'dictionary',
  'columns' => ['word'],
  'values' => []
];
foreach ($new_words as $word) {
  $opt['values'][] = [$word];
}
insertMultiColumns($opt);
//単語のid取得
$opt = [
  'method' => 'select',
  'tables' => ['dictionary'],
  'columns' => ['id', 'word'],
  'where' => 'word="'.implode('" OR word="', array_keys($used_words)).'"'
];
$results = controlMySQL($opt);
$word_list = [];
foreach ($results as $data) $word_list[$data['id']] = $data['word'];
//記事保存
$opt = [
  'method' => 'insert',
  'tables' => ['news'],
  'columns' => [
    'title' => $title,
    'article' => $article,
    'author_id' => $id,
    'photo' => $photo,
    'create_date' => 'SYSDATE()',
    'update_date' => 'SYSDATE()',
    'location' => $location,
    'show_flg' => 1
  ]
];
$news_id = controlMySQL($opt);
//記事中の単語使用回数登録
$opt = [
  'table' => 'news_word_frequency',
  'columns' => ['news_id', 'word_id', 'frequency'],
  'values' => []
];
foreach ($used_words as $word => $frequency) {
  $word_id = array_search($word, $word_list);
  $opt['values'][] = [$news_id, $word_id, $used_words[$word]];
}
insertMultiColumns($opt);

if ($news_id) {
  $_SESSION['message'] = '<p class="message success">記事を投稿しました</p>';
  header("Location: news.php?id={$news_id}");
} else {
  $_SESSION['message'] = '<p class="message failure">記事の投稿に失敗しました</p>';
  $_SESSION['title'] = $title;
  $_SESSION['article'] = $article;
  // header('Location: post.php');
}
?>
