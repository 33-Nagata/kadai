<?php
require('common.php');
require_once('functions/control_MySQL.php');
require('login_required.php');

//includeで呼び出されているか確認
if (!isset($news_id)) {
  $_SESSION['message'] = '<p class="message error">不正なアクセスです</p>';
  header('Location: login.php');
  exit;
}

foreach ($tf as $word_id => $value) $tf_idf[$word_id] = $value * $idf[$word_id];
?>
