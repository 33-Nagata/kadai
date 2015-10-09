function setTextLangBtn() {
  text_languages = [
    {value: 'ar', text: 'العربية'}, // Arabic
    {value: 'bs-Latn', text: 'bosanski'}, // Bosnian (Latin)
    {value: 'bg', text: 'български'}, // Bulgaria
    {value: 'ca', text: 'Català'}, // Catalan
    {value: 'zh-CHS', text: '简体字'}, // Chinese Simplified
    {value: 'zh-CHT', text: '繁体字'}, // Chinese Traditional
    {value: 'hr', text: 'Hrvatska'}, // Croatian
    {value: 'cs', text: 'čeština'}, // Czech
    {value: 'da', text: 'danske'}, // Danish
    {value: 'nl', text: 'Nederlandse'}, // Dutch
    {value: 'en', text: 'English'}, // English
    {value: 'et', text: 'eesti'}, // Estonian
    {value: 'fi', text: 'suomi'}, // Finnish
    {value: 'fr', text: 'français'}, // French
    {value: 'de', text: 'Deutsch'}, // German
    {value: 'el', text: 'Ελληνική'}, // Greek
    {value: 'ht', text: 'kreyòl ayisyen'}, // Haitian Creole
    {value: 'he', text: 'עברית'}, // Hebrew
    {value: 'hi', text: 'हिन्दी'}, // Hindi
    {value: 'mww', text: 'Hmoob'}, // Hmong Daw
    {value: 'hu', text: 'magyar'}, // Hungarian
    {value: 'id', text: 'indonesia'}, // Indonesian
    {value: 'it', text: 'italiano'}, // Italian
    {value: 'ja', text: '日本語'}, // Japanese
    {value: 'tlh', text: 'tlhIngan Hol'}, // Klingon
    {value: 'tlh-Qaak', text: 'pIqaD'}, // Klingon (pIqaD)
    {value: 'ko', text: '한국어'}, // Korean
    {value: 'lv', text: 'Latvijā'}, // Latvian
    {value: 'lt', text: 'Lietuvos'}, // Lithuanian
    {value: 'ms', text: 'Melayu'}, // Malay
    {value: 'mt', text: 'Malti'}, // Maltese
    {value: 'no', text: 'norsk'}, // Norwegian
    {value: 'fa', text: 'فارسی'}, // Persian
    {value: 'pl', text: 'Polskie'}, // Polish
    {value: 'pt', text: 'português'}, // Portuguese
    {value: 'otq', text: 'Hñohño'}, // Querétaro Otomi
    {value: 'ro', text: 'român'}, // Romanian
    {value: 'ru', text: 'русский'}, // Russian
    {value: 'sr-Cyrl', text: 'Српски'}, // Serbian (Cyrillic)
    {value: 'sr-Latn', text: 'Srpski'}, // Serbian (Latin)
    {value: 'sk', text: 'slovenský'}, // Slovak
    {value: 'sl', text: 'slovenščina'}, // Slovenian
    {value: 'es', text: 'español'}, // Spanish
    {value: 'sv', text: 'svenska'}, // Swedish
    {value: 'th', text: 'ไทย'}, // Thai
    {value: 'tr', text: 'Türk'}, // Turkish
    {value: 'uk', text: 'Український'}, // Ukrainian
    {value: 'ur', text: 'اردو'}, // Urdu
    {value: 'vi', text: 'Tiếng Việt'}, // Vietnamese
    {value: 'cy', text: 'Cymraeg'}, // Welsh
    {value: 'yua', text: "Màaya t'àan"}, // Yucatec Maya
  ];
  var select = document.createElement('select');
  select.setAttribute('name', 'text-language');
  for (var i = 0; i < text_languages.length; i++) {
    var option = document.createElement('option');
    option.setAttribute('value', text_languages[i]['value']);
    option.innerHTML = text_languages[i]['text'];
    select.appendChild(option);
  }
  $('label[for=text-language]').after(select);
  $('select[name=text-language]').children('option[value='+language+']').attr('selected', true);
}

// テキスト翻訳
function translate(id, text, from_lang, to_lang) {
  $.get(
    'get_translation.php?',
    {
      text: text,
      from: from_lang,
      to: to_lang
    },
    function(text){
      $('#'+id+' .mother').text(text);
      var messages = $('#messages');
      if (sort == 'asc') {
        messages.scrollTop(messages.get(0).scrollHeight);
      } else {
        messages.scrollTop(0);
      }
    }
  );
}
