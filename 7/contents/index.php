<h1>あなたのニュース</h1>
<h2>最新</h2>
<table class="news" id="latest">
  <thead>
    <tr>
      <th>記事タイトル</th>
      <th>投稿者</th>
      <th>投稿日</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
<hr>
<h2>フォローしてる人が話題にしていること</h2>
<table class="news" id="follow">
  <thead>
    <tr>
      <th>記事タイトル</th>
      <th>投稿者</th>
      <th>コメント日</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
<hr>
<h2>関心のありそうなこと</h2>
<table class="news" id="interest">
  <thead>
    <tr>
      <th>記事タイトル</th>
      <th>投稿者</th>
      <th>コメント日</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
<hr>
<h2>近くで起きたこと</h2>
<table class="news" id="close">
  <thead>
    <tr>
      <th>記事タイトル</th>
      <th>投稿者</th>
      <th>投稿日</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
<hr>
<h2>近くで話題になってること</h2>
<table class="news" id="popular">
  <thead>
    <tr>
      <th>記事タイトル</th>
      <th>投稿者</th>
      <th>投稿日</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>

<script>
var latest_news = <?php echo json_safe_encode($latest_news); ?>;
for (var i = 0; i < latest_news.length; i++) {
  latest_news[i]['date_obj'] = new Date(latest_news[i]['date_str']);
}
var follow_news = <?php echo json_safe_encode($follow_news); ?>;
for (var i = 0; i < follow_news.length; i++) {
  follow_news[i]['date_obj'] = new Date(follow_news[i]['date_str']);
}
var interest_news = <?php echo json_safe_encode($interest_news); ?>;
var close_news = [];
var popular_news = [];

function index_of(value, array) {
  for (var i = 0; i < array.length; i++) {
    if (array[i] == value) return i;
  }
  return false;
}
function append_news(i) {
  var category_news = [latest_news, follow_news, interest_news, close_news, popular_news];
  for (var j = 0; j < category_news[i].length; j++) {
    var news = $(news_template).clone(true);
    for (var k = 0; k < class_names.length; k++) {
      news.find("td:eq("+k+")").html(category_news[i][j][class_names[k]]);
    }
    $("#"+categories[i]+" tbody").append(news);
    $("#"+categories[i]+"latest tbody tr:eq("+j+")").attr('id', 'latest' + category_news[i][j]['news_id']);
  }
}

var categories = ['latest', 'follow', 'interest', 'close', 'popular'];
var class_names = ['title', 'author', 'date'];
var news_template = document.createElement('tr');
for (var i = 0; i < class_names.length; i++) {
  var td = document.createElement('td');
  td.className = class_names[i];
  news_template.appendChild(td);
}

if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(function(position){
    var latitude = position.coords.latitude,
      longitude = position.coords.longitude;
    $.get(
      'get_news_by_distance.php',
      {
        lat: latitude,
        lon: longitude
      },
      // 近くのニュース取得
      function(data, textStatus) {
        if (textStatus == 'success') {
          close_news = JSON.parse(data);
          for (var row = 0; row < close_news.length; row++) {
            var news_id = close_news[row]['news_id'];
            var title = close_news[row]['title'];
            close_news[row]['title'] = '<a href="news.php?id='+news_id+'">'+title+'</a>';
          }
          $("#close tbody tr").remove();
          append_news(index_of('close', categories));
        } else {
          console.log(textStatus);
        }
      },
      'html'
    );
    $.get(
      'get_popular_news.php',
      {
        lat: latitude,
        lon: longitude
      },
      //近くで話題のニュース取得
      function(data, textStatus) {
        if (textStatus == 'success') {
          popular_news = JSON.parse(data);
          for (var row = 0; row < popular_news.length; row++) {
            var news_id = popular_news[row]['news_id'];
            var title = popular_news[row]['title'];
            popular_news[row]['title'] = '<a href="news.php?id='+news_id+'">'+title+'</a>';
          }
          $("#popular tbody tr").remove();
          append_news(index_of('popular', categories));
        } else {
          console.log(textStatus);
        }
      },
      'html'
    );
  }, function(){
    console.log("位置情報取得不可");
  });
}

$(document).ready(function(){
  for (var i = 0; i < categories.length; i++) {
    append_news(i);
  }
});
</script>
