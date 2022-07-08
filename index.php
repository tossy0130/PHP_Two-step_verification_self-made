<?php

// エラーを出力する
// ini_set('display_errors', "On");

ini_set("display_errors", 1);
error_reporting(E_ALL);

// セッションスタート
session_start();

// セッションハイジャック対策 セッションID を更新して変更
session_regenerate_id(TRUE);

// ************ 二重送信防止用トークンの発行 ************
$token_jim = uniqid('', true);
//トークンをセッション変数にセット
$_SESSION['token_jim'] = $token_jim;


// functions.php 呼び出す
require(dirname(__FILE__)."/functions.php");

 // エラーページディレクトリ
 $kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

// エラーメッセージ格納用
$error = [];

// ****** エラーチェック 
if(isset($_POST['send_btn'])) {

  // ### 空チェック
  if(empty($_POST['email'])) {
    $error[0] = "「メールアドレス」を入力してください。";

  // email の　形式チェック
  } // ### 空チェック END 
  

  // ### アドレスの形式チェック
  /*
  if( !preg_match( '/^[0-9a-z_.\/?]+@([0-9a-z-]+\.)+[0-9a-z-]+$/',$_POST['email'])) {
    $error[1] = "メールアドレスは正しい形式で入力して下さい";

  } 
  */
 
  /*
  if (empty($error)) {

    header("Location: ./confirm.php");
  
    exit;
  
  } 
  */

} //=============== END if


//========================================
//=================== confirm.php から　エラーで戻ってきた処理
//========================================
$get_err = [];

if(isset($_GET['err0'])) {

  // メールアドレスは正しい形式で入力して下さい
  $get_err[0] = $_GET['err0'];

}


//====================================
//============== ブラウザ情報　ユーザー情報　判別用 class

class browser{
  
  function get_info(){
    
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $browser_name = $browser_version = $webkit_version = $platform = NULL;
    $is_webkit = false;
    
    //Browser
    if(preg_match('/Edge/i', $ua)){
      
      $browser_name = 'Edge';
      
      if(preg_match('/Edge\/([0-9.]*/', $ua, $match)){
      
        $browser_version = $match[1]; 
      }
      
    }elseif(preg_match('/(MSIE|Trident)/i', $ua)){
      
      $browser_name = 'IE';
      
      if(preg_match('/MSIE\s([0-9.]*)/', $ua, $match)){
        
        $browser_version = $match[1];
      
      }elseif(preg_match('/Trident\/7/', $ua, $match)){
        
        $browser_version = 11;
      }
    
    }elseif(preg_match('/Presto|OPR|OPiOS/i', $ua)){
      
      $browser_name = 'Opera';
      
      if(preg_match('/(Opera|OPR|OPiOS)\/([0-9.]*)/', $ua, $match)) $browser_version = $match[2];
      
    }elseif(preg_match('/Firefox/i', $ua)){
      
      $browser_name = 'Firefox';
      
      if(preg_match('/Firefox\/([0-9.]*)/', $ua, $match)) $browser_version = $match[1];
      
    }elseif(preg_match('/Chrome|CriOS/i', $ua)){
      
      $browser_name = 'Chrome';
      
      if(preg_match('/(Chrome|CriOS)\/([0-9.]*)/', $ua, $match)) $browser_version = $match[2];
      
    }elseif(preg_match('/Safari/i', $ua)){
      
      $browser_name = 'Safari';
      
      if(preg_match('/Version\/([0-9.]*)/', $ua, $match)) $browser_version = $match[1];
    }
    
    //Webkit
    if(preg_match('/AppleWebkit/i', $ua)){
      
      $is_webkit = true;
      
      if(preg_match('/AppleWebKit\/([0-9.]*)/', $ua, $match)) $webkit_version = $match[1];
    }
    
    //Platform
    if(preg_match('/ipod/i', $ua)){
      
      $platform = 'iPod';
      
    }elseif(preg_match('/iphone/i', $ua)){
      
      $platform = 'iPhone';
      
    }elseif(preg_match('/ipad/i', $ua)){
      
      $platform = 'iPad';
      
    }elseif(preg_match('/android/i', $ua)){
      
      $platform = 'Android';
      
    }elseif(preg_match('/windows phone/i', $ua)){
      
      $platform = 'Windows Phone';
      
    }elseif(preg_match('/linux/i', $ua)){
      
      $platform = 'Linux';
      
    }elseif(preg_match('/macintosh|mac os/i', $ua)) {
      
      $platform = 'Mac';
      
    }elseif(preg_match('/windows/i', $ua)){
      
      $platform = 'Windows';
    }
    
    return array(
      
      'ua' => $ua,
      'browser_name' => $browser_name,
      'browser_version' => intval($browser_version),
      'is_webkit' => $is_webkit,
      'webkit_version' => intval($webkit_version),
      'platform' => $platform
    );
  }//get_info()
};
 
$browser = new browser();
$browser_info = $browser->get_info();


// var_dump("<br /><br />" . "ブラウザ情報:::" . $browser_info['browser_name'] . "<br />"); 
// var_dump("<br /><br />" . "接続端末情報:::" . $browser_info['platform']);


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

    <!--CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    
    <!-- バリデーション用 -->
    <!--Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!--Font Awesome5-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">

    <!-- font awsome cdn -->
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">

    <!--JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" ></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" ></script>

    <!-- 修正箇所1 titleセクションを呼び出す。デフォルト値は no titleとする -->
    <title>アルバイト募集サイト TOP　画面１</title>

<style>

/* Vue.js 用 */
.v-enter-active, .v-leave-actibe {
  transition: opacity 1s;
}

.v-enter, .v-leave-to {
  opacity: 0;
  transition: opacity 1s;
}




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

<div class="container maile_c">
    <div class="row">

    <div class="col-lg-10 col-sm-10 col-10">

    <form action="confirm.php" method="POST" id="submitForm" class="needs-validation" 
    novalidate enctype="multipart/form-data">
      
           
       <!-- メール アドレス -->
      <div class="form-group was-validated" id="mail_div">
        <label for="mail" class="font-weight-bold label_01">仮登録用メールアドレスを入力してください。</label>
        <input type="email" id="mail" required class="form-control" name="email" placeholder="仮登録用メールアドレス">
      </div>

          <!-- ****** エラーメッセージ　表示 END ******  -->
          <?php if(!empty($get_err)) : ?>

            <?php foreach($get_err as $err) : ?>

              <div class="alert alert-danger err_ms" role="alert">
              <i class="fas fa-exclamation-triangle"></i><?php print h($err); ?>
              </div>

            <?php endforeach; ?>

          <?php endif; ?>

         <!--ボタンブロック-->
         <div class="form-group row">
            <div class="col-sm-10 col-lg-10">
                <button type="submit" id="btn" name="send_btn" class="btn btn-primary btn-block">
                  仮登録メール送信画面へ進む
                </button>
            </div>
        </div>
        <!--/ボタンブロック-->
              
        <input type="hidden" name="token_jim" value="<?php print h($token_jim); ?>">
       
       </form>

    </div>

  </div>
</div>  <!-- container END -->   


<!-- フッター start -->

<footer class="bg-light text-center text-lg-start">
  <!-- Copyright -->
  <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
  copyrightc2014 HATASHO NIIGATADELICA all rights reserved.
  </div>
  <!-- Copyright -->
</footer>

<!-- フッター END -->


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    
    <!-- Vue.js -->
   
    
    <!-- form エラー処理 -->

<script>

const mail = document.getElementById('mail');
const mail_div = document.getElementById('mail_div');

//　ページ読み込み時 処理
function firstscript() {
  mail.focus();
  mail_div.classList.remove('was-validated');
}
 
// ページの読み込み完了と同時に実行されるよう指定
window.onload = firstscript;

// エラーメッセージ用
/*
const err_m = document.getElementById('err_m');
err_m.style.display == "none";
*/

mail.addEventListener('focus', TurnOffValid);

function TurnOffValid() {
  mail_div.classList.remove('was-validated');
}

mail.addEventListener('blur', validMail);

function validMail() {

  //******** メールフォームが空ではないとき */
  if (mail.value != "") {
    mail_div.classList.add('was-validated');

  }

}


// ################## フォームのエンターキー　無効化
document.getElementById("submitForm").onkeypress = (e) => {
  // form1に入力されたキーを取得
  const key = e.keyCode || e.charCode || 0;
  // 13はEnterキーのキーコード
  if (key == 13) {
    // アクションを行わない
    e.preventDefault();

    //========== メールフォームに　クラスを追加
    mail_div.classList.add('was-validated');
  }
}


</script>
  
  </body>
</html>