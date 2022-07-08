<?php

// エラーを出力する
ini_set('display_errors', "On");

// functions.php 読み込み
require(dirname(__FILE__) . '/../functions.php');

// セッションスタート
session_start();

// エラーページディレクトリ
$kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

// ============ 変数宣言
$jim_token_FLG = "0";


//=============== リダイレクト防止用 トークン　==================== 
// リダイレクト防止用　トークン取得
$jim_token_2 = isset($_POST['jim_token_2']) ? $_POST['jim_token_2'] : "";
// セッション変数から値を取得
$session_jim_token_2 = isset($_SESSION['jim_token_2']) ? $_SESSION['jim_token_2'] : "";

// トークン削除
unset($_SESSION['jim_token_2']);

if ($jim_token_2 != "" && $jim_token_2 == $session_jim_token_2) {

    $jim_token_FLG = "1";


    //==========  Gmail用　エンコーディング対策
    $senderName = "株式会社 ●●";
    // 送信元の名前を7bitとして扱えるように修正
    $senderName = base64_encode($senderName);
    $senderName = "=?UTF-8?B?{$senderName}?=";
    // "=?[文字エンコーディング]?[B(base64)あるいはQ(Quoted-Printable)]?

    // info@niigatadelica.jp =>  本番用メール

    // 以上の設定から下記のメールヘッダーが想定される。
    $mailHeaders = <<< EOF
From: {$senderName} <<info@niigatadelica.jp>>
Reply-To: info@niigatadelica.jp
Return-Path: info@niigatadelica.jp
X-Mailer: X-Mailer
MIME-Version: 1.0
Content-Type: text/plain;charset=UTF-8
Content-Transfer-Encoding: 8bit
EOF;
    // Content-Type: メール本文はUTF-8でエンコードされる
    // Content-Transfer-Encoding: ただし、送信時はbase64化する

    //=========================== 登録 ok 処理後のメール送信
    $to = "test@co.jp";
    $subject = "アルバイトの登録がありました。";
    $message = "アルバイト登録フォームからご登録がありました。" . "\n";
    $message .= "ご確認をお願いします。" . "\n";

    // メール送信
    mail($to, $subject, $message, $mailHeaders);
} else {

    $jim_token_FLG = "2";

    //=== エラー処理
    header("Location: {$kari_uri}");
}



?>


<!doctype html>
<html lang="ja">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <head>
        <!-- jim style.css -->
        <link rel="stylesheet" href="../css/style.css">

        <!-- Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

        <!-- font awsome cdn -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">

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

    <!-- 入力ページ　（カレント表示） -->
    <div class="container">
        <div class="row form-flow my-5 text-center col-12">
            <div class="col-2 form-flow-list-wrap py-3">
                <span class="form-flow-list-no">1</span><span style="display:block;" id="cl_one">ワンタイムパスワード入力</span>
            </div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap py-3"><span class="form-flow-list-no">2</span>入力</div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap py-3"><span class="form-flow-list-no">3</span>確認</div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap form-flow-active py-3"><span class="form-flow-list-no">4</span>完了</div>
        </div>
    </div>


    <div class="container">

        <?php if (strcmp($jim_token_FLG, "1") == 0) : ?>
            <div class="col-lg-10 col-sm-10 col-10">
                <p style="text-align: center;"> 登録処理を完了しました。</p>

                <h2 class="my-4 sank_h2" style="text-align: center;" id="Thanks_text_h2">
                    ご登録ありがとうございました。
                </h2>


            </div>

        <?php elseif (strcmp($jim_token_FLG, "2") == 0) : ?>

            <div class="col-lg-10 col-sm-10 col-10">
                <h2 class="my-4 sank_h2" style="text-align: center;">
                    登録エラーが発生しました。
                </h2>

                <p style="text-align: center;">登録処理が完了しませんでした。</p>
            </div>

        <?php else : ?>

            <div class="col-lg-10 col-sm-10 col-10">
                <h2 class="my-4 sank_h2" style="text-align: center;">

                </h2>

                <p style="text-align: center;"></p>
            </div>

        <?php endif; ?>


    </div>


    <!-- フッター start -->
    <footer class="bg-light text-center text-lg-start">
        <!-- Copyright -->
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            copyrightc2014 HATASHO NIIGATADELICA all rights reserved.
        </div>
        <!-- Copyright -->
    </footer>
    <!-- フッター END -->

    <!-- ============= スマホ用　カレントページ表示対策 -->
    <script>
        function isSmartPhone() {

            if (window.matchMedia && window.matchMedia('(max-device-width: 599px)').matches) {

                let cl_one = document.getElementById("cl_one");

                cl_one.innerText = "パス";

                console.log(cl_one.innerText);

                return true;
            } else {

                // PC は　何もしない

            }

        }

        window.addEventListener('load', (event) => {

            isSmartPhone();

        });
    </script>

</body>

</html>