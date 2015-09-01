<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>アンケート回答</title>
</head>
<body>
  <h1>アンケートにご回答ください</h1>
  <form action="confirm_enq.php" method="post">
   <label for="name">名前：</label>
    <input name="name" type="text">
    <br>
    <label for="email">Eメール：</label>
    <input name="email" type="email">
    <br>
    <label for="age">年齢：</label>
    <input name="age" type="number">
    <br>
    <label for="sex">性別：</label>
    <select name="sex">
      <option value="1">男性</option>
      <option value="2">女性</option>
      <option value="3">未回答</option>
    </select>
    <br>
    <label for="hobby">趣味：</label>
    <input type="checkbox" name="hobby" value="1">スポーツ
    <input type="checkbox" name="hobby" value="2">読書
    <input type="checkbox" name="hobby" value="3">パソコン／インターネット
    <input type="checkbox" name="hobby" value="4">旅行
    <input type="checkbox" name="hobby" value="5">音楽鑑賞
    <input type="checkbox" name="hobby" value="6">料理
    <input type="checkbox" name="hobby" value="7">ショッピング
    <input type="checkbox" name="hobby" value="8">なし
    <br>
    <input type="submit" value="回答する">
  </form>
</body>
</html>
