# php-bbs
一言掲示板の作成

PHPの基礎学習のためのアウトプット


## テキストファイルからデータを取得する場合

```php
    if ($file_handle = fopen(FILENAME, "a")) {
      /*
      fopenでファイルを開く
      1：ファイル名を含めたパス
      2：モード　読み込みだけを行う「r」、書き込みを行う「w」や「a」などが
        「w」はファイル内容を一旦リセットして書き込みを行い、「a」は末端から追記する形で書き込みを行う
      */

      //書き込み日時を取得
      //サーバーでで世界標準時間になっている場合があるため
      $now_date = date("Y-m-d H:i:s");

      //書き込むデータを作成
      //「‘ (シングルクォーテーション)」で囲み、「表示名」「メッセージ」「投稿日時」をそれぞれ「, (コンマ)」で区切る
      $data = "'" . $clean['view_name'] . "','" . $clean['message'] . "','" . $now_date . "'\n";

      //書き込み
      fwrite($file_handle, $data);

      //ファイルを安全に閉じる関数（fopenとセットで使う）
      fclose($file_handle);

      $success_message = "メッセージを書き込みました";
    }
```

## テキストファイルから書き込む

```php

//テキストファイルからの読み込み
if ($file_handle = fopen(FILENAME, 'r')) {
  while ($data = fgets($file_handle)) {   //ファイルから1行ずつデータを取り出す fgets

    $split_data = preg_split('/\'/', $data);
    //preg_split関数は文字列を特定の文字で分割する関数「'」で分割

    // 一旦messageに入れる
    $message = array(
      'view_name' => $split_data[1],
      'message' => $split_data[3],
      'post_date' => $split_data[5]
    );
    array_unshift($message_array, $message);    //$message_arrayに入れる
  }
  //ファイルを閉じる
  fclose($file_handle);
}
```
