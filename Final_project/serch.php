<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="serch.css">
    <link rel="stylesheet" href="sanitize.css">    
    <title>カテゴリ検索</title>
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
            <li><a href="g.mam.fashion">ママファション</a></li>
            <li><a href="g.baby.kidz.php">ベビー・キッズ</a></li>
            <li><a href="g.maternity.php">マタニティ・授乳服</a></li>
        </ul>
        </div>
    </header>
    
    <div class="oya">
    <div class="container">
    <main>
        <article>
            <h3>カテゴリ・詳細検索</h3>
            <form action="#">
                <div class="box">
                <div class="inner_box">
                    <p>カテゴリ　　<select name="pref"></p>
                        <option value="">選択してください</option>
                        <option value="0">トップス</option>
                        <option value="1">アウター</option>
                        <option value="2">パンツ</option>
                        <option value="3">スカート</option>
                        <option value="4">ワンピース</option>
                        <option value="5">インナー</option>
                        <option value="6">その他</option>
                    </select>
                    <p><label>　価格　　　<input type="text" name="new_price" value=""></label>円　<label>〜　<input type="text" name="new_price" value=""></label>円</p>
                    <p>カラー別　　<select name="pref"></p>
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
                <p class="serch_button"><button class="button2" type="submit">検索する</button></p>
                </div>
                <h2 class="category_name">ママファッション</h2>
            </form>
        </article>
    </main>
    </div>
    </div>
    <footer>
        <p><small>Copyright &copy; Beautiful Mothers All Rights Reserved.</small></p>
    </footer>

</body>
</html>