<?php
require_once('common.php');

//includeで呼び出されているか確認
if (!isset($news_id)) {
  $_SESSION['message'] = '<p class="alert alert-danger">不正なアクセスです</p>';
  header('Location: login.php');
  exit;
}
// TF*IDF
foreach ($tf as $word_id => $value) $tf_idf[$word_id] = $value * $idf[$word_id];
?>
