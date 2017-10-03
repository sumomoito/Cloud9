<?php
// MySQL接続情報
$host     = 'localhost';
$username = 'sumo'; // MySQLのユーザ名
$password = '';     // MySQLのパスワード
$dbname   = 'camp'; // MySQLのDB名
$charset  = 'utf8';   // データベースの文字コード
// MySQL用のDSN文字列(DSNはデータソースネームの略)
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

// セッション開始
session_start();

try {
    // データベースに接続
    $dbh = new PDO($dsn, $username, $password);
} catch(Exception $e) {
die('データベースの接続に失敗しました。');
}
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);



// errパラメータの存在確認
$err_msg = [];
if (isset($_GET['err']) === true) {
    
    // err が err1の時
    if ($_GET['err'] === 'err1') {
        // 配列にエラーを代入
        $err_msg[] = 'アクセスエラーです。';
    } 
    // err が err2の時
    if ($_GET['err'] === 'err2') {
        $err_msg[] = 'ユーザー名またはパスワードが正しくありません';
    }
}


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
                
    /*
     if ($_SESSION['user_id']が有効なIDであることを確認) {
        // 無効IDだった時の処理
        return false;
     }
     */
    if (empty($rows)) {
        return false;
    }     
    
    // ここのタイミングではログイン済みと判定
    return true;
}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="sanitize.css">    
    <title>ログイン</title>
</head>

<body>
    <header>
        <div class="header_margin">
        <div class="header">
        <h1><a href="top.php">Beautiful Mothers</a></h1>
            
            <?php
            // ログインしてたらログアウトを表示(未完成)
            if (isLogin($dbh) === TRUE) { ?>
                <p class="login.menu"><button class="button1" type="submit"><a href="logout.php">ログアウト</a></button></p>
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
        
        <section>
            <div class="icon">
                <h2>会員登録がお済みの方</h2>
            </div>
            <form method="post" action="session.php">
                <div class="box">
                <div class="inner_box">
                <?php // <!-- moriyama! $errsを使ってこの辺にエラーメッセージを表示しましょう。--!?>
                <?php foreach ($err_msg as $value) { ?>
                    <p><?php print $value; ?></p>
                <?php } ?>
                    <label for="name">ユーザー名</label><br>
                    <input type="name" name="user_name" size="30">　(半角英数字６文字以上)<br>
                    <br>
                    <label for="password">パスワード</label><br>
                    <input type="password" name="password" size="30">　(半角英数字６文字以上)<br>
                </div>
                <p class="box_login_button"><button class="button2" type="submit">ログイン</button></p>
                </div>
                <input type="hidden" name="sql_login" value="insert">
                <p class="center_button"><button class="button2" type="submit"><a href="new.members.php">新規会員登録はこちら</a></button></p></a>
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