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

$sql_serch     = '';
$fashion_serch = '';
$category      = '';
$color         = '';
$result_msg    = '';
$data          = [];
$err_msg       = [];
$amount        = 1;
$user_id       = 1; // 一旦コメントアウト！！はここだけしない。$user_idがないってエラー出たから　

try {
    // データベースに接続
    $dbh = new PDO($dsn, $username, $password);
} catch(Exception $e) {
die('データベースの接続に失敗しました。');
}
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

// セッション開始
session_start();

//検索ボタンをおされたら
if($sql_serch === 'fashion_serch') {
    // $create_datetime = date('Y-m-d H:i:s');
    
    if(isset($_POST['category']) === TRUE){
        $category = preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $_POST['category']);
    }
/*  金額指定はまだできない
    if(isset($_POST['min_price']) === TRUE){
        $min_price = preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $_POST['min_price']);
    }
    if(isset($_POST['max_price']) === TRUE){
        $max_price = preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $_POST['max_price']);
    }
*/
    if(isset($_POST['color']) === TRUE){
        $color = preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $_POST['color']);
    }
    // エラーチェックをここでおこなう
}

    // SQL文を作成（新着順にt_itemに登録した商品を全部呼び出してる）
    $sql = 'SELECT
                t_item.item_id,
                t_item.item_name,
                t_item.price,
                t_item.stock,
                t_item.status,
                t_item.genre,
                t_item.category,
                t_item.color,
                t_item.description,
                t_item.img,
                t_item.created_at
            FROM 
                t_item
            WHERE
                genre = 0
            ORDER BY
                created_at DESC'; // 登録日時の降順(直近を上に)でソート
    
    // 洋服のカテゴリを指定して検索！            
    if($category !== '') {
        $sql .= ' AND t_item.category = :category';
    }
    // 洋服のカラーを指定して検索！            
    if($color !== '') {
        $sql .= ' AND t_item.color = :color';
    }
                
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    
    // SQL文のプレースホルダに値をバインド
    if($category !== '') {
        $stmt->bindValue(':category', $category);
    }
    if($color !== '') {
        $stmt->bindValue(':color', $color);
    }
    
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
        $data[$i]['genre']       = htmlspecialchars($row['genre'],       ENT_QUOTES, 'UTF-8');
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
    <link rel="stylesheet" href="g.maternity.css">
    <link rel="stylesheet" href="sanitize.css">    
    <title>ママファッション</title>
</head>

<body>
    <header>
        <div class="header_margin">
        <div class="header">
        <h1><a href="top.php">Beautiful Mothers</a></h1>
            
            <?php /* 一時保留ちゅう
            // ログインしてたらログアウトを表示
            if (isLogin($dbh) === TRUE) { ?>
                <p class="login.menu"><button class="button1" type="submit"><a href="login.php">ログアウト</a></button></p>
            <?php // ログインしてなければログインを表示
            } else { ?>
                <p class="login.menu"><button class="button1" type="submit"><a href="login.php">ログイン</a></button></p>
            <?php } */ ?>
            <p class="menu">   
                <a href="favorite.php"><img src="heart.png" class="small_size_menu"></a>
                <a href="cart.php"><img src="cart.png"  class="small_size_menu"></a>
            </p>
            
        </div>
        <ul>
            <li><a href="g.mam.fashion.php">ママファション</a></li>
            <li><a href="g.baby.kids.php">ベビー・キッズ</a></li>
            <li><a href="g.maternity.php">マタニティ・授乳服</a></li>
        </ul>
        </div>
    </header>
    
    <div class="oya">
    <div class="container">
    <main>
        
        <section class="width600px">
            <form method="post" action = "serch.php" enctype="multipart/form-data">
                <div class="box">
                <div class="inner_box">
                    <p>カテゴリ　　<select name="category"></p>
                        <option value="">選択してください</option>
                        <option value="0">トップス</option>
                        <option value="1">アウター</option>
                        <option value="2">パンツ</option>
                        <option value="3">スカート</option>
                        <option value="4">ワンピース</option>
                        <option value="5">インナー</option>
                        <option value="6">その他</option>
                    </select>
                    <p><label>　価格　　　<input type="text" name="min_price" value=""></label>円　<label>〜　<input type="text" name="max_price" value=""></label>円</p>
                    <p>カラー別　　<select name="color"></p>
                        <option value="">選択してください</option>
                        <option value="0">ホワイト系</option>
                        <option value="1">ブラック系</option>
                        <option value="2">ブラウン系</option>
                        <option value="3">レッド系</option>
                        <option value="4">ブルー系</option>
                        <option value="5">イエロー系</option>
                        <option value="6">グリーン系</option>
                    </select>
                </div>
                <input type="hidden" name="sql_serch" value="fashion_serch">
                <p class="serch_button"><button class="button2" type="submit">検索する</button></p>
            </form>
        </section>
        
        <?php if (empty($result_msg) !== TRUE) { ?>
            <p><?php print $result_msg; ?></p>
        <?php } ?>
        <?php foreach ($err_msg as $value) { ?>
            <p><?php print $value; ?></p>
        <?php } ?>
        
        <section>
            <ul class="new_item_list">
                <li class="flex_box_item">
<?php foreach ($data as $value) { ?>
                <?php if(isset($value['genre'])) { ?>
                    <?php if($value['genre'] === 0) { ?>
                    <dl class="dl_height">
                        <dt class="dt_margin"><img class="img_size" src="<?php print $img_dir . $value['img']; ?>"></dt>
                        <dd class="dd_margin name_font"><?php print $value['item_name']; ?></dd>
                        <dd class="dd_margin price_font"><?php print $value['price']; ?>円</dd>
                        <dd class="cf_center">
                            <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
                            <input type="hidden" name="sql_kind" value="insert">
                            <input type="submit" value="カートに追加" class="cf_button cart"></form>
                        </dd>
                        <dd class="cf_center">
                            <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
                            <input type="hidden" name="sql_favorite" value="insert">
                            <input type="submit" value="お気に入りへ" class="cf_button heart"></form>
                        </dd>
                    </dl>
                    <?php } ?>
                <?php } ?>    
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





