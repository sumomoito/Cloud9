<?php
// MySQL接続情報
$host     = 'localhost';
$username = 'sumo'; // MySQLのユーザ名
$password = '';     // MySQLのパスワード
$dbname   = 'camp'; // MySQLのDB名
// MySQL用のDSN文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host;

$sql_new    = '';
$result_msg = '';
$data       = [];
$err_msg    = [];


if (isset($_POST['sql_new']) === TRUE) {
    $sql_new = $_POST['sql_new'];  
}

if ($sql_new === 'insert') { 
    $new_user     = '';
    $new_password = '';
    $new_user     = $_POST['new_user'];
    $new_password = $_POST['new_password'];
    // 正規表現
    $new_user_regex = '/^[0-9a-zA-Z]{6,}$/';
    $new_password_regex = '/^[0-9a-zA-Z]{6,}$/';
    
    if (isset($_POST['new_user']) === TRUE) {
        if (preg_match($new_user_regex, $new_user)) {
        $new_user = trim($_POST['new_user']);
        } else {
            $err_msg[] = 'ユーザー名は半角英数字6文字以上で登録してください';
        }
    }
  
    if (isset($_POST['new_password']) === TRUE) {
        if (preg_match($new_password_regex, $new_password)) {
        $new_password = trim($_POST['new_password']);
        } else {
            $err_msg[] = 'パスワードは半角英数字6文字以上で登録してください';
        }
    }
}


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
    
    if (count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
        if ($sql_new === 'insert') {
            // 現在日時を取得
            $now_date = date('Y-m-d H:i:s');
            // SQL文を作成
            $sql = 'INSERT INTO t_user (user_name, password, created_at, updated_at) VALUES (?, ?, ?, ?)';
            
            try {
                // SQL文を実行する準備
                $stmt = $dbh->prepare($sql);
            } catch(Exception $e) {
                die('失敗しました。');
            }
            
            // SQL文のプレースホルダに値をバインド
            $stmt->bindValue(1, $new_user,     PDO::PARAM_STR);
            $stmt->bindValue(2, $new_password, PDO::PARAM_STR);
            $stmt->bindValue(3, $now_date,     PDO::PARAM_STR);
            $stmt->bindValue(4, $now_date,     PDO::PARAM_STR);
            // SQLを実行
            $stmt->execute();
            
            $result_msg ='会員登録が完了しました';
        }
    }

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="new.members.css">
    <link rel="stylesheet" href="sanitize.css">
    <title>新規会員登録</title>
</head>

<body>
    <header>
        <div class="header_margin">
        <div class="header">
            <h1><a href="top.php">Beautiful Mothers</a></h1>
            
            <?php
            // ログインしてたらログアウトを表示
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
        
        <section>
            <div class="icon">
                <h2>新規会員登録</h2>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="box">
                    <?php foreach ($err_msg as $value) { ?>
                        <p><?php print $value; ?></p>
                    <?php } ?>                   
                    <label for="name">ユーザー名</label><br>
                    <input type="name" name="new_user" size="30">　(半角英数字６文字以上)<br>
                    <br>
                    <label for="password">パスワード</label><br>
                    <input type="password" name="new_password" size="30">　(半角英数字６文字以上)<br>
                </div>
                <input type="hidden" name="sql_new" value="insert">
                <p class="center_button"><button class="button2" type="submit">登録する</button></p>
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