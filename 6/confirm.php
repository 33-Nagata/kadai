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

    <section class="contents-box">
        <h2 class="section-title text-center">
            <span class="section-title__white">Confirmation</span>
        </h2>
        <article class="news-detail">
            <table>
                <tr>
                    <th>氏名</th>
                    <td>:<?php echo $_POST['name']; ?></td>
                </tr>
                <tr>
                    <th>フリガナ</th>
                    <td>:<?php echo $_POST['kana']; ?></td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td>:<?php echo $_POST['email']; ?></td>
                </tr>
                <tr>
                    <th>希望日時</th>
                    <td>:<?php echo $_POST['date']; ?></td>
                </tr>
                <tr>
                    <th>志望動機</th>
                    <td>:<?php
                    if (array_key_exists('motivation', $_POST)) {
                      switch ($_POST['motivation'][0]) {
                        case '1':
                          echo '起業をしたい';
                          break;
                        case '2':
                          echo 'チーズ企業に就職したい。';
                          break;
                        case '3':
                          echo 'チーズと関わる仕事なので、知識をつけたい。';
                          break;
                        case '4':
                          echo '教養として身につけたい';
                          break;
                      }
                    } else {
                      echo '選択されていません';
                    }
                    ?></td>
                </tr>
            </table>
        </article>
    </section>

<p class="btn-pageTop"><a href="#"><img src="img/btn-pagetop.png" alt=""></a></p>
</body>
</html>
