<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>ひと言掲示板</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <h1>ひと言掲示板</h1>
  <pre>
<?php

//メッセージを保存するファイルのパスを指定
define('FILENAME', './message.txt');

//タイムゾーン設定
date_default_timezone_set('Asia_Tokyo');

$now_date = null;
$data = null;
$fule_handle = null;
/*変数の初期化
変数をあらかじめ「null」など空の値で宣言しておく
存在しない変数を参照するエラーを防ぎ
型をあらかじめ設定しておくことで意図しない動作を防ぐ
*/


$split_data = null;
$message = array();
$message_array = array();


/*入力データの受け渡しの有無を調べる
メッセージを書き込むのか、掲示板を表示するのか判断する
フォームで入力したデータは$_POSTに代入されるので、データがあるかを確認することで入力データの受け渡しがあるかを判断できる
*/
if (!empty($_POST['btn_submit'])) {

  if ($file_handle = fopen(FILENAME, "a")) {
    /*fopenでファイルを開く
    1：ファイル名を含めたパス
    2：モード　読み込みだけを行う「r」、書き込みを行う「w」や「a」などが
      「w」はファイル内容を一旦リセットして書き込みを行い、「a」は末端から追記する形で書き込みを行う
    */

    //書き込み日時を取得
    //サーバーでで世界標準時間になっている場合があるため
    $now_date = date("Y-m-d H:i:s");

    //書き込むデータを作成
    //「‘ (シングルクォーテーション)」で囲み、「表示名」「メッセージ」「投稿日時」をそれぞれ「, (コンマ)」で区切る
    $data = "'" . $_POST['view_name'] . "','" . $_POST['message'] . "','" . $now_date . "'\n";

    //書き込み
    fwrite($file_handle, $data);

    //ファイルを安全に閉じる関数（fopenとセットで使う）
    fclose($file_handle);
  }
}

if ($file_handle = fopen(FILENAME, 'r')) {
  while ($data = fgets($file_handle)) {

    $split_data = preg_split('/\'/', $data);
    //preg_split関数は文字列を特定の文字で分割する関数


    $message = array(
      'view_name' => $split_data[1],
      'message' => $split_data[3],
      'post_date' => $split_data[5]
    );
    array_unshift($message_array, $message);
  }

  //ファイルを閉じる
  fclose($file_handle);
}
?>
</pre>
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
