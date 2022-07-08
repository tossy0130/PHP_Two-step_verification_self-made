<?php 

// エラーを出力する
ini_set('display_errors', "On");

// functions.php 読み込み
require (dirname(__FILE__). '/../functions.php');

//================================  
//================== 接続情報
//================================  
$dsn = 'mysql:dbname=d05618yadb1;host=localhost;port=3306';
$user = 'd05618ya';
$password = 'deli7332+C';
// テーブル名: bank_api_table => 銀行用api テーブル


 $pdo = new PDO($dsn, $user, $password);

 // ==== トランザクション開始 ===
 $pdo->beginTransaction();

 $bank_name = $_GET['bank_name'];

 $test_sql = '新潟';
 $test_sql_kana = 'ﾆｲｶﾞﾀ';

 try {

    //============== 検索対象　多め
    /*   
        $stmt = $pdo->prepare("SELECT * FROM test_01_table WHERE 
            (bank_name like :val_01 or bank_name like :val_02 or bank_name like :val_03) OR 
            (bank_kana like :val_04 or bank_kana like :val_05 or bank_kana like :val_06);");
    */ 


    //======== 検索対象　絞り
    $stmt = $pdo->prepare("SELECT * FROM bank_api_table WHERE 
        (bank_name = :val_04 or bank_name like :val_01 or bank_name or bank_name like :val_02) and 
        bank_item = :val_03");


              /* === 検索対象　多め
        $stmt->bindValue(":val_01", '%' . $test_sql,PDO::PARAM_STR);
        $stmt->bindValue(":val_02", '%' . $test_sql . '%',PDO::PARAM_STR);
        $stmt->bindValue(":val_03", $test_sql . '%',PDO::PARAM_STR);
        $stmt->bindValue(":val_04", '%' . $test_sql,PDO::PARAM_STR);
        $stmt->bindValue(":val_05", '%' . $test_sql . '%',PDO::PARAM_STR);
        $stmt->bindValue(":val_06", $test_sql . '%',PDO::PARAM_STR);
        */
        
        $stmt->bindValue(":val_01", '%' . $bank_name,PDO::PARAM_STR);
        $stmt->bindValue(":val_02", $bank_name . '%',PDO::PARAM_STR);
        $stmt->bindValue(":val_03", "1" ,PDO::PARAM_STR);

        $stmt->bindValue(":val_04", $bank_name ,PDO::PARAM_STR);

   //     $stmt->bindValue(":val_03", $bank_name . '%',PDO::PARAM_STR);

        /*
        $stmt->bindValue(":val_04", '%' . $bank_name,PDO::PARAM_STR);
        $stmt->bindValue(":val_05", '%' . $bank_name . '%',PDO::PARAM_STR);
        $stmt->bindValue(":val_06", $bank_name . '%',PDO::PARAM_STR);
        */
        
  

        $stmt->execute();

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


    //    var_dump($get_arr);

       // ヘッダーを指定することによりjsonの動作を安定させる
       header('Content-type: application/json');

       // json に　変換する
       echo json_encode($get_arr);


 } catch(PDOException $e) {

    print('Error:'.$e->getMessage());
    // ======= (トランザクション) ロールバック =========
    $pdo->rollBack();

 } finally {

    $pdo = null;

 }


?>