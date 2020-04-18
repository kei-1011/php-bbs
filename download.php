<?php
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','root');
define('DB_NAME','php_bbs');

$csv_data     = null;
$sql          = null;
$res          = null;
$message_array= array();
$limit        = null;

session_start();

//件数
if(!empty($_GET['limit'])) {
  if( $_GET['limit'] === "10") {
    $limit = 10;
  } elseif( $_GET['limit'] === "30") {
    $limit = 30;
  }
}

if(!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) {

  //ファイル作成、出力処理

  //出力
  header("Content-Type:application/octet-stream");
  header("Content-Disposition:attachment; filename=メッセージデータ.csv");
  header("Content-Transfer-Encoding:binary");

  //DB接続
  $mysqli = new mysqli( DB_HOST,DB_USER,DB_PASS,DB_NAME);

  //接続エラーチェック,データ取得
  if(!$mysqli->connect_errno) {

    //取得件数の指定がある場合
    if(!empty($limit)) {
      $sql = "SELECT * FROM message ORDER BY post_date ASC LIMIT $limit";
    } else {
      $sql = "SELECT * FROM message ORDER BY post_date ASC";
    }
    $res = $mysqli->query($sql);

    if($res) {
      $message_array = $res->fetch_all(MYSQLI_ASSOC);
    }

    $mysqli->close();
  }

  //CSVデータ作成
  if(!empty($message_array)) {
    /*
    1行目のラベル作成
    CSVのデータは、行を""で囲み、,で区切る
    行末に\nの改行コードを入れることで改行できる
    */
    $csv_data .= '"ID","表示名","メッセージ","投稿日時"'."\n";

    foreach($message_array as $value) {
      //データを１行ずつ書き込み
      $csv_data .= '"' . $value["id"] . '","' . $value['view_name'] . '","' . $value['message'] . '","' . $value['post_date'] . "\"\n";
    }
  }

  //ファイルを出力
  echo $csv_data;

} else {
  //ログインページヘリダイレクト
  header("Location:./admin.php");
}
//download.phpは表示しないため、return
return;
