<?php
require_once('common.php');
include_once('functions/img.php');
include('yahoo_japan_application_id.php');

if (!isset($_POST['title']) || !isset($_POST['article']) || !isset($_POST['lat']) || !isset($_POST['lon'])) {
  $_SESSION['message'] = '<p class="alert alert-danger">パラメーターが不正です</p>';
  header('Location: post.php');
  exit;
}
$title = $_POST['title'];
$article = $_POST['article'];
$lat = $_POST['lat'];
$lon = $_POST['lon'];
$location = $lat != "" && $lon != "" ? "GeomFromText('POINT({$lon} {$lat})')" : NULL;

include('functions/control_news.php');
//記事保存
$opt = [
  'method' => 'insert',
  'tables' => ['news'],
  'columns' => [
    'title' => $title,
    'article' => $article,
    'author_id' => $id,
    'location' => $location,
  ]
];
$news_id = insertNews($opt);
if ($news_id) {
  // 画像保存
  if (isset($_FILES['photo'])) saveImg('news', $news_id, $_FILES['photo']);
  //テキスト解析
  $used_words = getNounCounts($article); // [word => frequency]
  //既登録単語取得
  $old_words = getWordIds(array_keys($used_words)); // [word_id => word]
  //未登録単語登録
  saveNewWords(array_keys($used_words), $old_words);
  //単語のid取得
  $ids = getWordIds(array_keys($used_words)); // [word_id => word]
  foreach ($ids as $index => $word) {
    // 半角スペースが間に入ったもの等がMySQLで引っかかるので除外
    if (isset($used_words[mb_strtolower($word)])) {
      $word_list[$index] = [
        'word' => mb_strtolower($word),
        'frequency' => $used_words[mb_strtolower($word)]
      ];
    }
  }
  //記事中の単語使用回数登録
  $opt = [
    'method' => 'insert',
    'tables' => ['news_word_frequency'],
    'columns' => ['news_id', 'word_id', 'frequency'],
    'values' => []
  ];
  foreach ($word_list as $word_id => $word_data) {
    $frequency = $word_data['frequency'];
    $opt['values'][] = [$news_id, $word_id, $frequency];
  }
  controlMySQL($opt);
  //記事のvector登録
  include("update_news_vector.php");

  //結果表示
  $_SESSION['message'] = '<p class="alert alert-success">記事を投稿しました</p>';
  header("Location: news.php?id={$news_id}");
} else {
  $_SESSION['message'] = '<p class="alert alert-danger">記事の投稿に失敗しました</p>';
  $_SESSION['title'] = $title;
  $_SESSION['article'] = $article;
  header('Location: post.php');
}
?>
