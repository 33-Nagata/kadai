<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <title>確認</title>
</head>

<body>
  <h1>この内容でよろしいでしょうか？</h1>
  <p>
    名前：<?php echo $_POST[ "name"]; ?>
    <br>
    Eメール：<?php echo $_POST[ "email"]; ?>
    <br>
    年齢：<?php echo $_POST[ "age"]; ?>
    <br>
    性別：
    <?php
    $sex = ["男性", "女性", "未回答"];
    echo $sex[$_POST["sex"]];
    ?>
    <br>
    趣味：
    <?php
    $hobby = ["スポーツ", "読書", "パソコン／インターネット", "旅行", "音楽鑑賞", "料理", "ショッピング", "なし"];
    if(array_key_exists("hobby", $_POST)){
      foreach($_POST["hobby"] as $value){
        echo $hobby[$value]." ";
      }
    } else {
      echo "なし";
    }
    ?>
    <br>
    <a href="input_finish.php"><button>送信</button></a>
    <a href="input_enq.php"><button>戻る</button></a>



  </p>
</body>

</html>
