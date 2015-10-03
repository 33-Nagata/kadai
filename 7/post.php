<?php
require_once('common.php');

if ($id == 0) {
  $_SESSION['message'] = '<p class="message error>ニュースを投稿するにはログインしてください</p>"';
  header('Location: login.php');
  exit;
}

$title = '';
$article = '';
if (isset($_SESSION['title']) && isset($_SESSION['article'])) {
  $title = $_SESSION['title'];
  unset($_SESSION['title']);
  $article = $_SESSION['article'];
  unset($_SESSION['article']);
}

$js = 'https://tinymce.cachefly.net/4.2/tinymce.min.js';
include('view.php');
?>
