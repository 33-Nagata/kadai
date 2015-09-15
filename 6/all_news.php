<?php
require_once('config.php');
require_once('functions/controlMySQL.php');
$opt = [
  'method' => 'select',
  'table' => 'news',
  'columns' => [
    'news_id AS id',
    'left(news_title, 10) AS title',
    'date_format(create_date, "%Y%.%m%.%d") AS date'
  ],
  'order' => 'create_date',
  'where' => 'show_flg=1'
];
$result = controlMySQL($opt);

$newsList = [];
foreach ($result as $news) {
  $url = 'news.php?id='.$news['id'];
  $title = $news['title'];
  $dateHtml = '<dt class="news-date"><a href="'.$url.'">'.$news['date'].'</a></dt>';
  $titleHtml = '<dd class="news-description"><a href="'.$url.'">'.$title.'</a></dd>';
  $newsList[] = $dateHtml.$titleHtml;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('header.php'); ?>

    <section class="news contents-box">
        <h2 class="section-title text-center">
            <span class="section-title__yellow">All News</span>
        </h2>
        <article class="news-detail">
            <dl class="clearfix">
              <?php foreach ($newsList as $news) echo $news; ?>
            </dl>
        </article>
    </section>

<p class="btn-pageTop"><a href="#"><img src="img/btn-pagetop.png" alt=""></a></p>
</body>
</html>
