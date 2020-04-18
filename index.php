<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>ひと言掲示板</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
<?php

//メッセージを保存するファイルのパスを指定
define('FILENAME', './message.txt');

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

$now_date     = null;
$data         = null;
$fule_handle  = null;

$split_data   = null;
$message      = array();
$message_array = array();
$success_message = null;
$error_message = null;
$clean = array();

/*変数の初期化
変数をあらかじめ「null」など空の値で宣言しておく
存在しない変数を参照するエラーを防ぎ
型をあらかじめ設定しておくことで意図しない動作を防ぐ
*/

/*入力データの受け渡しの有無を調べる
メッセージを書き込むのか、掲示板を表示するのか判断する
フォームで入力したデータは$_POSTに代入されるので、データがあるかを確認することで入力データの受け渡しがあるかを判断できる
*/
//

//テキストファイルへの書き込み
if (!empty($_POST['btn_submit'])) {

  //バリデーション
  if(empty($_POST['view_name'])) {
    $error_message[] = "表示名を入力してください";
  } else {
    $clean['view_name'] = htmlspecialchars($_POST['view_name'],ENT_QUOTES);
    $clean['view_name'] = preg_replace('/\\r\\n|\\n|\\r/', '', $clean['view_name']);  //改行コードがあれば削除
  }

  if(empty($_POST['message'])) {
    $error_message[] = "ひと言メッセージを入力してください";
  } else {
    $clean['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);
    $clean['message'] = preg_replace('/\\r\\n|\\n|\\r/', '<br>', $clean['message']);  // 改行コードがあればbrに置き換え
  }
  //エラーがなければ、$cleanにHTMLエンティティ化して格納


  //エラーがなければ書き込みを実行
  if( empty($error_message) ){

    // データベースに接続
    $mysqli = new mysqli('localhost', 'root', 'root', 'php_bbs');

    // 接続エラーの確認
    if ($mysqli->connect_errno) {
      $error_message[] = '書き込みに失敗しました。 エラー番号 ' . $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    } else {

      // 文字コード設定
      $mysqli->set_charset('utf8');

      // 書き込み日時を取得
      $now_date = date("Y-m-d H:i:s");

      // データを登録するSQL作成
      $sql = "INSERT INTO message (view_name, message, post_date) VALUES ( '$clean[view_name]', '$clean[message]', '$now_date')";

      // データを登録
      $res = $mysqli->query($sql);

      if ($res) {
        $success_message = 'メッセージを書き込みました。';
      } else {
        $error_message[] = '書き込みに失敗しました。';
      }

      // データベースの接続を閉じる
      $mysqli->close();
    }
  }
}


// データベースに接続
$mysqli = new mysqli('localhost', 'root', 'root', 'php_bbs');

// 接続エラーの確認
if ($mysqli->connect_errno) {
  $error_message[] = '書き込みに失敗しました。 エラー番号 ' . $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
} else {
  // 書き込み処理

  $sql = "SELECT view_name,message,post_date FROM message ORDER BY post_date DESC";
  /* - 基本的な文法 -
  SELECT 取得するカラム名 FROM テーブル名 WHERE 取得条件 ORDER BY ソートするカラム名 DESC */

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
  <h1>ひと言掲示板</h1>
  <?php if(!empty($success_message) ) :?>
    <p class="success_message"><?php echo $success_message; ?></p>
  <?php endif;?>
  <?php if(!empty($error_message) ) :?>
    <ul class="error_message">
    <?php foreach($error_message as $value):?>
      <li>・<?php echo $value;?></li>
    <?php endforeach;?>
  </ul>
  <?php endif;?>

  <form method="post">
    <div>
      <label for="view_name">表示名</label>
      <input id="view_name" type="text" name="view_name" value="">
    </div>
    <div>
      <label for="message">ひと言メッセージ</label>
      <textarea id="message" name="message"></textarea>
    </div>
    <input type="submit" name="btn_submit" value="書き込む">
  </form>
  <hr>
  <section>
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
    <?php endif; ?> </section>
</body>
</html>
