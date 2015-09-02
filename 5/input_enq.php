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
    <input name="name" type="text">
    <br>
    <label for="email">Eメール：</label>
    <input name="email" type="email">
    <br>
    <label for="age">年齢：</label>
    <input name="age" type="number" min="0">
    <br>
    <label for="sex">性別：</label>
    <input name="sex" type="radio" value="0">男性
    <input name="sex" type="radio" value="1">女性
    <input name="sex" type="radio" value="2" checked="checked">未回答
    <br>
    <label for="hobby">趣味：</label>
    <input type="checkbox" name="hobby[]" value="0">スポーツ
    <input type="checkbox" name="hobby[]" value="1">読書
    <input type="checkbox" name="hobby[]" value="2">パソコン／インターネット
    <input type="checkbox" name="hobby[]" value="3">旅行
    <input type="checkbox" name="hobby[]" value="4">音楽鑑賞
    <input type="checkbox" name="hobby[]" value="5">料理
    <input type="checkbox" name="hobby[]" value="6">ショッピング
    <br>
    <input type="submit" value="回答する" disabled>
  </form>

  <script>
  $(document).ready(function() {
    $("input").on("change", function(){
      if ($("input[name=name]").val() != "" && $("input[name=email]").val() != "" && $("input[name=age]").val() != "") {
        $("input[type=submit]").prop("disabled", false);
      } else {
        $("input[type=submit]").prop("disabled", true);
      }
    });
  }
  );
  </script>
</body>

</html>
