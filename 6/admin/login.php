<?php
session_start();

$message = $_SESSION != '' ? '<p>'.$_SESSION['message'].'</p>' : '';
$_SESSION['message'] = '';

$_SESSION['login'] = false;
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
    ログイン名: <input type="text" name="name" value="" />
  	パスワード: <input type="password" name="password" value="" />
    <br>
    <input type="submit" value="ログイン">
  </form>
</body>
</html>
