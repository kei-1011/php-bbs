<?php
/*
admin.phpで編集が押されると、投稿IDを渡して編集ページを開き、
編集ページで編集完了するか、キャンセルすると管理ページに戻る
*/
//DB接続情報を定数に格納
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','root');
define('DB_NAME','php_bbs');

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

/*  変数リセット */

session_start();

//管理者権限をチェック
if(empty($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {

  header("Location:./admin.php");
}

//投稿IDを取得した場合
if(!empty($_GET['message_id'])) {
  $message_id = (int)htmlspecialchars($_GET['message_id'],ENT_QUOTES);
  //GETで渡された投稿IDをHTMLエンティティ化して代入
  //この投稿IDを使って、データベースから投稿データを取り出す

  //DB接続
  $mysqli = new mysqli( DB_HOST,DB_USER,DB_PASS,DB_NAME);

  //接続エラーチェック
  if($mysqli->connect_errno) {
    $error_message[] = 'データベースの接続に失敗しました。エラー番号'.$mysqli->connect_errno.' : '.$mysqli->connect_error;
  } else {

    //データ読み込み
    $sql = "SELECT * FROM message WHERE id = '$message_id'";
    $res = $mysqli->query($sql);

    if($res) {
      $message_data = $res->fetch_assoc();
    } else {
      header("Location:./admin.php");
    }
    $mysqli->close();
  }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ひと言掲示板　管理ページ（投稿の編集）</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>ひと言掲示板　管理ページ（投稿の編集）</h1>
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
      <input id="view_name" type="text" name="view_name" value="<?php if(!empty($message_data['view_name'])) { echo $message_data['view_name'];}?>">
    </div>
    <div>
      <label for="message">ひと言メッセージ</label>
      <textarea id="message" name="message"><?php if(!empty($message_data['message'])) { echo $message_data['message'];}?></textarea>
    </div>
    <a href="admin.php" class="btn_cancel">キャンセル</a>
    <input type="submit" name="btn_submit" value="更新">
    <input type="hidden" name="message_id" value="<?php echo $message_data['id'];?>">
  </form>

</body>
</html>
