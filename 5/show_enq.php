<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>集計結果</title>

    <link rel="stylesheet" href="css/enq.css">
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
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

    <div class="chart" id="piechart_3d"></div>
    <div class="chart" id="barchart"></div>
    <a href="index.php"><button>ホームに戻る</button></a>

    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var sex_data = google.visualization.arrayToDataTable([
          ['性別', '割合'],
          [<?php echo "'".$sex[0]."', ".$sex_count[0]; ?>],
          [<?php echo "'".$sex[1]."', ".$sex_count[1]; ?>],
          [<?php echo "'".$sex[2]."', ".$sex_count[2]; ?>]
        ]);

        var sex_options = {
          title: '回答者の性別',
          is3D: true,
        };

        var pie_chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        pie_chart.draw(sex_data, sex_options);

        var color = ['#000000', '#000077', '#007700', '#007777', '#770000', '#770077', '#777700'];
        var hobby_data = google.visualization.arrayToDataTable([
          ['趣味', '人数', {role: 'style'}],
          [<?php echo "'".$hobby[0]."', ".$hobby_count[0]; ?>, color[0]],
          [<?php echo "'".$hobby[1]."', ".$hobby_count[1]; ?>, color[1]],
          [<?php echo "'".$hobby[2]."', ".$hobby_count[2]; ?>, color[2]],
          [<?php echo "'".$hobby[3]."', ".$hobby_count[3]; ?>, color[3]],
          [<?php echo "'".$hobby[4]."', ".$hobby_count[4]; ?>, color[4]],
          [<?php echo "'".$hobby[5]."', ".$hobby_count[5]; ?>, color[5]],
          [<?php echo "'".$hobby[6]."', ".$hobby_count[6]; ?>, color[6]]
        ]);

        var hobby_options = {
          title: '回答者の趣味'
        };

        var bar_chart = new google.visualization.BarChart(document.getElementById('barchart'));
        bar_chart.draw(hobby_data, hobby_options);
      }
    </script>
  </body>
</html>
