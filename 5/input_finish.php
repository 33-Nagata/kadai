<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>回答完了</title>
  </head>
  <body>
    <h1>ご回答いただき、ありがとうございます</h1>
    <?php
    $fp = fopen("data/data.csv", "a");
    flock($fp, LOCK_EX);
    $line = [];
    $line[] = $_POST["name"];
    $line[] = $_POST["email"];
    $line[] = $_POST["age"];
    $line[] = $_POST["sex"];
    $line[] = array_key_exists("hobby", $_POST) ? $_POST["hobby"] : "";
    fputcsv($fp, $line);
    flock($fp, LOCK_UN);
    fclose($fp);
    ?>
    <a href="index.php"><button>ホームに戻る</button></a>
  </body>
</html>
