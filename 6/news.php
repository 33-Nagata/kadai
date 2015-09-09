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

        </article>
    </section>

<p class="btn-pageTop"><a href="#"><img src="img/btn-pagetop.png" alt=""></a></p>
</body>
</html>
