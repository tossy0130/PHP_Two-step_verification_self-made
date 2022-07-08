<?php


// エラーを出力する
ini_set('display_errors', "On");

// functions.php 読み込み
require(dirname(__FILE__) . '/../functions.php');

// エラーページディレクトリ
$kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

// セッションスタート
session_start();

// ************ 二重送信防止用トークンの発行 ************
$jim_token_2 = uniqid('', true);
// トークンをセッション変数にセット
$_SESSION['jim_token_2'] = $jim_token_2;


//=============== リダイレクト防止用 トークン　==================== 
// リダイレクト防止用　トークン取得
$jim_token = isset($_POST['jim_token']) ? $_POST['jim_token'] : "";
// セッション変数から値を取得
$session_jim_token = isset($_SESSION['jim_token']) ? $_SESSION['jim_token'] : "";

// セッション削除
// unset($_SESSION['jim_token']);



//================================== 変数初期化
$get_form_eh = "";

// フォーム関係変数
$user_name = "";   // 氏名
$furi_name = "";   // 氏名　かな


$email = ""; // メールアドレス

$spouse = 0; // 配偶者有無  0: なし  1: あり

$sex = 0; // 性別 0: 男性  1: 女性

$dependents_num = "";   //  扶養控除対象者人数

$birthday = "";   // 生年月日

$brth_year = "";  // POST 用　　年
$brth_mnth = "";  // POST 用　　月
$brth_dy = "";  // POST 用　　　日

$tel = ""; //   電話番号


$zip = "";   // 郵便番号
$address_01 = "";   // 住所

/*
 $furikomi_saki = "";
 $branch_name = "";
 $account_type = 0;
 $account_number = "";
 $account_holder = "";
 */


$My_number_information = "";
$Inquiry_form = "";

/*====================== 銀行系　情報 ======================== */

$employee_id = 0;  // 社員コード

$bank_code = 0; // 銀行コード
$bank_name = ""; // 銀行名
$bank_siten_code = 0; // 支店コード
$bank_siten_name = ""; // 支店名
$bank_kamoku = "";  //  0 => 普通　=> 当座
$bank_holder = ""; // 口座名義人

$kouzzz_number = ""; // 口座番号


// ============= form を POST する
if (isset($_POST['get_form_eh'])) {

    // get_form_eh が　空だった場合は　エラーとして　リダイレクトする。
    if (empty($_POST['get_form_eh'])) {

        //=== GET パラメーターがない場合 isset されていない　エラー処理
        header("Location: {$kari_uri}");
    } else {

        // OK 処理
        $get_form_eh = $_POST['get_form_eh'];
    }
} else {

    //=== エラー処理
    header("Location: {$kari_uri}");
}



//========================================
//======================　フォーム POST処理
//========================================
if (!empty($get_form_eh)) {

    //========== POST 取得処理
    $user_name = $_POST['user_name'];
    $furi_name = $_POST['furi_name'];

    $email = $_POST['email'];

    $sex = $_POST['sex'];

    $spouse = $_POST['spouse'];  // 配偶者有無  0: なし  1: あり
    $dependents_num = $_POST['dependents_num'];

    $brth_year = $_POST['brth_year'];  // POST 用　　年
    $brth_mnth = $_POST['brth_mnth'];  // POST 用　　月
    $brth_dy = $_POST['brth_dy'];  // POST 用　　　日

    //========== 生年月日　 2021-00-00
    $birthday = $brth_year . "-" . $brth_mnth . "-" . $brth_dy;


    $tel = $_POST['tel']; //   電話番号

    $zip = $_POST['zip'];   // 郵便番号
    $address_01 = $_POST['address_01'];   // 住所

    /*
    $furikomi_saki = $_POST['furikomi_saki'];
    $branch_name = $_POST['branch_name'];
    $account_type = $_POST['account_type'];
    $account_number = $_POST['account_number'];
    $account_holder = $_POST['account_holder'];
    */

    $My_number_information = $_POST['My_number_information'];
    $Inquiry_form = $_POST['Inquiry_form'];

    /* =============================== 銀行系データ取得 ============================ */


    $employee_id = $_POST['employee_id'];  // 社員コード

    $bank_code = "0"; // 銀行コード
    $bank_siten_code = "0"; // 支店コード

    $bank_name = $_POST['bank_name']; // 銀行名
    $bank_siten_name = $_POST['bank_siten_name']; // 支店名
    $bank_kamoku = $_POST['bank_kamoku'];  //  0 => 普通　=> 当座
    $bank_holder = $_POST['bank_holder']; // 口座名義人

    $kouzzz_number = $_POST['kouzzz_number']; // 口座番号



} else {

    //=== エラー処理
    header("Location: {$kari_uri}");
}


// ====================== POST トークンと　セッションのトークンが一致した場合
if ($jim_token != "" && $jim_token == $session_jim_token) {

    //if(isset($_POST['send'])) {

    // ==========================================
    // ======= ボタンを submit したら インサート処理
    // ==========================================

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

        // ====== SQL 文
        $stmt = $pdo->prepare("INSERT INTO User_Info_Table(
        user_name, furi_name, email, spouse,
        dependents_num, sex, birthday,zip,
         address_01, tel, My_number_information,Inquiry_form,data_Flg,
         employee_id ,bank_code ,bank_name ,bank_siten_code,
         bank_siten_name, bank_kamoku, kouzzz_number, bank_holder
    ) VALUES(
       :in_01 ,:in_02,:in_03 ,:in_04, 
       :in_05,:in_06,:in_07, :in_08, 
       :in_09, :in_10,:in_11,:in_12,:in_13,
       :in_14, :in_15,:in_16,:in_17,
       :in_18, :in_19,:in_20, :in_21       
    )");

        // ====== バインド
        $stmt->bindParam(':in_01', $user_name, PDO::PARAM_STR);
        $stmt->bindParam(':in_02', $furi_name, PDO::PARAM_STR);
        $stmt->bindParam(':in_03', $email, PDO::PARAM_STR);
        $stmt->bindParam(':in_04', $spouse, PDO::PARAM_STR);

        $stmt->bindParam(':in_05', $dependents_num, PDO::PARAM_STR);
        $stmt->bindParam(':in_06', $sex, PDO::PARAM_STR);
        $stmt->bindParam(':in_07', $birthday, PDO::PARAM_STR);
        $stmt->bindParam(':in_08', $zip, PDO::PARAM_STR);

        $stmt->bindParam(':in_09', $address_01, PDO::PARAM_STR);
        $stmt->bindParam(':in_10', $tel, PDO::PARAM_STR);
        $stmt->bindParam(':in_11', $My_number_information, PDO::PARAM_STR);
        $stmt->bindParam(':in_12', $Inquiry_form, PDO::PARAM_STR);

        $data_Flg = "0";
        $stmt->bindParam(':in_13',  $data_Flg, PDO::PARAM_STR); // data_FLG

        //==========  銀行系　追加　カラム  :in_13  ～  :in_19 ============
        $stmt->bindParam(':in_14', $employee_id, PDO::PARAM_STR);
        $stmt->bindParam(':in_15', $bank_code, PDO::PARAM_STR);
        $stmt->bindParam(':in_16', $bank_name, PDO::PARAM_STR);
        $stmt->bindParam(':in_17', $bank_siten_code, PDO::PARAM_STR);
        $stmt->bindParam(':in_18', $bank_siten_name, PDO::PARAM_STR);
        $stmt->bindParam(':in_19', $bank_kamoku, PDO::PARAM_STR);

        $stmt->bindParam(':in_20', $kouzzz_number, PDO::PARAM_STR);

        $stmt->bindParam(':in_21', $bank_holder, PDO::PARAM_STR);


        // SQL 実行
        $res = $stmt->execute();

        // トランザクション　コミット
        if ($res) {
            $pdo->commit();
        }

        // ****** SQL の結果を取り出す ****** 

        /*
    if($res) {
            $data = $stmt->fetch();
           var_dump("insert データ:::" . $data);
    }
    */

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {

        print('Error:' . $e->getMessage());

        // (トランザクション) ロールバック
        $pdo->rollBack();

        // ＊＊＊　エラー処理　＊＊＊ 
        header("Location: {$kari_uri}");
    } finally {

        $pdo = null;
    }

    //} //============= send ボタン処理 END



} else {
    //================================== トークンが一致しない場合

    // ＊＊＊　エラー処理　＊＊＊ 
    // header( "Location: {$kari_uri}");

    var_dump($jim_token);
    print "<br /><br />";
    var_dump($session_jim_token);
}


?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- jim style.css -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>フォームインサート画面</title>
</head>

<body>


    <div class="jumbotron">
        <div class="container">
            <h1 class="display-5">株式会社　●●</h1>
            <p class="lead">
                <!-- アルバイト者情報登録 -->
                <?php site_name_hozyo(); ?><br />
                入力情報のご確認
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

            <div class="col-2 form-flow-list-wrap py-3" style="line-height: 2.5;"><span class="form-flow-list-no">2</span>入力</div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap form-flow-active py-3" style="line-height: 2.5;"><span class="form-flow-list-no">3</span>確認</div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap py-3" style="line-height: 2.5;"><span class="form-flow-list-no">4</span>完了</div>
        </div>
    </div>


    <!-- データ　確認画面 -->
    <div class="container">
        <table class="table table-hover col-10 ml-8">
            <thead>
                <tr>
                    <th>氏名</th>
                    <th><?php print h($user_name); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>氏名かな</td>
                    <td><?php print h($furi_name); ?></td>
                </tr>

                <tr>
                    <td>性別</td>
                    <td>

                        <?php if ($sex == 0) : ?>
                            <span>男性</span>
                        <?php else : ?>
                            <span>女性</span>
                        <?php endif; ?>

                    </td>
                </tr>

                <tr>
                    <td>生年月日</td>
                    <td><?php print h($birthday); ?></td>
                </tr>

                <tr>
                    <td>住所</td>
                    <td><?php print h($address_01); ?></td>
                </tr>

                <tr>
                    <td>電話番号</td>
                    <td><?php print h($tel); ?></td>
                </tr>

                <tr>
                    <td>メールアドレス</td>
                    <td><?php print h($email); ?></td>
                </tr>

                <tr>
                    <td>配偶者有無</td>
                    <td>
                        <?php if ($spouse == 0) : ?>
                            <span>無し</span>
                        <?php else : ?>
                            <span>有り</span>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <td>扶養控除 対象者人数</td>
                    <td><?php print h($dependents_num); ?></td>
                </tr>


                <tr>
                    <td>マイナンバー</td>
                    <td><?php print h($My_number_information); ?></td>
                </tr>

                <!--　=============== 銀行情報 ============== -->
                <tr>
                    <td>従業員コード</td>
                    <td><?php print h($employee_id); ?></td>
                </tr>

                <!-- 銀行コード
                <tr>
                    <td>銀行コード</td>
                    <td></td>
                </tr>
                 -->

                <tr>
                    <td>銀行名</td>
                    <td><?php print h($bank_name); ?></td>
                </tr>

                <!-- 支店コード
                <tr>
                    <td>支店コード</td>
                    <td></td>
                </tr>
                        -->

                <tr>
                    <td>支店名</td>
                    <td><?php print h($bank_siten_name); ?></td>
                </tr>

                <!-- 口座科目 bank_kamoku -->
                <tr>
                    <td>口座番号</td>
                    <td>
                        <?php if (strcmp($bank_kamoku, "0") == 0) : ?>
                            <?php print h("普通口座"); ?>
                        <?php else : ?>
                            <?php print h("当座預金"); ?>
                        <?php endif; ?>
                    </td>
                </tr>


                <tr>
                    <td>口座番号</td>
                    <td><?php print h($kouzzz_number); ?></td>
                </tr>
                コードコード
                <tr>
                    <td>口座名義</td>
                    <?php if ($bank_kamoku == 0) : ?>
                        <td><?php print h("普通"); ?></td>
                    <?php elseif ($bank_kamoku == 1) : ?>
                        <td><?php print h("当座"); ?></td>
                    <?php else : ?>
                        <td><?php print h("未選択"); ?></td>
                    <?php endif; ?>
                </tr>



                <tr>

                    <td>お問い合わせ内容</td>
                    <td><?php print h($Inquiry_form); ?></td>
                </tr>

            </tbody>
        </table>
    </div>


    <!-- 処理 -->
    <div class="container col-lg-12 col-xs-12 col-sm-12 col-12" style="margin-bottom:40px;">

        <form action="completion_page.php" method="POST" id="" class="col-lg-10 col-sm-10 col-10" novalidate enctype="multipart/form-data" style="margin: 60px 0 180px 0;">

            <input type="hidden" name="user_name" value="<?php print h($user_name); ?>">
            <input type="hidden" name="furi_name" value="<?php print h($furi_name); ?>">
            <input type="hidden" name="age" value="<?php print h($age); ?>">
            <input type="hidden" name="email" value="<?php print h($email);  ?>">

            <input type="hidden" name="spouse" value="<?php print h($spouse); ?>">
            <input type="hidden" name="dependents_num" value="<?php print h($dependents_num);  ?>">
            <input type="hidden" name="sex" value="<?php print h($sex); ?>">
            <input type="hidden" name="birthday" value="<?php print h($birthday); ?>">

            <input type="hidden" name="zip" value="<?php print h($zip); ?>">
            <input type="hidden" name="address_01" value="<?php print h($address_01); ?>">
            <input type="hidden" name="tel" value="<?php print h($tel); ?>">
            <input type="hidden" name="My_number_information" value="<?php print h($My_number_information); ?>">
            <input type="hidden" name="Inquiry_form" value="<?php print h($Inquiry_form); ?>">

            <!-- data_Flg -->
            <input type="hidden" name="data_Flg" value="<?php print h("0"); ?>">

            <!-- ================== 銀行情報　================== -->
            <input type="hidden" name="employee_id" value="<?php print h($employee_id); ?>">
            <input type="hidden" name="bank_code" value="<?php print h($bank_code); ?>">
            <input type="hidden" name="bank_name" value="<?php print h($bank_name); ?>">
            <input type="hidden" name="bank_siten_code" value="<?php print h($bank_siten_code); ?>">
            <input type="hidden" name="bank_siten_name" value="<?php print h($bank_siten_name); ?>">
            <input type="hidden" name="bank_kamoku" value="<?php print h($bank_kamoku); ?>">

            <input type="hidden" name="bank_kamoku" value="<?php print h($kouzzz_number); ?>">

            <input type="hidden" name="bank_holder" value="<?php print h($bank_holder); ?>">


            <!--　ボタン -->
            <div class="form-row">
                <div class="col">

                    <!-- Button trigger modal -->
                    <button type="button" id="url_go_back_btn" class="btn csv_btn_3" data-toggle="modal" data-target="#exampleModal3" style="color:#000;">
                        戻る
                    </button>

                    <!-- Modal 戻るボタン -->
                    <div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModal3Label" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModal3Label">確認画面</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    前の画面へ戻りますか？
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-info" data-dismiss="modal" onClick="history.back()">はい</button>
                                    <button type="button" class="btn btn-info" data-dismiss="modal" aria-label="Close">いいえ</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="col">
                    <button type="submit" id="url_go_send_btn" name="send" class="form-control btn csv_btn_3" style="color:#000;background: lightblue;">登録確定</button>
                </div>

            </div>


            <!-- リダイレクト防止用　トークン -->
            <input type="hidden" name="jim_token_2" value="<?php print h($jim_token_2); ?>">


        </form>


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


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    <!-- ============= スマホ用　カレントページ表示対策 -->
    <script>
        function isSmartPhone() {

            console.log("テスト function");

            if (window.matchMedia && window.matchMedia('(max-device-width: 599px)').matches) {

                console.log("テスト function スマホ");

                var cl_one = document.getElementById("cl_one");

                console.log(cl_one.innerText);

                cl_one.innerText = "パス";

                return true;
            } else {

                console.log("テスト function PC");

                // PC は　何もしない

            }

        }

        window.addEventListener('load', (event) => {

            console.log("テストイベント");

            isSmartPhone();

        });
    </script>



</body>

</html>