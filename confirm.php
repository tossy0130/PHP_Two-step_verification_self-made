<?php 

    // エラーを出力する
    ini_set('display_errors', "On");

    // functions.php 読み込み
    require (dirname(__FILE__). '/functions.php');

     // エラーページディレクトリ
     $kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

    // セッションスタート
    session_start();

    //================ 変数初期化 Start
    //******* エラーバリデーション　チェック */
    $error = [];

    //******* index.php フォームから  POST 取得 */
    $email = "";

    //================ 変数初期化 END

    // ************ 二重送信防止用トークンの発行 ************
    $token_jim_2 = uniqid('', true);
    //トークンをセッション変数にセット
    $_SESSION['token_jim_2'] = $token_jim_2;


    // *** POST データ取得 
    if (isset($_POST['email'])) {

        if(!empty($_POST['email'])) {

          $email = $_POST['email'];

              //******* $_SESSION の中へ入れる */
              if(!empty($_SESSION['email'])) {

              $_SESSION['email'] = $email;

            }

            if(!empty($_SESSION['pass'])) {

              $_SESSION['pass'] = $pass;

            }

        } else {

          // ＊＊＊　エラー処理　＊＊＊ 
          header( "Location: {$kari_uri}");

        }

    } else {

        // ＊＊＊　エラー処理　＊＊＊ 
        header( "Location: {$kari_uri}");

    }

    // ホワイトスペース削除
    $email = trim( $email );


    // ### アドレスの形式チェック
    if( !preg_match( '/^[0-9a-z_.\/?]+@([0-9a-z-]+\.)+[0-9a-z-]+$/',$_POST['email'])) {
        $error[0] = "メールアドレスは正しい形式で入力して下さい";
        $err0 = $error[0];

        header("Location: ./index.php?err0=" . $err0);
    } 

  
    //**************** */ GET 用 URL 作成 ***************
    $tmp_url = "http://localhost/php_password_01/url_go/index.php";

    
    //================= URL の　暗号化  has化 ================
    $email_has = password_hash($email, PASSWORD_DEFAULT);

  //  var_dump("url の暗号化:::" . $email_has . "<br /><br />");

  //  var_dump("メールアドレス POST ハッシュ化後" . $email . "<br />");


    // GET用　URL 
    $url = $tmp_url . "?email=" . $email_has;

  //  var_dump("GET用 URL" . $url);


    //**************** 6桁の　乱数を生成 ******************
    $pass = "";

    for($i = 0; $i < 6; $i++) {
        $pass.= mt_rand(0,9);
    }

  //  var_dump("乱数生成" . $pass . "<br />");

    //**************** 乱数を　ハッシュ化 ******************

    //　乱数をハッシュ化させる
    $has = password_hash($pass, PASSWORD_DEFAULT);

  //  var_dump("パスワード　ハッシュ化" . $has . "<br />");

     //**************** 現在日時の取得 ******************  
     $now = new DateTime();
     $d_now = $now->format('YmdHis'); // 20210915094507
    


     //============= index.php から　取得した $token 

     // === フォームからの　post　取得
     $token_jim = isset($_POST["token_jim"]) ? $_POST["token_jim"] : "";

     // === セッション変数の トークン取得
     $session_token_jim = isset($_SESSION["token_jim"]) ? $_SESSION["token_jim"] : "";

     // === セッション変数を削除する
     unset($_SESSION["token_jim"]);
 
    

// ======= 送信処理　（submit） を押されたら インサート処理
// if(isset($_POST['send'])) {

      // ================================
      // ======　メールアドレスを POST で受け取ったら、受け取った
      // ======   アドレスで　Table　に登録データがあった場合は削除する">

      //================ DB 接続 =============
      // *** エックスサーバー用 接続情報

      // トークンと　セッショントークンを比較して　同じなら　インサート処理を行う
      if($token_jim != "" && $token_jim == $session_token_jim) {

      
      //================================  
      //================== 接続情報
      //================================  
      $dsn = 'mysql:dbname=d05618yadb1;host=localhost;port=3306';
      $user = 'd05618ya';
      $password = 'deli7332+C';
  
      try{
  
          // PDO オブジェクト作成
          $pdo = new PDO($dsn, $user, $password);
  
          // ======  （トランザクション） トランザクション開始 ======
          $pdo->beginTransaction();


          $stmt = $pdo->prepare("DELETE FROM valid_table WHERE email = :del_column");
          // ======= バインド
          $stmt->bindParam(':del_column', $email, PDO::PARAM_STR);
          // SQL 実行
          $res = $stmt->execute();
          
  
          $stmt = $pdo->prepare("INSERT INTO valid_table(
              email, email_hh, pass , pass_hh, valid_date
          ) VALUES(
             :in_02 , :in_03, :in_04 , :in_05, :in_06
          )");
  
          // bind する場合は、  
          // $stmt = $pdo->prepare("SELECT * FROM user_list WHERE id = :id");
          // $stmt->bindParam(':id', $id, PDO::PARAM_INT);
          
          // ======  メールアドレス
          $stmt->bindParam(':in_02', $email, PDO::PARAM_STR);
          // ====== メールアドレス　の　（ハッシュ値）
          $stmt->bindParam(':in_03', $email_has, PDO::PARAM_STR);
          // ====== パスワード用　乱数
          $stmt->bindParam(':in_04', $pass, PDO::PARAM_STR);
          // ====== パスワード用　乱数（ハッシュ値）
          $stmt->bindParam(':in_05', $has, PDO::PARAM_STR);
          // ====== 仮登録用  登録　日付 & 時間
          $stmt->bindParam(':in_06',$d_now, PDO::PARAM_STR);
  
          // ====== *******  メールアドレスの　ハッシュ値　を　insert ********* ===
  
          // SQL 実行
          $res = $stmt->execute();
  
  
          // ======= (トランザクション) コミット =========
          if( $res ) {
              $pdo->commit();
          }
  
          // ****** SQL の結果を取り出す ****** 
          if($res) {
              $data = $stmt->fetch();
     //         var_dump("insert データ:::" . $data);
          }
  
  
      }catch (PDOException $e){
          print('Error:'.$e->getMessage());
  
          // (トランザクション) ロールバック
          $pdo->rollBack();
  
          // ＊＊＊　エラー処理　＊＊＊ 
          header( "Location: {$kari_uri}");
  
      } finally  {
          $pdo = null;
      }

    // =============================== トークンcheck
    } else {

    // トークンが 空か セッショントークンと違う場合
    // エラーページ飛ばす
    header("Location: {$kari_uri}");


    }


//  } // ==================== END if





    
    // ======= 登録日付から　年、月、日　を切り取り =======
    $e_year = substr($d_now, 0, 4);
    $e_month = substr($d_now, 4, 2);
    $e_day = substr($d_now,6, 2);


        // ****** データベースの接続解除 ******


     /**========== DB 接続 END =========== */

?>


<!doctype html>
<html lang="ja">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    
    <head>
    <!-- jim style.css -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!-- font awsome cdn -->
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">

    <!-- 修正箇所1 titleセクションを呼び出す。デフォルト値は no titleとする -->
    <title>アルバイト募集サイト　確認画面　画面２</title>

    <style>
    
 
   
    </style>


  </head>
  <body>
    
  <div class="jumbotron">
    <div class="container">
        <h1 class="display-5 m_h1">株式会社　●●</h1>
        <p class="lead">
         <!-- アルバイト者情報登録 -->
         <?php site_name_hozyo(); ?>
        </p>
    </div>
</div>

<div class="container">

    <form action="complete.php" method="POST">

          <!-- メール アドレス -->
          <div class="form-group row">
            <div class="col-sm-10">
                <p><label for="email">下記の、「メールアドレス」で仮登録を行います。よろしいですか？</label></p>
               
                <p><?php if(isset($_POST['email'])) { echo h($email); } ?></p>
            

                <div class="invalid-feedback">アドレスを入力してください。</div>



            </div>
        </div>

        <!-- DB データ　次へ送る -->
        <input type="hidden" name="email" value="<?php print h($email); ?>" />
        <input type="hidden" name="email_hh" value="<?php print h($email_has); ?>" />

        <!-- 乱数のパスワードを送る -->
        <input type="hidden" name="pass" value="<?php print h($pass); ?>">

        <!-- 日付を送る -->
        <!-- 年 -->
        <input type="hidden" name="e_year" value="<?php print h($e_year); ?>" />
        <!-- 月 -->
        <input type="hidden" name="e_month" value="<?php print h($e_month); ?>" />
        <!-- 日 -->
        <input type="hidden" name="e_day" value="<?php print h($e_day); ?>" />

        <!-- トークン token_jim_2 を post する -->
        <input type="hidden" name="token_jim_2" value="<?php print h($token_jim_2); ?>">


<!--　ボタン -->
<div class="form-row">
      <div class="col">

<!-- Button trigger modal -->
<button type="button" id="back_btn" class="btn e_btn" data-toggle="modal" data-target="#exampleModal3">
  戻る
</button>

<!-- Modal 戻るボタン -->
<div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModal3Label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModal3Label">確認画面</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        前の画面へ戻りますか？
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" data-dismiss="modal" onClick="history.back()">はい</button>
        <button type="button" class="btn btn-info" data-dismiss="modal" aria-label="Close">いいえ</button>
      </div>
    </div>
  </div>
</div>

</div>


<div class="col">
    <button type="submit" id="send_btn"  name="send" class="form-control btn e_btn">送信</button>
</div>

</div>
    
    </form>
</div> <!-- END container -->


<!-- フッター start -->
<footer class="bg-light text-center text-lg-start">
  <!-- Copyright -->
  <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
  copyrightc2014 HATASHO NIIGATADELICA all rights reserved.
  </div>
  <!-- Copyright -->
</footer>
<!-- フッター END -->

 <!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>




</body>

</html>