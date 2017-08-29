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

/*
// セッション変数からログイン済みか確認
// point! t_user に「存在するuser_idのみログイン済」と判定しましょう
if (isset($_SESSION['user_id'])) {
  // ログイン済みの場合、ホームページへリダイレクト
  header('Location: top.php');
  exit;
}
*/

if (isLogin() === TRUE) {
    // header(); // トップページにリダイレクト
    header('Location: top.php');
    // exit();
    exit;    
}

/* moriyama! コメントが多くて見づらくなってきたので整理のため書き直します。
// moriyama! インデントを整えましょう
// moriyama! このページは基本的にGETでしかアクセスがないのでメソッドの確認は不要です。
// ここでは下記のようなif分のほうが適切です。
if (isset($_GET['err']) === true) {
// if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // エラーメッセージ設定
    
    // moriyama! エラーメッセージを格納する配列
    $err_msg = [];
    // moriyama! https://beautiful-mothers-sumo.c9users.io/Final_project/login.php?err=err2
    // というURLのパラメータを取得するには$_GET['err']とします。
    // err=err1というパラメータをつける
    if (isset($_GET['err'])) {
        // 配列にエラーを代入
        $err_msg[] = 'アクセスエラーです。';
    } 
    // err=err2というパラメータをつける
    if (isset($_GET['err'])) {
        // moriyama! err1を習って実装してください
        // 配列にエラーを代入
        $err_msg[] = 'ログイン失敗。ユーザー名とパスワードをご確認ください。';
    }
}
*/


// moriyama! errパラメータの存在確認
$err_msg = [];
if (isset($_GET['err']) === true) {
    
    // err が err1の時
    if ($_GET['err'] === 'err1') {
        // 配列にエラーを代入
        $err_msg[] = 'アクセスエラーです。';
    } 

    // err が err2の時 
    // 上を参考に実装してみてください
    if ($_GET['err'] === 'err2') {
        $err_msg[] = 'ログイン失敗。ユーザー名とパスワードをご確認ください。';
    }
}

// isLogin()は()に何も入れなくていいが
// function isEnableUserId($user_id) { }は入れる

function isLogin() {
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
        var_dump($stmt);
        
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
            
        <form action="logout.php" class="menu">
            <?php
            // ログインしてたらログアウトを表示
            if (isLogin() === TRUE) { ?>
                <p><button class="button1" type="submit"><a href="login.php">ログアウト</a></button>
            <?php // ログインしてなければログインを表示
            } else { ?>
                <p><button class="button1" type="submit"><a href="login.php">ログイン</a></button>
            <?php } ?>            
            
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
        
        <section>
            <h3>会員登録がお済みの方</h3>
            <form method="post" action="session.php">
                <div class="box">
                <div class="inner_box">
                <?php // <!-- moriyama! $errsを使ってこの辺にエラーメッセージを表示しましょう。--!?>
                <?php foreach ($err_msg as $value) { ?>
                    <p><?php print $value; ?></p>
                <?php } ?>  
                    <label for="name">ユーザー名</label><br>
                    <input type="name" name="user_name"><br>
                    <br>
                    <label for="password">パスワード</label><br>
                    <input type="password" name="password"><br>
                </div>
                <p class="box_login_button"><button class="button2" type="submit">ログイン</button></p>
                </div>
                <input type="hidden" name="sql_login" value="insert">
                <a href="new.members.php"><p class="center_button"><button class="button2" type="submit">新規会員登録はこちら</button></p></a>
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