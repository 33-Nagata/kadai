<?php
require('common.php');
require_once('functions/control_MySQL.php');
if ($id == 0) {
  $_SESSION['message'] = '<p class="message error">ログインしてください</p>';
  header('Location: login.php');
  exit;
}
$request_id = isset($_GET['id']) ? $_GET['id'] : $id;
if ($id != $request_id) {
  $_SESSION['message'] = '<p class="message error">編集したいユーザーアカウントでログインしてください</p>';
  header('Location: user.php?id={$request_id}');
  exit;
}
$opt = [
  'method' => 'select',
  'tables' => ['user'],
  'columns' => ['name', 'email', 'photo'],
  'where' => "id='{$request_id}'"
];
$result = controlMySQL($opt);
if (!$result) {
  $_SESSION['message'] = '<p class="message error">ユーザーが存在しません</p>';
  header('Location: login.php');
  exit;
}
$name = $result[0]['name'];
$email = $result[0]['email'];
$photo_src = "http://127.0.0.1/kadai/news_site/get_img.php?table=user&id={$request_id}";
?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
  <?php echo $message; ?>
  <form action="update_user_execute.php" method="post" enctype="multipart/form-data">
    <label for="name">名前</label>
    <input name="name" type="text" value="<?php echo $name; ?>" required>
    <label for="email">メールアドレス</label>
    <input name="email" type="email" value="<?php echo $email; ?>" required>
    <label for="password">パスワード</label>
    <input name="password" type="password">
    <label for="confirm">パスワード(確認用)</label>
    <input name="confirm" type="password">
    <label for="photo">プロフィール画像</label>
    <img src="<?php echo $photo_src; ?>" />
    <input name="photo" type="file">
    <input id="submit" type="submit" value="変更">
    <button id="clear">クリア</button>
  </form>
  <a href="user.php?id=<?php echo $request_id; ?>">ユーザー情報へ戻る</a>

  <script>
    function confirm_passwords(){
      var pwd1 = $("input[name=password]").val();
      var pwd2 = $("input[name=confirm]").val();
      if (pwd1 == pwd2) {
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
</html>
