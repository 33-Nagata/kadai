<?php
if (array_key_exists('id', $_GET) && array_key_exists('email', $_GET)) {
  $id = $_GET['id'];
  $email = $_GET['email'];

  $pdo = new PDO('mysql:host=localhost;dbname=cs_academy;charset=utf8', 'root', '');
  $sql = "select email from entry WHERE id=".$id." LIMIT 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if ($email == $result[0]['email']) {
    $sql = "UPDATE entry SET attend=1,update_date=NOW() WHERE id=".$id;
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    echo 'お申し込みを受け付けました';
  } else {
    echo 'URLに誤りがあります';
  }
  $pdo = null;
} else {
  echo 'URLに誤りがあります';
}
?>
