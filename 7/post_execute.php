<?php
require('common.php');
require_once('functions/control_MySQL.php');
include('yahoo_japan_application_id.php');

$title = $_POST['title'];
$article = $_POST['article'];
$photo = isset($_FILES['photo']['tmp_name']) && $_FILES['photo']['error'] != UPLOAD_ERR_NO_FILE ? file_get_contents($_FILES['photo']['tmp_name']) : false;
$lat = $_POST['lat'];
$lon = $_POST['lon'];
$location = $lat != "" && $lon != "" ? "GeomFromText('POINT({$lon} {$lat})')" : NULL;

include('functions/control_news.php');
//記事保存
$opt = [
  'columns' => [
    'title' => $title,
    'article' => $article,
    'author_id' => $id,
    'location' => $location,
  ]
];
if ($photo) $opt['columns']['photo'] = $photo;
$news_id = insertNews($opt);

//テキスト解析
$used_words = getNounCounts($article);

//既登録単語取得
$old_words = getWordIds(array_keys($used_words));

//未登録単語登録
saveNewWords(array_keys($used_words), $old_words);

//単語のid取得
$ids = getWordIds(array_keys($used_words));
foreach ($ids as $index => $word) {
  $word_list[$index] = [
    'word' => $word,
    'frequency' => $used_words[$word]
  ];
}

//記事中の単語使用回数登録
$opt = [
  'table' => 'news_word_frequency',
  'columns' => ['news_id', 'word_id', 'frequency'],
  'values' => []
];
foreach ($word_list as $word_id => $word_data) {
  $frequency = $word_data['frequency'];
  $opt['values'][] = [$news_id, $word_id, $frequency];
}
insertMultiColumns($opt);

//記事のvector登録
include("update_news_vector.php");

//結果表示
if ($news_id) {
  $_SESSION['message'] = '<p class="message success">記事を投稿しました</p>';
  header("Location: news.php?id={$news_id}");
} else {
  $_SESSION['message'] = '<p class="message failure">記事の投稿に失敗しました</p>';
  $_SESSION['title'] = $title;
  $_SESSION['article'] = $article;
  header('Location: post.php');
}
?>
