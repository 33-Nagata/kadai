<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <title>アンケート回答</title>

  <script src="js/jquery-2.1.4.min.js"></script>
</head>

<body>
  <h1>アンケートにご回答ください</h1>
  <form action="confirm_enq.php" method="post">
    <label for="name">名前：</label>
    <?php
    $name_tag = '<input name="name" type="text"';
    if(array_key_exists("name", $_POST)){
      $name_tag .= ' value="'.$_POST["name"].'"';
    }
    $name_tag .= '>';
    echo $name_tag;
    ?>
    <br>
    <label for="email">Eメール：</label>
    <?php
    $email_tag = '<input name="email" type="email"';
    if(array_key_exists("email", $_POST)){
      $email_tag .= ' value="'.$_POST["email"].'"';
    }
    $email_tag .= '>';
    echo $email_tag;
    ?>
    <br>
    <label for="age">年齢：</label>
    <?php
    $age_tag = '<input name="age" type="number" min="0"';
    if(array_key_exists("age", $_POST)){
      $age_tag .= ' value="'.$_POST["age"].'"';
    }
    $age_tag .= '>';
    echo $age_tag;
    ?>
    <br>
    <label for="sex">性別：</label>
    <?php
    $sexes = ["男性", "女性", "未回答"];
    for($i=0;$i<3;$i++){
      $sex_tag = '<input name="sex" type="radio" value="'.$i.'"';
      if(array_key_exists("sex", $_POST) && $_POST["sex"] == strval($i)){
        $sex_tag .= ' checked="checked"';
      } else if(!array_key_exists("sex", $_POST) && $i == 2){
        $sex_tag .= ' checked="checked"';
      }
      $sex_tag .= '>'.$sexes[$i];
      echo $sex_tag;
    }
    ?>
    <br>
    <label for="hobby">趣味：</label>
    <?php
    $hobbies = ["スポーツ", "読書", "パソコン／インターネット", "旅行", "音楽鑑賞", "料理", "ショッピング"];
    for($i=0;$i<7;$i++){
      $hobby_tag = '<input type="checkbox" name="hobby[]" value="'.$i.'"';
      if(array_key_exists("hobby", $_POST) && strpos($_POST["hobby"], strval($i)) !== false){
        $hobby_tag .= 'checked="checked"';
      }
      $hobby_tag .= '>'.$hobbies[$i];
      echo $hobby_tag;
    }
    ?>
    <br>
    <?php
    $submit_tag = '<input type="submit" value="回答する"';
    if(!array_key_exists("name", $_POST)){
      $submit_tag .= ' disabled';
    }
    $submit_tag .= '>';
    echo $submit_tag;
    ?>
  </form>

  <script>
  $(document).ready(function() {
    $("input").on("change", function(){
      if ($("input[name=name]").val() != "" && $("input[name=email]").val() != "" && $("input[name=age]").val() != "") {
        $("input[type=sumit]").prop("disabled", false);
      } else {
        $("input[type=submit]").prop("disabled", true);
      }
    });
  }
  );
  </script>
</body>

</html>
