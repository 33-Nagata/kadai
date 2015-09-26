<?php
require_once('common.php');
require_once('functions/control_MySQL.php');
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
      'news.create_date AS create_date',
      'user.name AS name'
    ],
    'where' => "user.id=news.author_id AND news.show_flg=1 AND news.id NOT IN ('".implode("', '", $read_news)."')",
    'order' => 'news.create_date',
    'limit' => $NEWS_PER_PAGE,
    'offset' => $NEWS_PER_PAGE * ($page - 1)
  ];
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
</head>
<body>
  <?php echo $message; ?>
  <table class="news">
    <thead>
      <tr>
        <th>記事タイトル</th>
        <th>投稿者</th>
        <th>投稿日</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($all_news as $news): ?>
      <tr>
        <td><?php echo "<a href='news.php?id={$news['news_id']}'>".h($news['title'])."</a>" ?></td>
        <td><?php echo h($news['name']) ?></td>
        <td><?php echo $news['create_date'] ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</body>
</html>
