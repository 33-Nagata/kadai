<?php
require_once('functions/control_MySQL.php');

$id = $_GET['id'];
$table = $_GET['table'];
$opt = [
  'method' => 'select',
  'tables' => [$table],
  'columns' => ['photo'],
  'where' => "id='{$id}'"
];
$data = controlMySQL($opt);
if (count($data) == 0) {
  echo "データが存在しません";;
} else {
  // header("Content-Type: image/jpeg");
  echo $data[0]['photo'];
}
?>
