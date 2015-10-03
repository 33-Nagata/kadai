<p>ユーザー名：<?php echo h($name); ?></p>
<?php if ($is_owner): ?>
  <p>メールアドレス：<?php echo h($email); ?></p>
<?php endif; ?>
<?php if ($img != null): ?>
  <p>プロフィール写真：<img src="img/<?php echo $img; ?>" /></p>
<?php endif; ?>
<p>関心のあるワード：<?php echo implode(', ', $interests); ?></p>
<?php if ($is_owner): ?>
  <a href="update_user.php?id=<?php echo $request_id; ?>"><button>プロフィール変更</button></a>
  <a href="post.php"><button>ニュース投稿</button></a>
<?php else: ?>
  <form action="follow.php" method="post">
    <input name="id" type="hidden" value="<?php echo $request_id; ?>">
    <input type="submit" value="<?php echo $follow_button; ?>">
  </form>
<?php endif; ?>
<hr>
<p>最近の投稿</p>
<?php if ($recent_news == false || count($recent_news) == 0): ?>
  <p>なし</p>
<?php else: ?>
  <?php foreach ($recent_news as $news): ?>
    <p><a href="news.php?id=<?php echo $news['id']; ?>"><?php echo $news['title']; ?></a></p>
  <?php endforeach; ?>
<?php endif; ?>
<hr>
<p>最近のシェア</p>
<?php if ($recent_share == false || count($recent_share) == 0): ?>
  <p>なし</p>
<?php else: ?>
  <?php foreach ($recent_share as $news): ?>
    <p><a href="news.php?id=<?php echo $news['id']; ?>"><?php echo $news['title']; ?></a></p>
  <?php endforeach; ?>
<?php endif; ?>
