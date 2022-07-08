<?php 

// エラーを出力する
ini_set('display_errors', "On");

// functions.php 読み込み
require (dirname(__FILE__). '/../functions.php');

// === GET パラメーター取得
$banc_code = $_GET['bank_code'];

//================================  
//================== 接続情報
//================================  
$dsn = 'mysql:dbname=d05618yadb1;host=localhost;port=3306';
$user = 'd05618ya';
$password = 'deli7332+C';

// テーブル名: bank_api_table => 銀行用api テーブル

$pdo = new PDO($dsn, $user, $password);

 // === トランザクション開始 ===
// $pdo->beginTransaction();

try {

    $sql = "SELECT bank_code, bank_num, bank_kana,bank_name,bank_item
         FROM bank_api_table WHERE bank_code = ?";
    // SQLをセット
    $stmt = $pdo->prepare($sql);
    // SQLを実行
    $stmt->execute(array($banc_code));

    $get_arr = [];

    // === GET パラメータの値から SQL の値を取得
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
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

} catch(PDOException $e) {

    print('Error:'.$e->getMessage());

    // ======= (トランザクション) ロールバック =========
   // $pdo->rollBack();
        
} finally {

    $pdo = null;

}
