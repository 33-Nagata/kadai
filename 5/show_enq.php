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
          <?php while($array = fgetcsv($fp)): ?>
          <tr>
            <?php
            for ($i=0; $i < count($array); $i++) {
              if ($contents[$i] == "性別") {
                for ($j=0; $j < count($sex); $j++) {
                  $array[$i] = str_replace($j, $sex[$j], $array[$i]);
                }
              }
              if ($contents[$i] == "趣味") {
                if ($array[$i] == "") {
                  $array[$i] = "なし";
                } else {
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
    <a href="index.php"><button>ホームに戻る</button></a>
  </body>
</html>
