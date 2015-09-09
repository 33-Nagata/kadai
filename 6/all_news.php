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
            <?php
            $options = [
              'columns' => ['news_id', 'news_title', 'create_date'],
              'order' => 'create_date'
            ];
            include('load_news.php');
            ?>
            <dl class="clearfix">
                <?php foreach($result as $news): ?>
                    <dt class="news-date"><?php echo $news["create_date"]; ?></dt>
                    <?php
                    $url = 'news.php?id='.$news["news_id"];
                    $title = mb_substr($news["news_title"], 0, 10);
                    ?>
                    <dd class="news-description"><?php echo '<a href="'.$url.'">'.$title.'</a>'; ?></dd>
                <?php endforeach ?>
            </dl>
        </article>
    </section>

<p class="btn-pageTop"><a href="#"><img src="img/btn-pagetop.png" alt=""></a></p>
</body>
</html>