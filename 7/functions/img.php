<?php
// 画像処理
function saveImg ($table_name, $id, $upload_img) {
  // 画像の最大幅・高さ設定
  $max_length = 120;
  // パラメーターが適正か確認
  if (isset($upload_img['error']) && is_int($upload_img['error'])) {
    // アップロードエラー確認
    if ($upload_img['error'] == UPLOAD_ERR_OK) {
      $tmp_path = $upload_img['tmp_name'];
      // 画像形式取得
      $img_type = exif_imagetype($tmp_path);
      if (in_array($img_type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
        // 画像サイズ取得
        $img_info = getimagesize($tmp_path);
        $src_w = $img_info[0];
        $src_h = $img_info[1];
        // 保存サイズ設定
        if ($src_w > $max_length || $src_h > $max_length) {
          $dst_w = min($max_length, $src_w * $max_length / $src_h);
          $dst_h = min($max_length, $src_h * $max_length / $src_w);
        } else {
          $dst_w = $src_w;
          $dst_h = $src_h;
        }
        $dst_x = ($max_length - $dst_w) / 2;
        $dst_y = ($max_length - $dst_h) / 2;
        // 元画像リソース生成
        $src = imagecreatefromstring(file_get_contents($tmp_path));
        if ($src) {
          // 出力先リソース生成
          $dst = imagecreatetruecolor($max_length, $max_length);
          // 画像リサイズ
          imagecopyresampled($dst, $src, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
          // 保存ファイル名作成
          $file_name = date("YmdHis").sha1_file($tmp_path).'.png';
          // PNGに変換して保存
          if (imagepng($dst, 'img/'.$file_name)) {
            $opt = [
              'method' => 'insert',
              'tables' => ['img'],
              'columns' => [
                'table_name' => $table_name,
                'content_id' => $id,
                'file_name' => $file_name
              ]
            ];
            return controlMySQL($opt);
          }
        }
      }
    }
  }
  return false;
}

function getImg ($table_name, $target_id) {
  $opt = [
    'method' => 'select',
    'tables' => ['img'],
    'columns' => ['file_name'],
    'where' => "table_name='{$table_name}' AND content_id={$target_id}"
  ];
  $result = controlMySQL($opt);
  return $result ? $result[0]['file_name'] : false;
}
?>
