<?php
session_start();
session_destroy();

if (isset($_SESSION['message'])) {
  $message = $_SESSION['message'];
  unset($_SESSION['message']);
} else {
  $message = "";
}
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
</head>
<body>
  <?php echo $message; ?>
  <form action="login_execute.php" method="post">
    メールアドレス: <input type="email" name="email" value="" />
  	パスワード: <input type="password" name="password" value="" />
    <br>
    <input type="submit" value="ログイン">
  </form>
</body>
</html>
