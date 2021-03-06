<?php
require_once('../config.php');

function controlMySQL ($opt) {
  global $sqlConfig;
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
      // SELECT
      column_name1, column_name2
      // INSERT, UPDATE
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
  switch ($method) {
    case 'select':
      $column = $opt['columns'];
      break;

    case 'insert':
    case 'update':
      $column = [];
      $value = [];
      $trueValue = [];
      foreach ($opt['columns'] as $columnName => $columnValue) {
        $column[] = $columnName;
        if ($columnValue == NULL) {
          $value[] = 'NULL';
        } elseif (is_string($columnValue) && strtoupper($columnValue) == 'SYSDATE()') {
          $value[] = 'SYSDATE()';
        } else {
          $placeHolder = ':'.$columnName;
          $value[] = $placeHolder;
          $trueValue[$placeHolder] = $columnValue;
        }
      }
      break;
  }
  $where = isset($opt['where']) ? $opt['where'] : false;
  $order = isset($opt['order']) ? $opt['order'] : false;
  $limit = isset($opt['limit']) ? $opt['limit'] : false;
  $offset = isset($opt['offset']) ? $opt['offset'] : false;

  $pdo = new PDO('mysql:host='.$host.';dbname='.$db.';charset='.$char, $usr, $pwd);
  switch ($method) {
    case 'insert':
      $sql = 'INSERT';
      $sql .= ' INTO '.$table;
      $sql .= ' ('.implode(', ', $column).')';
      $sql .= ' VALUES ('.implode(', ', $value).')';
      $stmt = $pdo->prepare($sql);
      foreach ($trueValue as $k => $v) {
        $tableName = substr($k, 1, strlen($k) - 1);
        $stmt->bindValue($k, $v, $paramType[$tableName]);
      }
      return $stmt->execute();
      break;

    case 'select':
      $sql = 'SELECT';
      $sql .= ' '.implode(', ', $column);
      $sql .= ' FROM '.$table;
      $sql .= $where ? ' WHERE '.$where : '';
      $sql .= $order ? ' ORDER BY '.$order.' DESC' : '';
      $sql .= $limit ? ' LIMIT '.$limit : '';
      $sql .= $offset ? ' OFFSET '.$offset : '';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
      break;

    case 'update':
      if (!$where) {
        return false;
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
        return $stmt->execute();
      }
      break;
  }
  $pdo = null;
}
?>
