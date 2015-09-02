<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>集計結果</title>
  </head>
  <body>
    <h1>アンケート結果</h1>
    <?php
    if (file_exists("data/data.csv")) {
      $fp = fopen("data/data.csv", "r");
    } else {
      echo "まだデータがありません";
      $fp = false;
    }
    if ($fp):
      $contents = ["名前", "Eメール", "年齢", "性別", "趣味"];
      $sex = ["男性", "女性", "未回答"];
      $hobby = ["スポーツ", "読書", "パソコン／インターネット", "旅行", "音楽鑑賞", "料理", "ショッピング"];
      flock($fp, LOCK_SH);
    ?>
      <table>
        <thead>
          <tr>
            <th><?php echo implode("</th><th>", $contents) ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $total = 0;
          $sex_count = [];
          for ($i=0; $i < count($sex); $i++) {
            $sex_count[] = 0;
          }
          $hobby_count = [];
          for ($i=0; $i < count($hobby); $i++) {
            $hobby_count[] = 0;
          }
          while($array = fgetcsv($fp)):
            $total++;
          ?>
          <tr>
            <?php
            for ($i=0; $i < count($array); $i++) {
              if ($contents[$i] == "性別") {
                $sex_count[$array[$i]]++;
                for ($j=0; $j < count($sex); $j++) {
                  $array[$i] = str_replace($j, $sex[$j], $array[$i]);
                }
              }
              if ($contents[$i] == "趣味") {
                if ($array[$i] == "") {
                  $array[$i] = "なし";
                } else {
                  $hobbies = explode(" ", $array[$i]);
                  foreach ($hobbies as $value) {
                    $hobby_count[$value]++;
                  }
                  for ($j=0; $j < count($hobby); $j++) {
                    $array[$i] = str_replace($j, $hobby[$j], $array[$i]);
                  }
                  $array[$i] = str_replace(" ", ", ", $array[$i]);
                }
              }
            }
            ?>
            <td><?php echo implode("</td><td>", $array); ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <?php
        flock($fp, LOCK_UN);
        fclose($fp);
    endif;
    ?>

    <?php
    for ($i=0; $i < count($sex) - 1; $i++) {
      echo $sex[$i]."：".($sex_count[$i] / $total * 100)."%<br>";
    }
    for ($i=0; $i < count($hobby) - 1; $i++) {
      echo $hobby[$i]."：".($hobby_count[$i] / $total * 100)."%<br>";
    }
    ?>



    <a href="index.php"><button>ホームに戻る</button></a>
  </body>
</html>
