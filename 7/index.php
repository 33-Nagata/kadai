<?php
require_once('common.php');
require_once('functions/control_MySQL.php');
require_once('functions/json_endode.php');
$NEWS_PER_PAGE = 10;
$page = array_key_exists('page', $_GET) ? $_GET['page'] : 1;

if ($id == 0) {
  //未ログイン
  $opt = [
    'method' => 'select',
    'tables' => ['news', 'user'],
    'columns' => ['news.id', 'news.title', 'news.create_date', 'user.name'],
    'where' => 'user.id=news.author_id AND news.show_flg=1',
    'order' => 'news.create_date',
    'limit' => $NEWS_PER_PAGE,
    'offset' => $NEWS_PER_PAGE * ($page - 1)
  ];
  $all_news = controlMySQL($opt);
} else {
  //ログインユーザー
  $opt = [
    'method' => 'select',
    'tables' => ['mark_read'],
    'columns' => ['news_id'],
    'where' => "user_id='{$id}' AND valid=1"
  ];
  $results = controlMySQL($opt);
  $read_news = [];
  foreach ($results as $row) $read_news[] = $row['news_id'];
  $opt = [
    'method' => 'select',
    'tables' =>['news', 'user'],
    'columns' => [
      'news.id AS news_id',
      'news.title AS title',
      'DATE_FORMAT(create_date, "%Y/%c/%e") AS date',
      'DATE_FORMAT(create_date, "%k:%i:%s") AS time',
      '(UNIX_TIMESTAMP(CONVERT_TZ(SYSDATE(), "Etc/UTC", "SYSTEM")) - UNIX_TIMESTAMP()) / 3600 AS time_zone',
      'user.name AS name'
    ],
    'where' => "user.id=news.author_id AND news.show_flg=1 AND news.id NOT IN (".implode(", ", $read_news).")",
    'order' => 'news.create_date',
    'limit' => $NEWS_PER_PAGE,
    'offset' => $NEWS_PER_PAGE * ($page - 1)
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
  $all_news = controlMySQL($opt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

    <script>
    var latest_news = <?php echo json_safe_encode($latest_news); ?>;
    for (var i = 0; i < latest_news.length; i++) {
      latest_news[i]['date_obj'] = new Date(latest_news[i]['date_str']);
    }
    </script>
</head>
<body>
  <?php echo $message; ?>
  <h1>あなたのニュース</h1>
  <h2>最新</h2>
  <table class="news" id="latest">
    <thead>
      <tr>
        <th>記事タイトル</th>
        <th>投稿者</th>
        <th>投稿日</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
  <hr>

  <script>
    $(document).ready(function(){
      var class_names = ['title', 'author', 'date'];
      var news_template = document.createElement('tr');
      for (var i = 0; i < class_names.length; i++) {
        var td = document.createElement('td');
        td.className = class_names[i];
        news_template.appendChild(td);
      }
      for (var i = 0; i < latest_news.length; i++) {
        var news = $(news_template).clone(true);
        for (var j = 0; j < class_names.length; j++) {
          news.find("td:eq("+j+")").html(latest_news[i][class_names[j]]);
        }
        $("#latest tbody").append(news);
        $("#latest tbody tr:eq("+i+")").attr('id', 'latest' + latest_news[i]['news_id']);
      }
    });
  </script>
</body>
</html>
