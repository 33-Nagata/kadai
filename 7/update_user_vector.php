<?php
require_once('common.php');

//Term Frequency

// コメントかシェアした記事に含まれる単語と使用回数取得
$common_conditon = 'news_word_frequency.news_id=news.id AND news.show_flg=1';
$comment_condition = "news_word_frequency.news_id=comment.news_id AND comment.user_id='{$id}' AND comment.show_flg=1";
$share_condition = "news_word_frequency.news_id=share.news_id AND share.user_id='{$id}' AND share.valid=1";
$opt = [
  'method' => 'select',
  'tables' => ['news_word_frequency', 'share', 'comment', 'news'],
  'columns' => ['news_word_frequency.word_id', 'sum(news_word_frequency.frequency) AS count'],
  'where' => "(({$comment_condition}) OR ({$share_condition})) AND {$common_conditon}",
  'group' => "news_word_frequency.word_id"
];
$words = controlMySQL($opt);
$total = 0;
foreach ($words as $word) $total += $word['count'];
// ユーザーベクトルの要素として使用済みの単語取得
$opt = [
  'method' => 'select',
  'tables' => ['user_vector'],
  'columns' => ['word_id'],
  'where' => "user_id='{$id}'",
];
$results = controlMySQL($opt);
$resistered_id = [];
// ベクトル要素初期化
foreach ($results as $row) {
  $word_id = $row['word_id'];
  $tf[$word_id] = 0;
  $resistered_id[] = $word_id;
}
foreach ($words as $word) $tf[$word['word_id']] = $word['count'] / $total;

//Inverse Document Frequency
include('idf.php');

//TF-IDF
include('tf_idf.php');

//user.vector更新
$vector = serialize($tf_idf);
$opt = [
  'method' => 'update',
  'tables' => ['user'],
  'columns' => ['vector' => $vector],
  'where' => "id='{$id}'"
];
controlMySQL($opt);

//user_vector.tf_idf更新
$new_ids = array_diff(array_keys($tf_idf), $resistered_id);
if (count($new_ids) > 0) {
  $opt = [
    'table' => 'user_vector',
    'columns' => ['user_id', 'word_id', 'tf_idf'],
    'values' => []
  ];
  foreach ($new_ids as $word_id) $opt['values'][] = [$id, $word_id, $tf_idf[$word_id]];
  insertMultiColumns($opt);
}
foreach ($resistered_id as $word_id) {
  $opt = [
    'method' => 'update',
    'tables' => ['user_vector'],
    'columns' => ['tf_idf' => $tf_idf[$word_id]],
    'where' => "user_id='{$id}' AND word_id='{$word_id}'"
  ];
  controlMySQL($opt);
}
?>
