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
  return htmlspecialchars($str, ENT_QUOTES);
}
?>
