<?php
require_once('common.php');
require_once('functions/json_endode.php');
//既読ニュース取得
$opt = [
  'method' => 'select',
  'tables' => ['mark_read'],
  'columns' => ['news_id'],
  'where' => "user_id='{$id}' AND valid=1"
];
$results = controlMySQL($opt);
$read_news = [0];
foreach ($results as $row) $read_news[] = $row['news_id'];
//興味のありそうなニュース取得
$interest_news = [];
// ユーザーの関心ベクトル取得
$opt = [
  'method' => 'select',
  'tables' => ['user'],
  'columns' => ['vector'],
  'where' => "id={$id}"
];
$result = controlMySQL($opt);
if ($result[0]['vector'] != null) {
  $user_vector = unserialize($result[0]['vector']);
  // ユーザーベクトルの絶対値
  $abs_user_vector = 0;
  foreach ($user_vector as $value) $abs_user_vector += pow($value, 2);
  $abs_user_vector = sqrt($abs_user_vector);
  // 記事のベクトル取得
  $opt = [
    'method' => 'select',
    'tables' => ['news'],
    'columns' => [
      'id',
      'vector'
    ],
    'where' => 'show_flg=1 AND vector IS NOT NULL AND id NOT IN ('.implode(', ', $read_news).')'
  ];
  $results = controlMySQL($opt);
  $news_vector = [];
  foreach ($results as $row) $news_vector[$row['id']] = unserialize($row['vector']);
  // ベクトルの類似度計算
  $similarity = [];
  foreach ($news_vector as  $news_id => $vector) {
    if ($vector !== null) {
      $common_element = array_intersect(array_keys($user_vector), array_keys($vector));
      // ユーザーベクトルと記事ベクトルの内積
      $inner_product = 0;
      foreach ($common_element as $i) $inner_product += $user_vector[$i] * $vector[$i];
      // 記事ベクトルの絶対値
      $abs_news_vector = 0;
      foreach ($vector as $value) $abs_news_vector += pow($value, 2);
      $abs_news_vector = sqrt($abs_news_vector);
      // ユーザーベクトルと記事ベクトルの類似度
      $similarity[$news_id] = $inner_product / $abs_user_vector / $abs_news_vector;
    }
  }
  // 類似度の高い順に並び替え
  arsort($similarity);
  $list = [];
  $i = 0;
  foreach ($similarity as $key => $value) {
    $list[] = $key;
    $i++;
    if ($i >= $NEWS_PER_PAGE) break;
  }
  // 記事取得
  $opt = [
    'method' => 'select',
    'tables' => ['news', 'user'],
    'columns' => [
      'news.id AS news_id',
      'news.title AS title',
      'DATE_FORMAT(news.create_date, "%Y/%c/%e") AS date',
      'user.name AS name'
    ],
    'where' => 'user.id=news.author_id AND news.id IN ('.implode(', ', $list).')'
  ];
  $results = controlMySQL($opt);
  for ($i=0; $i < count($list); $i++) {
    foreach ($results as $row) {
      if ($row['news_id'] == $list[$i]) {
        $news_id = $row['news_id'];
        $title = $row['title'];
        $name = $row['name'];
        $date = $row['date'];
        $interest_news[] = [
          'news_id' => $news_id,
          'title' => "<a href='news.php?id={$news_id}'>{$title}</a>",
          'author' => $name,
          'date' => $date,
        ];
        break;
      }
    }
  }
  echo json_safe_encode($interest_news);
}
?>
