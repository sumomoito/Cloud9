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

$sql_delete = '';
$sql_completion = '';
$result_msg = '';
$data       = [];
$err_msg    = [];
$amount     = 1;
$user_id    = 1;


try {
    // データベースに接続
    $dbh = new PDO($dsn, $username, $password);
} catch (Eexception $e) {
die('データベースの接続に失敗しました。');
}
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
// セッション開始
session_start();

function isLogin($dbh) {
    if (empty($_SESSION['user_id'])) {
        return false;
    }
    $user_id = $_SESSION['user_id'];
    // SQL文を作成
    $sql = 'SELECT
                t_user.user_id
            FROM 
                t_user
            WHERE
                user_id = ' . $user_id;
    try {
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
    } catch(Exception $e) {
        die('失敗しました。');
    }
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $rows = $stmt->fetchAll();
                
    if (empty($rows)) {
        return false;
    }     
    // ここのタイミングではログイン済みと判定
    return true;
}

// もしリクエストメソッドがPOSTだったら？？
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // カートから削除処理スタート！！
    if (isset($_POST['sql_delete']) === TRUE) {
      $sql_delete = $_POST['sql_delete'];  
    }    
    if ($sql_delete === 'delete') {
        // item_idを初期化
        $item_id = null;
        if (isset($_POST['item_id']) === TRUE) {
           $item_id = trim($_POST['item_id']);
        }
        if (is_numeric($item_id) === FALSE) {
          $err_msg[] = '数値ではありません。'; 
        }
        if (count($err_msg) === 0) {

            // SQL文を作成
            $sql = 'DELETE
                    FROM
                        t_cart
                    WHERE
                        item_id = ' . $item_id;
            
            try {
                // SQL文を実行する準備
                $stmt = $dbh->prepare($sql);
            } catch(Exception $e) {
                die('失敗しました。');
            }
            // SQLを実行
            $stmt->execute();
            
            $result_msg = '商品を削除しました';
        }
    }
}

// SQL文を作成
$sql = 'SELECT
            t_cart.cart_id,
            t_cart.amount,
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
    $data[$i]['cart_id']     = htmlspecialchars($row['cart_id'],     ENT_QUOTES, 'UTF-8');
    $data[$i]['amount']      = htmlspecialchars($row['amount'],      ENT_QUOTES, 'UTF-8');
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
    <title>ショッピングカート</title>
</head>

<body>
    <header>
        <div class="header_margin">
        <div class="header">
        <h1><a href="top.php">Beautiful Mothers</a></h1>
        
            <?php
            // ログインしてたらログアウトを表示(未完成)
            if (isLogin($dbh) === TRUE) { ?>
                <p class="login.menu"><button class="button1" type="submit"><a href="login.php">ログアウト</a></button></p>
            <?php // ログインしてなければログインを表示
            } else { ?>
                <p class="login.menu"><button class="button1" type="submit"><a href="login.php">ログイン</a></button></p>
            <?php } ?>
            <p class="menu">   
                <a href="favorite.php"><img src="heart.png" class="small_size_menu"></a>
                <a href="cart.php"><img src="cart.png"  class="small_size_menu"></a>
            </p>        
        
        </div>
        <ul>
            <li><a href="search.php?genre=0">ママファション</a></li>
            <li><a href="search.php?genre=1">ベビー・キッズ</a></li>
            <li><a href="search.php?genre=2">マタニティ・授乳服</a></li>
        </ul>
        </div>
    </header>
    
    <div class="oya">
    <div class="container">
    <main>
        
        <?php if (empty($result_msg) !== TRUE) { ?>
            <p><?php print $result_msg; ?></p>
        <?php } ?>
        <?php foreach ($err_msg as $value) { ?>
            <p><?php print $value; ?></p>
        <?php } ?>
        
        <section>
            <div class="icon">
                <h2>Shopping Cart</h2>
            </div>
            <table>
                <tr>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>削除</th>
                </tr>
<?php foreach ($data as $value) { ?>
                <tr>
                    <td><img class="img_size" src="<?php print $img_dir . $value['img']; ?>"></td>
                    <td class="name_width"><?php print $value['item_name']; ?></td>
                    <td class="price_right"><?php print $value['price']; ?>円</td>
                    <td class="delete_size">
                        <form method="post">
                            <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
                            <input type="hidden" name="sql_delete" value="delete">
                            <input type="submit" value="削除" class="delete_button">
                        </form>
                    </td>
                </tr>                    
<?php } ?>
            </table>

<?php 
    $total_amount = 0;
    foreach ($data as $value) { 
    $total_amount += $value['amount']; 
}
    $total_price = 0;
    foreach ($data as $value) { 
    $total_price += $value['price']; 
} ?>
            <h3 class="top_line">点数：<?php print $total_amount; ?>点<br>　　　購入金額：<?php print $total_price; ?>円</h3>
            <form method="post" action="thank.you.php">
                <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
                <input type="hidden" name="sql_completion" value="completion">
                <p class="center_button"><button class="button2" type="submit">購入を確定する</button></p>
            </form>
        </section>
        
    </main>
    </div>
    </div>
    
    <footer>
        <p><small>Copyright &copy; Beautiful Mothers All Rights Reserved.</small></p>
    </footer>

</body>
</html>