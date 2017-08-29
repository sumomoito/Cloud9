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
} catch (Eexception $e) {
die('データベースの接続に失敗しました。');
}
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  
    // SQL文を作成
    $sql = 'SELECT
                t_cart.item_id,
                t_item.item_name,
                t_item.price,
                t_item.stock,
                t_item.status,
                t_item.category,
                t_item.color,
                t_item.description,
                t_item.img,
                t_item.created_at
            FROM
                t_item
                INNER JOIN t_cart
                ON t_item.item_id = t_cart.item_id
            ORDER BY
                t_cart.created_at DESC'; // 登録日時の降順(直近を上に)でソート
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

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="sanitize.css">  
    <title>購入完了画面</title>
</head>

<body>
    <header>
        <div class="header_margin">
        <div class="header">
        <h1><a href="top.php">Beautiful Mothers</a></h1>
        <p class="menu"><button class="button1" type="submit"><a href="login.php">ログイン</a></button>
            <a href="favorite.php"><img src="heart.png" class="small_size_menu"></a>
            <a href="cart.php"><img src="cart.png"  class="small_size_menu"></a>
        </p>
        </div>
        <ul>
            <li><a href="g.mam.fasion.php">ママファション</a></li>
            <li><a href="g.baby.kids.php">ベビー・キッズ</a></li>
            <li><a href="g.maternity.php">マタニティ・授乳服</a></li>
        </ul>
        </div>
    </header>
    
    <div class="oya">
    <div class="container">
    <main>
        
        <section>
            <h2>ご購入ありがとうございます</h2>
            <table>
                <tr>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                </tr>
<?php foreach ($data as $value) { ?>
                <tr>
                <form method="post">
                    <td><img class="img_size" src="<?php print $img_dir . $value['img']; ?>"></td>
                    <td class="name_width"><?php print $value['item_name']; ?></td>
                    <td class="price_right"><?php print $value['price']; ?>円</td>
                </form>
                </tr>                    
<?php } ?>
            </table>
<?php 
    $total = 0;
    foreach ($data as $value) { 
    $total += $value['price']; ?>
<?php } ?>
            <h3 class="top_line">合計額　<?php print $total; ?>円</h3>
        </section>
        
    </main>
    </div>
    </div>
    
    <footer>
        <p><small>Copyright &copy; Beautiful Mothers All Rights Reserved.</small></p>
    </footer>

</body>
</html>