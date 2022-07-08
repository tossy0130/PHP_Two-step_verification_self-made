<?php

// エラーを出力する
ini_set('display_errors', "On");

// functions.php 読み込み
require(dirname(__FILE__) . '/../functions.php');

// header('Location: ./'); 

// カレントディレクトリ　を　取得
$dir = __DIR__;


// ====================== 変数宣言 ====================

//$test_uri = "http://xs810378.xsrv.jp/php_password_01/csv/";
$test_uri = "url";


$dowonload_uri = "http://ドメイン/job_recruit/csv/download_go.php";

$get_dir_code = "";

$get_files = [];

//============ GET パラメーター　取得

if (isset($_GET['view_code'])) {

    if (!empty($_GET['view_code'])) {

        $get_dir_code = $_GET['view_code'];

        //*********  Excelファイル　、　CSV ファイル取得 */
        $get_excel_files = glob($get_dir_code . "/*.xlsx");
        $get_csv_files = glob($get_dir_code . "/*.csv");
    }
} else {
}

/*
// ディレクトリ一覧取得
$res = glob('file*', GLOB_ONLYDIR);
var_dump("出力テスト：：：res" . $res);
*/


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
        <title>登録用フォーム 前チェック</title>

        <style>
            .view_csv_item {
                list-style: none;
            }
        </style>

    </head>

<body>


    <div class="jumbotron">
        <div class="container">
            <h1 class="display-5 m_h1">株式会社 新潟デリカ</h1>
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


    <!-- ============== CSV  and  Excel　データ　取得　=================== -->
    <!-- CSV ファイル一覧　取得 -->
    <?php $csv_files = []; ?>
    <?php $i = 0; ?>

    <?php foreach ($get_csv_files as $c_file) : ?>

        <?php $csv_files[$i] = $c_file; ?>
        <?php $i++; ?>

    <?php endforeach; ?>

    <!-- ファイル一覧　取得 END -->

    <!-- CSV ファイル一覧　取得 -->
    <?php $excel_files = []; ?>
    <?php $j = 0; ?>

    <?php if (!empty($get_excel_files)) : ?>


        <?php foreach ($get_excel_files as $e_file) : ?>

            <?php $excel_files[$j] = $e_file; ?>
            <?php $j++; ?>

        <?php endforeach; ?>

    <?php endif; ?>

    <!-- ファイル一覧　取得 END -->

    <!-- ============== CSV  and  Excel　データ　取得 END　=================== -->

    <!-- CSVデータ　と　Excel　を　マージ -->
    <?php $get_files = array_merge($csv_files, $excel_files) ?>
    <!-- CSV データ　と　Excelデータ　を結合して出力  -->

    <!-- 文字化け対策 -->
    <?php mb_convert_variables('UTF-8', 'auto', $get_files);  ?>

    <!-- 降順にソート -->
    <?php rsort($get_files); ?>

    <?php $idx = 0; ?>

    <div class="container" id="app">

        <div class="container">

            <form method="POST" enctype="multipart/form-data" action="">

                <!--
    <ul class="view_csv_item">
-->

                <!-- ============== ループ 開始 ==============  -->

                <?php foreach ($get_files as $g_file) : ?>



                    <?php if ($idx == 0) : ?>

                        <ul class="t">

                        <?php elseif ($idx != 0 && $idx % 3 == 0) : ?>

                        </ul>

                        <ul class="tt">

                        <?php endif; ?>

                        <?php $file_start = strpos($g_file, '/'); ?>

                        <?php $file_result = substr($g_file, $file_start + 1); ?>


                        <!-- =========================== Excel ファイルの出力 ================================== -->
                        <!-- .xlsx が　ファイル名に含まれていたら -->
                        <?php if (strpos($file_result, ".xlsx")) : ?>

                            <!-- 日付　選択時　（Excel ファイル名変換） -->
                            <?php $file_kanri_excel = ""; ?>
                            <?php $file_bank_excel = ""; ?>
                            <?php $file_kanri_excel_all = ""; ?>
                            <?php $file_bank_excel_all = ""; ?>

                            <?php if (strpos($file_result, "R_Baito_Kanri") !== false) : ?>
                                <?php $file_kanri_excel = str_replace("R_Baito_Kanri_", "アルバイト管理_", $file_result); ?>

                            <?php elseif (strpos($file_result, "Ginkou_Baito_Kanri") !== false) : ?>
                                <?php $file_bank_excel = str_replace("Ginkou_Baito_Kanri_", "銀行管理_", $file_result); ?>

                            <?php elseif (strpos($file_result, "M_baito_") !== false) : ?>
                                <?php $file_bank_excel = str_replace("M_baito_", "（未入力 全件）アルバイト管理_", $file_result); ?>

                            <?php elseif (strpos($file_result, "M_ginkou_") !== false) : ?>
                                <?php $file_bank_excel = str_replace("M_ginkou_", "（未入力 全件）銀行管理_", $file_result); ?>

                            <?php endif; ?>


                            <li class="item_xlsx">

                                <div class="item_xlsx_box">

                                    <!--
        <a href=" php file パス " class="li_csv_item" download>
            <i class="fas fa-file-excel"></i>
        </a>
        -->

                                    <i class="fas fa-file-excel"></i>
                                    <span class="li_csv_item">

                                        <!-- ==========  Excel 日付選択ファイル 出力 ============ -->
                                        <?php if ($file_kanri_excel != "") : ?>
                                            <?php print h($file_kanri_excel); ?>
                                        <?php endif; ?>

                                        <?php if ($file_bank_excel != "") : ?>
                                            <?php print h($file_bank_excel); ?>
                                        <?php endif; ?>

                                        <!-- ==========  Excel （未入力）ファイル出力 出力 END ============ -->
                                        <?php if ($file_kanri_excel_all != "") : ?>
                                            <?php print h($file_kanri_excel_all); ?>
                                        <?php endif; ?>

                                        <?php if ($file_bank_excel_all != "") : ?>
                                            <?php print h($file_bank_excel_all); ?>
                                        <?php endif; ?>


                                    </span>

                                </div>

                                <!-- ============ ダウンロードボタン ============ -->
                                <a class="btn-gradient-3d-dl d_btn" href="<?php print h($dowonload_uri . "?d=" . $g_file); ?>">
                                    ダウンロード
                                </a>



                                <!-- ============== 削除処理 Start ============== -->
                                <button class="btn-gradient-3d-orange" type="submit" name="deletex<?php print $idx; ?>" id="delete" value="<?php print h($test_uri) . h($g_file); ?>">
                                    ファイルを削除
                                </button>

                                <?php if (isset($_POST['deletex' . $idx])) : ?>

                                    <?php $name = $_POST['deletex' . $idx]; ?>

                                    <?php $new_name = str_replace("http://www.niigatadelica.jp/job_recruit/csv", "", $name); ?>

                                    <?php unlink("." . $new_name); ?>

                                    <?php echo ("<meta http-equiv='refresh' content='1'>"); ?>

                                    <?php break; ?>


                                <?php endif; ?>
                                <!-- ============== 削除処理　End ============== -->

                            </li>

                            <!-- =========================== CSV ファイルの出力 ================================== -->
                        <?php elseif (strpos($file_result, ".csv")) : ?>

                            <!-- 日付　選択時　（CSV ファイル名変換） -->
                            <?php $file_kanri_csv = ""; ?>
                            <?php $file_bank_csv = ""; ?>

                            <!-- 未入力ファイル　（CSV ファイル） -->
                            <?php $file_kanri_csv_all = ""; ?>
                            <?php $file_bank_csv_all = ""; ?>

                            <?php if (strpos($file_result, "Mcsv") !== false && strpos($file_result, "GinkouJ_") !== false) : ?>
                                <?php $file_bank_csv_all = str_replace("Mcsv_GinkouJ_", "（未入力 全件）銀行管理_", $file_result); ?>

                            <?php elseif (strpos($file_result, "Mcsv") !== false && strpos($file_result, "baito_kanri_") !== false) : ?>
                                <?php $file_kanri_csv_all = str_replace("Mcsv_baito_kanri_", "（未入力 全件）アルバイト管理_", $file_result); ?>

                            <?php elseif (strpos($file_result, "Baito_Kanri_") !== false && strpos($file_result, "Mcsv") == false) : ?>
                                <?php $file_kanri_csv = str_replace("Baito_Kanri_", "アルバイト管理_", $file_result); ?>

                            <?php elseif (strpos($file_result, "GinkouJ_") !== false && strpos($file_result, "Mcsv") == false) : ?>
                                <?php $file_bank_csv = str_replace("GinkouJ_", "銀行管理_", $file_result); ?>

                            <?php endif; ?>


                            <li class="item_csv">

                                <div class="item_csv_box">

                                    <!--
        <a href=" php file パス " class="li_csv_item" download>
        </a>                        
        -->

                                    <i class="fas fa-file-csv"></i>
                                    <span class="li_csv_item">

                                        <!-- ==========  CSV 日付選択ファイル 出力 ============ -->
                                        <?php if ($file_bank_csv != "") : ?>
                                            <?php print h($file_bank_csv); ?>
                                        <?php endif; ?>

                                        <?php if ($file_kanri_csv != "") : ?>
                                            <?php print h($file_kanri_csv); ?>
                                        <?php endif; ?>

                                        <!-- ==========  CSV 未入力ファイル択ファイル 出力 END ============ -->
                                        <?php if ($file_bank_csv_all != "") : ?>
                                            <?php print h($file_bank_csv_all); ?>
                                        <?php endif; ?>

                                        <?php if ($file_kanri_csv_all != "") : ?>
                                            <?php print h($file_kanri_csv_all); ?>
                                        <?php endif; ?>

                                    </span>

                                </div>

                                <!-- ============ ダウンロードボタン ============ -->
                                <a class="btn-gradient-3d-dl d_btn" href="<?php print h($dowonload_uri . "?d=" . $g_file); ?>">
                                    ダウンロード
                                </a>

                                <!-- ============== 削除処理 Start ============== -->
                                <button class="btn-gradient-3d-orange" type="submit" name="deletec<?php print $idx; ?>" id="delete" value="<?php print h($test_uri) . h($g_file); ?>">
                                    ファイルを削除
                                </button>

                                <?php if (isset($_POST['deletec' . $idx])) : ?>

                                    <?php $name = $_POST['deletec' . $idx]; ?>


                                    <?php $new_name_csv = str_replace("http://www.niigatadelica.jp/job_recruit/csv", "", $name); ?>


                                    <?php unlink("." . $new_name_csv); ?>


                                    <?php echo ("<meta http-equiv='refresh' content='1'>"); ?>

                                    <?php break; ?>


                                <?php endif; ?>
                                <!-- ============== 削除処理　End ============== -->

                            </li>

                        <?php endif; ?>

                        <?php $idx += 1; ?>

                    <?php endforeach; ?>
                    <!-- ============== ループ 終了 ==============  -->

                    <!--
</ul>
-->

            </form>
        </div>


    </div> <!-- END -->

    <!-- bootstrap マテリアル　読み込み -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.min.js"></script>



</body>

</html>