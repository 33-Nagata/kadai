<?php
require_once('common.php');

// エラー処理
if (!isset($_GET['id'])) {
  $_SESSION['message'] = '<p class="alert alert-danger">削除対象の{$target_str}が選択されていません</p>';
  header('Location: index.php');
  exit;
}

$result = controlMySQL($opt);
if (count($result) == 0) {
  $_SESSION['message'] = '<p class="alert alert-danger">削除対象の{$target_str}がありません</p>';
  header("Location: index.php");
  exit;
}

$author_id = $result[0]['author_id'];
$news_id = $result[0]['news_id'];
if ($id != $author_id) {
  $_SESSION['message'] = '<p class="alert alert-danger">削除権限がありません</p>';
  header("Location: news.php?id={$news_id}");
  exit;
}
// 削除実行
$opt = [
  'method' => 'update',
  'tables' => [$target_table],
  'columns' => ['show_flg' => 0],
  'where' => "id='{$target_id}'"
];
if (controlMySQL($opt)) {
  $_SESSION['message'] = '<p class="alert alert-success">{$target_str}を削除しました</p>';
} else {
  $_SESSION['message'] = '<p class="alert alert-danger">{$target_str}を削除できませんでした</p>';
}
?>
