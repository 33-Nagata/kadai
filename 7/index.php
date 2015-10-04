<?php
require_once('common.php');
require_once('functions/json_endode.php');

if ($id == 0) {
  //未ログイン
  $opt = [
    'method' => 'select',
    'tables' => ['news', 'user'],
    'columns' => ['news.id', 'news.title', 'news.create_date', 'user.name'],
    'where' => 'user.id=news.author_id AND news.show_flg=1',
    'order' => 'news.create_date',
    'limit' => $NEWS_PER_PAGE,
  ];
  $all_news = controlMySQL($opt);
  //ログインユーザー
} else {
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
  //最新ニュース取得
  $opt = [
    'method' => 'select',
    'tables' =>['news', 'user'],
    'columns' => [
      'news.id AS news_id',
      'news.title AS title',
      'DATE_FORMAT(news.create_date, "%Y/%c/%e") AS date',
      'DATE_FORMAT(news.create_date, "%k:%i:%s") AS time',
      '(UNIX_TIMESTAMP(CONVERT_TZ(SYSDATE(), "Etc/UTC", "SYSTEM")) - UNIX_TIMESTAMP()) / 3600 AS time_zone',
      'user.name AS name'
    ],
    'where' => "user.id=news.author_id AND news.show_flg=1 AND news.id NOT IN (".implode(", ", $read_news).")",
    'order' => 'news.create_date',
    'limit' => $NEWS_PER_PAGE,
  ];
  $results = controlMySQL($opt);
  $latest_news = [];
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
    $latest_news[] = [
      'news_id' => $news_id,
      'title' => "<a href='news.php?id={$news_id}'>{$title}</a>",
      'author' => $name,
      'date' => $date,
      'date_str' => $date_str,
    ];
  }
}

include('view.php');
?>
