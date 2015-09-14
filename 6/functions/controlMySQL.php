<?php
$host = $sqlConfig['host'];
$db   = $sqlConfig['dbname'];
$char = $sqlConfig['charset'];
$usr  = $sqlConfig['user'];
$pwd  = $sqlConfig['password'];
/*
$opt = [
  'method' => 'insert' || 'select' || 'update',
  'table' => table_name,
  'columns' => [
    column_name1 => value1,
    column_name2 => value2
  ],
  'where' => 'where statement',
  'order' => 'column_name',
  'limit' => NUM
];
*/
$method = $opt['method'];
$table = $opt['table'];
$column = [];
$value = [];
$trueValue = [];
foreach ($opt['columns'] as $col) {
  if (is_array($col)) {
    foreach ($col as $columnName => $columnValue) {
      $column[] = $columnName;
      if ($columnValue != NULL) {
        $placeHolder = ':'.$columnName;
        $value[] = $placeHolder;
        $trueValue[$placeHolder] = $columnValue;
      } else {
        $value[] = 'NULL';
      }
    }
  } else {
    $column[] = $col;
  }
}
$where = isset($opt['where']) ? $opt['where'] : false;
$order = isset($opt['order']) ? $opt['order'] : false;
$limit = isset($opt['limit']) ? $opt['limit'] : false;

$pdo = new PDO('mysql:host='.$host.';dbname='.$db.';charset='.$char, $usr, $pwd);
switch ($method) {
  case 'insert':
    $sql = 'INSERT';
    $sql .= ' INTO '.$table;
    $sql .= ' ('.implode(', ', $column).')';
    $sql .= ' VALUES ('.implode(', ', $value).')';
    echo $sql;
    $stmt = $pdo->prepare($sql);
    foreach ($trueValue as $k => $v) {
      $tableName = substr($k, 1, strlen($k) - 1);
      $stmt->bindValue($k, $v, $paramType[$tableName]);
    }
    $result = $stmt->execute();
    break;
  case 'select':
    $sql = 'SELECT';
    $sql .= ' '.implode(', ', $column);
    $sql .= ' FROM '.$table;
    $sql .= $where ? ' WHERE '.$where : '';
    $sql .= $order ? ' ORDER BY '.$order.' DESC' : '';
    $sql .= $limit ? 'LIMIT '.$limit : '';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    break;
  case 'update':
    if (!$where) {
      $result = 'whereを指定してください';
    } else {
      $sql = 'UPDATE';
      $sql .= ' '.$table;
      $set = [];
      for ($i=0; $i < count($column); $i++) {
        $set[] = $column[$i].'='.$value[$i];
      }
      $sql .= ' SET '.implode(', ', $set);
      $sql .= ' WHERE '.$where;
      $stmt = $pdo->prepare($sql);
      foreach ($trueValue as $k => $v) {
        $tableName = substr($k, 1, strlen($k) - 1);
        $stmt->bindValue($k, $v, $paramType[$tableName]);
      }
      $result = $stmt->execute() ? '更新しました' : '更新に失敗しました';
    }
    break;
}
$pdo = null;
?>
