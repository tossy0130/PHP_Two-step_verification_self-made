<?php 

     // エラーを出力する
     ini_set('display_errors', "On");

     // functions.php 読み込み
     require (dirname(__FILE__). '/../functions.php');
    
     // エラーページディレクトリ
     $kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

     // フラグ
     $one_time_Flg = true;

     //============ 変数定義 ============ 
     $email = ""; // 
     $email_hh = ""; // email の　ハッシュ値
     $pass = ""; // 
     $pass_hh = ""; // パスワード　ハッシュ値
     $valid_date = ""; // 日にち取得

     $form_pass = "";

     $get_e_t = ""; // メールアドレス　取得用 前方
     $get_e_b = ""; // メールアドレス　取得用　後方　

     // ====== エラー格納用　配列 ======
     $arr_error = []; 

     // 現在の URL 取得
     //  echo (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

   
     // URL の値を GET パラメーター　取得  
     $get_email_hh = "";
     if(isset($_GET['email'])) {

        $get_email_hh = $_GET['email'];

        }

//     var_dump("<br />GETパラメーターで取得した値:::<br />" . $get_email_hh . "<br />");
    
     // メールアドレス用　GET パラメーター　取得
     if(isset($_GET['e_t'])) {

        $get_e_t = $_GET['e_t'];

     }

     if(isset($_GET['e_b'])) {

        $get_e_b = $_GET['e_b'];

     }

     // === メールアドレス　復元
     $get_email = $get_e_t . "@" . $get_e_b;
     

     /**========== DB 接続 =========== */
    
    // *** xampp 用　接続情報
    /*
    $dsn = 'mysql:dbname=tossy_01_db;host=localhost;port=3308';
    $user = 'root';
    $password = '';
    */

//================================  
//================== 接続情報
//================================  
$dsn = 'mysql:dbname=d05618yadb1;host=localhost;port=3306';
$user = 'd05618ya';
$password = 'deli7332+C';


    try{

        // PDO オブジェクト作成
        $pdo = new PDO($dsn, $user, $password);

        // bind する場合は、  
        // $stmt = $pdo->prepare("SELECT * FROM user_list WHERE id = :id");
        // $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        //***** GET の末尾の　URL を取って、　最新の１件　取得 */
        $stmt = $pdo->prepare("SELECT * FROM valid_table WHERE email = :email_hh order by karitouroku_id DESC limit 1");
        
        // メールアドレス　を　パラメーター　として渡す
        $stmt->bindParam(':email_hh', $get_email, PDO::PARAM_STR);

        // SQL 実行
        $res = $stmt->execute();
        
      
        // ****** SQL の結果を取り出す ****** 
        if($res) {
            $data = $stmt->fetch();

            $email = $data['email'];

            $email_hh = $data['email_hh'];
            $pass = $data['pass'];
            $pass_hh = $data['pass_hh'];
            $valid_date = $data['valid_date'];

   //         var_dump($email . "<br />" . $email_hh . "<br />" . $pass . "<br />" . $pass_hh . "<br />" . $valid_date);

        }


    }catch (PDOException $e){
        print('Error:'.$e->getMessage());
        die();
    }

        
        $pdo = null;
        // ****** データベースの接続解除 ******
     /**========== DB 接続 END =========== */


    // ===================== email & email ハッシュチェック ========================
    //===== 仮登録用メール　と　GETで取得した　メールアドレスが違ったら,
    //======処理を停止させる。
    if (strcmp($get_email_hh, $email_hh) == 0) {
        
    //    echo "GET:::::::OK:::::: emailのハッシュ値 同じ";

    } else {

        //========= エラー処理
      //  echo "*** URL NG *** ハッシュ値が違う";

        $arr_error[0] = "[*** URL リンク NG ***] URLが間違っています。正しい URL でログインしてください。";

        // =============== リダイレクトで別のエラーページへ飛ばす
        header( "Location: {$kari_uri}");

    }


    //====================== ワンタイムパスワード　チェック

    // チェック用データ作成
    
    //========== date time 01 ====> DB から取得した 時刻
    $time0 = new DateTime();
    $time_tmp = $time0->format('Y-m-d H:i:s');

    $t_0 = $time0->format('Y-m-d H:i:s');

    $tt_0 = str_replace("-", "", $t_0);
    $ttt_0 = str_replace(":", "", $tt_0);
    $t_get_0 = str_replace(" ", "", $ttt_0);

    // ======== 切り出し 年　月　日
    $year_0 = mb_substr($t_get_0, 0, 4);
    $month_0 = mb_substr($t_get_0, 4, 2);
    $day_0 = mb_substr($t_get_0, 6, 2);
  
    // ======= 時間切り出し
    $hour_0 = mb_substr($t_get_0, 8, 2);
    $minute_0 = mb_substr($t_get_0, 10, 2);
    $second_0 = mb_substr($t_get_0, 12, 2);

 

    //******************  比較用　日付作成　時刻 + 30分  */
    $time1 = new DateTime($valid_date);
    
    $da_tmp = $time1->modify('+30 minute');

    $t = $time1->format('Y-m-d H:i:s');

    $tt = str_replace("-", "", $t);
    $ttt = str_replace(":", "", $tt);
    $t_get = str_replace(" ", "", $ttt);

    // ======== 切り出し 年　月　日
    $year = mb_substr($t_get, 0, 4);
    $month = mb_substr($t_get, 4, 2);
    $day = mb_substr($t_get, 6, 2);

    // ======= 時間切り出し
    $hour = mb_substr($t_get, 8, 2);
    $minute = mb_substr($t_get, 10, 2);
    $second = mb_substr($t_get, 12, 2);


    // ワンタイムパスワードが 期限内で　有効か　チェック

     $time_err = [];
    
     //=== 時間　H の計算
     $hour_0_i = intval($hour_0); // 現在時刻 １時間
     $hour_i = intval($hour); // ワンタイムパスワードを発行して、有効時刻　時間
     $sum_h = $hour_0_i - $hour_i;

     //=== 分の　i の計算
     $minute_0_i = intval($minute_0); // 現在時刻 １分
     $minute_i = intval($minute_i); // ワンタイムパスワードを発行して、有効時刻 分


    // ==================== 年　チェック  //  月　　// 日　チェック
    if (strcmp($year_0, $year) !== 0 || strcmp($month_0, $month) !== 0 || 
    strcmp($day_0, $day) !== 0) {

        $time_err = "ワンタイムパスワードの有効期限が切れています。もう一度取得してください。";

        // === エラー　配列へ格納
        $arr_error[1] = "[ワンタイムパスワード 期限エラー]ワンタイムパスワードの有効期限が切れています。もう一度取得してください。";

       
        //++++++++++++++++++++++++++++++++++++++++++++++++++
        //++++++++++++++++++ 削除 function ++++++++++++++++++
        DB_delete_column($pass_hh);
        //++++++++++++++++++++++++++++++++++++++++++++++++++

        $one_time_Flg = false;

        // === エラー画面へ リダイレクトする
        header( "Location: {$kari_uri}");

    } else {

  
        //==================== 現在時刻が ２時間過ぎている場合
        if($hour_0_i - $hour_i >= 2) {

            $time_err = "ワンタイムパスワードの有効期限が切れています。もう一度取得してください。";

            // === エラー　配列へ格納
            $arr_error[1] = "[ワンタイムパスワード 期限エラー]ワンタイムパスワードの有効期限が切れています。もう一度取得してください。";
            
            //++++++++++++++++++++++++++++++++++++++++++++++++++
            //++++++++++++++++++ 削除 function ++++++++++++++++++
            DB_delete_column($pass_hh);
            //++++++++++++++++++++++++++++++++++++++++++++++++++

            $one_time_Flg = false;

            // === エラー画面へ リダイレクトする
            header( "Location: {$kari_uri}");
            

        // =============  時 ,  分　を比較して、ワンタイムパスワードが切れているか比較
        } else if ($hour_0_i - $hour_i == 1 && 60 + $minute_0_i - $minute_i > 30) {

            $time_err = "ワンタイムパスワードの有効期限が切れています。もう一度取得してください。";

            // === エラー　配列へ格納
            $arr_error[1] = "[ワンタイムパスワード 期限エラー]ワンタイムパスワードの有効期限が切れています。もう一度取得してください。";
            
            //++++++++++++++++++++++++++++++++++++++++++++++++++
            //++++++++++++++++++ 削除 function ++++++++++++++++++
            DB_delete_column($pass_hh);
            //++++++++++++++++++++++++++++++++++++++++++++++++++

            $one_time_Flg = false;

            // === エラー画面へ リダイレクトする
            header( "Location: {$kari_uri}");

        }else if(strcmp($hour_0, $hour) == 0 && $minute_0 > $minute) {

            $time_err = "ワンタイムパスワードの有効期限が切れています。もう一度取得してください。";
            
            // === エラー　配列へ格納
            $arr_error[1] = "[ワンタイムパスワード 期限エラー]ワンタイムパスワードの有効期限が切れています。もう一度取得してください。";

            //++++++++++++++++++++++++++++++++++++++++++++++++++
            //++++++++++++++++++ 削除 function ++++++++++++++++++
            DB_delete_column($pass_hh);
            //++++++++++++++++++++++++++++++++++++++++++++++++++

            $one_time_Flg = false;

            // === エラー画面へ リダイレクトする
            header( "Location: {$kari_uri}");
 
        } else {

            // ========== ワンタイムパスワードが有効な場合
            // ******* ワンタイムチェック 通過　OK **********
   //         var_dump("******* ワンタイムチェック 通過　OK **********");

            $one_time_Flg = true;

            if(empty($_POST['one_pass'])) {

  //              print "パスワード入力が空です";

            } else {

                $form_pass = $_POST['one_pass'];
            }

            //==================== ワンタイムパスワードの入力値とのチェック
            if (strcmp($pass, $form_pass) == 0) {

        //        print "ログイン OK";

                // リダイレクト
	            header( "Location: ./register.php?e=" . $get_e_t . "-" . $get_e_b . "&h=" . $email_hh);

                exit;
 
            } else {

//                print "入力値が違っています";

            }

        } // ===================================== else END

    }

    // ワンタイムパスワード　と　ハッシュをチェック
    if(isset($_POST['one_pass'])) {
               
         $hikaku_pass = $_POST['one_pass'];

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

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!-- 修正箇所1 titleセクションを呼び出す。デフォルト値は no titleとする -->
    <title>登録用フォーム　前チェック</title>

    <style>
    
    </style>

  </head>
  <body>


<div class="jumbotron">
    <div class="container">
        <h1 class="display-5 m_h1">株式会社　●●</h1>
        <p class="lead">
         <!-- アルバイト者情報登録 -->
         <?php site_name_hozyo(); ?><br />
        ワンタイムパスワード入力
        </p>
    </div>
</div>

<!-- 入力ページ　（カレント表示） -->
<div class="container">
<div class="row form-flow my-5 text-center col-12">
            <div class="col-2 form-flow-list-wrap form-flow-active py-3">
                <span class="form-flow-list-no">1</span><span style="display:block;" id="cl_one">ワンタイムパスワード入力</span></div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap py-3" style="line-height: 2.5;"><span class="form-flow-list-no">2</span>入力</div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap py-3" style="line-height: 2.5;"><span class="form-flow-list-no">3</span>確認</div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap py-3" style="line-height: 2.5;"><span class="form-flow-list-no">4</span>完了</div>
</div>
</div>

<div class="container">
   <!-- <form action="register.php" method="POST"> -->
    <form action="" method="POST">
      
           <!-- ワンタイムパスワード -->
           <div class="form-group row">
            <div class="col-sm-10">
                <label for="one_pass">ワンタイムパスワードを入力してください。</label>


            <!-- ================= input type="password" パスワードから　ナンバーへ　変更 =============== -->
            <!--   <input type="password" class="form-control" id="inputEmail" name="one_pass" 
                placeholder="ワンタイムパスワード"> -->

            <input type="number" class="form-control" id="inputEmail" name="one_pass" 
                placeholder="ワンタイムパスワード">

                <div class="invalid-feedback">ワンタイムパスワードを入力してください。</div>


            </div>
        </div>

         <!--ボタンブロック-->
         <div class="form-group row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary btn-block">登録画面へ進む</button>
            </div>
        </div>
        <!--/ボタンブロック-->


       <!-- ワンタイムパスワード　有効か判定  -->
       <?php if($one_time_Flg == true) { ?>

        <p>ワンタイムパスワード　有効</p>

       <?php } else { ?>
        
        <p>ワンタイムパスワード が無効になっています。</p>

        <?php } ?>

</div>    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    
<!-- ブラウザバック　無効化 -->
<script>

window.addEventListener('DOMContentLoaded', function () {
  // 戻るボタンを制御
  history.pushState(null, null, location.href);
  window.addEventListener('popstate', (e) => {
    history.go(1);
  });
});

</script>

<!-- ============= スマホ用　カレントページ表示対策 -->
<script>

function isSmartPhone() {
    
  if (window.matchMedia && window.matchMedia('(max-device-width: 599px)').matches) {

    let cl_one = document.getElementById("cl_one");

    cl_one.innerText  = "パス";

    console.log(cl_one.innerText );

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