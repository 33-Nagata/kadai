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
      'where' => 'news.id=comment.news_id AND user.id=comment.user_id AND news.show_flg=1 AND comment.show_flg=1 AND comment.user_id IN ('.implode(", ", $followed_ids).")",
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
  }
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
  }
}

include('view.php');
?>
