function renderMessage(message) {
  var date_html = '<span class="post-date">'+escapeHTML( new Date(message.timestamp).toLocaleString())+'</span>';
  var name_html = '<span class="post-name">' + escapeHTML(message.value.name+'@'+message.value.hash.substr(0,5)) + '</span>';
  var sent_id = message.id;
  var sent_message = escapeHTML(message.value.content);
  var sent_language = message.value.language;
  if (sent_language == language) {
    var foreign_html = '';
    var mother_html = '<p class="post-text mother">'+sent_message+'</p>';
  } else {
    var foreign_html = '<p class="post-text foreign">'+sent_message+'</p>';
    var mother_html = '<p class="post-text mother"></p>';
  }
  var delete_html = '<button class="delete-btn" id="delete'+sent_id+'">削除</button>';
  var new_html = date_html + name_html + foreign_html + mother_html + delete_html;
  new_html = sort == 'asc' ? '<hr>' + new_html : new_html + '<hr>';
  new_html = '<div id="'+sent_id+'" class="post">'+new_html+'</div>';
  var messages = $('#messages');
  if (last_message) {
    if (sort == 'asc') {
      $('#'+last_message).after(new_html);
      messages.scrollTop(messages.get(0).scrollHeight);
    } else {
      $('#'+last_message).before(new_html);
      messages.scrollTop(0);
    }
  } else {
    messages.append(new_html);
  }
  if (sent_language != language) {
    $('#'+sent_id+' .mother').text(translate(sent_id, sent_message, sent_language, language));
  }
  last_message = sent_id;
}

function post(dataStore) {
  var name = escapeHTML($("input[name=name]").val());
  var hash = escapeHTML(CryptoJS.MD5($("input[name=email]").val()));
  var name = $('input[name=name]').val();
  var email = $('input[name=email]').val();
  var content = escapeHTML($("#content").val());
  if (name !== "" && email !== "" && content && content !== "") {
    dataStore.push(
      {
        name: name,
        hash: hash,
        content: content,
        language: language
      },
      function (err, data) {
        if (!err) {
          $("#content").val("");
        } else {
          console.log(err);
        }
      },
      function (err) {
        console.log(err);
      }
    );
  } else {
    alert("必要事項を入力してください");
  }
}

function deleteElement(id) {
  $('#'+id).remove();
  if (sort == 'asc') {
    last_message = $('.post:last').attr('id');
  } else {
    last_message = $('.post:first').attr('id');
  }
}

function deleteMessage(dataStore, id) {
  var result;
  dataStore.remove(
    id,
    function(err, data){
      if (err != null) {
        console.log(err);
        result = false;
      }
      result = true;
    },
    function(err){
      console.log(err);
      result = false;
    }
  );
  if (result) deleteElement(id);
  return result;
}

function deleteAllMessages(dataStore) {
  while (last_message) {
    deleteMessage(dataStore, last_message);
  }
}

//インジェクション対策
function escapeHTML(val) {
  return $('<div>').text(val).html();
};
