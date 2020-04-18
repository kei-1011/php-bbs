<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>ひと言掲示板　管理ページ</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
<?php

//管理ページのログインパスワード
define('PASSWORD','admin');

//DB接続情報を定数に格納
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','root');
define('DB_NAME','php_bbs');

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

/*  変数リセット
変数をあらかじめ「null」など空の値で宣言しておく
存在しない変数を参照するエラーを防ぎ
型をあらかじめ設定しておくことで意図しない動作を防ぐ
*/
$now_date         = null;
$data             = null;
$file_handle      = null;
$split_data       = null;
$message          = array();
$message_array    = array();
$success_message  = null;
$error_message    = null;
$clean            = array();

// セッションの使用開始
session_start();

/*  送信ボタンが押された時の処理
バリデーションチェック
エラーがなければDB書き込み
*/
if (!empty($_POST['btn_submit'])) {

  //ログイン判定
  if(!empty($_POST['admin_password']) && $_POST['admin_password'] === PASSWORD) {
    $_SESSION['admin_login'] = true;
  } else {
    $error_message[] = 'ログインに失敗しました';
  }
}


// データベースに接続
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 接続エラーの確認
if ($mysqli->connect_errno) {
  $error_message[] = '書き込みに失敗しました。 エラー番号 ' . $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
} else {
  // 書き込み処理

  $sql = "SELECT view_name,message,post_date FROM message ORDER BY post_date DESC";
  /* - 基本的な文法 -
  SELECT 取得するカラム名 FROM テーブル名 WHERE 取得条件 ORDER BY ソートするカラム名 DESC
  */

  $res = $mysqli->query($sql);  // queryメソッドで実行

  if($res) {
    $message_array = $res->fetch_all(MYSQLI_ASSOC);
    /*
    fetch_allで全てのデータを取得
    MYSQLI_ASSOCでファイル読み込みの時と同様の配列型式で取得

    一覧に出力するため、$message_arrayに$resを格納する
    */
  }

  $mysqli->close();
}
?>
  <h1>ひと言掲示板 管理ページ</h1>
  <?php if(!empty($error_message) ) :?>
    <ul class="error_message">
    <?php foreach($error_message as $value):?>
      <li>・<?php echo $value;?></li>
    <?php endforeach;?>
  </ul>
  <?php endif;?>

  <section>
  <?php if(!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) :?>

    <?php if (!empty($message_array)) : ?>
      <?php foreach ($message_array as $value) : ?>
        <!-- foreach文で$message_arrayからメッセージ1件分のデータを取り出し、$valueに入れた -->
        <!-- 表示名、投稿日時、メッセージ内容の3つをそれぞれecho関数で出力 -->
        <article>
          <div class="info">
            <h2><?php echo $value['view_name']; ?></h2>
            <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
          </div>
          <p><?php echo $value['message']; ?></p>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  <?php else:?>
  <form method="post">
    <div>
      <label for="admin_password">ログインパスワード</label>
      <input id="admin_password" name="admin_password" type="password" value="">
    </div>
    <input type="submit" name="btn_submit" value="ログイン">
  </form>
  <?php endif;?>
  </section>
</body>
</html>
