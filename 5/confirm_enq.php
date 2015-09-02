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
    $hobby = ["スポーツ", "読書", "パソコン／インターネット", "旅行", "音楽鑑賞", "料理", "ショッピング"];
    if(array_key_exists("hobby", $_POST)){
      $output = "";
      foreach($_POST["hobby"] as $value){
        $output .= $hobby[$value].", ";
      }
      $output = substr($output, 0, -2);
      echo $output;
    } else {
      echo "なし";
    }
    ?>
    <br>
    <form action="input_finish.php" method="POST">
      <?php
      echo '<input type="hidden" name="name" value='.$_POST["name"].'>';
      echo '<input type="hidden" name="email" value='.$_POST["email"].'>';
      echo '<input type="hidden" name="age" value='.$_POST["age"].'>';
      echo '<input type="hidden" name="sex" value='.$_POST["sex"].'>';
      if(array_key_exists("hobby", $_POST)){
        echo '<input type="hidden" name="hobby" value="'.implode(" ", $_POST["hobby"]).'">';
      }
      ?>
      <a href="input_finish.php"><button>送信</button></a>
    </form>
    <form action="input_enq.php" method="POST">
      <?php
      echo '<input type="hidden" name="name" value='.$_POST["name"].'>';
      echo '<input type="hidden" name="email" value='.$_POST["email"].'>';
      echo '<input type="hidden" name="age" value='.$_POST["age"].'>';
      echo '<input type="hidden" name="sex" value='.$_POST["sex"].'>';
      if(array_key_exists("hobby", $_POST)){
        echo '<input type="hidden" name="hobby" value="'.implode(" ", $_POST["hobby"]).'">';
      }
      ?>
      <a href="input_enq.php"><button>戻る</button></a>
    </form>



  </p>
</body>

</html>
