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
            <?php
            $options = [
              'columns' => ['news_title', 'news_detail', 'create_date'],
              'where' => 'news_id='.$_GET['id']
            ];
            include('load_news.php');
            ?>
            <span class="section-title-ja text-center"><?php echo substr($result[0]["create_date"], 0, 10); ?></span>
        </h2>
        <article class="news-detail">
            <dl class="clearfix">
                <dd class="news-title"><?php echo $result[0]["news_title"]; ?></dd>
                <dd>ニュース詳細：<?php echo $result[0]["news_detail"]; ?></dd>
            </dl>
            <?php
            if($_GET['id'] > 1){
              echo '<a class="left" href="news.php?id='.($_GET['id']-1).'">前の記事</a>';
            }
            echo '<a class="right" href="news.php?id='.($_GET['id']+1).'">次の記事</a>';
            ?>
        </article>
    </section>

<p class="btn-pageTop"><a href="#"><img src="img/btn-pagetop.png" alt=""></a></p>
</body>
</html>
