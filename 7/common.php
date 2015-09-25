<?php
session_start();

if (isset($_SESSION['id']) && intval($_SESSION['id'])) {
  $id = $_SESSION['id'];
} else {
  $id = 0;
}
$message = '';
if (array_key_exists('message', $_SESSION)) {
  $message = $_SESSION['message'];
  $_SESSION['message'] = '';
}

function h($str) {
  $result = htmlspecialchars($str, ENT_QUOTES);
  $result = str_replace(PHP_EOL, '<br>', $result);
  return $result;
}

function format_var_dump($var) {
  echo '<pre>';
  var_dump($var);
  echo '</pre>';
}
?>
