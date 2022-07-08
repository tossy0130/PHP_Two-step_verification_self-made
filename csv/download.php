<?php

// エラーを出力する
ini_set('display_errors', "On");

// functions.php 読み込み
require(dirname(__FILE__) . '/../functions.php');

// カレントディレクトリ　を　取得
$dir = __DIR__;


// ディレクトリ一覧取得
$res = glob('file*', GLOB_ONLYDIR);


// ===================== 空のディレクトリがあったら　削除する
foreach ($res as $r_del) {
    
    // ディレクトリの中身が空の場合
    if (count(glob($r_del . "/*")) == 0) {

        if (is_dir($r_del)) {
            // ディレクトリ削除
            rmdir($r_del);
            // ページリロード
            header("Location: " . $_SERVER['PHP_SELF']);
        } else {
            return;
        }
    } //============ END if
}
// ===================== END 

// 「csv」ディレクトリ　の index
// $test_uri = "http://xs810378.xsrv.jp/php_password_01/csv/";
$test_uri = "url";

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- jim style.css -->
    <link rel="stylesheet" href="../css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!--Font Awesome5-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <!-- font awsome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">

    <!-- bootstrap マテリアル　読み込み -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.dark.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.dark.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.rtl.min.css">

    <title>ダウンロードページ</title>

    <style>
        /* csv アイコン */
        .fas.fa-file-csv {
            padding: 0 5px 0 0;
            font-size: 1.6em;
            margin: 7px 0 0px 0;
            display: inline-block;
            color: #D07682;
        }

        /* Excel　アイコン */
        .fas.fa-file-excel {
            padding: 0 5px 0 0;
            font-size: 1.6em;
            margin: 7px 0 0px 0;
            display: inline-block;
            color: #29754C;
        }

        .dl_item_01 {
            background: #29754C;
            color: #fff;
            padding: 5px 15px;
            border-radius: 3px;
            font-size: 0.9em;
            transition: .22s;
        }

        .dl_item_02 {
            background: #D07682;
            color: #fff;
            padding: 5px 15px;
            border-radius: 3px;
            font-size: 0.9em;
            transition: .22s;
        }


        /* フォルダ名 */
        .dl_folder {
            margin: 0 0 0px 5px;
            font-size: 1.3em;
            border: 1px solid #333;
            padding: 0.5em 2em;
            border-radius: 4px;
            cursor: pointer !important;
            background: #FFD775;
            transition: .55s;
        }

        /* フォルダ名を hober した時の処理 */
        .dl_folder:hover {
            opacity: 0.55;
        }




        .li_csv_item:hover {
            background: rgb(255, 215, 117);
        }


        /* ファイル一覧 */
        .ul_csv_item {
            margin: 15px 0 10px 0;
            position: relative;
            left: 8%;
        }

        .ul_csv_item>li {
            margin: 0px 0 10px 0;
            border-bottom: 1px solid #999;
            padding: 5px 0px 10px 0;
            width: 65%;
        }

        .ul_csv_item>li>a {
            color: #333 !important;
            display: inline-block;
            margin: 0px 0px;
            padding: 0 0px 0 0px;
            font-size: 1.05em;
            letter-spacing: 0.06em;
        }

        /* ファイルダウンロード　部分 */
        .dl_icon {
            color: #666;
        }

        .dl_item {
            color: #333;
            padding: 5px 15px;
            border-radius: 3px;
            font-size: 0.9em;
            transition: .22s;
        }

        .dl_item:hover {
            opacity: .66;
        }


        /* Vue js フェードアウト */
        .v-enter-active,
        .v-leave-actibe {
            transition: opacity 0.33s;
        }

        .v-enter,
        .v-leave-to {
            opacity: 0;
            transition: opacity 0.33s;
        }

        .dl_link>a {
            background: #007bff;
            color: #fff;
            padding: 1.2em 1.6em;
            font-size: 1.0em !important;
            display: block;
            transition: .33s;
            margin-bottom: 25px;
            text-align: center;
        }

        .dl_link>a:hover {
            opacity: .55;
        }


        /* 背景 */
        #app {
            background: #F5F5F5;
        }

        /* ゴミ箱アイコン */
        .fas.fa-trash-alt {color:#D07682; font-size:1.47em;}

        .del_text:hover { color:red;}

    </style>

</head>

<body>

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
    <div class="container">

        <div class="col-lg4 col-xs-10 col-md-4 dl_link">

            <a href="./csv_export.php" style="color:#fff !important;">
                Excel,CSVファイル作成 画面へ
            </a>

        </div>

    </div> <!-- container END -->

    <!-- フォルダ　& ファイル一覧　表示画面 -->
    <div class="container">

        <h1>ダウンロード ファイル選択</h1>
        <p style="margin: 0 0 35px 0;">
            ※ファルダを選択し、対象の日付フォルダからファイルをダウンロードしてください。
        </p>

    </div>

    <?php $gg = []; ?>
    <?php $idx = 0; ?>


    <?php $arr_first = ""; ?>

    <!-- 配列のキーを取得 -->
    <?php $arr_key = array_keys($res); ?>

    <!-- 配列の最初の要素を取得 -->
    <?php foreach ($arr_key as $key) : ?>

        <?php if ($key == 0) : ?>
            <?php $arr_first = $res[0];  ?>
        <?php endif; ?>

    <?php endforeach; ?>

    <!-- $arr_first に　配列の最初の要素を格納する -->

    <!-- 配列の最初の要素を取得 END -->

    <!-- フォルダ　& ファイル一覧　取得 -->

    <?php $get_files = []; ?>
    <?php foreach ($res as $re) : ?>

        <?php $gg[$idx] = $re; ?>

        <?php $idx++; ?>
        </span>
    <?php endforeach; ?>



    <!-- 取得したフォルダ要素の最初の要素 -->
    <?php $csv_files = []; ?>

    <!-- 配列のキーを取得 -->
    <?php $arr = array_keys($gg); ?>

    <?php $count_num = 0; ?>


    <div class="container" id="app">

        <ul class="ul_folder_view">

            <?php foreach ($gg as $g) : ?>

                <!-- ============== 先頭の要素 ==================== -->
                <?php if ($count_num % 3 == 0) : ?>

        </ul>

    </div>

    <div class="container">
        <ul class="ul_folder_view">

            <li class="btn btn-light">
                <i class="fas fa-folder-open"></i>
                <!-- Vue.js クリックイベント -->
                <!--    <span class="dl_folder" @click.prevent="show=!show">   -->
                <!--    <span class="dl_folder" @click="onClick(show, $event)"> -->
                <span class="">
                    <!-- フォルダ 出力 -->
                    <a href="./download_view.php?view_code=<?php print h($g); ?>">
                        <!-- END if -->
                        <?php print $g; ?>
                    </a>
                </span>

                <!-- ファイル削除 処理画面へ　遷移 -->
                <span class="" style="display: block;padding: 10px 0 0 0;">
                    <a class="del_text" href="./delete_file.php?view_delete_code=<?php print h($g); ?>">
                    <i class="fas fa-trash-alt"></i>ファイル削除
                </a>
                </span>

            </li>

        <?php else : ?>

            <!-- ================  それ以外の要素 ======================= -->

            <li class="btn btn-light">
                <i class="fas fa-folder-open"></i>
                <!-- Vue.js クリックイベント -->
                <!--    <span class="dl_folder" @click.prevent="show=!show">   -->
                <!--    <span class="dl_folder" @click="onClick(show, $event)"> -->
                <span class="dl_item">
                    <!-- フォルダ 出力 -->

                    <a href="./download_view.php?view_code=<?php print h($g); ?>">
                        <?php print $g; ?>
                    </a>

                    <!-- ファイル削除 処理画面へ　遷移 -->
                    <span class="">
                        <a href="./delete_file.php?view_delete_code=<?php print h($g); ?>">ファイル削除</a>
                    </span>

                </span>

            </li>


        <?php endif; ?>

        <?php $count_num++; ?>

    <?php endforeach; ?>

        </ul>
    </div>


    <!-- フッター start
<footer class="bg-light text-center text-lg-start">
  <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
  copyrightc2014 HATASHO NIIGATADELICA all rights reserved.
  </div>
</footer>
 -->
    <!-- フッター END -->


    <!-- Vue.js -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>

    <!-- bootstrap マテリアル　読み込み -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.min.js"></script>

    <script>
        /*
var app = new Vue({
        el: '#app',
            data: {
                show: false,
                hoverFlag: false,
                event:'click',
            },
            methods: {

                
                onClick(show, event) {
                        this.show=!this.show
                },
                
  
                // マウスオーバーしたら
                mouseOverAction(event) {
                    //event.this.hoverFlag = true
                    console.log(event.target.innerHTML) //htmlの取得
                    event.target.className = 'hover_add';
                  
                    
                   
                },
                // マウスが対象から外れたら
                mouseLeaveAction(event) {
                    //event.this.hoverFlag = false
                    console.log(event.target.innerHTML) //htmlの取得
                    event.target.classList.remove('hover_add');
                   
                  
                }
             }
    });

*/
    </script>


</body>

</html>