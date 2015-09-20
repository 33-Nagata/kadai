<?php
if (array_key_exists('error', $_GET)) {
  $error_message = '<p class="error message">';
  switch ($_GET['error']) {
    case '0':
      $error_message .= '登録に失敗しました';
      break;
    default:
      $error_message .= '未知のエラーです';
      break;
  }
  $error_message .= '</p>';
}
?>

<!DOCTYPE html>
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

</head>
<body>
  <div class="container">
    <?php echo $error_message; ?>
    <form class="form-signin" action="register_execute.php" method="post">
      <label for="name">名前</label>
      <input name="name" type="text">
      <label for="email">メールアドレス</label>
      <input name="email" type="email">
      <label for="password">パスワード</label>
      <input name="password" type="password">
      <label for="confirm">パスワード(確認用)</label>
      <input name="confirm" type="password">
      <input id="submit" type="submit" value="登録" disabled="disabled">
      <button id="clear">クリア</button>
    </form>
  </div>

  <script>
    function confirm_passwords(){
      var pwd1 = $("input[name=password]").val();
      var pwd2 = $("input[name=confirm]").val();
      if (bool = pwd1 != "" && pwd1 == pwd2) {
        $("#submit").removeAttr("disabled");
      } else {
        $("#submit").attr("disabled", "disabled");
      }
    }
    $(document).ready(function(){
      $("input[name=password]").on("change", function(){
        confirm_passwords();
      });
      $("input[name=confirm]").on("change", function(){
        confirm_passwords();
      });
    });
  </script>
</body>
