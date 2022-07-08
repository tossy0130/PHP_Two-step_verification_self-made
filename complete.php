<?php

// エラーを出力する
ini_set('display_errors', "On");

// エラーページディレクトリ
$kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

// functions.php 読み込み
require(dirname(__FILE__) . '/functions.php');

// セッションスタート
session_start();

//==============　変数宣言
$e_year = "";
$e_month = "";
$e_day = "";

$pass = "";

$e_t = "";
$e_b = "";

$mail_FLG = 0;

//====================== トークンを取得する
$token_jim_2 = isset($_POST['token_jim_2']) ? $_POST["token_jim_2"] : "";

$session_token_jim_2 = isset($_SESSION['token_jim_2']) ? $_SESSION["token_jim_2"] : "";

// === セッション変数の削除
unset($_SESSION['token_jim_2']);


$email = "";
if (isset($_POST['email'])) {
  $email = $_POST['email'];
}

// === メールアドレスを @ で切る

$email_arr = [];
$email_arr = explode("@", $email);

// メールアドレス　@ より 前
$e_t = $email_arr[0];

// メールアドレス　@ より 後
$e_b = $email_arr[1];


if (isset($_POST['email_hh'])) {
  $email_hh = $_POST['email_hh'];
}




//============================= フォームから日付を取得 （仮メール登録日）
// 年 取得
if (isset($_POST['e_year'])) {

  $e_year = $_POST['e_year'];
}
// 月 取得
if (isset($_POST['e_month'])) {

  $e_month = $_POST['e_month'];
}
// 日 取得
if (isset($_POST['e_day'])) {

  $e_day = $_POST['e_day'];
}

//============================== パスワード（乱数取得）

if (isset($_POST['pass'])) {

  $pass = $_POST['pass'];
}


//======= 会社名 漢字　エンコーディング =======
$kaisya = "株式会社 ●●";
$tmp_kaisya = mb_convert_encoding($kaisya, 'UTF-8', 'AUTO');
$e_kaisya = mb_encode_mimeheader($tmp_kaisya, "UTF-8", "B");

//======= 会社名 漢字　エンコーディング END =======

//======= 登録先　URL　=======
/*
    $send_url = "http://xs810378.xsrv.jp/php_password_01/url_go/index.php?email=" . $email_hh . 
      "&e_b=" . $e_b . "&e_t=" . $e_t;
    */

$send_url = "http://www.ドメイン/job_recruit/url_go/index.php?email=" . $email_hh .
  "&e_b=" . $e_b . "&e_t=" . $e_t;


//======= 登録先　URL END　=======

// ************** メール送信処理 *****************
//  $MAILTO = $email . "?email=" . $email_hh;  //宛先メールアドレス

$MAILTO = $email;  //宛先メールアドレス

// メールタイトル
const SUBJECT = "お客様専用画面ログイン用ワンタイムパスワードのご案内";

// メール本文
$content =  $e_year . "年" . $e_month . "月" . $e_day . "日" . "\n";
$content .= "※このメールは自動返信メールです。" . "\n\n";

$content .= "お客さま専用画面ログイン用ワンタイムパスワードのご案内" . "\n";
$content .= "この度は、弊社求人にご応募いただきまして誠にありがとうございます。" . "\n";
$content .= "お客さま専用画面ログイン用のワンタイムパスワードを送付いたします。下記ＵＲ" . "\n" .
  "Ｌにアクセスいただき、アクセスキーとワンタイムパスワードにてログインをお願" . "\n" .
  "いいたします。" . "\n\n" .
  "最初の操作から30分以内に入力が完了しない場合、ワンタイムパスワードは無効になります。" . "\n\n";;

//　＊＊＊ ワンタイムパスワードの有効期限を記載するならここから下　＊＊＊

$content .= "○ワンタイムパスワード" . "\n\n";

// ワンタイム用　パスワード
$content .= "    ・ワンタイムパスワード：" . $pass . "\n";

// 登録用　URL
$content .= "    ・ログイン用ＵＲＬ：" .  $send_url . "\n";


//==========  Gmail用　エンコーディング対策
$senderName = "株式会社 ●●事業部";
// 送信元の名前を7bitとして扱えるように修正
$senderName = base64_encode($senderName);
$senderName = "=?UTF-8?B?{$senderName}?=";
// "=?[文字エンコーディング]?[B(base64)あるいはQ(Quoted-Printable)]?

// info@niigatadelica.jp =>  本番用メール

// 以上の設定から下記のメールヘッダーが想定される。
$mailHeaders = <<< EOF
From: {$senderName} <<info@ドメイン.jp>>
Reply-To: info@ドメイン.jp
Return-Path: info@ドメイン.jp
X-Mailer: X-Mailer
MIME-Version: 1.0
Content-Type: text/plain;charset=UTF-8
Content-Transfer-Encoding: 8bit
EOF;
// Content-Type: メール本文はUTF-8でエンコードされる
// Content-Transfer-Encoding: ただし、送信時はbase64化する


// 送信テスト　OK
//    $headers = "{$e_kaisya} From:<tossy0130@xs810378.xsrv.jp>";


if ($token_jim_2 != "" && $token_jim_2 == $session_token_jim_2) {


  // 文字化けするようなら下記のコメントアウト解除して試す
  // mb_language("ja");

  mb_internal_encoding("UTF-8");
  $is_success = mb_send_mail($MAILTO, SUBJECT, $content, $mailHeaders);

  if (!$is_success) {

    // ==== メール失敗時の処理
    $mail_FLG = 0;
  } else {

    // === メール成功時の処理

    $mail_FLG = 1;
    // ===================== $_SESSION 削除 =====================

    // セッション削除
    unset($_SESSION['email']);
  }
} else {

  // エラーページ飛ばす
  header("Location: {$kari_uri}");
}


// === 自分自身へ　飛ばす
/*
    header('Location: ./');
    exit;
    */


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

    <!-- 修正箇所1 titleセクションを呼び出す。デフォルト値は no titleとする -->
    <title>アルバイト募集サイト 完了画面　画面３</title>

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

    <?php if ($mail_FLG == 1) : ?>
      <h2 class="my-4 sank_h2">
        仮登録ありがとうございました。<br />
        下記のメールアドレスへ、メール送信を完了いたしました。<br /><br />
      </h2>

    <?php else : ?>

      <h2 class="my-4 sank_h2">
        メールの送信処理に失敗しました。<br />
        Wi-Fi環境を確認の上もう一度お手続きください。<br /><br />
      </h2>

    <?php endif; ?>

    <p class="p_01"> 送信先アドレス：<span class="mozi_01"><?php echo h($email); ?></span></p>

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

</body>

</html>