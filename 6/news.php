<?php
require_once('functions/controlMySQL.php');
$opt = [
  'method' => 'select',
  'table' => 'news',
  'columns' => [
    'news_title AS title',
    'news_detail AS detail',
    'date_format(create_date, "%Y%.%m%.%d") AS date'
  ],
  'where' => 'news_id='.$_GET['id']
];
$result = controlMySQL($opt);

$dateHtml = '<span class="section-title-ja text-center">'.$result[0]['date'].'</span>';
$titleHtml = '<dd class="news-title">'.$result[0]['title'].'</dd>';
$detailHtml = '<dd>ニュース詳細：'.$result[0]['detail'].'</dd>';

$opt = [
  'method' => 'select',
  'table' => 'news',
  'columns' => ['news_id AS id'],
  'order' => 'create_date',
  'where' => 'show_flg=1'
];
$newsList = controlMySQL($opt);
$prev = '';
$next = '';
for($i=0;$i<count($newsList);$i++){
  if($newsList[$i]['id'] == $_GET['id']){
    if ($i != 0) $prev = '<a class="left" href="news.php?id='.$newsList[$i-1]['id'].'">前の記事</a>';
    if ($i != count($newsList)-1) $next = '<a class="right" href="news.php?id='.$newsList[$i+1]['id'].'">次の記事</a>';
    break;
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
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/news.css">
</head>
<body>
    <?php include('header.php'); ?>

    <section class="news contents-box">
        <h2 class="section-title text-center">
            <span class="section-title__yellow">News</span>
            <?php echo $dateHtml; ?>
        </h2>
        <article class="news-detail">
            <dl class="clearfix">
              <?php
              echo $titleHtml;
              echo $detailHtml;
              ?>
            </dl>
            <?php
            echo $prev;
            echo $next;
            ?>
        </article>
    </section>

<p class="btn-pageTop"><a href="#"><img src="img/btn-pagetop.png" alt=""></a></p>
</body>
</html>
