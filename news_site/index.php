<?php
session_start();
require_once('functions/control_MySQL.php');
$NEWS_PER_PAGE = 10;
$login = isset($_SESSION['login']) && $_SESSION['login'] == true ? true : false;
$page = array_key_exists('page', $_GET) ? $_GET['page'] : 1;
$message = '';
if (array_key_exists('message', $_SESSION)) {
  $message = $_SESSION['message'];
  $_SESSION['message'] = '';
}

if (!$login) {
  $opt = [
    'method' => 'select',
    'tables' => ['news', 'user'],
    'columns' => ['news.title', 'news.create_date', 'user.name'],
    'where' => 'news.author_id=user.id',
    'order' => 'news.create_date',
    'limit' => $NEWS_PER_PAGE,
    'offset' => $NEWS_PER_PAGE * ($page - 1)
  ];
  $all_news = controlMySQL($opt);
} else {
  $opt = [];
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
        <th>投稿日</th>
        <th>投稿者</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($all_news as $news): ?>
      <tr>
        <td><?php echo $news['title'] ?></td>
        <td><?php echo $news['create_date'] ?></td>
        <td><?php echo $news['name'] ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</body>
</html>
