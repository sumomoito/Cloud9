<?php
// MySQL接続情報
$host     = 'localhost';
$username = 'sumo'; // MySQLのユーザ名
$password = '';     // MySQLのパスワード
$dbname   = 'camp'; // MySQLのDB名
// MySQL用のDSN文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host;

$img_dir = './img/';

$sql_kind   = '';
$result_msg = '';
$data       = [];
$err_msg    = [];

if (isset($_POST['sql_kind']) === TRUE) {
  $sql_kind = $_POST['sql_kind'];  
}

if ($sql_kind === 'insert') {
  
  $new_name        = '';
  $new_price       = '';
  $new_stock       = '';
  $new_status      = '';
  // genreはあとから足した為「product.php」にしかまだ入ってない
  $new_genre       = '';
  $new_category    = '';
  $new_color       = '';
  $new_description = '';
  $new_img         = 'no_image.png';
  
  if (isset($_POST['new_name']) === TRUE) {
    $new_name = trim($_POST['new_name']);
  }
  
  if (isset($_POST['new_price']) === TRUE) {
    $new_price = trim($_POST['new_price']);
  }
  
  if (isset($_POST['new_stock']) === TRUE) {
    $new_stock = trim($_POST['new_stock']);
  }
  
  if (isset($_POST['new_status']) === TRUE) {
    $new_status = trim($_POST['new_status']);
  }
  
  if (isset($_POST['new_genre']) === TRUE) {
    $new_genre = trim($_POST['new_genre']);
  }  
  
  if (isset($_POST['new_category']) === TRUE) {
    $new_category = trim($_POST['new_category']);
  }
  
  if (isset($_POST['new_color']) === TRUE) {
    $new_color = trim($_POST['new_color']);
  }
  
  if (isset($_POST['new_description']) === TRUE) {
    $new_description = trim($_POST['new_description']);
  }
  
  
  //  HTTP POST でファイルがアップロードされたか確認
  if (is_uploaded_file($_FILES['new_img']['tmp_name']) === TRUE) {
    
    $new_img = $_FILES['new_img']['name'];
    
    // 画像の拡張子取得
    $extension = pathinfo($new_img, PATHINFO_EXTENSION);
    
    // 拡張子チェック
    if ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png') {
      
      // ユニークID生成し保存ファイルの名前を変更
      $new_img = md5(uniqid(mt_rand(), true)) . '.' . $extension;
      
      // 同名ファイルが存在するか確認
      if (is_file($img_dir . $new_img) !== TRUE) {
        
        // ファイルを移動し保存
        if (move_uploaded_file($_FILES['new_img']['tmp_name'], $img_dir . $new_img) !== TRUE) {
          $err_msg[] = 'アップロードに失敗しました';
        }
        
      // 生成したIDがかぶることは通常ないため、IDの再生成ではなく再アップロードを促すようにした
      } else {
        $err_msg[] = 'アップロード失敗。再度お試しください。';
      }
      
    } else {
      $err_msg[] = 'ファイル形式が異なります。画像はJPEGかPNGのみ利用可能です。';
    }
    
  } else {
    $err_msg[] = 'ファイルを選択してください';
  }
}

try {
  // データベースに接続
  $dbh = new PDO($dsn, $username, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  
  if (count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if ($sql_kind === 'insert') {
      // 現在日時を取得
      $now_date = date('Y-m-d H:i:s');
      try {
        // SQL文を作成
        $sql = 'INSERT INTO t_item (item_name, price, stock, status, genre, category, color, description, img, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1,  $new_name,        PDO::PARAM_STR);
        $stmt->bindValue(2,  $new_price,       PDO::PARAM_INT);
        $stmt->bindValue(3,  $new_stock,       PDO::PARAM_INT);
        $stmt->bindValue(4,  $new_status,      PDO::PARAM_INT);
        $stmt->bindValue(5,  $new_genre,       PDO::PARAM_INT);
        $stmt->bindValue(6,  $new_category,    PDO::PARAM_INT);
        $stmt->bindValue(7,  $new_color,       PDO::PARAM_INT);
        $stmt->bindValue(8,  $new_description, PDO::PARAM_STR);
        $stmt->bindValue(9,  $new_img,         PDO::PARAM_STR);
        $stmt->bindValue(10, $now_date,        PDO::PARAM_STR);
        $stmt->bindValue(11, $now_date,        PDO::PARAM_STR);
        // SQLを実行
        $stmt->execute();
        
        $result_msg = '追加成功';
      } catch (PDOException $e) {
        // 例外をスロー
        throw $e;
      }
    }
  }
  
  try {
    // SQL文を作成
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
              t_item.img
            FROM t_item';
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
      $data[$i]['genre']       = htmlspecialchars($row['genre'],       ENT_QUOTES, 'UTF-8');
      $data[$i]['category']    = htmlspecialchars($row['category'],    ENT_QUOTES, 'UTF-8');
      $data[$i]['color']       = htmlspecialchars($row['color'],       ENT_QUOTES, 'UTF-8');
      $data[$i]['description'] = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
      $data[$i]['img']         = htmlspecialchars($row['img'],         ENT_QUOTES, 'UTF-8');
      $i++;
    }
    
  } catch (PDOEexception $e) {
    // 例外をスロー
    throw $e;
  }
} catch (PDOEexception $e) {
  $err_msg[] = '予期せぬエラーが発生しました。管理者へお問い合わせください。'.$e->getMessage();
}

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

?>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="sanitize.css">  
    <title>商品管理画面</title>
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
        <?php foreach ($err_msg as $value) { ?>
            <p><?php print $value; ?></p>
        <?php } ?>
        
        <section>
            <h2>新規商品追加</h2>
            <div class="width720px">
            <form method="post" enctype="multipart/form-data">
              <div class="box">
              <div class="inner_box">
                <p><label>商品名　　<input type="text" name="new_name" value=""></label></p>
                <p><label>価格　　　<input type="text" name="new_price" value=""></label> 円　(半角数字)</p>
                <p><label>個数　　　<input type="text" name="new_stock" value=""></label> 点　(半角数字)</p>
                <p>公開状態　<select name="new_status"></p>
                    <option value="0">非公開</option>
                    <option value="1">公開</option>
                </select>
                <p>ジャンル　<select name="new_genre"></p>
                    <option value="">選択してください</option>
                    <option value="0">ママファッション</option>
                    <option value="1">ベビー・キッズ</option>
                    <option value="2">マタニティ・授乳服</option> 
                </select>
                <p>カテゴリ　<select name="new_category"></p>
                    <option value="">選択してください</option>
                    <option value="0">トップス</option>
                    <option value="1">アウター</option>
                    <option value="2">パンツ</option>
                    <option value="3">スカート</option>
                    <option value="4">ワンピース</option>
                    <option value="5">インナー</option>
                    <option value="6">その他</option>
                </select>
                <p>カラー　　<select name="new_color"></p>
                    <option value="">選択してください</option>
                    <option value="0">ホワイト系</option>
                    <option value="1">ブラック系</option>
                    <option value="2">ブラウン系</option>
                    <option value="3">レッド系</option>
                    <option value="4">ブルー系</option>
                    <option value="5">イエロー系</option>
                    <option value="6">グリーン系</option>
                </select>
                <p><label>商品説明　<input type="text" name="new_description" value="" size="60px"></label></p>
                <p>商品画像　<input type="file" name="new_img"></p>
                <input type="hidden" name="sql_kind" value="insert">
              </div>
                <p class="add_button"><input type="submit" value="商品を追加する" class="add_radius"></p>
              </div>
            </form>
            </div>
        </section>
        
        <section>
            <h2>商品一覧</h2>
            <table>
                <tr>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>個数</th>
                    <th>公開</th>
                    <th>ジャンル</th>
                    <th>カテゴリ</th>
                    <th>カラー</th>
                    <th>商品説明</th>
                </tr>
<?php foreach ($data as $value) { ?>
                <tr>
                <form method="post">
                    <td><img class="img_size" src="<?php print $img_dir . $value['img']; ?>"></td>
                    <td class="name_width"><?php print $value['item_name']; ?></td>
                    <td class="price_right"><?php print $value['price']; ?>円</td>
                    <td class="stock_left"><?php print $value['stock']; ?>点</td>
                    <td class="status_width"><?php print $value['status']; ?></td>
                    <td class="category_color_width"><?php print $value['genre']; ?></td>
                    <td class="category_color_width"><?php print $value['category']; ?></td>
                    <td class="category_color_width"><?php print $value['color']; ?></td>
                    <td class="description_left"><?php print $value['description']; ?></td>
                </form>
                </tr>
<?php } ?>
            </table>
        </section>
        
    </main>
    </div>
    </div>
    
    <footer>
        <p><small>Copyright &copy; Beautiful Mothers All Rights Reserved.</small></p>
    </footer>

</body>
</html>