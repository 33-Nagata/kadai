<?php
require_once('common.php');
require_once('functions/json_endode.php');

//フォローしてる人が話題にしているニュース取得
$follow_news = [];
$opt = [
  'method' => 'select',
  'tables' => ['follow'],
  'columns' => ['followed_id'],
  'where' => "follower_id={$id} AND valid=1"
];
$results = controlMySQL($opt);
// フォローユーザーが存在
if ($results) {
  $followed_ids = [];
  foreach ($results as $row) $followed_ids[] = $row['followed_id'];
  $opt = [
    'method' => 'select',
    'tables' => ['news', 'comment', 'user'],
    'columns' => [
      'news.id AS news_id',
      'news.title AS title',
      'DATE_FORMAT(comment.create_date, "%Y/%c/%e") AS date',
      'DATE_FORMAT(comment.create_date, "%k:%i:%s") AS time',
      '(UNIX_TIMESTAMP(CONVERT_TZ(SYSDATE(), "Etc/UTC", "SYSTEM")) - UNIX_TIMESTAMP()) / 3600 AS time_zone',
      'user.name AS name'
    ],
    'where' => 'news.id=comment.news_id AND user.id=comment.user_id AND news.show_flg=1 AND comment.show_flg=1 AND comment.user_id IN ('.implode(", ", $followed_ids).')',
    'order' => 'comment.create_date'
  ];
  $results = controlMySQL($opt);
  foreach ($results as $row) {
    $news_id = $row['news_id'];
    $title = h($row['title']);
    $name = h($row['name']);
    $date = $row['date'];
    $time = $row['time'];
    $tz = $row['time_zone'];
    $decimal = abs($tz) - (int)abs($tz);
    $minute = $decimal == 0 ? 0 : round(60 / $decimal);
    $tz_str = $tz < 0 ? '-' : '+';
    $tz_str .= abs($tz) < 10 ? '0' : '';
    $tz_str .= (int)abs($tz) * 100 + $minute;
    $date_str = "{$date} {$time} {$tz_str}";
    $follow_news[] = [
      'news_id' => $news_id,
      'title' => "<a href='news.php?id={$news_id}'>{$title}</a>",
      'author' => $name,
      'date' => $date,
      'date_str' => $date_str,
    ];
  }
  echo json_safe_encode($follow_news);
}
?>
