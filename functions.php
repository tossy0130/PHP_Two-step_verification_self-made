<?php

// エスケープ処理　関数
function h($var)
{
    return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
}


//*********************** */
//**************** DB カラム 削除 関数 */
//*********************** */
function DB_delete_column($p_h)
{

    //================================  
    //================== 接続情報
    //================================  
    $dsn = 'mysql:dbname=d05618yadb1;host=localhost;port=3306';
    $user = 'd05618ya';
    $password = 'deli7332+C';

    try {

        // PDO オブジェクト作成
        $pdo = new PDO($dsn, $user, $password);

        // ======  （トランザクション） トランザクション開始 ======
        $pdo->beginTransaction();

        // bind する場合は、  
        // $stmt = $pdo->prepare("SELECT * FROM user_list WHERE id = :id");
        // $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        //***** GET の末尾の　URL を取って、　最新の１件　取得 */
        $stmt = $pdo->prepare("DELETE FROM valid_table WHERE pass_hh = :pass_hh");

        // パスワード　ハッシュ値
        $stmt->bindParam(':pass_hh', $p_h, PDO::PARAM_STR);

        $stmt->execute();

        // ======= (トランザクション) コミット =========
        $pdo->commit();
    } catch (PDOException $e) {
        print('Error:' . $e->getMessage());

        // ======= (トランザクション) ロールバック =========
        $pdo->rollBack();

        die();
    }


    $pdo = null;
    // ****** データベースの接続解除 ******
    /**========== DB 接続 END =========== */
} // =========================== END function DB_delete_column()


//================== サイト名　表示関数
function site_name_hozyo()
{

    print h("アルバイト者情報登録");
}



//================== 半年経過したデータ　は自動削除
function half_year_delete()
{

    //================================  
    //================== 接続情報
    //================================  
    $dsn = 'mysql:dbname=d05618yadb1;host=localhost;port=3306';
    $user = 'd05618ya';
    $password = 'deli7332+C';


    try {

        // PDO オブジェクト作成
        $pdo = new PDO($dsn, $user, $password);

        // pdo トランザクション開始
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM User_Info_Table WHERE 
                        creation_time < DATE_SUB(CURDATE(), INTERVAL ? MONTH)");

        // 削除リミット
        $limit = 6;
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);

        $res = $stmt->execute();

        if ($res) {
            // コミット
            $pdo->commit();
        }
    } catch (PDOException $e) {

        print('Error:' . $e->getMessage());

        // ======= (トランザクション) ロールバック =========
        $pdo->rollBack();
    }
} // ============ END half_year_delete


//====================================
//============== ファイルダウンロード
//====================================
function t_download($pPath, $pMimeType = null)
{

    // ファイルが読めない時はエラー
    if (!is_readable($pMimeType)) {
        print "err" . die($pPath);
    }

    //-- Content-Typeとして送信するMIMEタイプ(第2引数を渡さない場合は自動判定) 

    $mimeType = (isset($pMimeType)) ? $pMimeType : (new finfo(FILEINFO_MIME_TYPE))->file($pPath);

    //-- 適切なMIMEタイプが得られない時は、未知のファイルを示すapplication/octet-streamとする
    if (preg_match('/\A\S+?\/\S+/', $mimeType)) {
        $mimeType = 'application/octet-stream';
    }

    // Content-Type
    header('Content-Type:' . $mimeType);

    //-- ウェブブラウザが独自にMIMEタイプを判断する処理を抑止する
    //  header('X-Content-Type-Options: nosniff');

    //-- ダウンロードファイルのサイズ
    header('Content-Disposition: attachment; filename="' . basename($pPath) . '"');

    //-- keep-aliveを無効にする
    header('Connection: close');

    //-- readfile()の前に出力バッファリングを無効化する 
    while (ob_get_level()) {
        ob_end_clean();
    }
    //-- 出力
    readfile($pPath);

    //-- 最後に終了させるのを忘れない


}

//===================== 西暦を和暦変換
function Wareki_Parse($year)
{

    $pars = [
        ['year' => 2018, 'year_name' => '令和'],
        ['year' => 1988, 'year_name' => '平成'],
        ['year' => 1925, 'year_name' => '昭和'],
        ['year' => 1911, 'year_name' => '大正'],
        ['year' => 1867, 'year_name' => '明治']
    ];

    foreach ($pars as $pas) {

        $base_year = $pas['year'];
        $base_year_name = $pas['year_name'];

        if ($year > $base_year) {
            // 年の計算  入力値 - $pars[year]
            $result_year = $year - $base_year;

            // 元年
            if ($result_year === 1) {
                return $base_year_name . "元年";
            }

            return $base_year_name . $result_year . "年";
        }
    }
    return null;
} //======== END Function
