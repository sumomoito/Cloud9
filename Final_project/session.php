<?php
// MySQL接続情報
$host     = 'localhost';
$username = 'sumo'; // MySQLのユーザ名
$password = '';     // MySQLのパスワード
$dbname   = 'camp'; // MySQLのDB名
$charset  = 'utf8';   // データベースの文字コード
// MySQL用のDSN文字列(DSNはデータソースネームの略)
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

$data = [];

try {
    // データベースに接続
    $dbh = new PDO($dsn, $username, $password);
} catch(Exception $e) {
die('データベースの接続に失敗しました。');
}
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    


// リクエストメソッド確認
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  // POSTでなければログインページへリダイレクト
  // moriyama! POST以外のアクセスでは、err=err1というパラメータをつけてリダイレクト
  header('Location: login.php?err=err1');
  exit;
}


// もしリクエストメソッドがPOSTだったら？？
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  
  // セッション開始
  session_start();
  // POST値取得
  $user_name  = get_post_data('user_name');  // ユーザー名
  $password = get_post_data('password'); // パスワード
  
  
  // SQL文を作成
  // moriyama!文字列での連結はちょっとわかりづらかったですね。
  // プレースホルダーを使うようにしましょう
  $sql = 'SELECT
              t_user.user_id,
              t_user.user_name,
              t_user.password,
              t_user.created_at
          FROM 
              t_user
          WHERE
              user_name = :user_name
              AND password = :password';
              
  try {
      // SQL文を実行する準備
      $stmt = $dbh->prepare($sql);
  } catch(Exception $e) {
      die('失敗しました。');
  }
  // moriyama! urser_name / password に $stmt->bindValue() しましょう。
  // SQL文のプレースホルダに値をバインド
  $stmt->bindValue(1, $user_name, PDO::PARAM_STR);
  $stmt->bindValue(2, $password,  PDO::PARAM_STR);
  // SQLを実行
  $stmt->execute();
  // レコードの取得
  $rows = $stmt->fetchAll();
  
  if (empty($rows)) {
    // このif内はログイン失敗
    // 行を取得できない＝ユーザ名とパスワードが間違っている
    // moriyama! ログインエラー時にはerr=err2というパラメータをつけてリダイレクト
    header('Location: login.php?err=err2');
    exit;
  }
}

// 行が取得できているので、1行目を$dataに代入
$data = $rows[0];

// 登録データを取得できたか確認
if (isset($data['user_id']) === TRUE) {
  // セッション変数にuser_idを保存
  $_SESSION = $data['user_id'];
  // ログイン済みユーザのホームページへリダイレクト
  header('Location: top.php');
  exit;
} else {
  // ログインページへリダイレクト
  header('Location: login.php');
  exit;
}
//POSTデータから任意データの取得
function get_post_data($key) {
  $str = '';
  if (isset($_POST[$key])) {
    $str = $_POST[$key];
  }
  return $str;
}
?>