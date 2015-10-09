function setSpeechLangBtn() {
  speech_languages =[
    ['Afrikaans',       ['af-ZA']],
    ['Bahasa Indonesia',['id-ID']],
    ['Bahasa Melayu',   ['ms-MY']],
    ['Català',          ['ca-ES']],
    ['Čeština',         ['cs-CZ']],
    ['Dansk',           ['da-DK']],
    ['Deutsch',         ['de-DE']],
    ['English',         ['en-AU', 'Australia'],
                        ['en-CA', 'Canada'],
                        ['en-IN', 'India'],
                        ['en-NZ', 'New Zealand'],
                        ['en-ZA', 'South Africa'],
                        ['en-GB', 'United Kingdom'],
                        ['en-US', 'United States']],
    ['Español',         ['es-AR', 'Argentina'],
                        ['es-BO', 'Bolivia'],
                        ['es-CL', 'Chile'],
                        ['es-CO', 'Colombia'],
                        ['es-CR', 'Costa Rica'],
                        ['es-EC', 'Ecuador'],
                        ['es-SV', 'El Salvador'],
                        ['es-ES', 'España'],
                        ['es-US', 'Estados Unidos'],
                        ['es-GT', 'Guatemala'],
                        ['es-HN', 'Honduras'],
                        ['es-MX', 'México'],
                        ['es-NI', 'Nicaragua'],
                        ['es-PA', 'Panamá'],
                        ['es-PY', 'Paraguay'],
                        ['es-PE', 'Perú'],
                        ['es-PR', 'Puerto Rico'],
                        ['es-DO', 'República Dominicana'],
                        ['es-UY', 'Uruguay'],
                        ['es-VE', 'Venezuela']],
    ['Euskara',         ['eu-ES']],
    ['Filipino',        ['fil-PH']],
    ['Français',        ['fr-FR']],
    ['Galego',          ['gl-ES']],
    ['Hrvatski',        ['hr_HR']],
    ['IsiZulu',         ['zu-ZA']],
    ['Íslenska',        ['is-IS']],
    ['Italiano',        ['it-IT', 'Italia'],
                        ['it-CH', 'Svizzera']],
    ['Lietuvių',        ['lt-LT']],
    ['Magyar',          ['hu-HU']],
    ['Nederlands',      ['nl-NL']],
    ['Norsk bokmål',    ['nb-NO']],
    ['Polski',          ['pl-PL']],
    ['Português',       ['pt-BR', 'Brasil'],
                        ['pt-PT', 'Portugal']],
    ['Română',          ['ro-RO']],
    ['Slovenščina',     ['sl-SI']],
    ['Slovenčina',      ['sk-SK']],
    ['Suomi',           ['fi-FI']],
    ['Svenska',         ['sv-SE']],
    ['Tiếng Việt',      ['vi-VN']],
    ['Türkçe',          ['tr-TR']],
    ['Ελληνικά',        ['el-GR']],
    ['български',       ['bg-BG']],
    ['Pусский',         ['ru-RU']],
    ['Српски',          ['sr-RS']],
    ['Українська',      ['uk-UA']],
    ['한국어',            ['ko-KR']],
    ['中文',             ['cmn-Hans-CN', '普通话 (中国大陆)'],
                        ['cmn-Hans-HK', '普通话 (香港)'],
                        ['cmn-Hant-TW', '中文 (台灣)'],
                        ['yue-Hant-HK', '粵語 (香港)']],
    ['日本語',           ['ja-JP']],
    ['हिन्दी',             ['hi-IN']],
    ['ภาษาไทย',         ['th-TH']]
  ];
  var speech_default = 'ja-JP';
  // 言語選択ボタン作成
  var select = document.createElement('select');
  select.setAttribute('name', 'speech-language');
  for (var i = 0; i < speech_languages.length; i++) {
    var option = document.createElement('option');
    option.id = 'langage'+i;
    option.value = speech_languages[i][1][0];
    option.textContent = speech_languages[i][0];
    select.appendChild(option);
  }
  $('label[for=speech-language]').after(select);
  var btn = $('.speech');
  var lang_select = $('select[name=speech-language]');
  // デフォルト言語選択
  lang_select.find('option[value='+speech_default+']').attr({selected: true});
  // 言語変更
  lang_select.on('change', function(){
    changeSpeechLang();
  });
  // ボタン操作設定
  btn.on('click', function(){
    var dialect_select = document.querySelector('select[name=dialect]');
    $('.speech').toggle();
    // on
    if ($(this).hasClass('on')) {
      lang_select.prop('disabled', true);
      if (dialect_select !== null) dialect_select.disabled = true;
      recStart();
    // off
    } else {
      lang_select.prop('disabled', false);
      if (dialect_select !== null) dialect_select.disabled = false;
      rec.stop();
      rec = null;
      if (timer) {
        clearTimeout(timer);
        timer = false;
      }
    }
  });
}

function changeSpeechLang() {
  $('select[name=dialect]').remove();
  var language_id = $('select[name=speech-language]').children('option:selected').attr('id').substr(7);
  var chosen = speech_languages[language_id];
  // dialectがある場合
  if (chosen.length > 2) {
    var select = document.createElement('select');
    select.setAttribute('name', 'dialect');
    for (var i = 1; i < chosen.length; i++) {
      var option = document.createElement('option');
      option.textContent = chosen[i][1];
      option.value = chosen[i][0];
      select.appendChild(option);
    }
    $('select[name=speech-language]').after(select);
  }
}

function recStart() {
  // 画面への出力が終了した音声認識の結果数
  var line = 0;
  // 音声認識インスタンス
  rec = new webkitSpeechRecognition();
  // 言語設定
  if (document.querySelector('select[name=dialect]') === null) {
    var select = $('select[name=speech-language]');
  } else {
    var select = $('select[name=dialect]');
  }
  rec.lang = select.children('option:selected').val();
  // 継続的に処理を行う
  rec.continuous = true;
  // 認識結果を取得するコールバック
  rec.onresult = function (e) {
    var lines = e.results.length;
    var content = document.getElementById('content');
    if (timer) {
      clearTimeout(timer);
      timer = false;
    }
    for (var i = line; i < lines; i++) {
      // isFinalがtrueの場合は確定した内容
      if (e.results[i].isFinal) {
        content.value += e.results[i][0].transcript;
      }
    }
    line = lines;
    // 一定時間入力がなければ送信
    timer = setTimeout(function(){
      if (content.value != '') {
        post(ds);
      }
    }, 2000);
  };
  rec.start();
}
