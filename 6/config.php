<?php
$sqlConfig = [
  'host' => 'localhost',
  'dbname' => 'cs_academy',
  'charset' => 'utf8',
  'user' => 'root',
  'password' => '',
  // 'method' => 'insert' || 'select' || 'update',
  // 'table' => table_name,
  // 'columns' => [
  //   column_name1 => value1,
  //   column_name2 => value2
  // ]
];
$paramType = [
  'create_date' => PDO::PARAM_STR,
  'update_date' => PDO::PARAM_STR,
  // news
  'news_title' => PDO::PARAM_STR,
  'news_detail' => PDO::PARAM_STR,
  'show_flg' => PDO::PARAM_INT,
  'author' => PDO::PARAM_STR,
  // entry
  'name' => PDO::PARAM_STR,
  'kana' => PDO::PARAM_STR,
  'email' => PDO::PARAM_STR,
  'date' => PDO::PARAM_STR,
  'motiv' => PDO::PARAM_INT,
  'attend' => PDO::PARAM_INT,
];
?>
