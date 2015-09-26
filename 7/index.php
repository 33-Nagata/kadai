<?php
require_once('common.php');
require_once('functions/control_MySQL.php');
require_once('functions/json_endode.php');
$NEWS_PER_PAGE = 10;

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
  $read_news = [];
  foreach ($results as $row) $read_news[] = $row['news_id'];
  //最新ニュース取得
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
    var close_news = [];
    var popular_news = [];
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
  <h2>近くで起きたこと</h2>
  <table class="news" id="close">
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
  <h2>近くで話題になってること</h2>
  <table class="news" id="popular">
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

  <script>
  function append_news(i) {
    var category_news = [latest_news, close_news, popular_news];
    for (var j = 0; j < category_news[i].length; j++) {
      var news = $(news_template).clone(true);
      for (var k = 0; k < class_names.length; k++) {
        news.find("td:eq("+k+")").html(category_news[i][j][class_names[k]]);
      }
      $("#"+categories[i]+" tbody").append(news);
      $("#"+categories[i]+"latest tbody tr:eq("+j+")").attr('id', 'latest' + category_news[i][j]['news_id']);
    }
  }

  var categories = ['latest', 'close', 'popular'];
  var class_names = ['title', 'author', 'date'];
  var news_template = document.createElement('tr');
  for (var i = 0; i < class_names.length; i++) {
    var td = document.createElement('td');
    td.className = class_names[i];
    news_template.appendChild(td);
  }

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position){
      var latitude = position.coords.latitude,
        longitude = position.coords.longitude;
      $.get(
        'get_news_by_distance.php',
        {
          lat: latitude,
          lon: longitude
        },
        // 近くのニュース取得
        function(data, textStatus) {
          if (textStatus == 'success') {
            close_news = JSON.parse(data);
            for (var row = 0; row < close_news.length; row++) {
              var news_id = close_news[row]['news_id'];
              var title = close_news[row]['title'];
              close_news[row]['title'] = '<a href="news.php?id='+news_id+'">'+title+'</a>';
            }
            $("#close tbody tr").remove();
            append_news(1);
          } else {
            console.log(textStatus);
          }
        },
        'html'
      );
      $.get(
        'get_popular_news.php',
        {
          lat: latitude,
          lon: longitude
        },
        //近くで話題のニュース取得
        function(data, textStatus) {
          if (textStatus == 'success') {
            popular_news = JSON.parse(data);
            for (var row = 0; row < popular_news.length; row++) {
              var news_id = popular_news[row]['news_id'];
              var title = popular_news[row]['title'];
              popular_news[row]['title'] = '<a href="news.php?id='+news_id+'">'+title+'</a>';
            }
            $("#popular tbody tr").remove();
            append_news(2);
          } else {
            console.log(textStatus);
          }
        },
        'html'
      );
    }, function(){
      console.log("位置情報取得不可");
    });
  }

  $(document).ready(function(){
    for (var i = 0; i < categories.length; i++) {
      append_news(i);
    }
  });
  </script>
</body>
</html>
