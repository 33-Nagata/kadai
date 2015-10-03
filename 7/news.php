<?php
require_once('common.php');
include_once('functions/img.php');

// パラメーター確認
if (!isset($_GET['id']) || !intval($_GET['id'])) {
  $_SESSION['message'] = '<p class="alert alert-danger">パラメーターが不正です</p>';
  header('Location: index.php');
  exit;
}
// 記事取得
$news_id = $_GET['id'];
$opt = [
  'method' => 'select',
  'tables' => ['news', 'user'],
  'columns' => [
    'news.id AS news_id',
    'news.title AS title',
    'news.author_id AS author_id',
    'news.article AS article',
    'user.name AS name'
  ],
  'where' => "news.id={$news_id} AND user.id=news.author_id AND news.show_flg=1"
];
$news = controlMySQL($opt);
if (count($news) == 0) {
  $_SESSION['message'] = '<p class="alert alert-danger">記事が存在しません</p>';
  header('Location: index.php');
  exit;
}
//使用変数用意
$news_id = $news[0]['news_id'];
$title = $news[0]['title'];
$author_id = $news[0]['author_id'];
$article = $news[0]['article'];
$img = getImg('news', $news_id);
$author = $news[0]['name'];
$is_owner = $id == $author_id;
// シェア数取得
$opt = [
  'method' => 'select',
  'tables' => ['share'],
  'columns' => ['count(valid) AS cnt'],
  'where' => "news_id='{$news_id}' AND valid=1"
];
$result = controlMySQL($opt);
$share_count = $result[0]['cnt'];
//シェアボタン作成
$opt = [
  'method' => 'select',
  'tables' => ['share'],
  'columns' => ['valid'],
  'where' => "user_id='{$id}' AND news_id='{$news_id}'"
];
$result = controlMySQL($opt);
//データがあればフラグを反転・なければ2
//0:シェアを外す, 1:再度シェアする, 2:新規にシェアする
$valid = count($result) != 0 ? (intval($result[0]['valid']) + 1) % 2 : 2;

$share_button = $share_count.'シェア';
if (!$valid) $share_button .= '✓';
//コメント取得
$opt = [
  'method' => 'select',
  'tables' => ['comment', 'user'],
  'columns' => ['user.name', 'comment.id', 'comment.user_id', 'comment.text'],
  'where' => "comment.news_id='{$news_id}' AND comment.show_flg=1 AND user.id=comment.user_id",
  'order' => 'create_date'
];
$comments = controlMySQL($opt);
//既読にする
$opt = [
  'method' => 'select',
  'tables' => ['mark_read'],
  'columns' => ['COUNT(id) AS count'],
  'where' => "user_id='{$id}' AND news_id='{$news_id}'"
];
$result = controlMySQL($opt);
$count = $result[0]['count'];
// レコードなし
if ($count == 0) {
  $opt = [
    'method' => 'insert',
    'tables' => ['mark_read'],
    'columns' => [
      'user_id' => $id,
      'news_id' => $news_id,
      'valid' => 1
    ]
  ];
// レコードあり
} else {
  $opt = [
    'method' => 'update',
    'tables' => ['mark_read'],
    'columns' => ['valid' => 1],
    'where' => "user_id='{$id}' AND news_id='{$news_id}'"
  ];
controlMySQL($opt);
}
// HTML表示
include('view.php');
?>
