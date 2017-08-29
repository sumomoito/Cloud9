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

//$new_user変数は初期化したあと　$_POST['new_user']をうけとらないといけない気がします

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
            <p class="menu"><button class="button1" type="submit"><a href="login.php">ログイン</a></button>
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
            <?php if (empty($result_msg) !== TRUE) { ?>
                <p><?php print $result_msg; ?></p>
            <?php } ?>  
            <h3>新規会員登録</h3>
            <form method="post" enctype="multipart/form-data">
                <div class="box">
                    <?php foreach ($err_msg as $value) { ?>
                        <p><?php print $value; ?></p>
                    <?php } ?>                   
                    <label for="name">ユーザー名</label><br>
                    <input type="name" name="new_user">　(半角英数字６文字以上)<br>
                    <br>
                    <label for="password">パスワード</label><br>
                    <input type="password" name="new_password">　(半角英数字６文字以上)<br>
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