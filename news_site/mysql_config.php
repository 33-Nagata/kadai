<?php
$host = 'localhost';
$dbname = 'news_site';
$charset = 'utf8';
$user = 'root';
$password = '';
$limit = 5;

$paramType = [
  'create_date' => PDO::PARAM_STR,
  'update_date' => PDO::PARAM_STR,
  // user
  'name' => PDO::PARAM_STR,
  'email' => PDO::PARAM_STR,
  'password' => PDO::PARAM_STR,
  'photo' => PDO::PARAM_STR,
  'vector' => PDO::PARAM_STR,
  //news
  'title' => PDO::PARAM_STR,
  'detail' => PDO::PARAM_STR,
  'show_flg' => PDO::PARAM_INT,
  'user_id' => PDO::PARAM_STR,
];
?>
