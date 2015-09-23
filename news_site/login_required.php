<?php
if ($id == 0) {
  $_SESSION['message'] = 'ログインしてください';
  header('Location: login.php');
  exit;
}
?>
