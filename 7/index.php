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
  $opt = [
    'method' => 'select',
    'tables' => ['follow'],
    'columns' => ['followed_id'],
    'where' => "follower_id={$id} AND valid=1"
  ];
  $results = controlMySQL($opt);
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
  $follow_news = [];
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
  //興味のありそうなニュース取得
  $opt = [
    'method' => 'select',
    'tables' => ['user'],
    'columns' => ['vector'],
    'where' => "id={$id}"
  ];
  $result = controlMySQL($opt);
  $user_vector = unserialize($result[0]['vector']);
  $abs_user_vector = 0;
  foreach ($user_vector as $value) $abs_user_vector += pow($value, 2);
  $abs_user_vector = sqrt($abs_user_vector);
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
  $similarity = [];
  foreach ($news_vector as  $news_id => $vector) {
    $common_element = array_intersect(array_keys($user_vector), array_keys($vector));
    $inner_product = 0;
    foreach ($common_element as $i) $inner_product += $user_vector[$i] * $vector[$i];
    $abs_news_vector = 0;
    foreach ($vector as $value) $abs_news_vector += pow($value, 2);
    $abs_news_vector = sqrt($abs_news_vector);
    $similarity[$news_id] = $inner_product / $abs_user_vector / $abs_news_vector;
  }
  arsort($similarity);
  $list = [];
  $i = 0;
  foreach ($similarity as $key => $value) {
    $list[] = $key;
    $i++;
    if ($i >= $NEWS_PER_PAGE) break;
  }
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
  $interest_news = [];
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
    var follow_news = <?php echo json_safe_encode($follow_news); ?>;
    for (var i = 0; i < follow_news.length; i++) {
      follow_news[i]['date_obj'] = new Date(follow_news[i]['date_str']);
    }
    var interest_news = <?php echo json_safe_encode($interest_news); ?>;
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
  <h2>フォローしてる人が話題にしていること</h2>
  <table class="news" id="follow">
    <thead>
      <tr>
        <th>記事タイトル</th>
        <th>投稿者</th>
        <th>コメント日</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
  <hr>
  <h2>関心のありそうなこと</h2>
  <table class="news" id="interest">
    <thead>
      <tr>
        <th>記事タイトル</th>
        <th>投稿者</th>
        <th>コメント日</th>
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
  function index_of(value, array) {
    for (var i = 0; i < array.length; i++) {
      if (array[i] == value) return i;
    }
    return false;
  }
  function append_news(i) {
    var category_news = [latest_news, follow_news, interest_news, close_news, popular_news];
    for (var j = 0; j < category_news[i].length; j++) {
      var news = $(news_template).clone(true);
      for (var k = 0; k < class_names.length; k++) {
        news.find("td:eq("+k+")").html(category_news[i][j][class_names[k]]);
      }
      $("#"+categories[i]+" tbody").append(news);
      $("#"+categories[i]+"latest tbody tr:eq("+j+")").attr('id', 'latest' + category_news[i][j]['news_id']);
    }
  }

  var categories = ['latest', 'follow', 'interest', 'close', 'popular'];
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
            append_news(index_of('close', categories));
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
            append_news(index_of('popular', categories));
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
