<?php
function insertNews ($columns) {
  $opt = $columns;
  $opt['method'] = 'insert';
  $opt['create_date'] = 'SYSDATE()';
  return saveNews($opt);
}

function updateNews ($columns) {
  $opt = $columns;
  $opt['method'] = 'update';
  return saveNews($opt);
}

function saveNews ($option) {
  require_once('functions/control_MySQL.php');

  $opt = $option;
  $opt['tables'] = ['news'];
  $opt['columns']['update_date'] = 'SYSDATE()';
  return controlMySQL($opt);
}

function getNounCounts ($raw_sentence) {
  include('yahoo_japan_application_id.php');
  $sentence = mb_convert_encoding($raw_sentence, 'utf-8', 'auto');
  $params = [
    'sentence' => $sentence,
    'results' => 'uniq',
    'response' => 'surface',
    'filter' => '9'
  ];
  $ch = curl_init('http://jlp.yahooapis.jp/MAService/V1/parse');
  curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT => "Yahoo AppID: {$yahoo_japan_application_id}",
    CURLOPT_POSTFIELDS => http_build_query($params),
  ]);
  $xml = new SimpleXMLElement(curl_exec($ch));
  curl_close($ch);
  $num_str = ['.', ','];
  // 数字のみのものを除外
  for ($i=0; $i < 10; $i++) {
    $num_str[] = (string)$i;
  }
  foreach ($xml->uniq_result->word_list->word as $data) {
    $noun = mb_strtolower((string)$data->surface);
    $count = (int)$data->count;
    $is_str = false;
    for ($i=0; $i < mb_strlen($noun); $i++) {
      $char = mb_substr($noun, $i, 1);
      if (!in_array($char, $num_str)) {
        $is_str = true;
        break;
      }
    }
    if ($is_str) {
      if (isset($results[$noun])) {
        $results[$noun] += $count;
      } else {
        $results[$noun] = $count;
      }
    }
  }
  return $results;
}

function getWordIds ($words) {
  $opt = [
    'method' => 'select',
    'tables' => ['dictionary'],
    'columns' => ['id', 'word'],
    'where' => 'word IN ("'.implode('", "', $words).'")',
  ];
  $data = controlMySQL($opt);
  $results = [];
  foreach ($data as $row) $results[$row['id']] = mb_strtolower($row['word']);
  return $results;
}

function saveNewWords ($all, $old) {
  $new = [];
  foreach ($all as $word_in_all) {
    foreach ($old as $word_in_old) {
      if (mb_strtolower($word_in_all) == mb_strtolower($word_in_old)) $new[] = $word_in_all;
    }
  }
  $opt = [
    'method' => 'insert',
    'table' => 'dictionary',
    'columns' => ['word'],
    'values' => []
  ];
  foreach ($new as $word) $opt['values'][] = [$word];
  controlMySQL($opt);
}
?>
