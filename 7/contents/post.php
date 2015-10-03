<form class="form-post" action="post_execute.php" method="post" enctype="multipart/form-data">
  <h2 class="form-post-heading">記事を投稿する</h2>
  <label for="title">タイトル</label><br>
  <input name="title" type="text" class="form-control" value="<?php echo $title; ?>" required><br>
  <label for="article">記事</label>
  <textarea name="article" value="<?php echo $article; ?>" rows="7"></textarea><br>
  <label for="photo">写真</label>
  <input name="photo" type="file" class="form-control"><br>
  <input name="lat" type="hidden" value="">
  <input name="lon" type="hidden" value="">
  <input type="submit" class="btn btn-lg btn-primary btn-block" value="投稿">
</form>

<script>
$(document).on('ready', function(){
  // WYSIWYGセット
  tinymce.init({
    selector: "textarea"
  });
  // 位置情報取得
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position){
      $("input[name=lat]").val(position.coords.latitude);
      $("input[name=lon]").val(position.coords.longitude);
    }, function(){
      console.log("位置情報取得不可");
    });
  }
});
</script>
