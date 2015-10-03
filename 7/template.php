<?php
if (!isset($head_title)) $head_title = '';
if (!isset($meta_description)) $meta_description = '';
if (!isset($meta_author)) $meta_author = '';
if (!isset($meta_keywords)) $meta_keywords = '';
if (!isset($icon)) $icon = '';
$css_link = '';
if (isset($css)) {
  if (!is_array($css)) {
    $css_link = "<link rel='stylesheet' href='{$css}' />";
  } else {
    foreach ($css as $c) {
      $css_link .= "<link rel='stylesheet' href='{$c}' />";
    }
  }
}
$js_link = '';
if (isset($js)) {
  if (!is_array($js)) {
    $js_link = "<script src='{$js}'></script>";
  } else {
    foreach ($js as $j) {
      $js_link .= "<script src='{$j}'></script>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <title><?php echo $head_title; ?></title>
  <meta charset="UTF-8">
  <meta name="description" content="<?php echo $meta_description; ?>" />
  <meta name="author" content="<?php echo $meta_author; ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="keywords" content="<?php echo $meta_keywords; ?>" />

  <link rel="shortcut icon" href="<?php echo $icon; ?>" />

  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <?php echo $css_link; ?>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <?php echo $js_link; ?>
</head>
<body>
  <header class="header">
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a href="index.php" class="navbar-brand">News Site</a>
        </div>
        <a href="login.php" class="btn btn-primary btn-sm pull-right btn-header">Logout</a>
        <a href="resister.php" class="btn btn-primary btn-sm pull-right btn-header">Sign Up/In</a>
        <a href="user.php" class="btn btn-primary btn-sm pull-right btn-header">My Page</a>
      </div>
    </div>
  </header>
  <div class="container content">
    <main>
      <!-- <?php echo $message; ?> -->
      <?php include($content); ?>

    </main>
  </div>
  <footer class="footer">
    <div class="navbar navbar-inverse">
      <div class="container">
        <p class="text-center text-muted">Copyright ___</p>
      </div>
    </div>
  </footer>
</body>
</html>

<!--
<h1>Your News</h1>
<nav class="navbar">
  <ul class="nav navbar-nav">
    <li class="active"><a href="#">Tab 1</a></li>
    <li><a href="#">Tab 2</a></li>
    <li><a href="#">Tab 3</a></li>
  </ul>
</nav>
<article>
  <h2>Title</h2>
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla, ut commodo diam libero vitae erat. Aenean faucibus nibh et justo cursus id rutrum lorem imperdiet. Nunc ut sem vitae risus tristique posuere.</p>
  <hr>
  <h2>Title</h2>
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla, ut commodo diam libero vitae erat. Aenean faucibus nibh et justo cursus id rutrum lorem imperdiet. Nunc ut sem vitae risus tristique posuere.</p>
</article>
-->
