<?php
$pdo = new PDO('mysql:host=localhost;dbname=cs_academy;charset=utf8', 'root', '');
$sql = "select ";
foreach ($options['columns'] as $col) {
  $sql .= $col.', ';
}
$sql = substr($sql, 0, -2).' from news';
if (array_key_exists('where', $options)) {
  $sql .= ' WHERE '.$options['where'];
}
if (array_key_exists('order', $options)) {
  $sql .= ' ORDER BY '.$options['order'].' DESC';
}
if (array_key_exists('limit', $options)) {
  $sql .= ' LIMIT '.$options['limit'];
}
$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pdo = null;
?>