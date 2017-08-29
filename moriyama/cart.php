<?php
// MySQL接続情報
$host     = 'localhost';
$username = 'sumo'; // MySQLのユーザ名
$password = '';     // MySQLのパスワード
$dbname   = 'camp'; // MySQLのDB名
$charset  = 'utf8';   // データベースの文字コード
// MySQL用のDSN文字列(DSNはデータソースネームの略)
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

$img_dir = './img/';

$data = [];


try {
  // データベースに接続
  $dbh = new PDO($dsn, $username, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  
    // SQL文を作成（まだ内容直してない。t_cart）
    $sql = 'SELECT
    t_item.item_id,
    t_item.item_name,
    t_item.price,
    t_item.stock,
    t_item.status,
    t_item.category,
    t_item.color,
    t_item.description,
    t_item.img,
    t_item.created_at
    FROM t_item
    ORDER BY created_at DESC'; // 登録日時の降順(直近を上に)でソート
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $rows = $stmt->fetchAll();
    // 1行ずつ結果を配列で取得します
    $i = 0;
    foreach ($rows as $row) {
      $data[$i]['item_id']     = htmlspecialchars($row['item_id'],     ENT_QUOTES, 'UTF-8');
      $data[$i]['item_name']   = htmlspecialchars($row['item_name'],   ENT_QUOTES, 'UTF-8');
      $data[$i]['price']       = htmlspecialchars($row['price'],       ENT_QUOTES, 'UTF-8');
      $data[$i]['stock']       = htmlspecialchars($row['stock'],       ENT_QUOTES, 'UTF-8');
      $data[$i]['status']      = htmlspecialchars($row['status'],      ENT_QUOTES, 'UTF-8');
      $data[$i]['category']    = htmlspecialchars($row['category'],    ENT_QUOTES, 'UTF-8');
      $data[$i]['color']       = htmlspecialchars($row['color'],       ENT_QUOTES, 'UTF-8');
      $data[$i]['description'] = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
      $data[$i]['img']         = htmlspecialchars($row['img'],         ENT_QUOTES, 'UTF-8');
      $data[$i]['created_at']  = htmlspecialchars($row['created_at'],  ENT_QUOTES, 'UTF-8');
      $i++;
    }
    
  } catch (PDOEexception $e) {
    // 例外をスロー
    throw $e;
  }

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="sanitize.css">  
    <title>ショッピングカート</title>
</head>

<body>
    <header>
        <div class="header_margin">
        <div class="header">
        <h1><a href="#">Beautiful Mothers</a></h1>
        <p class="menu"><button class="button1" type="submit">ログイン</button>
            <a href="#"><img src="heart.png" class="small_size_menu"></a>
            <a href="#"><img src="cart.png"  class="small_size_menu"></a>
        </p>
        </div>
        <ul>
            <li><a href="#">ママファション</a></li>
            <li><a href="#">ベビー・キッズ</a></li>
            <li><a href="#">マタニティ・授乳服</a></li>
        </ul>
        </div>
    </header>
    
    <div class="oya">
    <div class="container">
    <main>
        <section>
            <h2>ショッピングカート</h2>
            <ul class="new_item_list">
                <li class="flex_box_item">
<?php foreach ($data as $value) { ?>
                    <dl class="dl_height">
                    <form method="post">
                        <dt class="dt_margin"><img class="img_size" src="<?php print $img_dir . $value['img']; ?>"></dt>
                        <dd class="dd_margin"><?php print $value['item_name']; ?></dd>
                        <dd class="dd_margin"><?php print $value['price']; ?>円</dd>
                    </dl>
<?php } ?>
                </li>                
            </ul>
        </section>
        
    </main>
    </div>
    </div>
    
    <footer>
        <p><small>Copyright &copy; Beautiful Mothers All Rights Reserved.</small></p>
    </footer>

</body>
</html>