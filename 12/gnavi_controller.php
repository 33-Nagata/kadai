<?php
// アクセスキー
define('GNAVI_API_KEY', 'f773185ed62a7d385aa6992d613acd27');
define('RESTAURANT_SEARCH_API_URL', 'http://api.gnavi.co.jp/RestSearchAPI/20150630/');

$gc = new gnaviController($_GET);

if (!$gc->downloadMaster($_GET)) {
  $gc->download();
  $gc->show();
}

class gnaviController {
  // レストラン情報格納変数
  public $raw_info = null;
  public $restaurant_info = null;
  // 固定オプション
  protected $basic_query = [
    'keyid' => GNAVI_API_KEY,
    // レスポンス形式->xmlまたはjson
    'format' => 'json'
  ];
  // マスタデータURL
  protected $master_urls = [
    'region' => 'http://api.gnavi.co.jp/master/AreaSearchAPI/20150630/',
    'pref' => 'http://api.gnavi.co.jp/master/PrefSearchAPI/20150630/',
    'area_l' => 'http://api.gnavi.co.jp/master/GAreaLargeSearchAPI/20150630/',
    'area_m' => 'http://api.gnavi.co.jp/master/GAreaMiddleSearchAPI/20150630/',
    'area_s' => 'http://api.gnavi.co.jp/master/GAreaSmallSearchAPI/20150630/',
    'category_l' => 'http://api.gnavi.co.jp/master/CategoryLargeSearchAPI/20150630/',
    'category_s' => 'http://api.gnavi.co.jp/master/CategorySmallSearchAPI/20150630/'
  ];
  // GETのパラメータ => 対応するぐるなびAPIのパラメータ
  // (ネストされているものは最下層が該当)
  protected $get_params = [
    // 店舗ID->「,」区切りで店舗IDを複数検索可能（１０個まで）
    'id' => 'id',
    // 電話番号->ハイフン必須
    'tel' => 'tel',
    // 要URLエンコード(UTF-8)
    'need_encode' => [
      // 店舗名
      'name' => 'name',
      // 店舗名読み
      'kana' => 'name_kana',
      // 住所->（都道府県＋市町村＋番地）の文字列
      'address' => 'address',
      // フリーワード検索->「,」区切りで複数ワードが検索可能（１０個まで）
      //                 フリーワード検索条件タイプ(デフォルト:AND, 1:AND, 2:OR)
      'word' => [
        'words' => 'freeword',
        'word_condition' => 'freeword_condition'
      ],
    ],
    // 場所絞り込み(小分類から順に設定)
    'location' => [
      'area_s' => 'areacode_s',
      'area_m' => 'areacode_m',
      'area_l' => 'areacode_l',
      'pref' => 'pref',
      'region' => 'area'
    ],
    // 業態絞り込み(小分類から順に設定)
    'category' => [
      'category_s' => 'category_s',
      'category_l' => 'category_l'
    ],
    // 入力測地系タイプ->入力する緯度/経度の測地系のタイプ(1:日本測地系, 2:世界測地系)
    'coordinates_in' => 'input_coordinates_mode',
    // 測地系タイプ->レスポンスに含まれる緯度/経度の測地系(1:日本測地系, 2:世界測地系)
    'coordinates_out' => 'coordinates_mode',
    // 位置情報
    'geo' => [
      // 緯度, 経度->分秒十進式(入力測地系タイプの選択したタイプの値で指定)
      'latitude' => 'latitude',
      'longitude' => 'longitude',
      // 範囲->緯度/経度からの検索範囲(1:300m, 2:500m, 3:1000m, 4:2000m, 5:3000m)
      'range' => 'range'
    ],
    // ソート順->レスポンスデータのソート順(指定なし:ぐるなびソート順, 1:店舗名, 2:業態)
    'sort' => 'sort',
    // 検索開始位置->検索開始レコードの位置(デフォルト:1)
    'offset' => 'offset',
    // ヒット件数->レスポンスデータの最大件数(デフォルト:10)
    'max' => 'hit_per_page',
    // 検索開始ページ(デフォルト:1)
    'page' => 'offset_page',
    // 絞込みオプション(0:絞込みなし(デフォルト), 1:絞込みあり)
      // ランチ営業あり
      'lunch' => 'lunch',
      // 禁煙席あり
      'no_smoking' => 'no_smoking',
      // カード利用可
      'card' => 'card',
      // 携帯の電波
      'mobilephone' => 'mobilephone',
      // 飲み放題
      'bottomless' => 'bottomless_cup',
      // 日曜営業
      'sunday' => 'sunday_open',
      // テイクアウト
      'takeout' => 'takeout',
      // 個室
      'private_room' => 'private_room',
      // 深夜営業
      'midnight' => 'midnight',
      // 駐車場
      'parking' => 'parking',
      // 法事利用
      'memorial' => 'memorial_service',
      // 誕生日特典
      'birthday' => 'birthday_privilege',
      // 結納利用
      'engagement' => 'betrothal_present',
      // キッズメニュー
      'kids' => 'kids_menu',
      // 電源
      'power_supply' => 'outret',
      // wifi
      'wifi' => 'wifi',
      // マイク
      'microphone' => 'microphone',
      // 食べ放題
      'buffe' => 'buffe',
      // 14時以降のランチ
      'late_lunch' => 'late_lunch',
      // スポーツ観戦
      'sports' => 'sports',
      // 朝まで営業
      'until_morning' => 'until_morning',
      // ランチデザート
      'lunch_desert' => 'lunch_desert',
      // プロジェクター・スクリーン
      'projecter' => 'projecter_screen',
      // ペット同伴
      'pets' => 'with_pet',
      // デリバリー
      'deliverly' => 'deliverly',
      // 土日特別ランチ
      'holiday_lunch' => 'special_holiday_lunch',
      // 電子マネー
      'e_money' => 'e_money',
      // ケータリング
      'caterling' => 'caterling',
      // モーニング・朝ごはん
      'breakfast' => 'breakfast',
      // デザートビュッフェ
      'desert_buffet' => 'desert_buffet',
      // ランチビュッフェ
      'lunch_buffet' => 'lunch_buffet',
      // お弁当
      'bento' => 'bento',
      // ランチサラダバー
      'lunch_salad_buffet' => 'lunch_salad_buffet',
      // ダーツ
      'darts' => 'darts'
  ];

  function __construct($param_array = null) {
    /******************************
     * 地方・都道府県・エリアコード取得
     * region_codes, pref_codes,
     * areal_codes, aream_codes,
     * areas_codes
    *******************************/
    $this->downloadFixedCode(array_keys($this->get_params['location']));
    /******************************
     * 業態コード取得
     * categoryl_codes,
     * categorys_codes
     ******************************/
    $this->downloadFixedCode(array_keys($this->get_params['category']));
    // リクエストパラメータ初期化
    $this->initRequestParams();
    if (count($param_array) > 0) $this->changeRequestParams($param_array);
  }

  public function downloadMaster($param) {
    foreach (array_keys($this->master_urls) as $key) {
      if (in_array($key.'_master', array_keys($param))) {
        $master_data = $this->execGnaviApi($this->master_urls[$key], $this->basic_query);
        switch ($key) {
          case 'region':
            $type = 'area';
            break;
          case 'area_l':
            $type = 'garea_large';
            break;
          case 'area_m':
            $type = 'garea_middle';
            break;
          case 'area_s':
            $type = 'garea_small';
            break;
          default:
            $type = $key;
            break;
        }
        $json = json_encode($master_data->$type);
        $replace_str = [
          'area_code' => 'region_code',
          'area_name' => 'region_name',
          'garea_large' => 'area_l',
          'areacode_l' => 'area_l_code',
          'areaname_l' => 'area_l_name',
          'garea_middle' => 'area_m',
          'areacode_m' => 'area_m_code',
          'areaname_m' => 'area_m_name',
          'garea_small' => 'area_s',
          'areacode_s' => 'area_s_code',
          'areaname_s' => 'area_s_name'
        ];
        $json = str_replace(array_keys($replace_str), array_values($replace_str), $json);
        echo $json;
        return true;
      }
    }
    return false;
  }

  public function initRequestParams() {
    $this->req_params = $this->basic_query;
  }

  public function changeRequestParams($param_array) {
    foreach ($this->get_params as $param => $gnavi_param) {
      if ($param == 'need_encode') {
        $this->encodeParam($param_array, $gnavi_param);
      } elseif (in_array($param, ['location', 'category'])) {
        $this->setClassificationParam($param_array, $gnavi_param);
      } elseif ($param == 'geo') {
        if (array_key_exists('latitude', $param_array) && array_key_exists('longitude', $param_array)) {
          $this->setGeoParam($param_array, $gnavi_param);
        }
      } elseif (array_key_exists($param, $param_array)) {
        $this->req_params[$gnavi_param] = $param_array[$param];
      }
    }
  }

  public function show() {
    echo json_encode($this->restaurant_info);
  }

  public function download() {
    $this->raw_info = $this->execGnaviApi(RESTAURANT_SEARCH_API_URL, $this->req_params);
    $this->restaurant_info = $this->formatRestaurantInfo($this->raw_info->rest);
  }

  protected function execGnaviApi($url, $query_array) {
    $query = http_build_query($query_array);
    $req = $url.'?'.$query;
    $json = file_get_contents($req);
    $data = json_decode($json);
    return $data;
  }

  protected function downloadFixedCode($array) {
    foreach ($array as $param) {
      // ex. 'area_l' -> 'areal_codes'
      $codes = str_replace('_', '', $param).'_codes';
      // ex. 'area_l' -> ['area', 'l']
      $tmp = explode('_', $param);
      switch ($tmp[0]) {
        case 'region':
        case 'pref':
          $this->$codes = $this->fetchLocationArray($param);
          break;
        case 'area':
          $this->$codes = $this->fetchGAreaArray($tmp[1]);
          break;
        case 'category':
          $this->$codes = $this->fetchCategoryArray($tmp[1]);
          break;
      }
    }
  }

  protected function fetchLocationArray($param) {
    $type = $this->get_params['location'][$param];
    $url = $this->master_urls[$param];
    $code = $type.'_code';
    $name = $type.'_name';
    $data = $this->execGnaviApi($url, $this->basic_query)->$type;
    return $this->formatResult($data, $code, $name);
  }

  protected function fetchGAreaArray($size) {
    $url = $this->master_urls['area_'.$size];
    $code = 'areacode_'.$size;
    $name = 'areaname_'.$size;
    $type = 'garea_';
    switch ($size) {
      case 'l':
        $type .= 'large';
        break;
      case 'm':
        $type .= 'middle';
        break;
      case 's':
        $type .= 'small';
        break;
    }
    $data = $this->execGnaviApi($url, $this->basic_query)->$type;
    return $this->formatResult($data, $code, $name);
  }

  protected function fetchCategoryArray($size) {
    $type = 'category_'.$size;
    $url = $this->master_urls[$type];
    $code = $type.'_code';
    $name = $type.'_name';
    $data = $this->execGnaviApi($url, $this->basic_query)->$type;
    return $this->formatResult($data, $code, $name);
  }

  protected function formatResult($data, $code, $name) {
    foreach ($data as $row) $array[$row->$code] = $row->$name;
    return $array;
  }

  protected function getCode($param, $codes) {
    // コードならそのまま、名称ならコードに変換
    $code = array_search($param, $codes);
    return $code === false ? $param : $code;
  }

  protected function encodeParam($param_array, $array) {
    foreach ($array as $param => $gnavi_param) {
      if ($param == 'word') {
        $this->setFreewordParam($param_array, $gnavi_param);
      } elseif (array_key_exists($param, $param_array)) {
        $this->req_params[$gnavi_param] = urlencode($param_array[$param]);
      }
    }
  }

  protected function setFreewordParam($param_array, $array) {
    foreach ($array as $param => $gnavi_param) {
      if (array_key_exists($param, $param_array)) {
        $this->req_params[$gnavi_param] = urlencode($param_array[$param]);
      // フリーワードが設定されていなければスキップ
      } else {
        break;
      }
    }
  }

  protected function setClassificationParam($param_array, $array) {
    foreach ($array as $param => $gnavi_param) {
      if (array_key_exists($param, $param_array)) {
        // 既存の値を初期化
        foreach ($array as $unset_param) {
          unset($this->req_params[$unset_param]);
        }
        // 値を設定
        $codes = str_replace('_', '', $param).'_codes';
        $value = $this->getCode($param_array[$param], $this->$codes);
        $this->req_params[$gnavi_param] = $value;
        // 小分類が指定されていたら大分類はスキップ
        break;
      }
    }
  }

  protected function setGeoParam($param_array, $array) {
    foreach ($array as $param => $gnavi_param) {
      if (array_key_exists($param, $param_array)) {
        $this->req_params[$gnavi_param] = $param_array[$param];
      }
    }
  }

  protected function formatRestaurantInfo($data) {
    $needless_property = '@attributes';
    $gareas = $this->execGnaviApi($this->master_urls['area_s'], $this->basic_query)->garea_small;
    for ($i=0; $i < count($data); $i++) {
      unset($data[$i]->$needless_property);
      // name_kana -> kana
      $data[$i] = $this->changePropertyName($data[$i], 'name_kana', 'kana');
      // category -> freeword_category
      $data[$i] = $this->changePropertyName($data[$i], 'category', 'freeword_category');
      // code -> location
      $code = $data[$i]->code;
      $location['region'] = [$code->areacode => $code->areaname];
      $location['pref'] = [$code->prefcode => $code->prefname];
      foreach ($gareas as $garea) {
        if ($garea->areaname_s == $code->areaname_s) {
          $area_l = $garea->garea_large;
          $area_m = $garea->garea_middle;
          $location['area_l'] = [$area_l->areacode_l => $area_l->areaname_l];
          $location['area_m'] = [$area_m->areacode_m => $area_m->areaname_m];
          $location['area_s'] = [$garea->areacode_s => $garea->areaname_s];
          break;
        }
      }
      $data[$i]->location = $location;
      // code -> category
      $category['category_l'] = $this->formatCategoryResult($code, 'category_l');
      $category['category_s'] = $this->formatCategoryResult($code, 'category_s');
      $data[$i]->category = $category;

      unset($data[$i]->code);
      // credit_card -> card
      $data[$i] = $this->changePropertyName($data[$i], 'credit_card', 'card');
    }
    return $data;
  }

  protected function changePropertyName($data, $from, $to) {
    $data->$to = $data->$from;
    unset($data->$from);
    return $data;
  }

  protected function formatCategoryResult($data, $name) {
    $category = [];
    // ex. category_l -> category_code_l
    $code_property = str_replace('_', '_code_', $name);
    $name_property = str_replace('_', '_name_', $name);
    $code = $data->$code_property;
    $name = $data->$name_property;
    for ($i=0; $i < count($code); $i++) {
      if (is_string($code[$i])) $category[] = [$code[$i] => $name[$i]];
    }
    return $category;
  }
}
?>
