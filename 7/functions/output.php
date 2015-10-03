<?php
// ユーザー入力文字列のHTML表示適正化
function h($str) {
  $result = htmlspecialchars($str, ENT_QUOTES);
  $result = str_replace(PHP_EOL, '<br>', $result);
  return $result;
}
// var_dumpの表示改善
function format_var_dump($var) {
  echo '<pre>';
  var_dump($var);
  echo '</pre>';
}
?>
