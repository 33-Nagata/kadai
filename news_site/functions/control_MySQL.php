<?php
function controlMySQL ($opt) {
  require("mysql_config.php");
  /*
  $opt = [
    'method' => 'insert' || 'select' || 'update',
    'tables' => [table_name1, table_name2],
    'columns' => [
      // SELECT
      column_name1, column_name2
      // INSERT, UPDATE
      column_name1 => value1,
      column_name2 => value2
    ],
    'where' => 'where statement',
    'group' => 'column_name',
    'order' => 'column_name',
    'limit' => NUM,
    'offset' => NUM
  ];
  */
  $method = $opt['method'];
  $table = is_array($opt['tables']) ? implode(', ', $opt['tables']) : $opt['tables'];
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
        } elseif (strpos($columnValue, 'GeomFromText') !== false) {
          $placeHolder = ':'.$columnName;
          $value[] = 'GeomFromText('.$placeHolder.')';
          $start = strlen('GeomFromText("');
          $length = strlen($columnValue) - strlen('GeomFromText("")');
          $trueValue[$placeHolder] = substr($columnValue, $start, $length);
        } else {
          $placeHolder = ':'.$columnName;
          $value[] = $placeHolder;
          $trueValue[$placeHolder] = $columnValue;
        }
      }
      break;
  }
  $where = isset($opt['where']) ? $opt['where'] : false;
  $group = isset($opt['group']) ? $opt['group'] : false;
  $order = isset($opt['order']) ? $opt['order'] : false;
  $limit = isset($opt['limit']) ? $opt['limit'] : false;
  $offset = isset($opt['offset']) ? $opt['offset'] : false;

  $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset='.$charset, $user, $password);
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
      $last_id = false;
      if ($stmt->execute()) $last_id = $pdo->lastInsertId();
      $pdo = NULL;
      return $last_id;
      break;

    case 'select':
      $sql = 'SELECT';
      $sql .= ' '.implode(', ', $column);
      $sql .= ' FROM '.$table;
      $sql .= $where ? ' WHERE '.$where : '';
      $sql .= $group ? ' GROUP BY '.$group : '';
      $sql .= $order ? ' ORDER BY '.$order.' DESC' : '';
      $sql .= $limit ? ' LIMIT '.$limit : '';
      $sql .= $offset ? ' OFFSET '.$offset : '';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $pdo = NULL;
      return $res;
      break;

    case 'update':
      if (!$where) {
        $pdo = NULL;
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
        $res = $stmt->execute();
        $pdo = NULL;
        return $res;
      }
      break;
  }
  $pdo = null;
}

function insertMultiColumns($opt) {
  require("mysql_config.php");
  /*
  $opt = [
    'table' => table_name,
    'columns' => [column_name1, column_name2],
    'values' => [
      [value1, value2],
      [value3, value4]
    ]
  ];
  */
  $table = $opt['table'];
  $columns = $opt['columns'];
  $values = $opt['values'];
  $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset='.$charset, $user, $password);
  $sql = "INSERT INTO {$table} (".implode(', ', $columns).") VALUES ";
  $set = [];
  foreach ($values as $index => $value) {
    // (:'column1'1, :'column2'1)
    // (:'column1'2, :'column2'2)
    $set[] = '(:'.implode("{$index}, :", $columns).$index.')';
  }
  $sql .= implode(', ', $set);
  $stmt = $pdo->prepare($sql);
  foreach ($values as $i => $value) {
    foreach ($columns as $j => $column) {
      $placeHolder = ':'.$column.$i;
      $stmt->bindValue($placeHolder, $value[$j], $paramType[$column]);
    }
  }
  $last_id = false;
  if ($stmt->execute()) $last_id = $pdo->lastInsertId();
  $pdo = NULL;
  return $last_id;
}
?>
