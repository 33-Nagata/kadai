<?php
require('common.php');
require_once('functions/control_MySQL.php');

if (!isset($_POST['title']) || $_POST['title'] == "" || !isset($_POST['article']) || $_POST['article'] == "") {
  if (isset($_POST['id'])) {
    header("Location: update_news.php?id={$_POST['id']}");
  } else {
    header('Location: index.php');
  }
  exit;
}

$news_id = $_POST['news_id'];
$title = $_POST['title'];
$article = $_POST['article'];
$photo = isset($_FILES['photo']['tmp_name']) && $_FILES['photo']['error'] != UPLOAD_ERR_NO_FILE ? file_get_contents($_FILES['photo']['tmp_name']) : false;
$lat = $_POST['lat'];
$lon = $_POST['lon'];
$location = $lat != "" && $lon != "" ? "GeomFromText('POINT({$lon} {$lat})')" : NULL;
$show_flg = isset($_POST['delete']) ? 0 : 1;

//記事保存
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

$opt = [
  'columns' => [
    'title' => $title,
    'article' => $article,
    'author_id' => $id,
    'location' => $location,
    'show_flg' => $show_flg,
    ]
];
if ($photo) $opt['columns']['photo'] = $photo;
if (updateNews($opt)) {
  $_SESSION['message'] = '<p class="message success">記事を更新しました</p>';
  header("Location: news.php?id={$news_id}");
} else {
  $_SESSION['message'] = '<p class="message failure">記事の更新に失敗しました</p>';
  $_SESSION['title'] = $title;
  $_SESSION['article'] = $article;
  header("Location: update_news.php?id={$news_id}");
}

//テキスト解析
function getNounCounts ($sentence) {
  include('yahoo_japan_application_id.php');
  $sentence = mb_convert_encoding($article, 'utf-8', 'auto');
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
  foreach ($xml->uniq_result->word_list->word as $data) {
    $noun = (string)$data->surface;
    $count = (int)$data->count;
    $results[$noun] = $count;
  }
  return $results;
}

$used_words = getNounCounts($sentence);

//既登録単語取得
function getWordIds ($words) {
  require_once('functions/control_MySQL.php');

  $opt = [
    'method' => 'select',
    'tables' => ['dictionary'],
    'columns' => ['id', 'word'],
    'where' => 'word IN ("'.implode('" , "', array_keys($words)).'")'
  ];
  $data = controlMySQL($opt);
  $results = [];
  foreach ($data as $row) $results[$row['id']] = $row['word'];
  return $results;
}

$old_words = getWordIds(array_keys($used_words));

//未登録単語登録
function saveNewWords ($all, $old) {
  $new = array_diff($all, $old);
  $opt = [
    'table' => 'dictionary',
    'columns' => ['word'],
    'values' => []
  ];
  foreach ($new as $word) {
    $opt['values'][] = [$word];
  }
  insertMultiColumns($opt);
}

saveNewWords(array_keys($used_words), $old_words);

//単語のid取得
$ids = getWordIds($used_words);
foreach ($ids as $index => $word) {
  $word_list[$index] = [
    'word' => $word,
    'frequency' => $frequency
  ];
}

//記事中の単語使用回数更新
$opt = [
  'method' => 'select',
  'tables' => ['news_word_frequency'],
  'columns' => ['word_id'],
  'where' => 'news_id="'.$news_id.'" AND word_id IN ("'.implode('" , "', array_keys($word_list)).'")'
];
$results = controlMySQL($opt);
$savedList = [];
foreach ($results as $row) $savedList[] = $row['word_id'];
foreach ($word_list as $word_id => $data) {
  if (in_array($word_id, $savedList)) {
    $opt = [
      'method' => 'update',
      'where' => "news_id='{$news_id}' AND word_id='{$word_id}'"
    ];
  } else {
    $opt = [
      'method' => 'insert',
      'columns' => [
        'news_id' => $news_id,
        'word_id' => $word_id,
      ]
    ];
  }
  $opt['tables'] = ['news_word_frequency'];
  $opt['columns']['frequency'] = $data['frequency'];
  controlMySQL($opt);
}
?>
