<form class="form-user_update"action="update_user_execute.php" method="post" enctype="multipart/form-data">
  <h2 class="form-user_update-heading">登録情報変更</h2>
  <label for="name" class="sr-only">名前</label>
  <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" placeholder="名前" required>
  <label for="email" class="sr-only">メールアドレス</label>
  <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" placeholder="メールアドレス" required>
  <label for="password" class="sr-only">パスワード</label>
  <input type="password" name="password" class="form-control" placeholder="パスワード">
  <label for="confirm" class="sr-only">パスワード(確認用)</label>
  <input type="password" name="confirm" class="form-control" placeholder="パスワード(確認用)">
  <label for="photo">プロフィール画像</label>
  <?php if ($img): ?>
    <img src="img/<?php echo $img; ?>" />
  <?php endif; ?>
  <input type="file" name="photo" class="form-control">
  <input type="submit" id="submit" class="btn btn-lg btn-primary btn-block" value="変更">
  <button id="clear" class="btn btn-lg btn-warning">クリア</button>
  <a href="user.php?id=<?php echo $request_id; ?>">ユーザー情報へ戻る</a>
</form>

<script>
  function confirm_passwords(){
    var pwd1 = $("input[name=password]").val();
    var pwd2 = $("input[name=confirm]").val();
    if (pwd1 == pwd2) {
      $("#submit").removeAttr("disabled");
    } else {
      $("#submit").attr("disabled", "disabled");
    }
  }
  $(document).ready(function(){
    $(window).keyup(function(){
      confirm_passwords();
    });
    $("#clear").on("click", function(){
      $("input[type=text]").val("");
      $("input[type=email]").val("");
      $("input[type=password]").val("");
      $("input[type=file]").val("");
    })
  });
</script>
