function reverse () {
  // 操作用のDocumentFragment作成
  var fragment = document.createDocumentFragment();
  var messages = document.getElementById('messages');
  var base = document.getElementById(last_message);
  var postarea = document.getElementById('postarea');
  // messagesをfragmentに移動
  fragment.appendChild(messages);
  // フラグ反転
  sort = sort == 'asc' ? 'desc' : 'asc';
  // 昇順
  if (sort == 'asc') {
    var next;
    // baseのhrタグ位置変更
    base.insertBefore(base.lastChild, base.firstChild);
    // 並べ替え
    while (messages.lastChild.id != last_message) {
      next = base.nextSibling;
      // hrタグ位置変更
      next.insertBefore(next.lastChild, next.firstChild);
      // nextを先頭に移動
      messages.insertBefore(next, messages.firstChild);
    }
    // postarea.prepend(messages)
    postarea.parentNode.insertBefore(messages, postarea);
    $('#messages').scrollTop($('#messages').get(0).scrollHeight);
  // 降順
  } else {
    var prev;
    // baseのhrタグ位置変更
    base.appendChild(base.firstChild);
    // 並べ替え
    while (messages.firstChild.id != last_message) {
      prev = base.previousSibling;
      // hrタグ位置変更
      prev.appendChild(prev.firstChild);
      // prevを最後に移動
      messages.appendChild(prev);
    }
    // postarea.append(messages)
    postarea.parentNode.appendChild(messages);
  }
}

Array.prototype.diff = function(array) {
  return this.filter(function(i) {
    return array.indexOf(i) < 0;
  });
}

function setControllers (order) {
  if (order == 'enable') {
    $('button.on').removeAttr('disabled');
    $('textarea').removeAttr('disabled');
  } else {
    $('button.on').attr('disabled', 'disabled');
    $('textarea').attr('disabled', 'disabled');
  }
}
