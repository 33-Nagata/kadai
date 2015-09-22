<?php
$host = 'localhost';
$dbname = 'news_site';
$charset = 'utf8';
$user = 'root';
$password = '';
$limit = 5;

$paramType = [
  'photo' => PDO::PARAM_STR,
  'vector' => PDO::PARAM_STR,
  'create_date' => PDO::PARAM_STR,
  'update_date' => PDO::PARAM_STR,
  // user
  'name' => PDO::PARAM_STR,
  'email' => PDO::PARAM_STR,
  'password' => PDO::PARAM_STR,
  //news
  'title' => PDO::PARAM_STR,
  'author_id' => PDO::PARAM_STR,
  'article' => PDO::PARAM_STR,
  'location' => PDO::PARAM_STR,
  'show_flg' => PDO::PARAM_INT,
  //dictionary
  'word' => PDO::PARAM_STR,
  //news_word_frequency
  'news_id' => PDO::PARAM_INT,
  'word_id' => PDO::PARAM_INT,
  'frequency' => PDO::PARAM_INT,
];
?>
