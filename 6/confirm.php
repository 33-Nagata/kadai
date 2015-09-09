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
    <?php
    $name = $_POST['name'];
    $kana = $_POST['kana'];
    $email = $_POST['email'];
    $date = $_POST['date'];
    $motiv = array_key_exists('motivation', $_POST) ? $_POST['motivation'] : '0';
    ?>
    <?php include('header.php'); ?>

    <section class="contents-box">
        <div class="contents-heading bg-yellow">
            <h2 class="section-title text-center">
                <span class="section-title__white">Confirmation</span>
            </h2>
        </div>
        <article class="news-detail">
          <?php
          $pdo = new PDO('mysql:host=localhost;dbname=cs_academy;charset=utf8', 'root', '');
          $sql = "INSERT INTO entry (name, kana, email, date, motiv, attend, create_date, update_date) VALUES (:name, :kana, :email, :date, :motiv, :attend, NOW(), NOW())";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':name', $name, PDO::PARAM_STR);
          $stmt->bindParam(':kana', $kana, PDO::PARAM_STR);
          $stmt->bindParam(':email', $email, PDO::PARAM_STR);
          $stmt->bindParam(':date', str_replace('/', '-', $date), PDO::PARAM_STR);
          $stmt->bindParam(':motiv', $motiv, PDO::PARAM_INT);
          $stmt->bindValue(':attend', false, PDO::PARAM_BOOL);
          $stmt->execute();
          $id = $pdo->lastInsertId();
          $pdo = null;

          $url = parse_url("localhost/kadai/6/complete.php?id=".$id."&email=".$email);
          $message = "下記の内容で説明会のお申し込みを受け付けました。\r\n\r\n";
          $message .= "氏名：".$name."\r\n";
          $message .= "フリガナ".$kana."\r\n";
          $message .= "希望日時".$date."\r\n\r\n";
          $message .= "内容に誤りがなければ下のURLから手続きを完了してください。\r\n";
          $message .= $url['path']."?".$url['query']."\r\n";
          if (mail($email, '説明会申込み確認', $message, "From: shinsuke.ngt@gmail.com")):
          ?>
            <p>下記の内容で確認メールを送信しました。</p>
            <table>
              <tr>
                <th>氏名</th>
                <td>:<?php echo $name; ?></td>
              </tr>
              <tr>
                <th>フリガナ</th>
                <td>:<?php echo $kana; ?></td>
              </tr>
              <tr>
                <th>メールアドレス</th>
                <td>:<?php echo $email; ?></td>
              </tr>
              <tr>
                <th>希望日時</th>
                <td>:<?php echo $date; ?></td>
              </tr>
              <tr>
                <th>志望動機</th>
                <td>:<?php
                  switch ($motiv) {
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
                    default:
                      echo '選択されていません';
                  }
                ?></td>
              </tr>
            </table>
          <?php else:
            echo '確認メールの送信に失敗しました';
          endif; ?>
        </article>
    </section>

<p class="btn-pageTop"><a href="#"><img src="img/btn-pagetop.png" alt=""></a></p>
</body>
</html>
