<?php

ini_set('display_errors', "On");

// functions.php 読み込み
require(dirname(__FILE__) . '/../functions.php');

$get_file_name = "";

// get パラメーター　取得
if (isset($_GET['d'])) {

    if (!empty($_GET['d'])) {
        $get_file_name = $_GET['d'];
    }
}



//===================================
// =========== 現在のフルURL 取得
//===================================
if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {

    $protocol = 'https://';
} else {

    $protocol = 'http://';
}

// ./files の . を削除
$new_create_excel = mb_substr($create_excel, 1);



$protocol .= $_SERVER["HTTP_HOST"] . '/job_recruit/csv/' . $get_file_name;
//. $_SERVER["REQUEST_URI"];

//==============================================
//*************** ファイルのダウンロード処理 */
//==============================================
//  t_download($protocol);

// files_20211207_
$file_name = substr($get_file_name, 15);

// ======================= ********* =============================
// =======================  ファイル名 変換　=============================
// ======================= ********* =============================
if (strpos($file_name, "Mcsv") !== false && strpos($file_name, "baito_kanri_") !== false) {
    $file_name = str_replace("Mcsv_baito_kanri_", "（未入力 全件）アルバイト管理_", $file_name);
} elseif (strpos($file_name, "Mcsv") !== false && strpos($file_name, "GinkouJ_") !== false) {
    $file_name = str_replace("Mcsv_GinkouJ_", "（未入力 全件）銀行管理_", $file_name);
} elseif (strpos($file_name, "R_Baito_Kanri") !== false && strpos($file_name, "M_baito_") == false) {
    $file_name = str_replace("R_Baito_Kanri", "アルバイト管理_", $file_name);
} elseif (strpos($file_name, "Ginkou_Baito_Kanri") !== false && strpos($file_name, "M_ginkou_") == false) {
    $file_name = str_replace("Ginkou_Baito_Kanri", "銀行管理_", $file_name);
} elseif (strpos($file_name, "Baito_Kanri_") !== false && strpos($file_name, "Mcsv") == false && strpos($file_name, 'R_Baito_Kanri') == false) {
    $file_name = str_replace("Baito_Kanri_", "アルバイト管理_", $file_name);
} elseif (strpos($file_name, "GinkouJ_") !== false && strpos($file_name, "Mcsv") == false && strpos($file_name, "Ginkou") == false) {
    $file_name = str_replace("GinkouJ_", "銀行管理_", $file_name);
} elseif (strpos($file_name, "M_baito_") !== false) {
    $file_name = str_replace("M_baito_", "（未入力 全件）アルバイト管理_", $file_name);
} elseif (strpos($file_name, "M_ginkou_") !== false) {
    $file_name = str_replace("M_ginkou_", "（未入力 全件）銀行管理_", $file_name);
}


// ファイルタイプを指定
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
// ファイルサイズを取得し、ダウンロードの進捗を表示
//        header('Content-Length: '.filesize($protocol));
// ファイルのダウンロード、リネームを指示
header('Content-Disposition: attachment; filename="' . $file_name . '"');

ob_clean();  //追記
flush();     //追記

// ファイルを読み込みダウンロードを実行
readfile($protocol);
exit;

while (ob_get_level() > 0) {
    ob_end_clean();
}
ob_start();

if ($file = fopen($filepath, 'rb')) {
    while (!feof($file) and (connection_status() == 0)) {
        echo fread($file, '4096');
        ob_flush();
    }
    ob_flush();
    fclose($file);
}
ob_end_clean();
