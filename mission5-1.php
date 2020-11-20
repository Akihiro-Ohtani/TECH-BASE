<!DOCTYPE html>
<html lang="ja" dir="ltr">
 <head>
  <meta charset="utf-8">
  <title></title>
 </head>
 <body>
  <?php

  $dsn = 'データベース名'; //データベースに接続するために必要な情報 (Data Source Name)
  $user = 'ユーザー名';                              //ユーザネームを入れる
  $password = 'パスワード';                         //パスワードを入れる
  $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)); //new PDOはMySQLへの接続  arrayはデータベース操作でエラーが発生した場合に警告（Worning: ）として表示するために設定するオプション　PDOはクラス
 //PDOクラスは、10,11,12行目が初期値として必要
  $sql = "CREATE TABLE IF NOT EXISTS test1" //もしまだtest1のテーブルが存在しないなら新しくtest1というテーブルを作成する　データベースにテーブルの定義をする　
  ." (" 
  . "id INT AUTO_INCREMENT PRIMARY KEY,"    //対象のカラムに今までに格納されたことのある最大の値に1が加えられた値が格納される
  . "name varchar(255),"                    // nameの文字数を255文字に制限
  . "comment varchar(255),"                 // commentの文字数を255文字に制限
  . "date datetime,"                        //日時データを取得
  . "password char(30)"                     //passwordの文字数を30文字に制限
  .");";
  // $stmt = $pdo->query($sql);
  $pdo->query($sql);    //　　queryメソッドを使用して、sqlをデータベースに届ける　矢印はデフォルトでPDOに入っている関数を呼び出している

    //編集機能
  if (!empty($_POST["edit_num"]) && find_record($_POST["edit_num"]) != null) {  //フォーム内が空でない&編集番号がnullでない場合【find_recordは投稿番号があるか確認　nullでなければ返す　（nullは中身が空、値が何もない状態)】
   $edit_num = $_POST["edit_num"];                                              //編集対象番号を変数$edit_numに代入
   $select_line = find_record($edit_num);                                       //変数$select_lineに編集番号を代入 $select_lineに配列が入ってる
   $select_name = $select_line['name'];                                         //変数$select_nameに変数$select_lineのnameを代入  連想配列はキーを指定する
   $select_comment = $select_line['comment'];                                   //変数$select_nameに変数$select_lineのcommentを代入
   $edit_temp = $edit_num;                                                      //変数$edit_tempに$edit_numを代入
  }
    //編集機能end
    ?>
   
   <!-- 投稿フォーム -->
  <form class="" action="" method="post">
   <input type="text" name="name" value="<?php if(isset($select_name)){echo $select_name;} ?>" placeholder="名前">
   <br>
   <input type="text" name="comment" value="<?php if(isset($select_comment)){echo $select_comment;} ?>" placeholder="コメント">
   <br>
   <input type="password" name="pass1" value="">
   <br>
   <input type="submit" name="submit" value="送信">
   <input type="hidden" name="edit_temp" value="<?php if(isset($edit_temp)){echo $edit_temp;} ?>">
  </form>
  <!-- 投稿フォームend -->
  
  <!-- 削除フォーム -->
  <br>
  <form class="" action="" method="post">
   <input type="number" name="delete" value="" placeholder="削除対象番号">
   <br>
   <input type="password" name="pass2" value="">
   <br>
   <input type="submit" name="submit" value="削除">
  </form>
  <!-- 削除フォームend -->
  
  <!-- 編集フォーム -->
  <br>
  <form class="" action="" method="post">
   <input type="number" name="edit_num" value="" placeholder="編集対象番号">
   <br>
   <input type="submit" name="submit" value="編集">
  </form>
  <!-- 編集フォームend -->

  <?php
    //投稿機能
   if (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass1"])) { //フォーム内が空でない場合に以下の処理
    $name = $_POST["name"];                                                              //入力データの受け取りを変数に代入
    $comment = $_POST["comment"];                                                        //入力データの受け取りを変数に代入
    $pass1 = $_POST["pass1"];                                                            //入力データの受け取りを変数に代入

    if (empty($_POST["edit_temp"])) {                                                    //edit_tempが空の場合以下の処理 (edit_tempは編集する投稿番号)
     echo "名前:" . $name . " コメント:" . $comment . " を受け付けました。<br>";         //文字列と変数を結合してechoする  ＊（ここから新規書き込み機能）＊
     insert_record($name,$comment,$pass1);                                      //insert_recordを使って$name,$comment,$pass1を引数に入れる
     echo "保存完了<br>";                                                       //保存完了をechoする                      ＊（新規書き込み機能end）＊
    }else{                                                                      //edit_tempが空でない場合以下の処理
     $edit_con = $_POST["edit_temp"];                                           //入力データの受け取りを変数に代入
     if (password_confirm($edit_con,$pass1)) {                                  //パスワードが一致していれば以下の処理  ＊【ここから編集モードのとき（編集機能）】＊
      echo "名前:" . $name . " コメント:" . $comment . " に変更しました。<br>"; //文字列と変数を結合してechoする
      edit_record($edit_con,$name,$comment);                                    //162行目の自作関数であるedit_recordを呼び出している     ＊【編集機能end】＊
     }
    }
   }
   //投稿機能end


  //削除機能
  if (!empty($_POST["delete"]) && !empty($_POST["pass2"])) { //フォーム内が空でない場合に以下の処理
   $delete_num = $_POST["delete"];                           //入力データの受け取りを変数に代入
   $delete_pass = $_POST["pass2"];                           //入力データの受け取りを変数に代入
   if (password_confirm($delete_num,$delete_pass)) {         //パスワードが一致していれば以下の処理
    delete_record($delete_num);                              //136行目のdelete_recordを呼び出す
   }
  }
  //削除機能end

  call_line(); //自作関数である119行目のcall_lineを呼び出している
  ?>

  <?php
  
  //テーブルに追加する機能  phpではテーブルに直接書き込めないから、PDOの関数(prepare,bindParam,executeなど)を使って書き込むための指示をする
  function insert_record($name, $comment, $pass){           //引数に$name,$comment,$passを指定する　　79行目の引数の順番で結び付けている
   global $pdo;                                             //13行目の$pdo変数をglobalを使って呼び出している
   $sql = $pdo -> prepare("insert into test1 (name, comment, date, password) values(:name, :comment, now(), :password)"); //insert intoがテーブルの中に書き込むやつ　INSERT文を変数に格納し、挿入する値は空のまま、SQL実行の準備をする 　now()は現在日時を取得するSQL関数
   $sql -> bindParam(':name', $name, PDO::PARAM_STR);       //111行目のnameにバインドする　PDO::PARAM_STRは文字列ってこと　:name'はsqlの言語で $nameはphpの言語だから結び付けている　
   $sql -> bindParam(':comment', $comment, PDO::PARAM_STR); //111行目のcommentにバインドする　PDO::PARAM_STRは文字列ってこと
   $sql -> bindParam(':password', $pass, PDO::PARAM_STR);   //111行目のpasswordにバインドする　PDO::PARAM_STRは文字列ってこと
   $sql -> execute();                                       //execute関数を使うことでSQL文を実行することができる
  }
  //テーブルに追加する機能end

  //表示機能
  function call_line(){
   global $pdo;                         //13行目の$pdoをglobalを使って呼び出している
   $sql = "SELECT * FROM test1";        //全カラムのデータを取得する場合は「＊」の記号を用いる  「FROM」を用いて、SELECTで選んだカラムが「どのテーブルのカラムか」を指定する。 
   $stmt = $pdo->query($sql);           //queryメソッドを使用して、$sqlをデータベースに届ける
   $results = $stmt->fetchAll();        //fetchAllでは全てのレコードを2次元配列として取得してる。
   foreach ($results as $row){          //$rowの中にはテーブルのカラム名が入る
    echo "id:".$row['id']." ";          ////idをechoする
    echo "name:".$row['name']." ";      //nameをechoする
    echo "comment:".$row['comment']." "; //commentをechoする
    // echo "password:".$row['password']." ";
    echo "date:".$row['date']."<br>";   //dateをechoする
   }
  }
  //表示機能end
  
  //削除機能       //↓$idは97行目の引数と結びついている
  function delete_record($id){              //引数に$idを指定する　$idは削除したい投稿番号  $idに投稿番号が入って投稿ごとに136行目から141行目の処理が行われる　(関数の引数は別の行で定義しなくていい)　
   global $pdo;                             //13行目の$pdo変数をglobalを使って呼び出している
   $sql = "delete from test1 where id=:id"; //テーブルtest1の指定したidの行を削除する　sql文の時はイコールを表すとき=でいい　:idはテーブルの中のもの
   // $sql = "delete from test1 where id=" . $id;
   $stmt = $pdo->prepare($sql);             //SQL実行の準備をする
   $stmt->bindParam(':id', $id, PDO::PARAM_INT); //137行目のid = :id"にバインドする　PDO::PARAM_INTは数字ってこと
   $stmt->execute();                        //execute関数を使うことでSQL文を実行することができる
  }
  //削除機能end

  //編集モード&削除モードのとき投稿番号を探す機能(test1テーブルに入力済みである文字列の投稿番号と、編集モード&削除モードで入力した投稿番号が一致しているかどうか確認する機能)
  function find_record($id){                    //引数に$idを指定する　ここでの$idは探したい投稿番号
   global $pdo;                                 //13行目の$pdo変数をglobalを使って呼び出している
   $sql = "select * from test1 where id = :id"; //全カラムのデータを取得する場合は「＊」の記号を用いる  「FROM」を用いて、SELECTで選んだカラムが「どのテーブルのカラムか」を指定する。 「WHERE」を用いて、「どこのレコード（横の列）を取得するか」を指定する。要素は15行目のCREATEで指定した順になっている
   $stmt = $pdo -> prepare($sql);               //SQL実行の準備をする　$sqlになんかのクラスののメソッドを入れてるから$stmtでメソッドが使える
   $stmt -> bindParam(':id',$id,PDO::PARAM_INT); ////行目のid = :id"にバインドする　PDO::PARAM_INTは数字ってこと
   $stmt -> execute();                          //execute関数を使うことでSQL文を実行することができる
   $result = $stmt->fetch(PDO::FETCH_ASSOC);    //fetch(PDO::FETCH_ASSOC)は連想配列として取得する(fetchAllでは全てのレコードを2次元配列として取得してる。)　ここで連想配列を作っている（連想配列）でも、普通の配列FETCH_NUMを使う　データベースのほうでは連勝配列の形で入っているからphpでも同じように連想配列を使う
   if (!empty($result)) {                       //$resultが空でない場合以下の処理
    return $result;   //$resultを返す
   }
   return null;       //nullを返す
  }
  //編集モード&削除モードのとき投稿番号を探す機能end

 //新しく入力した文字列を上書きする関数
  function edit_record($id,$name,$comment){ //$idは編集対象の投稿番号 $nameは上書きしたい名前 $commentは上書きしたいコメント内容
   global $pdo;                             //13行目の$pdo変数をglobalを使って呼び出している

   $sql = "UPDATE test1 SET name=:name,comment=:comment,date=now() WHERE id=:id"; //WHEREで指定したidをSETで編集する
   $stmt = $pdo->prepare($sql);                             //SQL実行の準備をする
   $stmt->bindParam(':name', $name, PDO::PARAM_STR);        //164行目のname=:nameにバインドする　PDO::PARAM_STRは文字列ってこと
   $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);  //164行目のcomment=:commentにバインドする　PDO::PARAM_STRは文字列ってこと
   $stmt->bindParam(':id', $id, PDO::PARAM_INT);            //164行目のid = :idにバインドする　PDO::PARAM_INTは数字ってこと
   $stmt->execute();                                        //execute関数を使うことでSQL文を実行することができる
  }
  //新しく入力した文字列を上書きする関数end

  //入力したパスワードが一致しているか確認する関数
  function password_confirm($id,$pass){                   //$idは投稿番号　$passは自分で決めたパスワード(最初に投稿するときに自分でパスワードを決めることができる)
   global $pdo;                                           //13行目の$pdo変数をglobalを使って呼び出している
   $record = find_record($id);                            //変数$recordにfind_record($id)を代入する　$idは29行目の編集対象番号
   if ($record != null && $record['password'] == $pass) { //変数$recordの中身がnullでないかつ、 152行目で連想配列にしているからキーを取り出している
    return true;    //trueを返す
   }
   return false;    //falseを返す
  }
  //入力したパスワードが一致しているか確認する関数end
   ?>


 </body>
</html>