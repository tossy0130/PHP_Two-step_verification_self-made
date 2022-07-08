<?php

// エラーを出力する
ini_set('display_errors', "On");

// functions.php 読み込み
require(dirname(__FILE__) . '/../functions.php');

//================================  
//================== 接続情報
//================================  
$dsn = 'mysql:dbname=d05618yadb1;host=localhost;port=3306';
$user = 'd05618ya';
$password = 'deli7332+C';
// テーブル名: bank_api_table => 銀行用api テーブル

$pdo = new PDO($dsn, $user, $password);

//========== GET パラメーター取得
$bank_code = $_GET['bank_code'];
$bank_num = $_GET['bank_num'];

try {

    $stmt = $pdo->prepare("SELECT * FROM bank_api_table WHERE 
        bank_code = :in_01 and bank_num = :in_02 and bank_item = '2' ");

    $stmt->bindValue('in_01', $bank_code, PDO::PARAM_STR);
    $stmt->bindValue('in_02', $bank_num, PDO::PARAM_STR);

    $stmt->execute();

    // === GET パラメータの値から SQL の値を取得
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $get_arr[] = array(
            'bank_code' => $row['bank_code'],
            'bank_num'  => $row['bank_num'],
            'bank_kana' => $row['bank_kana'],
            'bank_name' => $row['bank_name'],
            'bank_item' => $row['bank_item']
        );
    }

    // ヘッダーを指定することによりjsonの動作を安定させる
    header('Content-type: application/json');

    // json に　変換する
    echo json_encode($get_arr);
} catch (PDOException $e) {

    print('Error:' . $e->getMessage());
} finally {

    $pdo = null;
}
