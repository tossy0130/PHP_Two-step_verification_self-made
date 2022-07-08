<?php

// エラーを出力する
ini_set('display_errors', "On");

// functions.php 読み込み
require(dirname(__FILE__) . '/../functions.php');

// セッションスタート
session_start();

$uri = (dirname(__FILE__) . '/err/err.php');

// エラーページディレクトリ
$kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

// =========== 変数定義
$get_e = ""; // GET 用

$get_h = ""; // GET 用


$max_user = 0;

// ************ 二重送信防止用トークンの発行 ************
$jim_token = uniqid('', true);
// トークンをセッション変数にセット
$_SESSION['jim_token'] = $jim_token;

// GET パラメーター　取得処理
// ====== 空うちの URL を弾く ======

// natsume-jimnet.co.jp
if (isset($_GET['e'])) {

    if (!empty($_GET['e'])) {

        // GET パラメーター　格納
        $get_e = $_GET['e'];
    } else {

        //=== GET パラメーターが　「空」　エラー処理
        header("Location: {$kari_uri}");
    }
} else {

    //=== GET パラメーターがない場合 isset されていない　エラー処理
    header("Location: {$kari_uri}");
}

// GET パラメーター　取得処理
// ====== 空うちの URL を弾く ======
if (isset($_GET['h'])) {

    if (!empty($_GET['h'])) {

        // GET パラメーター　格納
        $get_h = $_GET['h'];
    } else {

        //=== GET パラメーターが　「空」　エラー処理
        header("Location: {$kari_uri}");
    }
} else {

    //=== GET パラメーターがない場合 isset されていない　エラー処理
    header("Location: {$kari_uri}");
}

// === GET パラメーター　結合
$get_form_eh = $get_e . $get_h;


//================================  
//================== 接続情報
//================================  
$dsn = 'mysql:dbname=d05618yadb1;host=localhost;port=3306';
$user = 'd05618ya';
$password = 'deli7332+C';

//========== user_id の MAX を取得

try {

    // PDO オブジェクト作成
    $pdo = new PDO($dsn, $user, $password);

    // user_id の MAX を取得
    $stmt = $pdo->prepare("SELECT MAX(user_id) as user_max FROM User_Info_Table");

    // 実行
    $res = $stmt->execute();

    // 結果取得
    $result = $stmt->fetch();

    // 銀行 id を入力
    $max_user = (int)($result['user_max']) + 101;

    //   print("MAX取得:::" . $max_user);


} catch (PDOException $e) {

    print('Error:' . $e->getMessage());

    // =============== リダイレクトで別のエラーページへ飛ばす
    //    header( "Location: {$kari_uri}");

} finally {

    $pdo = null;
}


?>

<!doctype html>
<html lang="ja">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->

    <head>

        <!-- jim style.css -->
        <link rel="stylesheet" href="../css/style.css">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <!-- font-awsome -->
        <!--Font Awesome5-->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">

        <!-- font awsome cdn -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">

        <style>
            .f_h3 {
                font-size: 20px;
                margin: 15px 0px;
                padding: 5px 0 5px 15px;
                border-left: 7px solid #4CAF50;
            }


            input[type="number"]::-webkit-outer-spin-button,
            input[type="number"]::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }


            input[type="number"] {
                -moz-appearance: textfield;
            }

            /*===================== エラー =====================*/
            .error {
                color: #8a0421;
                border-color: #dd0f3b;
                background-color: #ffd9d9;
            }

            .m_err {
                color: #f00;
                font-weight: 400;
                margin: 5px 0 0 0;
                display: inline-block;
            }

            /* ラジオボックス */
            .ra_label {
                margin: 0 15px 0 15px;
            }

            .ra_box {
                width: 80%;
                height: 1.4em;
            }


            /* ================== フォームcss ================= */
        </style>



        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>


        <!-- ajaxzip3 -->
        <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>


        <!-- 修正箇所1 titleセクションを呼び出す。デフォルト値は no titleとする -->
        <title></title>
    </head>

<body>


    <div class="jumbotron">
        <div class="container">
            <h1 class="display-5">株式会社　●●</h1>
            <p class="lead">
                <!-- アルバイト者情報登録 -->
                <?php site_name_hozyo(); ?><br />
                情報ご入力
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

            <div class="col-2 form-flow-list-wrap form-flow-active py-3" style="line-height: 2.5;"><span class="form-flow-list-no">2</span>入力</div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap py-3" style="line-height: 2.5;"><span class="form-flow-list-no">3</span>確認</div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap py-3" style="line-height: 2.5;"><span class="form-flow-list-no">4</span>完了</div>
        </div>
    </div>


    <div class="container" id="app">
        <!-- Vue.js 用  id="app" -->
        <!-- Vue.js 用　テスト -->
        <p>{{ title }}</p>

        <form action="coinfrm.php" method="POST" class="form-horizontal" @submit.prevent="submitForm" name="t_submit">


            <!--　＊＊＊ 氏名 ＊＊＊ -->
            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">
                    <dt>
                        <span class="form-input-tbl-head">氏名</span>
                        <span class="mark-required">必須</span>
                    </dt>

                    <dd>
                        <div class="jsc-required jsc-limited">
                            <!--
        <div class="form-group">
        <span class="mark-required">必須</span>
        <label class="col-sm-3 control-label" for="name1">氏名</label>
        -->

                            <input type="text" class="form-control w_320" name="user_name" id="user_name" v-model="user_name" @blur="$v.user_name.$touch()" maxlength="20" :class="{ error : $v.user_name.$error,'form-control': true }">

                            <div v-if="$v.user_name.$error">
                                <!-- エラーメッセージ -->
                                <span class="m_err" v-if="!$v.user_name.required">
                                    <i class="fas fa-exclamation-triangle"></i>「氏名」が入力されていません。
                                </span>
                            </div>

                        </div>
                    </dd>

                </dl>
            </div>
            <!--　＊＊＊ 氏名 ＊＊＊ END -->

            <!--　＊＊＊ 氏名かな ＊＊＊ -->
            <div class="col-xs-12 col-sm-12 col-lg-10 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">
                    <dt>
                        <span class="form-input-tbl-head">氏名かな</span>
                        <span class="mark-required">必須</span>
                    </dt>

                    <dd>
                        <div class="jsc-required jsc-limited">

                            <input type="text" class="form-control w_320" name="furi_name" id="furi_name" v-model="furi_name" @blur="$v.furi_name.$touch()" maxlength="20" :class="{ error : $v.furi_name.$error,'form-control': true }">

                            <div v-if="$v.furi_name.$error">
                                <!-- エラーメッセージ -->
                                <span class="m_err" v-if="!$v.furi_name.required">
                                    <i class="fas fa-exclamation-triangle"></i>「氏名かな」が入力されていません。
                                </span>
                            </div>

                        </div>
                    </dd>

                </dl>
            </div>
            <!--　＊＊＊ 氏名かな END ＊＊＊ -->

            <!--　＊＊＊ 性別 ＊＊＊ -->
            <div class="col-xs-12 col-sm-12 col-lg-10 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">
                    <dt>
                        <span class="form-input-tbl-head">性別</span>
                        <span class="mark-required">必須</span>
                    </dt>

                    <dd>
                        <div class="jsc-required jsc-limited">

                            <label class="ra_label" for="male">
                                男姓
                                <input type="radio" name="sex" value="0" id="male" v-model="sex" @blur="$v.sex.$touch()" class="ra_box">
                            </label>

                            <label class="ra_label" for="female">
                                女姓
                                <input type="radio" name="sex" value="1" id="female" v-model="sex" @blur="$v.sex.$touch()" class="ra_box">
                            </label>

                            <!-- エラーメッセージ -->
                            <div v-if="$v.sex.$error">
                                <span class="m_err" v-if="!$v.sex.required">
                                    <i class="fas fa-exclamation-triangle"></i>「性別」を選択してください。
                                </span>
                            </div>

                        </div>
                    </dd>
                </dl>

            </div>
            <!--　＊＊＊ 性別 ＊＊＊ END -->


            <!--　＊＊＊ 生年月日 ＊＊＊ -->
            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">

                    <dt>

                        <span class="form-input-tbl-head">生年月日</span>
                        <span class="mark-required">必須</span>

                    </dt>


                    <dd>
                        <div class="jsc-required jsc-limited">

                            <!-- 生年月日 西暦 -->
                            <select name="brth_year" id="brth_year" v-model="brth_year" @blur="$v.brth_year.$touch()" class="app_input_form date-birth-year jsc-select-bold">

                                <option value="" class="jsc-default-selected">選択</option>

                                <!-- 値 -->
                                <?php for ($i = 2006; $i >= 1922; $i--) : ?>

                                    <option class="jsc-select-bold" value="<?php print h($i) ?>">
                                        <?php print h($i); ?>
                                    </option>

                                <?php endfor; ?>

                            </select>



                            <span class="period-m">年</span>

                            <!-- 生年月日 月 -->
                            <select name="brth_mnth" id="brth_mnth" class="date-birth-month jsc-select-bold" v-model="brth_mnth" @blur="$v.brth_mnth.$touch()">
                                <option value="" class="jsc-default-selected">選択</option>


                                <?php for ($i = 1; $i <= 12; $i++) : ?>

                                    <option class="jsc-select-bold" value="<?php print h($i); ?>">
                                        <?php print h($i); ?>
                                    </option>

                                <?php endfor; ?>

                            </select>



                            <span class="period-m">月</span>

                            <!-- 生年月日 日 -->
                            <select name="brth_dy" id="brth_dy" class="date-birth-day jsc-select-bold error-field" v-model="brth_dy" @blur="$v.brth_dy.$touch">

                                <option value="" class="jsc-default-selected">選択</option>

                                <?php for ($i = 1; $i <= 31; $i++) : ?>

                                    <option class="jsc-select-bold" value="<?php print h($i); ?>">
                                        <?php print h($i); ?>
                                    </option>

                                <?php endfor; ?>

                            </select>

                            <span class="period-m">日</span>


                        </div>
                    </dd>

                    <!-- エラーメッセージ -->
                    <div v-if="$v.brth_year.$error">
                        <span class="m_err" v-if="!$v.brth_year.required">
                            <i class="fas fa-exclamation-triangle"></i>「年」を選択してください。
                        </span>
                    </div>
                    <!-- エラーメッセージ END -->

                    <!-- エラーメッセージ -->
                    <div v-if="$v.brth_mnth.$error">
                        <span class="m_err" v-if="!$v.brth_mnth.required">
                            <i class="fas fa-exclamation-triangle"></i>「月」を選択してください。
                        </span>
                    </div>
                    <!-- エラーメッセージ END -->

                    <!-- エラーメッセージ -->
                    <div v-if="$v.brth_dy.$error">
                        <span class="m_err" v-if="!$v.brth_dy.required">
                            <i class="fas fa-exclamation-triangle"></i>「月」を選択してください。
                        </span>
                    </div>
                    <!-- エラーメッセージ END -->

                </dl>
            </div>


            <!-- ＊＊＊　生年月日 END ＊＊＊ -->


            <!--　＊＊＊ 住所 ＊＊＊ -->
            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <p style="color:#FF9294;font-weight: bold;">※住民票に記載されているご住所をご入力ください。</p>
                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">

                    <dt>

                        <span class="form-input-tbl-head">住所</span>
                        <span class="mark-required">必須</span>
                    </dt>

                    <dd>
                        <div class="jsc-required jsc-limited">

                            〒<br /><span id="haifun">（-ハイフン入れずにご入力ください）</span><input type="number" name="zip" class="form-control w_160" onkeyup="AjaxZip3.zip2addr(this, '', 'address_01', 'address_01');" maxlength="7" v-model="zip" /><br>

                            <!-- エラーメッセージ -->
                            <div v-if="$v.zip.$error">

                                <span class="m_err" v-if="!$v.zip.required">
                                    <i class="fas fa-exclamation-triangle"></i>「郵便番号」を選択してください。
                                </span>

                                <span class="m_err" v-if="!$v.zip.minLength">
                                    <i class="fas fa-exclamation-triangle"></i>「7桁（-ハイフン無し）」で入力してください。
                                </span>

                                <span class="m_err" v-if="!$v.zip.maxLength">
                                    <i class="fas fa-exclamation-triangle"></i>「7桁（-ハイフン無し）」で入力してください。
                                </span>

                            </div>


                            住所<input type="text" name="address_01" class="form-control w_540" v-model="address_01" @blur="$v.address_01.$touch" />

                            <!-- エラーメッセージ -->
                            <div v-if="$v.address_01.$error">
                                <span class="m_err" v-if="!$v.address_01.required">
                                    <i class="fas fa-exclamation-triangle"></i>「住所」を入力してください。
                                </span>
                            </div>

                        </div>
                    </dd>
                </dl>
            </div>
            <!--　＊＊＊ 住所 END ＊＊＊ -->


            <!--　＊＊＊ 電話番号 ＊＊＊ -->
            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">

                    <dt>
                        <span class="form-input-tbl-head">電話番号</span>
                        <span class="mark-required">必須</span>
                    </dt>


                    <dd>
                        <div class="jsc-required jsc-limited">

                            <input type="tel" class="form-control w_320" name="tel" maxlength="13" id="tel" v-model="tel" @blur="$v.tel.$touch()" :class="{ error : $v.tel.$error,'form-control': true }">

                            <div v-if="$v.tel.$error">
                                <!-- エラーメッセージ -->
                                <span class="m_err" v-if="!$v.tel.required">
                                    <i class="fas fa-exclamation-triangle"></i>「電話番号」が入力されていません。
                                </span>

                            </div>
                    </dd>

                </dl>
            </div>
            <!--　＊＊＊ 電話番号 END ＊＊＊ -->


            <!--　＊＊＊ メールアドレス ＊＊＊ -->
            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">

                    <dt>
                        <span class="form-input-tbl-head">Mail</span>
                        <span class="mark-required">必須</span>
                    </dt>


                    <dd>
                        <div class="jsc-required jsc-limited">

                            <input type="email" class="form-control w_320" name="email" id="email" v-model="email" @blur="$v.email.$touch()" :class="{ error : $v.email.$error,'form-control': true }">

                            <div v-if="$v.email.$error">
                                <!-- エラーメッセージ -->
                                <span class="m_err" v-if="!$v.email.required">
                                    <i class="fas fa-exclamation-triangle"></i>「E-mail」が入力されていません。
                                </span>

                                <span class="m_err" v-if="!$v.email.email">
                                    <i class="fas fa-exclamation-triangle"></i>正しい形式で入力してください。
                                </span>
                            </div>

                        </div>
                    </dd>

                </dl>
            </div>
            <!--　＊＊＊ メールアドレス END ＊＊＊ -->


            <!--　＊＊＊ 配偶者 ＊＊＊ -->
            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">
                    <dt>
                        <span class="form-input-tbl-head">配偶者有無</span>
                        <span class="mark-required">必須</span>
                    </dt>

                    <dd>
                        <div class="jsc-required jsc-limited">

                            <label class="ra_label" for="male">
                                無し
                                <input type="radio" name="spouse" value="0" id="male" v-model="spouse" @blur="$v.spouse.$touch()" class="ra_box">
                            </label>

                            <label class="ra_label" for="female">
                                有り
                                <input type="radio" name="spouse" value="1" id="female" v-model="spouse" @blur="$v.spouse.$touch()" class="ra_box">
                            </label>

                            <!-- エラーメッセージ -->
                            <div v-if="$v.spouse.$error">
                                <span class="m_err" v-if="!$v.spouse.required">
                                    <i class="fas fa-exclamation-triangle"></i>「配偶者有無」を選択してください。
                                </span>
                            </div>

                        </div>
                    </dd>
                </dl>

            </div>
            <!--　＊＊＊ 性別 ＊＊＊ END -->



            <!--　＊＊＊ 扶養控除対象者人数 ＊＊＊ -->
            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">

                    <dt>

                        <span class="form-input-tbl-head" style="font-size: 12px !important;">扶養控除 対象者人数</span>
                        <span class="mark-required">必須</span>

                    </dt>


                    <dd>
                        <div class="jsc-required jsc-limited">

                            <!-- 生年月日 西暦 -->
                            <select name="dependents_num" id="dependents_num" v-model="dependents_num" @blur="$v.dependents_num.$touch()" class="app_input_form date-birth-year jsc-select-bold">

                                <option value="" class="jsc-default-selected">選択</option>

                                <?php for ($i = 0; $i <= 9; $i++) : ?>
                                    <option value="<?php print h($i); ?>" class="jsc-default-selected">
                                        <?php print h($i); ?>
                                    </option>
                                <?php endfor; ?>

                            </select>

                        </div>

                        <!-- エラーメッセージ -->
                        <div v-if="$v.dependents_num.$error">
                            <span class="m_err" v-if="!$v.dependents_num.required">
                                <i class="fas fa-exclamation-triangle"></i>「扶養控除 対象者人数」を選択してください。
                            </span>
                        </div>

                    </dd>
                </dl>

            </div>
            <!--　＊＊＊ 扶養控除対象者人数 END ＊＊＊ -->


            <h3 class="f_h3">マイナンバー情報</h3>


            <!--　＊＊＊ マイナンバー  ＊＊＊ -->
            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">

                    <dt>
                        <span class="form-input-tbl-head">マイナンバー</span>
                        <span class="mark-required">必須</span>
                    </dt>

                    <dd>
                        <div class="jsc-required jsc-limited">

                            <input type="number" class="form-control w_320" name="My_number_information" oninput="javascript:if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="12" v-model="My_number_information" @blur="$v.My_number_information.$touch()" :class="{ error : $v.My_number_information.$error,'form-control': true }">

                            <div v-if="$v.My_number_information.$error">
                                <!-- エラーメッセージ -->
                                <span class="m_err" v-if="!$v.My_number_information.required">
                                    <i class="fas fa-exclamation-triangle"></i>「マイナンバー」を入力してください。
                                </span>

                                <span class="m_err" v-if="!$v.My_number_information.numeric">
                                    <i class="fas fa-exclamation-triangle"></i>「数字」で入力してください。
                                </span>

                                <span class="m_err" v-if="!$v.My_number_information.maxLength">
                                    <i class="fas fa-exclamation-triangle"></i>「12桁」で入力してください。
                                </span>

                                <span class="m_err" v-if="!$v.My_number_information.minLength">
                                    <i class="fas fa-exclamation-triangle"></i>「12桁」で入力してください。
                                </span>

                            </div>

                        </div>
                    </dd>

                </dl>
            </div>
            <!--　＊＊＊ マイナンバー　END  ＊＊＊ -->


            <!-- ==============================  振込先情報 =============================== -->
            <h3 class="f_h3">振込先情報</h3>


            <!--　＊＊＊ 銀行コード ＊＊＊ 
            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">

                    <dt>
                        <span class="form-input-tbl-head">銀行コード</span>
                        <span class="mark-required">必須</span>
                    </dt>


                    <dd>
                        <div class="jsc-required jsc-limited">

                            <input type="number" class="form-control w_320" name="bank_code" oninput="javascript:if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" id="bank_code" v-model="bank_code" @blur="$v.bank_code.$touch()" :class="{ error : $v.bank_code.$error,'form-control': true }" placeholder="※ 入力後,自動で銀行名が入ります。">

                            <div v-if="$v.bank_code.$error">
                               
                                <span class="m_err" v-if="!$v.bank_code.required">
                                    <i class="fas fa-exclamation-triangle"></i>「銀行コード」が入力されていません。
                                </span>

                            </div>
                    </dd>

                </dl>
            </div>
            -->
            <!--　＊＊＊ 銀行コード END ＊＊＊ -->

            　　
            <!-- 銀行コード検索  
            <div style="margin: 25px 0;">
                <a href="javascript:void(0)" class="cp_btn" id="ginkou_h_btn">
                    銀行名から検索する
                    <span id="cp_btn_icon">
                        <i class="fas fa-angle-down"></i>
                    </span>
                </a>
            </div>

                                -->

            <!--　＊＊＊ 銀行名 検索 ＊＊＊

            <div class="col-xs-12 col-sm-12 col-lg-10 col-10" id="ginkou_k_box">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">

                    <div class="jsc-required jsc-limited">


                        <span class="bank_kensaku">銀行コード 検索</span>

                        <span class="">

                            <input type="text" class="form-control w_320" name="bank_code_kensaku" id="bank_code_kensaku" maxlength="50" placeholder="銀行名を入力してください。" />

                            <div class="">

                                <select name="bank_kensaku_val" id="bank_kensaku_val" class="app_input_form date-birth-year jsc-select-bold" style="margin: 10px 0 0px 0;">

                                    <option value="" class="jsc-default-selected">下記項目から選択してください</option>

                                </select>

                            </div>


                    </div>


                </dl>
            </div>
            -->
            <!--　＊＊＊ 銀行名 検索 END ＊＊＊ -->

            <!--　＊＊＊ 銀行名 ＊＊＊ -->
            <div class="col-xs-12 col-sm-12 col-lg-10 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">
                    <dt>
                        <span class="form-input-tbl-head">銀行名</span>
                        <span class="mark-required">必須</span>
                    </dt>

                    <dd>
                        <div class="jsc-required jsc-limited">

                            <input type="text" class="form-control w_320" name="bank_name" id="bank_name" v-model="bank_name" @input="$v.bank_name.$touch()" maxlength="50" :class="{ error : $v.bank_name.$error,'form-control': true }">

                            <div v-if="$v.bank_name.$error">
                                <!-- エラーメッセージ -->
                                <span class="m_err" v-if="!$v.bank_name.required">
                                    <i class="fas fa-exclamation-triangle"></i>「銀行名」が入力されていません。
                                </span>
                            </div>

                        </div>
                    </dd>

                </dl>
            </div>

            <!--　＊＊＊ 銀行名 END ＊＊＊ -->


            <!--　＊＊＊ 支店コード ＊＊＊ 
            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">

                    <dt>
                        <span class="form-input-tbl-head">支店コード</span>
                        <span class="mark-required">必須</span>
                    </dt>


                    <dd>
                        <div class="jsc-required jsc-limited">

                            <input type="number" class="form-control w_320" name="bank_siten_code" maxlength="4" id="bank_siten_code" v-model="bank_siten_code" @blur="$v.bank_siten_code.$touch()" :class="{ error : $v.bank_siten_code.$error,'form-control': true }" placeholder="">

                            <div v-if="$v.bank_siten_code.$error">
                              
                                <span class="m_err" v-if="!$v.bank_siten_code.required">
                                    <i class="fas fa-exclamation-triangle"></i>「支店コード」が入力されていません。
                                </span>

                            </div>
                    </dd>

                </dl>
            </div>
            -->
            <!--　＊＊＊ 支店コード END ＊＊＊ -->


            <!--　＊＊＊ 支店名 ＊＊＊ -->
            <div class="col-xs-12 col-sm-12 col-lg-10 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">
                    <dt>
                        <span class="form-input-tbl-head">支店名</span>
                        <span class="mark-required">必須</span>
                    </dt>

                    <dd>
                        <div class="jsc-required jsc-limited">

                            <input type="text" class="form-control w_320" name="bank_siten_name" value="" id="bank_siten_name" v-model="bank_siten_name" @blur="$v.bank_siten_name.$touch()" maxlength="50" :class="{ error : $v.bank_siten_name.$error,'form-control': true }">

                            <div v-if="$v.bank_siten_name.$error">
                                <!-- エラーメッセージ -->
                                <span class="m_err" v-if="!$v.bank_siten_name.required">
                                    <i class="fas fa-exclamation-triangle"></i>「支店名」が入力されていません。
                                </span>
                            </div>

                        </div>
                    </dd>

                </dl>
            </div>
            <!--　＊＊＊ 支店名 END ＊＊＊ -->


            <!--　＊＊＊ 銀行　科目　普通、定期 ＊＊＊ -->
            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">

                    <dt>

                        <span class="form-input-tbl-head" style="font-size: 12px !important;">口座科目</span>
                        <span class="mark-required">必須</span>

                    </dt>


                    <dd>
                        <div class="jsc-required jsc-limited">

                            <!-- 口座科目  0 == 普通  ,  1 == 当座 -->
                            <select name="bank_kamoku" id="bank_kamoku" v-model="bank_kamoku" @blur="$v.bank_kamoku.$touch()" class="app_input_form date-birth-year jsc-select-bold">

                                <option value="" class="jsc-default-selected">選択</option>
                                <option value="0" class="jsc-default-selected">普通</option>
                                <option value="1" class="jsc-default-selected">当座</option>

                            </select>

                        </div>

                        <!-- エラーメッセージ -->
                        <div v-if="$v.bank_kamoku.$error">
                            <span class="m_err" v-if="!$v.bank_kamoku.required">
                                <i class="fas fa-exclamation-triangle"></i>「口座科目」を選択してください。
                            </span>
                        </div>

                    </dd>
                </dl>

            </div>
            <!--　＊＊＊ 銀行　科目　普通、定期　END ＊＊＊ -->

            <!--　＊＊＊ 口座番号追加 ＊＊＊ -->
            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">

                    <dt>
                        <span class="form-input-tbl-head">口座番号</span>
                        <span class="mark-required">必須</span>
                    </dt>

                    <dd>
                        <div class="jsc-required jsc-limited">

                            <input type="number" class="form-control w_320" name="kouzzz_number" oninput="javascript:if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="8" v-model="kouzzz_number" @blur="$v.kouzzz_number.$touch()" :class="{ error : $v.kouzzz_number.$error,'form-control': true }">

                            <div v-if="$v.kouzzz_number.$error">
                                <!-- エラーメッセージ -->
                                <span class="m_err" v-if="!$v.kouzzz_number.required">
                                    <i class="fas fa-exclamation-triangle"></i>「口座番号」を入力してください。
                                </span>

                                <span class="m_err" v-if="!$v.kouzzz_number.numeric">
                                    <i class="fas fa-exclamation-triangle"></i>「数字」で入力してください。
                                </span>

                                <span class="m_err" v-if="!$v.kouzzz_number.maxLength">
                                    <i class="fas fa-exclamation-triangle"></i>「8桁」以内で入力してください。
                                </span>

                            </div>

                        </div>
                    </dd>

                </dl>
            </div>

            <!--　＊＊＊ 口座番号追加 END ＊＊＊ -->


            <!--　＊＊＊ 口座名義人 ＊＊＊ -->
            <div class="col-xs-12 col-sm-12 col-lg-10 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap">
                    <dt>
                        <span class="form-input-tbl-head">口座名義（カタカナ）</span>
                        <span class="mark-required">必須</span>
                    </dt>

                    <dd>
                        <div class="jsc-required jsc-limited">

                            <input type="text" class="form-control w_320" name="bank_holder" id="bank_holder" v-model="bank_holder" @blur="$v.bank_holder.$touch()" maxlength="20" :class="{ error : $v.bank_holder.$error,'form-control': true }">

                            <div v-if="$v.bank_holder.$error">
                                <!-- エラーメッセージ -->
                                <span class="m_err" v-if="!$v.bank_holder.required">
                                    <i class="fas fa-exclamation-triangle"></i>「口座名義（カタカナ）」が入力されていません。
                                </span>
                            </div>

                        </div>
                    </dd>

                </dl>
            </div>
            <!--　＊＊＊ 口座名義人 END ＊＊＊ -->



            <div class="col-lg-10 col-xs-12 col-sm-12 col-10">

                <dl class="form-input-tbl-inner jsc-name-limit-min-wrap" id="policy_box">

                    <dt>
                        <span class="form-input-tbl-head">お問い合わせ内容</span>

                    </dt>


                    <dd>
                        <div class="jsc-required jsc-limited">

                            <textarea rows="7" name="Inquiry_form" class="form-control form_text_box"></textarea>

                        </div>
                    </dd>
                </dl>
            </div>

            <!-- プライバシーポリシー -->
            <div class="col-lg-12 col-xs-12 col-sm-12 col-12">

                <div class="col-lg-10 col-xs-10 col-sm-10 col-10" id="policy_box">
                    <p>
                        <span>プライバシーポリシー</span>
                        <iframe id="inline-frame" style="font: size 12px;" width="100%" height="auto" src="privacypolicy.html">
                        </iframe>

                    </p>

                    <div>
                        <label> プライバシーポリシーを確認し、同意しました。</label><br />
                        <input type="checkbox" name="agree_privacy" v-model="agree_privacy" @blur="$v.agree_privacy.$touch()" />

                    </div>

                    <!-- エラーメッセージ -->
                    <div v-if="$v.agree_privacy.$error">
                        <span class="m_err" v-if="!$v.agree_privacy.required">
                            <i class="fas fa-exclamation-triangle"></i>プライバシーポリシーをご確認の上、チェックしてください。
                        </span>
                    </div>

                </div>

            </div> <!-- END プライバシー　ポリシー -->




            <div class="col-lg-12 col-xs-12 col-sm-12 col-12" style="margin-bottom:20px;">
                <div class="form-row">

                    <div class="col">

                        <button class="btn csv_btn_2" style="color:#000;" id="back_btn_02">リセット</button>

                    </div>

                    <!-- バリデーション　処理が通過するまで　「送信ボタン」を押せないようにする -->

                    <div class="col">
                        <button :disabled="$v.$invalid" class="btn csv_btn_2" id="send_btn_02" style="color:#000;" type="submit">登録</button>

                    </div>
                </div>
            </div>

            <!-- フォームを通過した用の値を POST する -->
            <input type="hidden" name="get_form_eh" value="<?php print h($get_form_eh); ?>" />

            <!-- フォームリダイレクト用禁止　用　トークン -->
            <input type="hidden" name="jim_token" value="<?php print h($jim_token); ?>">

            <!-- カラム項目 employee_id =>  000100 から始める _ user_id の　MAXを取って 100 + 1 をする -->
            <input type="hidden" name="employee_id" value="<?php print h($max_user); ?>">

        </form>

    </div> <!-- END container -->

    <!-- ajax 用 cdn -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>


    <!-- ========== 銀行検索 ajax 読み込み ========== -->
    <!-- php 銀行コード検索 -->
    <!-- php 銀行名前検索 -->
    <!-- php 銀行名前検索 -->

    <!-- 
    <script src="ajax_bank_01.js"></script>

    <script src="ajax_bank_02.js"></script>
 
    <script src="ajax_bank_03.js"></script>
                                -->


    <!-- vue.js -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/vuelidate@0.7.4/dist/validators.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuelidate@0.7.4/dist/vuelidate.min.js"></script>
    <!-- vue.js END -->


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


    <!-- ブラウザバック　無効化 -->
    <script>
        window.addEventListener("popstate", function(e) {

            history.pushState(null, null, null);
            return;

        });
    </script>

    <script>
        // ========= 銀行検索ボックス 表示, 非表示

        $(function() {

            $("#ginkou_k_box").hide();

            $('#ginkou_h_btn').click(function() {

                $('#ginkou_k_box').fadeToggle(600);

            });

        });




        //============= select box の　値を取得 （銀行検索 用 の値を ） split して　テキストボックスへ入れる

        $(function() {

            $("#bank_kensaku_val").change(function() {

                var str = $(this).val();
                // split で　2　つに分割
                var res = str.split(':', 2);

                $("#bank_code").val(res[0]);
                $("#bank_name").val(res[1]);

                /*
                console.log(res[0]);
                console.log(res[1]);
                */

            });
        });

        //============= select box の　値を取得 （銀行検索 用） END ===================>
    </script>




    <script>
        // ==================== ページ　離脱時　アラートメッセージ
        window.addEventListener("beforeunload", function(e) {
            var confirmationMessage = "入力内容を破棄します。";
            e.returnValue = confirmationMessage;
            return confirmationMessage;
        });
    </script>


    <script>
        //===============================================
        //=================== Vue.js フォームバリデーション
        //===============================================

        Vue.use(window.vuelidate.default);
        const {
            required,
            email,
            numeric,
            maxLength,
            minLength
        } = window.validators;
        // required => 必須項目 , email => 書式チェック , minLength => 文字の長さ

        // numeric => 数値のみ ok  

        const app = new Vue({
            el: '#app',
            data: {
                title: '',
                user_name: '',
                furi_name: '',
                tel: '',
                email: '',
                sex: '',
                brth_year: '',
                brth_mnth: '',
                brth_dy: '',
                spouse: '',
                zip: '',
                address_01: '',
                My_number_information: '',
                agree_privacy: '',
                dependents_num: '',
                //     bank_code: '',   銀行コード
                bank_name: '',
                bank_siten_name: '',
                //     bank_siten_code: '', 支店コード
                bank_kamoku: '',
                bank_holder: '',
                kouzzz_number: ''
            },
            validations: {
                user_name: {
                    required
                },
                furi_name: {
                    required
                },
                tel: {
                    required
                },
                email: {
                    required,
                    email
                },
                sex: {
                    required
                },
                brth_year: {
                    required
                },
                brth_mnth: {
                    required
                },
                brth_dy: {
                    required
                },
                spouse: {
                    required
                },
                zip: {
                    required,
                    minLength: minLength(7),
                    maxLength: maxLength(7),
                },
                address_01: {
                    required
                },
                My_number_information: {
                    required,
                    numeric,
                    maxLength: maxLength(12),
                    minLength: minLength(12),
                },
                agree_privacy: {
                    required
                },
                dependents_num: {
                    required
                },
                /* 銀行コード
                bank_code: {
                    required
                },
                */
                bank_name: {
                    required
                },
                bank_siten_name: {
                    required
                },
                /* 支店コード
                bank_siten_code: {
                    required
                },
                */
                bank_kamoku: {
                    required
                },
                bank_holder: {
                    required
                },
                kouzzz_number: {
                    required,
                    numeric,
                    maxLength: maxLength(8),
                },
            },
            methods: {
                submitForm() {
                    this.$v.$touch();
                    if (this.$v.$invalid) {
                        console.log('バリデーションエラー');
                    } else {
                        // データ登録の処理をここに記述
                        document.t_submit.submit();
                        console.log('submit');
                    }
                }
            }
        });
    </script>


</body>

</html>