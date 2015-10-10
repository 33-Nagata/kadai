function getMediaPermission (callback) {
  // ビデオ・音声の使用許可確認
  navigator.getUserMedia(
    {audio: true, video: true},
    // 成功
    function(stream){
      window.localStream = stream;
      callback();
    },
    // 失敗
    function(err){
      console.log(err);
    }
  );
}

function setSkyway () {
  toggleVideoControllers();
  showVideo('me', localStream);
  // debug : {0: no log, 1: err, 2: err/alert, 3: all}
  peer = new Peer({ key: skyway_api_key, debug: 2});
  // PeerServer接続時イベント
  peer.on('open', function(){
    peer.listAllPeers(function(list){
      // Peer情報を取得
      askInfos(list);
    });
  });
  // 定期的にActive Peerを取得
  peer_checker = setInterval(function(){
    peer.listAllPeers(function(new_list){
      new_list = new_list.diff(peer.id);
      var old_list = [];
      for (var i = 1; i < $('#peer-list>button').length + 1; i++) {
        old_list.push($('#peer-list>button:nth-child('+i+')').attr('id').substr('peer'.length));
      }
      // 接続が切れていたらリストから削除
      for (var i = 0; i < old_list.length; i++) {
        if ($.inArray(old_list[i], new_list) == -1) {
          $('#peer'+old_list[i]).remove();
        }
      }
      // リストになければ追加
      var add_list = new_list.diff(old_list);
      if (add_list.length > 0) askInfos(add_list);
    });
  }, 10 * 1000);
  /*********************
   * Peer Event Handlers
   *********************/
  // 着信
  peer.on('call', function(mediaConnection){
    // 確認
    var name = $('#peer'+mediaConnection.peer).text();
    if (confirm(name+'からのビデオ通話を受信します')) {
      mediaConnection.answer(window.localStream);
      setVideo(mediaConnection);
    } else {
      // refuse video call
      mediaConnection.close();
    }
  });
  // ユーザー情報受信
  peer.on('connection', function(dataConnection){
    // 登録
    resisterInfo(dataConnection.metadata);
    // 自分の情報送信
    var my_info = getMyInfo();
    if (my_info) dataConnection.send(my_info);
  });
  // エラー
  peer.on('error', function(err){
    alert(err.message);
    // 切断
    $('button.video-control.off').trigger('click');
  });
}

function toggleVideoControllers () {
  $('#video-disabled').toggle();
  $('button.video-control').toggle();
}

function showVideo (id, stream) {
  if (id != 'me') {
    var video = document.createElement('video');
    video.id = id;
    video.className = 'others';
    video.setAttribute('autoplay', true);
    $('.video-container').append(video);
  }
  changeVideoSize();
  $('#'+id).prop('src', URL.createObjectURL(stream));
}

function sendUserInfo (id, info) {
  // 接続リクエストと一緒にデータ送信
  var dataConnection = peer.connect(
    id,
    {
      metadata : info,
      serialization: 'json'
    }
  );
  // 相手の情報が届いたら切断
  dataConnection.on('data', function(data){
    // 情報を登録
    resisterInfo(data.metadata);
    // 切断
    dataConnection.close();
  });
  // エラー処理
  dataConnection.on('error', function(err){
    console.log(err);
  });
}
/*****
 * info = {
 * 	id : string,
 * 	name : string,
 * 	hash : string
 * }
 *****/
function resisterInfo (info) {
  if ($('#peer'+info.id).length == 0) {
    var btn = document.createElement('button');
    console.log(info.hash);
    btn.textContent = info.name+'@'+info.hash.substr(0, 5);
    btn.id = 'peer'+info.id;
    $('#peer-list').append(btn);
    // コールイベント追加
    $('#'+btn.id).on('click', function(){
      var id = $(this).attr('id').substr('peer'.length);
      var mediaConnection = peer.call(id, window.localStream);
      setVideo(mediaConnection);
    });
  }
}

function setVideo (mediaConnection) {
  var media = mediaConnection;
  var id = mediaConnection.peer;
  mediaConnection.on('stream', function(stream){
    // 接続リスト追加
    connections[id] = media;
    // 相手のビデオを追加
    showVideo(id, stream);
    // 接続ボタン操作不可
    $('#peer'+id).prop('disabled', true);
    // 切断
    $('#'+id).on('click', function(){
      connections[id].close();
    });
    // 切断時イベント
    mediaConnection.on('close', function(){
      closeConnection(id);
    });
  });
}

function closeConnection(id) {
  delete connections[id];
  $('#'+id).remove();
  changeVideoSize();
  $('#peer'+id).prop('disabled', false);
}

function changeVideoSize () {
  var videos = $('video');
  videos.prop('width', videos.parent().width() / Math.ceil(Math.sqrt(videos.length)));
}

function askInfos (id_list) {
  id_list = id_list.diff(peer.id);
  var my_info = getMyInfo();
  if (my_info) {
    for (var i = 0; i < id_list.length; i++) {
      sendUserInfo(id_list[i], my_info);
    }
  }
}

function getMyInfo () {
  var name = escapeHTML($("input[name=name]").val());
  var email = $('input[name=email]').val();
  var hash = CryptoJS.MD5(email).toString(CryptoJS.enc.hex);
  if (name != "" && email != "") {
    return {
      id : peer.id,
      name : name,
      hash : hash
    };
  } else {
    return false;
  }
}

function stopTracks (streams) {
  for (var i = 0; i < streams.length; i++) {
    streams[i].stop();
  }
}
