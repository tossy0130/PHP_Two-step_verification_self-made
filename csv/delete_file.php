<?php

// エラーを出力する
ini_set('display_errors', "On");

// functions.php 読み込み
require(dirname(__FILE__) . '/../functions.php');

$kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

$Download_Dir = "http://www.niigatadelica.jp/job_recruit/csv/delete_file.php";

// カレントディレクトリ　を　取得
$dir = __DIR__;

$get_delete = "";

if (isset($_GET['view_delete_code'])) {

    if (!empty($_GET['view_delete_code'])) {

        $get_delete_code = $_GET['view_delete_code'];

        $delete_files = glob($get_delete_code . "/*");
    }
} // === isset

// var_dump($delete_files);

//========================= ファイル削除　処理
foreach ($delete_files as $del_file) {

    // ファイル削除
    unlink($del_file);

    if (file_exists($del_file)) {
        header("Location: {$Download_Dir}");
        exit;
    }
}

?>

<!doctype html>
<html lang="ja">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <head>

        <!-- Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <!--Font Awesome5-->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <!-- font awsome cdn -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">

        <!-- jim style.css -->
        <link rel="stylesheet" href="../css/style.css">

        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

        <!-- bootstrap マテリアル　読み込み -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.dark.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.dark.rtl.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.rtl.min.css">



        <!-- 修正箇所1 titleセクションを呼び出す。デフォルト値は no titleとする -->
        <title>ファイル削除 処理</title>

    </head>


    <div class="jumbotron">
        <div class="container">
            <h1 class="display-5 m_h1">株式会社　新潟デリカ</h1>
            <p class="lead">
                <!-- アルバイト者情報登録 -->
                <?php site_name_hozyo(); ?>
            </p>
        </div>
    </div>


    <!-- ダウンロードページ　への　遷移 -->
    <div class="container" style="margin-bottom:30px;">

        <div class="col-lg4 col-xs-10 col-md-4 dl_link">

            <a href="./csv_export.php" style="color:#fff !important;">
                前へ戻る
            </a>

        </div>
    </div>


    <div class="container" style="margin-bottom:30px;">
        <div>
            <a href="./csv_export.php" class="view_back_btn">
                Excel,CSV出力画面へ戻る
            </a>
        </div>
    </div>


<body>


</body>

</html>