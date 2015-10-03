<h1><?php echo h($title); ?></h1>
<?php if ($img): ?>
  <img src="img/<?php echo $img; ?>" />
<?php endif; ?>
<p><?php echo h($author); ?></p>
<article><?php echo $article; ?></article>
<form action="unmark_read.php" method="post">
  <input name="news_id" type="hidden" value="<?php echo $news_id; ?>">
  <input type="submit" value="未読にする">
</form>
<?php if ($is_owner): ?>
  <a href="delete_news.php?id=<?php echo $news_id; ?>"><button>削除</button></a>
<?php else: ?>
  <form action="share.php?id=<?php echo $news_id; ?>" method="post">
    <input name="lat" type="hidden" value="">
    <input name="lon" type="hidden" value="">
    <input name="valid" type="hidden" value="<?php echo $valid; ?>">
    <input type="submit" value="<?php echo $share_button ?>">
  </form>
<?php endif; ?>
<form action="comment.php?id=<?php echo $news_id; ?>" method="post">
  <textarea name="comment" required></textarea>
  <input name="lat" type="hidden" value="">
  <input name="lon" type="hidden" value="">
  <input type="submit" value="コメントする">
</form>
<?php foreach ($comments as $comment): ?>
  <hr>
  <p class="user_name"><?php echo $comment['name']; ?></p><br>
  <p class="comment"><?php echo $comment['text']; ?></p><br>
  <?php if ($id == $comment['user_id']): ?>
    <a href="delete_comment.php?id=<?php echo $comment['id']; ?>"><button>削除</button></a>
  <?php endif; ?>
<?php endforeach; ?>
<a href="index.php">ニュース一覧へ戻る</a>

<script>
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(function(position){
    $("input[name=lat]").val(position.coords.latitude);
    $("input[name=lon]").val(position.coords.longitude);
  }, function(){
    console.log("位置情報取得不可");
  });
}
</script>
