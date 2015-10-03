<?php
/*****
$opt = [
  'method' => 'insert' || 'select' || 'update',
  'tables' => [table_name1, table_name2],
  // 単一行
  'columns' => [
    // SELECT
    column_name1, column_name2
    // INSERT, UPDATE
    column_name1 => value1,
    column_name2 => value2
  ],
  // 複数行(INSERT)
  'columns' => [
    [
      column_name1 => value11,
      column_name2 => value21
    ],
    [
      column_name1 => value12,
      column_name2 => value22
    ]
  ],
  // 複数行(INSERT)
  'columns' => [column_name1, column_name2],
  'values' => [
    [value11, value21],
    [value12, value22],
  ],
  'where' => 'where statement',
  'group' => 'column_name',
  'order' => 'column_name',
  'limit' => NUM,
  'offset' => NUM
];
*****/
function controlMySQL ($opt) {
  require("mysql_config.php");
  // パラメーター確認
  if (!isset($opt['method'])) return false;
  $method = strtolower($opt['method']);
  if (!in_array($method, ['insert', 'select', 'update'])) return false;
  if (!isset($opt['tables']) || !is_array($opt['tables'])) return false;
  $table_sql = implode(', ', $opt['tables']);
  if (!isset($opt['columns'])) return false;
  // WHERE句のないUPDATEを不許可
  if ($method == 'update' && !isset($opt['where'])) return false;

  $raw_columns = $opt['columns'];
  $columns = getColumnNamesFrom($raw_columns);
  $column_sql = implode(', ', $columns);

  $raw_values = isset($opt['values']) ? $opt['values'] : null;
  $value_set = getValueArraysFrom($raw_columns, $raw_values);
  $input_value = []; // 実際にカラムに代入する値 : [place_holder => value]
  if (count($value_set) > 0) {
    // プレースホルダー用意
    $place_holders = [];
    foreach ($value_set as $row_id => $values) {
      $values_in_row = [];
      foreach ($values as $i => $value) {
        $value_sql = $method == 'update' ? $columns[$i].'=' : '';
        if ($value == NULL) {
          $value_sql .= 'NULL';
        } elseif (strtoupper($value) == 'SYSDATE()') {
          $value_sql .= 'SYSDATE()';
        } elseif (is_string($value) && strpos($value, 'GeomFromText(') === 0 && substr($value, strlen($value) - 1) == ')') {
          // (ex):location0
          $place_holder = ':'.$columns[$i].$row_id;
          $value_sql .= "GeomFromText({$place_holder})";
          $start = strlen('GeomFromText("');
          $length = strlen($value) - strlen('GeomFromText("")');
          $input_value[$place_holder] = substr($value, $start, $length);
        } else {
          $place_holder = ':'.$columns[$i].$row_id;
          $value_sql .= $place_holder;
          $input_value[$place_holder] = $value;
        }
        $values_in_row[] = $value_sql;
      }
      $ph_sql = implode(', ', $values_in_row);
      $place_holders[] = $method == 'insert' ? "({$ph_sql})" : $ph_sql;
    }
    // INSERT
    if ($method == 'insert') {
      $values_sql = implode(', ', $place_holders);
    // UPDATE
    } else {
      $set_sql = $place_holders[0];
    }
  }
  // その他オプション
  $where = isset($opt['where']) ? $opt['where'] : false;
  $group = isset($opt['group']) ? $opt['group'] : false;
  $order = isset($opt['order']) ? $opt['order'] : false;
  $limit = isset($opt['limit']) ? $opt['limit'] : false;
  $offset = isset($opt['offset']) ? $opt['offset'] : false;
  // PDO
  $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset='.$charset, $user, $password);
  // SQL文用意
  switch ($method) {
    case 'insert':
      $sql = "INSERT INTO {$table_sql} ({$column_sql}) VALUES {$values_sql}";
      break;
    case 'select':
      $sql = "SELECT {$column_sql} FROM {$table_sql}";
      break;
    case 'update':
      $sql = "UPDATE {$table_sql} SET {$set_sql}";
      break;
  }
  if ($method != 'insert') {
    $sql .= $where ? ' WHERE '.$where : '';
    if ($method == 'select') {
      $sql .= $group ? ' GROUP BY '.$group : '';
      $sql .= $order ? ' ORDER BY '.$order.' DESC' : '';
      $sql .= $limit ? ' LIMIT '.$limit : '';
      $sql .= $offset ? ' OFFSET '.$offset : '';
    }
  }
  // SQL実行
  $stmt = $pdo->prepare($sql);
  bindValue($stmt, $input_value);
  $result = $stmt->execute();
  if ($result) {
    switch ($method) {
      case 'insert':
        $result = $pdo->lastInsertId();
        break;
      case 'select':
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  }
  $pdo = null;
  return $result;
}

function isHash ($array) {
  return array_values($array) !== $array;
}

function getColumnNamesFrom ($columns) {
  if (isHash($columns)) {
    return array_keys($columns);
  } else {
    // 単一行
    if (!is_array($columns[0])) {
      return $columns;
    // 複数行
    } else {
      return array_keys($columns[0]);
    }
  }
}
/*****
return [
  [value11, value21],
  [value12, value22]
];
*****/
function getValueArraysFrom($columns, $values) {
  // VALUESが定義されている場合
  if ($values !== null) {
    return $values;
  // 単一行操作(INSERT, UPDATE)の場合
  } elseif (isHash($columns)) {
    return [array_values($columns)];
  // COLUMNSで複数行追加している場合
  } elseif (is_array($columns[0])) {
    $results = [];
    foreach ($columns as $row) $results[] = array_values($row);
    return $results;
  }
  // SELECTの場合
  return [];
}

function bindValue ($stmt, $trueValue) {
  // $trueValue = [:placeHolder => value]
  global $paramTypeInt;
  foreach ($trueValue as $k => $v) {
    // :placeHolderからテーブル名取得
    $tableName = substr($k, 1);
    // テーブル名がINTのリストにあるか
    $intList = ['_id', '_flg'];
    str_replace($intList, $intList, $tableName, $cnt);
    // リストにあればINT, なければSTR
    if ($cnt > 0 || in_array($tableName, $paramTypeInt)) {
      $stmt->bindValue($k, $v, PDO::PARAM_INT);
    } else {
      $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }
  }
}
?>
