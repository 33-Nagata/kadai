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
          // MySQL table format
          // table name: entry
          // id          int(11)      AUTO_INCREMENT PRIMARY_KEY
          // name        varchar(12)
          // kana        varchar(12)
          // email       varchar(255)
          // date        datetime
          // motiv       int(1)
          // attend      tinyint(1)
          // create_date datetime
          // update_date datetime
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
          $message = "<html>";
          $message .= "<head>";
          $message .= "<title>説明会申込み確認</title>";
          $message .= "</head>";
          $message .= "<body>";
          $message .= "<p>下記の内容で説明会のお申し込みを受け付けました。</p>";
          $message .= "<table>";
          $message .= "<tr><th>氏名</th><td>".$name."</td></tr>";
          $message .= "<tr><th>フリガナ</th><td>".$kana."</td></tr>";
          $message .= "<tr><th>希望日時</th><td>".$date."</td></tr>";
          $message .= "</table>";
          $message .= "<p>";
          $message .= '<a href="'.$url['path'].'?'.$url['query'].'">';
          $message .= '内容に誤りがなければこちらから手続きを完了してください。';
          $message .= '</a>';
          $message .= "</p>";
          $headers = "MIME-Version: 1.0\r\n";
          $headers .= "Content-type: text/html; charset=utf8\r\n";
          $headers .= "From: shinsuke.ngt@gmail.com";
          if (mail($email, '説明会申込み確認', $message, $headers)):
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
                  include('motivation.php');
                  echo $motivations[$motiv];
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
