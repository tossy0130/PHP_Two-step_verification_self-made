<?php


// エラーを出力する
ini_set('display_errors', "On");
ini_set("memory_limit", "3072M");

// functions.php 読み込み
require(dirname(__FILE__) . '/../functions.php');

require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// エラーページディレクトリ
$kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

//=============== 変数　定義
$date_target = "";
$date_target_02 = "";

//  左の日付ボックスだけ　入力あり => 1
//  右の日付ボックスだけ　入力あり => 2
//  両方のボックス　入力あり => 3
//  両方のボックス　入力なし => 0 
$csv_FLG = "0";
$csv_file_ok_FLG = "0";

$excel_file_FLG = "0"; //  0 => デフォルト何もなし , 1 => 出力 OK , 2 エラー

//====== 日付　選択用 格納　配列
$arr_user_name = []; // 名前
$arr_furi_name = []; // ふりがな
$arr_age = []; // 年齢
$arr_email = []; // メールアドレス
$arr_sex = []; // 性別

$arr_get_test = [];

$file_name = "";

$csv_file_ok = "";

$export_output = "";

$export_output_err = "0";

//==== excel　file エラー管理フラグ
$excel_file_err = 0;

// 未入力ファイルのcheckboxにチェックが入っているか、いないか。
$check_box_FLG = "";

$h_check_box_FLG = "";

//  $arr_user_name = "";

// =========================== 日付取得　クラス
class get_now_date
{

  function get_to_date()
  {

    //====================== 現在時刻の取得
    $n_year = "";
    $n_month = "";
    $n_day = "";

    $n_hour = "";
    $n_minute = "";
    $n_second = "";

    $now = new DateTime();

    $now_tm = $now->format('Y-m-d H:i:s');

    //======== 日付の - 空白　: を取る
    $tt_0 = str_replace("-", "", $now_tm);
    $ttt_0 = str_replace(":", "", $tt_0);
    $now_tmp = str_replace(" ", "", $ttt_0);

    // ======== 切り出し 年　月　日
    $n_year = mb_substr($now_tmp, 0, 4);
    $n_month = mb_substr($now_tmp, 4, 2);
    $n_day = mb_substr($now_tmp, 6, 2);

    // ======= 時間切り出し
    $n_hour = mb_substr($now_tmp, 8, 2);
    $n_minute = mb_substr($now_tmp, 10, 2);
    $n_second = mb_substr($now_tmp, 12, 2);

    return array(

      'year' => $n_year,
      'month' => $n_month,
      'day' => $n_day,
      'hour' => $n_hour,
      'minute' => $n_minute,
      'second' => $n_second

    );
  } //======= END Function

}; // ================== END class get_now_date

// ===============================================
//====================== 半年経過した　データを削除 ::::: half_year_delete();
half_year_delete();
// ===============================================

// オブジェクト
$get_now_date = new get_now_date();
$get_now_arr = $get_now_date->get_to_date();


// === 日付ボックス　空用　
$go_now_sql = $get_now_arr['year'] . "-" . $get_now_arr['month'] . "-" . $get_now_arr['day'];



//=============================================================================
//====================================
//======================== view_btn 表示処理
//====================================
//=============================================================================

//================= チェックボックス　checkテスト
if (isset($_POST['uninput_file'])) {

  $h_check_box_FLG = $_POST['uninput_file'];

  //  print("checkボックステスト:::" .  $test_check_box);

  $h_check_box_FLG = "1";
} else {

  //  print("checkがついていません。");

  $h_check_box_FLG = "0";
}


if (isset($_POST["view_btn"])) {

  //================================  
  //================== 接続情報
  //================================  

  $dsn = '';
  $user = '';
  $password = '';


  // === エラー変数
  $display_err = "";

  //========================================== $csv_FLG 判定
  //================= エラー処理 日付 01 開始日
  if (isset($_POST['date_target']) || isset($_POST['date_target_02'])) {

    if (empty($_POST['date_target']) && empty($_POST['date_target_02'])) {

      $display_err = "表示できません。日付ボックスを選択してください";

      // === 日付ボックスが　２つとも　空だった場合
      $csv_FLG = "0";
    } else if (!empty($_POST['date_target']) && empty($_POST['date_target_02'])) {
      // === 左側 が 入力ありの処理

      $date_target = $_POST['date_target'];

      $date_target = str_replace("/", "-", $date_target);

      $display_err = "";

      $csv_FLG = "1";
    } else if (empty($_POST['date_target']) && !empty($_POST['date_target_02'])) {
      // === 右側だけ　入力ありの処理
      $date_target_02 = $_POST['date_target_02'];

      $date_target_02 = str_replace("/", "-", $date_target_02);

      $display_err = "";

      $csv_FLG = "2";
    } else if (!empty($_POST['date_target']) && !empty($_POST['date_target_02'])) {

      $date_target = $_POST['date_target'];

      $date_target = str_replace("/", "-", $date_target);

      $date_target_02 = $_POST['date_target_02'];

      $date_target_02 = str_replace("/", "-", $date_target_02);

      $display_err = "";

      $csv_FLG = "3";
    }
  } else {

    // === 日付ボックスが　２つとも　空だった場合
    $csv_FLG = "0";

    $display_err = "表示できません。日付ボックスを選択してください";
  }

  //========================================== $csv_FLG 判定　END




  //===================================
  // ========================== 接続処理
  //===================================

  try {

    // PDO オブジェクト
    $pdo = new PDO($dsn, $user, $password);

    //=======================================================================
    //=======================================================================
    //====================  ＊＊＊表示＊＊＊　日付 BOX が２つ入っていた場合
    //=======================================================================
    //=======================================================================

    // check_box_FLG = 0  ==> 未入力ファイル　にチェックなし

    if (strcmp($csv_FLG, "3") == 0 && strcmp($h_check_box_FLG, "0") == 0) {

      // ページリロード
      //    header("Location: " . $_SERVER['PHP_SELF']);

      // エラー変数　初期化
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
                  WHERE creation_time BETWEEN ? AND ?  ORDER BY user_id DESC");

      // CSV エクスポート 開始日
      $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

      // CSV エクスポート 終了日
      $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);


      // SQL 実行 
      $res = $stmt->execute();

      // トランザクション　コミット
      if ($res) {
      }


      $idx = 0;

      // 絞り込み　結果　格納配列
      $retunr = [];

      //======= 日付選択　データ　取得
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'], 'creation_time' => $row['creation_time'],
          'data_Flg' => $row['data_Flg']
        );
      }


      // ******************************************************
      // 「未入力ファイル出力」　に　チェックあり
      // ******************************************************
    } else if (strcmp($csv_FLG, "3") == 0 && strcmp($h_check_box_FLG, "1") == 0) {

      // ページリロード
      //  header("Location: " . $_SERVER['PHP_SELF']);

      // PDO オブジェクト
      $pdo = new PDO($dsn, $user, $password);

      // エラー変数　初期化
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
                  WHERE creation_time BETWEEN ? AND ? AND data_Flg = '0' ORDER BY user_id DESC");

      // CSV エクスポート 開始日
      $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

      // CSV エクスポート 終了日
      $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);


      // SQL 実行 
      $res = $stmt->execute();



      $idx = 0;

      // 絞り込み　結果　格納配列
      $retunr = [];

      //======= 日付選択　データ　取得
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'], 'creation_time' => $row['creation_time'],
          'data_Flg' => $row['data_Flg']
        );
      }


      //=======================================================================
      //=======================================================================
      //====================  ＊＊＊表示＊＊＊　日付 BOX が 左側だけ入っていた場合
      //=======================================================================
      //=======================================================================
    } else if (strcmp($csv_FLG, "1") == 0 && strcmp($h_check_box_FLG, "0") == 0) {


      // PDO オブジェクト
      $pdo = new PDO($dsn, $user, $password);

      // エラー変数　初期化
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table WHERE 
                creation_time >= ? ORDER BY creation_time DESC;");

      // CSV エクスポート 開始日
      $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

      // CSV エクスポート 終了日
      //  $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);


      // SQL 実行 
      $res = $stmt->execute();

      $idx = 0;

      // 絞り込み　結果　格納配列
      $retunr = [];

      //======= 日付選択　データ　取得
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'], 'creation_time' => $row['creation_time'],
          'data_Flg' => $row['data_Flg']
        );
      }


      //=======================================================================
      //=======================================================================
      //====================  ＊＊＊表示＊＊＊　日付 BOX が 左側だけ入っていた場合
      // 「未入力ファイル出力」　チェックあり
      //=======================================================================
      //=======================================================================
    } else if (strcmp($csv_FLG, "1") == 0 && strcmp($h_check_box_FLG, "1") == 0) {

      // エラー変数　初期化
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table WHERE 
                creation_time >= ? AND data_Flg = '0' ORDER BY creation_time DESC;");

      $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

      // 現在時刻  2000-00-00 => $go_now_sql
      /*
            $stmt->bindValue(2, $go_now_sql, PDO::PARAM_STR);
            */

      // SQL 実行 
      $res = $stmt->execute();

      $idx = 0;

      // 絞り込み　結果　格納配列
      $retunr = [];

      //======= 日付選択　データ　取得
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'], 'creation_time' => $row['creation_time'],
          'data_Flg' => $row['data_Flg']
        );
      }

      //=======================================================================
      //=======================================================================
      //====================  ＊＊＊表示＊＊＊　日付 BOX が 右側だけ　入っていた場合
      //=======================================================================
      //=======================================================================
    } else if (strcmp($csv_FLG, "2") == 0 && strcmp($h_check_box_FLG, "0") == 0) {

      // エラー変数　初期化
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
              WHERE creation_time  <= ? ORDER BY user_id DESC");

      $stmt->bindValue(1, $date_target_02, PDO::PARAM_STR);

      // SQL 実行 
      $res = $stmt->execute();

      // トランザクション　コミット
      if ($res) {
      }



      $idx = 0;

      // 絞り込み　結果　格納配列
      $retunr = [];

      //======= 日付選択　データ　取得
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'],
          'creation_time' => $row['creation_time'], 'data_Flg' => $row['data_Flg']
        );
      }


      //=======================================================================
      //=======================================================================
      //====================  ＊＊＊表示＊＊＊　日付 BOX が 右側だけ　入っていた場合 
      // 「未入力ファイル出力」　チェックあり
      //=======================================================================
      //=======================================================================
    } else if (strcmp($csv_FLG, "2") == 0 && strcmp($h_check_box_FLG, "1") == 0) {


      // エラー変数　初期化
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
              WHERE creation_time  <= ? AND data_Flg = '0' ORDER BY user_id DESC");

      $stmt->bindValue(1, $date_target_02, PDO::PARAM_STR);

      // SQL 実行 
      $res = $stmt->execute();

      // トランザクション　コミット
      if ($res) {
      }



      $idx = 0;

      // 絞り込み　結果　格納配列
      $retunr = [];

      //======= 日付選択　データ　取得
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'],
          'creation_time' => $row['creation_time'], 'data_Flg' => $row['data_Flg']
        );
      }


      //=======================================================================
      //=======================================================================
      //====================  ＊＊＊表示＊＊＊　日付 BOX が 何も入っていない場合  全件出力
      //=======================================================================
      //=======================================================================

    } else if (strcmp($csv_FLG, "0") == 0 && strcmp($h_check_box_FLG, "0") == 0) {


      // エラー変数　初期化
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table order by user_id DESC");

      /*
               // CSV エクスポート 開始日
               $stmt->bindValue(1, $date_target, PDO::PARAM_STR);
               
               // CSV エクスポート 終了日
               $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);
               */


      // SQL 実行 
      $res = $stmt->execute();

      // トランザクション　コミット
      if ($res) {
      }


      $idx = 0;

      // 絞り込み　結果　格納配列
      $retunr = [];

      //======= 日付選択　データ　取得
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'],
          'creation_time' => $row['creation_time'], 'data_Flg' => $row['data_Flg']
        );
      }
    } else if (strcmp($csv_FLG, "0") == 0 && strcmp($h_check_box_FLG, "1") == 0) {


      // エラー変数　初期化
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table WHERE 
                    data_Flg = '0' order by user_id DESC");

      /*
               // CSV エクスポート 開始日
               $stmt->bindValue(1, $date_target, PDO::PARAM_STR);
               
               // CSV エクスポート 終了日
               $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);
               */


      // SQL 実行 
      $res = $stmt->execute();

      // トランザクション　コミット
      if ($res) {
      }


      $idx = 0;

      // 絞り込み　結果　格納配列
      $retunr = [];

      //======= 日付選択　データ　取得
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'],
          'creation_time' => $row['creation_time'], 'data_Flg' => $row['data_Flg']
        );
      }
    }
  } catch (PDOException $e) {

    //  print('Error:'.$e->getMessage());

    // ************** エラー処理 ***************
    header("Location: {$kari_uri}");
  } finally {

    $pdo = null;
  }
} //============== END download_btn  POST


//==============================================================================
//==============================================================================
//======================== download_btn CSV ダウンロード処理
//===============================================================================
//==============================================================================

if (isset($_POST['download_btn'])) {

  //===============================================================================
  //===============================================================================
  //  =============================== CSV 出力処理 　日付選択　ダウンロード
  //===============================================================================
  //===============================================================================


  //======================================
  if (isset($_POST['export_output'])) {

    $export_output = $_POST['export_output'];

    // テスト　出力
    // print ("セレクトボックスの値" . $export_output . "<br />");

  }

  //================= チェックボックス　checkテスト
  if (isset($_POST['uninput_file'])) {

    $test_check_box = $_POST['uninput_file'];

    //  print("checkボックステスト:::" .  $test_check_box);

    $check_box_FLG = "1";
  } else {

    //  print("checkがついていません。");

    $check_box_FLG = "0";
  }


  if ($export_output === "csv" && empty($_POST['uninput_file'])) {

    //============================================
    //============== 修正 2022_01_13  日付が空の場合
    //============================================
    if (isset($_POST['date_target']) || isset($_POST['date_target_02'])) {

      if (empty($_POST['date_target']) && empty($_POST['date_target_02'])) {

        $display_err = "ファイル作成ができません。日付ボックスが空です。「日付を入力するか」、「未出力ファイル」にチェックをつけてください。";

        // === 日付ボックスが　２つとも　空だった場合
        $csv_FLG = "0";
      } else {


        //=============================================
        /*================ DB 接続 CSV　エクスポート ===================== */
        //=============================================


        //================================  
        //================== 接続情報
        //================================  
        $dsn = '';
        $user = '';
        $password = '';


        // ====== エラー用　配列
        $error = "";

        //========================================== $csv_FLG 判定
        //================= エラー処理 日付 01 開始日


        if (isset($_POST['date_target']) || isset($_POST['date_target_02'])) {

          if (empty($_POST['date_target']) && empty($_POST['date_target_02'])) {

            // === 日付ボックスが　２つとも　空だった場合 *** エラー処理 ***
            $error = "日付指定が空です。どちらか日付を入力してください。";
          } else if (!empty($_POST['date_target']) && empty($_POST['date_target_02'])) {
            // === 左側 が 入力ありの処理

            // === 日付ボックスが　２つとも　空だった場合
            $csv_FLG_exp = "0";

            // === 日付ボックスが　２つとも　空だった場合 *** エラー処理 ***
            $error = "日付指定が空です。入力してください。";
          } else if (empty($_POST['date_target']) && !empty($_POST['date_target_02'])) {
            // === 右側だけ　入力ありの処理

            // === 日付ボックスが　２つとも　空だった場合
            $csv_FLG_exp = "0";

            // === 日付ボックスが　２つとも　空だった場合 *** エラー処理 ***
            $error = "日付指定が空です。入力してください。";
          } else if (!empty($_POST['date_target']) && !empty($_POST['date_target_02'])) {

            // ========== 両方とも入力あり　ok ==========

            $date_target = $_POST['date_target'];

            $date_target = str_replace("/", "-", $date_target);

            $date_target_02 = $_POST['date_target_02'];

            $date_target_02 = str_replace("/", "-", $date_target_02);

            $error = "";

            $csv_FLG_exp = "3";
          }
        } else {

          // === 日付ボックスが　２つとも　空だった場合
          $csv_FLG_exp = "0";

          // === 日付ボックスが　２つとも　空だった場合 *** エラー処理 ***
          $error = "日付指定が空です。どちらか日付を入力してください。";
        }


        // ==============================================================
        //====== 日付が同じなら、日付の右側ボックスを加算させる。
        // ==============================================================

        if (!empty($date_target) && !empty($date_target_02)) {

          if ($date_target === $date_target_02) {

            $tmp_date = strtotime($date_target_02);

            $date_target_02 = date("Y-m-d", strtotime("+1 day", $tmp_date));
          } else {

            $tmp_date = strtotime($date_target_02);

            $date_target_02 = date("Y-m-d", strtotime("+1 day", $tmp_date));
          }
        } // ==== END if

        //==================== 日付プラス  1 END =========================>

        //======================================
        //============== CSV ファイル名 
        //=======================================

        // CSV ファイル名  => アルバイト管理_2021-10-12_121212.csv
        $file_name = "Baito_Kanri_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
          "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
          $get_now_arr['minute'] . $get_now_arr['second'] . ".csv";

        // CSV ファイル名 2 => file_name_02

        // ファイル名の文字コード変換  
        $file_name = mb_convert_encoding($file_name, "SJIS", "UTF-8");

        /*
    $export_csv_title = [
      "名前", "ふりがな", "年齢","メールアドレス", "性別", "登録日"
    ]; //DBテーブルのヘッダー項目
    */

        // ======= CSV ファイル　ヘッダータイトル　作成
        //============= 項目 237 
        $export_csv_title = [
          "社員番号",
          "社員氏名",
          "フリガナ",
          "性別",
          "生年月日",
          "生年月日（西暦）",
          "入社区分",
          "入社年月日",
          "入社年月日（西暦）",
          "勤続年数",
          "勤続年数_月数",
          "社員区分",
          "役職",
          "学歴",
          "所属部門",
          "職種",
          "郵便番号",
          "住所１",
          "住所２",
          "住所（フリガナ）１",
          "住所（フリガナ）２",
          "電話番号",
          "メールアドレス",
          "障害区分",
          "寡婦",
          "寡夫",
          "勤労学生",
          "災害者",
          "外国人",
          "退職理由",
          "退職年月日",
          "退職年月日（西暦）",
          "給与区分",
          "税表区分",
          "年末調整対象",
          "支払形態",
          "支給日パターン",
          "給与明細書パターン",
          "賞与明細書パターン",
          "健康保険対象",
          "介護保険対象",
          "健保月額（千円）",
          "健保証番号（協会）",
          "健保証番号（組合）",
          "健保・厚年　資格取得年月日",
          "厚生年金対象",
          "70歳以上被用者",
          "保険者種別",
          "厚年月額（千円）",
          "基礎年金番号１",
          "基礎年金番号２",
          "短時間労働者（3/4未満）",
          "労災保険対象",
          "雇用保険対象",
          "労働保険用区分",
          "雇用保険被保険者番号１",
          "雇用保険被保険者番号２",
          "雇用保険被保険者番号３",
          "労働保険　資格取得年月日",
          "配偶者",
          "配偶者氏名",
          "配偶者フリガナ",
          "配偶者性別",
          "配偶者生年月日",
          "配偶者生年月日（西暦）",
          "源泉控除対象配偶者",
          "配偶者老人",
          "配偶者障害者",
          "配偶者同居",
          "配偶者非居住者",
          "扶養控除対象人数",
          "扶養親族_扶養親族名_1",
          "扶養親族_フリガナ_1",
          "扶養親族_続柄_1",
          "扶養親族_生年月日_1",
          "扶養親族_生年月日（西暦）_1",
          "扶養親族_扶養区分_1",
          "扶養親族_障害者区分_1",
          "扶養親族_同居区分_1",
          "扶養親族_同居老親等区分_1",
          "扶養親族_非居住者区分_1",
          "扶養親族_控除計算区分_1",
          "扶養親族_扶養親族名_2",
          "扶養親族_フリガナ_2",
          "扶養親族_続柄_2",
          "扶養親族_生年月日_2",
          "扶養親族_生年月日（西暦）_2",
          "扶養親族_扶養区分_2",
          "扶養親族_障害者区分_2",
          "扶養親族_同居区分_2",
          "扶養親族_同居老親等区分_2",
          "扶養親族_非居住者区分_2",
          "扶養親族_控除計算区分_2",
          "扶養親族_扶養親族名_3",
          "扶養親族_フリガナ_3",
          "扶養親族_続柄_3",
          "扶養親族_生年月日_3",
          "扶養親族_生年月日（西暦）_3",
          "扶養親族_扶養区分_3",
          "扶養親族_障害者区分_3",
          "扶養親族_同居区分_3",
          "扶養親族_同居老親等区分_3",
          "扶養親族_非居住者区分_3",
          "扶養親族_控除計算区分_3",
          "扶養親族_扶養親族名_4",
          "扶養親族_フリガナ_4",
          "扶養親族_続柄_4",
          "扶養親族_生年月日_4",
          "扶養親族_生年月日（西暦）_4",
          "扶養親族_扶養区分_4",
          "扶養親族_障害者区分_4",
          "扶養親族_同居区分_4",
          "扶養親族_同居老親等区分_4",
          "扶養親族_非居住者区分_4",
          "扶養親族_控除計算区分_4",
          "扶養親族_扶養親族名_5",
          "扶養親族_フリガナ_5",
          "扶養親族_続柄_5",
          "扶養親族_生年月日_5",
          "扶養親族_生年月日（西暦）_5",
          "扶養親族_扶養区分_5",
          "扶養親族_障害者区分_5",
          "扶養親族_同居区分_5",
          "扶養親族_同居老親等区分_5",
          "扶養親族_非居住者区分_5",
          "扶養親族_控除計算区分_5",
          "扶養親族_扶養親族名_6",
          "扶養親族_フリガナ_6",
          "扶養親族_続柄_6",
          "扶養親族_生年月日_6",
          "扶養親族_生年月日（西暦）_6",
          "扶養親族_扶養区分_6",
          "扶養親族_障害者区分_6",
          "扶養親族_同居区分_6",
          "扶養親族_同居老親等区分_6",
          "扶養親族_非居住者区分_6",
          "扶養親族_控除計算区分_6",
          "扶養親族_扶養親族名_7",
          "扶養親族_フリガナ_7",
          "扶養親族_続柄_7",
          "扶養親族_生年月日_7",
          "扶養親族_生年月日（西暦）_7",
          "扶養親族_扶養区分_7",
          "扶養親族_障害者区分_7",
          "扶養親族_同居区分_7",
          "扶養親族_同居老親等区分_7",
          "扶養親族_非居住者区分_7",
          "扶養親族_控除計算区分_7",
          "扶養親族_扶養親族名_8",
          "扶養親族_フリガナ_8",
          "扶養親族_続柄_8",
          "扶養親族_生年月日_8",
          "扶養親族_生年月日（西暦）_8",
          "扶養親族_扶養区分_8",
          "扶養親族_障害者区分_8",
          "扶養親族_同居区分_8",
          "扶養親族_同居老親等区分_8",
          "扶養親族_非居住者区分_8",
          "扶養親族_控除計算区分_8",
          "扶養親族_扶養親族名_9",
          "扶養親族_フリガナ_9",
          "扶養親族_続柄_9",
          "扶養親族_生年月日_9",
          "扶養親族_生年月日（西暦）_9",
          "扶養親族_扶養区分_9",
          "扶養親族_障害者区分_9",
          "扶養親族_同居区分_9",
          "扶養親族_同居老親等区分_9",
          "扶養親族_非居住者区分_9",
          "扶養親族_控除計算区分_9",
          "扶養親族_扶養親族名_10",
          "扶養親族_フリガナ_10",
          "扶養親族_続柄_10",
          "扶養親族_生年月日_10",
          "扶養親族_生年月日（西暦）_10",
          "扶養親族_扶養区分_10",
          "扶養親族_障害者区分_10",
          "扶養親族_同居区分_10",
          "扶養親族_同居老親等区分_10",
          "扶養親族_非居住者区分_10",
          "扶養親族_控除計算区分_10",
          "扶養親族_扶養親族名_11",
          "扶養親族_フリガナ_11",
          "扶養親族_続柄_11",
          "扶養親族_生年月日_11",
          "扶養親族_生年月日（西暦）_11",
          "扶養親族_扶養区分_11",
          "扶養親族_障害者区分_11",
          "扶養親族_同居区分_11",
          "扶養親族_同居老親等区分_11",
          "扶養親族_非居住者区分_11",
          "扶養親族_控除計算区分_11",
          "扶養親族_扶養親族名_12",
          "扶養親族_フリガナ_12",
          "扶養親族_続柄_12",
          "扶養親族_生年月日_12",
          "扶養親族_生年月日（西暦）_12",
          "扶養親族_扶養区分_12",
          "扶養親族_障害者区分_12",
          "扶養親族_同居区分_12",
          "扶養親族_同居老親等区分_12",
          "扶養親族_非居住者区分_12",
          "扶養親族_控除計算区分_12",
          "扶養親族_扶養親族名_13",
          "扶養親族_フリガナ_13",
          "扶養親族_続柄_13",
          "扶養親族_生年月日_13",
          "扶養親族_生年月日（西暦）_13",
          "扶養親族_扶養区分_13",
          "扶養親族_障害者区分_13",
          "扶養親族_同居区分_13",
          "扶養親族_同居老親等区分_13",
          "扶養親族_非居住者区分_13",
          "扶養親族_控除計算区分_13",
          "扶養親族_扶養親族名_14",
          "扶養親族_フリガナ_14",
          "扶養親族_続柄_14",
          "扶養親族_生年月日_14",
          "扶養親族_生年月日（西暦）_14",
          "扶養親族_扶養区分_14",
          "扶養親族_障害者区分_14",
          "扶養親族_同居区分_14",
          "扶養親族_同居老親等区分_14",
          "扶養親族_非居住者区分_14",
          "扶養親族_控除計算区分_14",
          "扶養親族_扶養親族名_15",
          "扶養親族_フリガナ_15",
          "扶養親族_続柄_15",
          "扶養親族_生年月日_15",
          "扶養親族_生年月日（西暦）_15",
          "扶養親族_扶養区分_15",
          "扶養親族_障害者区分_15",
          "扶養親族_同居区分_15",
          "扶養親族_同居老親等区分_15",
          "扶養親族_非居住者区分_15",
          "扶養親族_控除計算区分_15",
        ];

        $export_header = [];

        foreach ($export_csv_title as $key => $val) {

          $export_header[] = mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
        }



        $export_header_02 = [];

        $export_csv_title_02 = [];

        $export_csv_title_02 = [
          '社員番号',
          '社員氏名',
          '支払元登録No',
          '振込先①　金融機関コード',
          '振込先①　金融機関名',
          '振込先①　金融機関フリガナ',
          '振込先①　支店コード',
          '振込先①　支店名',
          '振込先①　支店フリガナ',
          '振込先①　預金種目',
          '振込先①　口座番号',
          '振込先①　振込手数料',
          '振込先①　定額振込',
          '振込先①　金額',
          '振込先②　金融機関コード',
          '振込先②　金融機関名',
          '振込先②　金融機関フリガナ',
          '振込先②　支店コード',
          '振込先②　支店名',
          '振込先②　支店フリガナ',
          '振込先②　預金種目',
          '振込先②　口座番号',
          '振込先②　振込手数料',
          '振込先②　定額振込',
          '振込先②　金額'
        ];

        foreach ($export_csv_title_02 as $key => $val) {

          $export_header_02[] = mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
        }



        try {

          // PDO オブジェクト
          $pdo = new PDO($dsn, $user, $password);

          //========= トランザクション開始
          //    $pdo->beginTransaction();

          //====== SQL文　文を　日付テキストボックスの状況に合わせて　分岐
          if (strcmp($csv_FLG_exp, "3") == 0) {
          } else if (strcmp($csv_FLG_exp, "0") == 0) {

            $export_err = "表示できません";
          }

          // SQL
          $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
                  WHERE creation_time BETWEEN ? AND ? ORDER BY user_id DESC");

          // CSV エクスポート 開始日
          $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

          // CSV エクスポート 終了日
          $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);

          // SQL 実行 
          $res = $stmt->execute();

          $idx = 0;

          // 絞り込み　結果　格納配列
          $retunr_tmp = [];
          $return = [];

          // ファルダがなかったら、フォルダを作成  形式 files_20211025
          $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];
          if (!file_exists($dirpath)) {
            mkdir($dirpath, 0777);

            //chmod関数で「hoge」ディレクトリのパーミッションを「0777」にする
            //   chmod($dirpath, 0777);
          }

          //====== ファイルの保存
          // ファイル生成場所
          $create_csv = $dirpath . "/" . $file_name;


          if (touch($create_csv)) {
            $file = new SplFileObject($create_csv, "w");

            // 出力する CSV に　ヘッダーを書きこむ
            $file->fputcsv($export_header);

            //====================================================== 
            //=========================== CSV 用　変数宣言
            //====================================================== 
            $sex = ""; // 性別
            $wareki = ""; // 和暦の年
            $wareki_r = ""; // 和暦 年月日 出力用
            $month_r = "";
            $day_r = ""; // 日付　日にち

            $Seireki_r = ""; // 西暦出力用

            $hokensya_K = ""; // 保険者種別  男子、女子


            //======= 日付選択　データ　取得
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


              // ============ 文字化け対策 ============
              $row['user_name'] = mb_convert_encoding($row['user_name'], "SJIS-win", "auto"); // 社員氏名


              //=== ふりがな　を　カナ　へ変換
              $row['furi_name'] = mb_convert_kana($row['furi_name'], "h", "UTF-8"); // フリガナ
              $row['furi_name'] = mb_convert_encoding($row['furi_name'], "SJIS-win", "auto");

              // メールアドレス
              $row['email'] = mb_convert_encoding($row['email'], "SJIS-win", "auto");

              // 配偶者 spouse
              if (strcmp($row['spouse'], "0") == 0) {
                $row['spouse'] = "なし";
                $row['spouse'] = mb_convert_encoding($row['spouse'], "SJIS-win", "auto");
              } else {
                $row['spouse'] = "あり";
                $row['spouse'] = mb_convert_encoding($row['spouse'], "SJIS-win", "auto");
              }

              // 扶養人数 	dependents_num
              $row['dependents_num'] = mb_convert_encoding($row['dependents_num'], "SJIS-win", "auto");

              $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto"); // 0 => 男性 , 1 => 女性

              //=== 性別
              if (strcmp($row['sex'], "0") == 0) {
                $row['sex'] = "男"; // 保険者種別
                $hokensya_K = ""; // 保険者種別

                $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
                $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
              } else {
                $row['sex'] = "女"; // 保険者種別
                $hokensya_K = ""; // 保険者種別

                $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
                $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
              }

              //=== 生年月日　（西暦 => 和暦　変換）
              $row['birthday'] = mb_convert_encoding($row['birthday'], "SJIS-win", "auto");

              // 年
              $year = mb_substr($row['birthday'], 0, 4); // 西暦　取り出し 1900

              // 和暦変換用 function  Wareki_Parse
              $wareki = Wareki_Parse($year);

              // 月
              $month = mb_substr($row['birthday'], 5, 2);

              if (strpos(
                $month,
                '-'
              ) !== false) {
                // - ハイフン が含まれている場合 , 0　パディング
                $month = str_replace("-", "", $month);
                $month_r = "0" . $month;
              } else {
                $month_r = $month;
              }


              // 日
              $day = mb_substr($row['birthday'], 7, 2);
              // 文字列の長さ取得  1,  10
              $day_num = mb_strlen($day);

              if ($day_num == 1) {
                $day_r = "0" . $day;
              } else if (strpos($day, '-') !== false) {
                $day = str_replace("-", "", $day);
                $day_r = $day;
              } else {
                $day_r = $day;
              }

              $wareki_r = $wareki . $month_r . "月" . $day_r . "日";
              $wareki_r = mb_convert_encoding($wareki_r, "SJIS-win", "auto");

              // 西暦
              $Seireki_r = $year . "年" . $month_r . "月" . $day_r . "日";
              $Seireki_r = mb_convert_encoding($Seireki_r, "SJIS-win", "auto");

              //=== 郵便番号 zip  （-） をつける
              $row['zip'] = substr($row['zip'], 0, 3) . "-" . substr($row['zip'], 3);
              $row['zip'] = mb_convert_encoding($row['zip'], "SJIS-win", "auto");

              //=== 住所１ address_01
              $row['address_01'] = mb_convert_encoding($row['address_01'], "SJIS-win", "auto");


              //=== 電話番号 tel
              // 電話番号 （-） ハイフン付加
              $row['tel'] = substr($row['tel'], 0, 3) . "-" . substr($row['tel'], 3, 4) . "-" . substr($row['tel'], 7);
              $row['tel'] = mb_convert_encoding($row['tel'], "SJIS-win", "auto");


              $row['creation_time'] = mb_convert_encoding($row['creation_time'], "SJIS-win", "auto");
              $row['data_Flg'] = mb_convert_encoding($row['data_Flg'], "SJIS-win", "auto");

              // 社員番号
              // ================ 0 パディング
              $row['employee_id'] = sprintf('%06d', $row['employee_id']);
              $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");

              //=== 空文字
              $row['kara_01'] = ""; // 空 項目 ,   // 項目7: 入社区分
              $row['kara_02'] = ""; // 空 項目 ,   // 項目8: 入社年月日
              $row['kara_03'] = ""; // 空 項目 ,   // 項目9: 入社年月日（西暦）
              $row['kara_04'] = ""; // 空 項目 ,   // 項目10: 勤続年数
              $row['kara_05'] = ""; // 空 項目 ,   // 項目11: 勤続年数_月数

              $syasin_K = "アルバイト"; // 空 項目 ,   // 項目12: 社員区分 => アルバイト
              $syasin_K = mb_convert_encoding($syasin_K, "SJIS-win", "auto");


              $row['kara_07'] = ""; // 空 項目 ,   // 項目13: 役職
              $row['kara_08'] = ""; // 空 項目 ,   // 項目14: 学歴

              //==============================
              //=========== 2021_12_21 値 変更
              //============================== 
              // $syozoku_b = "003";                 // 項目15: 所属部門
              $syozoku_b = "アルバイト";
              $syozoku_b = mb_convert_encoding($syozoku_b, "SJIS-win", "auto");

              $row['kara_09'] = ""; // 空 項目 ,   // 
              $row['kara_10'] = ""; // 空 項目 ,   // 職種

              $row['kara_11'] = ""; // 空 項目 ,  // 住所 2 
              $row['kara_12'] = ""; // 空 項目 ,  // 住所 （フリガナ）１ ※入れたい 
              $row['kara_13'] = ""; // 空 項目 ,  // 住所 （フリガナ）２

              //=== 非該当 Start
              $row['higaitou_01'] = "非該当";
              $row['higaitou_02'] = "非該当";
              $row['higaitou_03'] = "非該当";
              $row['higaitou_04'] = "非該当";
              $row['higaitou_05'] = "非該当";
              $row['higaitou_06'] = "非該当";
              // エンコード
              $row['higaitou_01'] = mb_convert_encoding($row['higaitou_01'], "SJIS-win", "auto");
              $row['higaitou_02'] = mb_convert_encoding($row['higaitou_02'], "SJIS-win", "auto");
              $row['higaitou_03'] = mb_convert_encoding($row['higaitou_03'], "SJIS-win", "auto");
              $row['higaitou_04'] = mb_convert_encoding($row['higaitou_04'], "SJIS-win", "auto");
              $row['higaitou_05'] = mb_convert_encoding($row['higaitou_05'], "SJIS-win", "auto");
              $row['higaitou_06'] = mb_convert_encoding($row['higaitou_06'], "SJIS-win", "auto");
              //=== 非該当 End

              //=== 空文字
              $row['kara_14'] = "";  // 退職理由
              $row['kara_15'] = "";  // 退職年月日
              $row['kara_16'] = "";  // 退職年月日（西暦）

              //=== 定型　区分　
              $row['kubun_01'] =  "月給"; // 給与区分
              $row['kubun_02'] =  "月額甲欄"; // 税表区分
              $row['kubun_03'] =  "する"; // 年末調整対象 
              $row['kubun_04'] =  "振込"; // 支払形態
              $row['kubun_05'] =  "サンプル"; // 支給日パターン

              //=============== 2021_12_21 値変更
              //  $row['kubun_06'] =  "社員パターン"; // 給与明細書パターン
              $row['kubun_06'] =  ""; // 給与明細書パターン

              //=============== 2021_12_21 値変更
              //   $row['kubun_07'] =  "賞与サンプル"; // 賞与明細書パターン
              $row['kubun_07'] =  ""; // 賞与明細書パターン

              $row['kubun_08'] =  "";   // 健康保険対象
              $row['kubun_09'] =  "";   //介護保険対象

              //=============== 2021_12_21 値変更
              //     $row['kubun_10'] =  "";   //  健保月額（千円）
              $row['kubun_10'] =  "";   //  健保月額（千円）

              //=============== 2021_12_21 値変更
              //     $row['kubun_10_02'] = "367"; // 健保証番号
              $row['kubun_10_02'] = ""; // 健保証番号


              // エンコード
              $row['kubun_01'] = mb_convert_encoding($row['kubun_01'], "SJIS-win", "auto");
              $row['kubun_02'] = mb_convert_encoding($row['kubun_02'], "SJIS-win", "auto");
              $row['kubun_03'] = mb_convert_encoding($row['kubun_03'], "SJIS-win", "auto");
              $row['kubun_04'] = mb_convert_encoding($row['kubun_04'], "SJIS-win", "auto");
              $row['kubun_05'] = mb_convert_encoding($row['kubun_05'], "SJIS-win", "auto");
              $row['kubun_06'] = mb_convert_encoding($row['kubun_06'], "SJIS-win", "auto");
              $row['kubun_07'] = mb_convert_encoding($row['kubun_07'], "SJIS-win", "auto");
              $row['kubun_08'] = mb_convert_encoding($row['kubun_08'], "SJIS-win", "auto");
              $row['kubun_09'] = mb_convert_encoding($row['kubun_09'], "SJIS-win", "auto");
              $row['kubun_10'] = mb_convert_encoding($row['kubun_10'], "SJIS-win", "auto");
              //=== 定型　区分 END

              //=== 空文字
              $row['kara_17'] = "";   // 基礎年金番号１
              $row['kara_18'] = "";   // 基礎年金番号２

              //=== 定型　区分　
              $row['kubun_11'] = ""; // 厚生年金対象 , 対象  空欄

              $row['kubun_12'] = "非該当"; // 70歳以上被用者
              $row['kubun_13'] = $hokensya_K; // 保険者種別 , 男子、女子
              $row['kubun_14'] = ""; // 項目49:厚年月額（千円） 空欄
              // エンコード
              $row['kubun_11'] = mb_convert_encoding($row['kubun_11'], "SJIS-win", "auto");
              $row['kubun_12'] = mb_convert_encoding($row['kubun_12'], "SJIS-win", "auto");
              $row['kubun_13'] = mb_convert_encoding($row['kubun_13'], "SJIS-win", "auto");
              $row['kubun_14'] = mb_convert_encoding($row['kubun_14'], "SJIS-win", "auto");
              //=== 定型　区分　END

              $row['kara_19'] = ""; // 基礎年金番号１
              $row['kara_20'] = ""; // 基礎年金番号２

              //=== 定型区分
              $row['kubun_15'] =  "対象外";  // 短時間労働者（3/4未満）
              $row['kubun_16'] =  "対象";  // 労災保険対象

              //=============== 2021_12_21 値変更
              //  $row['kubun_17'] =  "対象";  // 雇用保険対象
              $row['kubun_17'] =  "";  // 雇用保険対象

              //=============== 2021_12_21 値変更
              $row['kubun_18'] =  "";  // 労働保険用区分

              //=============== 2021_12_21 値変更
              //     $row['kubun_19'] =  "5029";  // 雇用保険被保険者番号１
              $row['kubun_19'] =  "";  // 雇用保険被保険者番号１

              //=============== 2021_12_21 値変更
              //     $row['kubun_20'] =  "530842";  // 雇用保険被保険者番号 2
              $row['kubun_20'] =  "";  // 雇用保険被保険者番号 2

              //=============== 2021_12_21 値変更
              //     $row['kubun_21'] =  "4";  // 雇用保険被保険者番号 3
              $row['kubun_21'] =  "";  // 雇用保険被保険者番号 3

              // エンコード
              $row['kubun_15'] = mb_convert_encoding($row['kubun_15'], "SJIS-win", "auto");
              $row['kubun_16'] = mb_convert_encoding($row['kubun_16'], "SJIS-win", "auto");
              $row['kubun_17'] = mb_convert_encoding($row['kubun_17'], "SJIS-win", "auto");
              $row['kubun_18'] = mb_convert_encoding($row['kubun_18'], "SJIS-win", "auto");
              $row['kubun_19'] = mb_convert_encoding($row['kubun_19'], "SJIS-win", "auto");
              $row['kubun_20'] = mb_convert_encoding($row['kubun_20'], "SJIS-win", "auto");
              $row['kubun_21'] = mb_convert_encoding($row['kubun_21'], "SJIS-win", "auto");

              //=== 空文字
              $row['kara_22'] = ""; // 労働保険　資格取得年月日

              $row['kara_23'] = "";   // 
              $row['kara_24'] = "";   //  配偶者性別 ※ 2021_12_07 修正

              $row['kubun_22'] =  "";  // 配偶者生年月日
              $row['kubun_22'] = mb_convert_encoding($row['kubun_22'], "SJIS-win", "auto");

              $row['kara_25'] = "";  //  配偶者生年月日（西暦）
              //       $row['kara_26'] = "";  // 

              $row['higaitou_07'] = "非該当";   // 源泉控除対象配偶者
              $row['higaitou_08'] = "非該当";   // 配偶者老人
              $row['higaitou_09'] = "非該当";   // 配偶者障害者
              $row['higaitou_10'] = "非同居";   // 配偶者同居  : 非同居
              $row['higaitou_11'] = "非該当";   // 配偶者非居住者

              $row['higaitou_07'] = mb_convert_encoding($row['higaitou_07'], "SJIS-win", "auto");
              $row['higaitou_08'] = mb_convert_encoding($row['higaitou_08'], "SJIS-win", "auto");
              $row['higaitou_09'] = mb_convert_encoding($row['higaitou_09'], "SJIS-win", "auto");
              $row['higaitou_10'] = mb_convert_encoding($row['higaitou_10'], "SJIS-win", "auto");
              $row['higaitou_11'] = mb_convert_encoding($row['higaitou_11'], "SJIS-win", "auto");

              // =========　※必須 扶養控除対象人数
              $row['dependents_num'] = mb_convert_encoding($row['dependents_num'], "SJIS-win", "auto");

              //======== 165 の　空白行
              $row['last_kara_01'] = "";
              $row['last_kara_02'] = "";
              $row['last_kara_03'] = "";
              $row['last_kara_04'] = "";
              $row['last_kara_05'] = "";
              $row['last_kara_06'] = "";
              $row['last_kara_07'] = "";
              $row['last_kara_08'] = "";
              $row['last_kara_09'] = "";
              $row['last_kara_10'] = "";

              $row['last_kara_11'] = "";
              $row['last_kara_12'] = "";
              $row['last_kara_13'] = "";
              $row['last_kara_14'] = "";
              $row['last_kara_15'] = "";
              $row['last_kara_16'] = "";
              $row['last_kara_17'] = "";
              $row['last_kara_18'] = "";
              $row['last_kara_19'] = "";
              $row['last_kara_20'] = "";

              $row['last_kara_21'] = "";
              $row['last_kara_22'] = "";
              $row['last_kara_23'] = "";
              $row['last_kara_24'] = "";
              $row['last_kara_25'] = "";
              $row['last_kara_26'] = "";
              $row['last_kara_27'] = "";
              $row['last_kara_28'] = "";
              $row['last_kara_29'] = "";
              $row['last_kara_30'] = "";

              $row['last_kara_31'] = "";
              $row['last_kara_32'] = "";
              $row['last_kara_33'] = "";
              $row['last_kara_34'] = "";
              $row['last_kara_35'] = "";
              $row['last_kara_36'] = "";
              $row['last_kara_37'] = "";
              $row['last_kara_38'] = "";
              $row['last_kara_39'] = "";
              $row['last_kara_40'] = "";

              $row['last_kara_41'] = "";
              $row['last_kara_42'] = "";
              $row['last_kara_43'] = "";
              $row['last_kara_44'] = "";
              $row['last_kara_45'] = "";
              $row['last_kara_46'] = "";
              $row['last_kara_47'] = "";
              $row['last_kara_48'] = "";
              $row['last_kara_49'] = "";
              $row['last_kara_50'] = "";
              //============================ 50
              $row['last_kara_51'] = "";
              $row['last_kara_52'] = "";
              $row['last_kara_53'] = "";
              $row['last_kara_54'] = "";
              $row['last_kara_55'] = "";
              $row['last_kara_56'] = "";
              $row['last_kara_57'] = "";
              $row['last_kara_58'] = "";
              $row['last_kara_59'] = "";
              $row['last_kara_60'] = "";

              $row['last_kara_61'] = "";
              $row['last_kara_62'] = "";
              $row['last_kara_63'] = "";
              $row['last_kara_64'] = "";
              $row['last_kara_65'] = "";
              $row['last_kara_66'] = "";
              $row['last_kara_67'] = "";
              $row['last_kara_68'] = "";
              $row['last_kara_69'] = "";
              $row['last_kara_70'] = "";

              $row['last_kara_71'] = "";
              $row['last_kara_72'] = "";
              $row['last_kara_73'] = "";
              $row['last_kara_74'] = "";
              $row['last_kara_75'] = "";
              $row['last_kara_76'] = "";
              $row['last_kara_77'] = "";
              $row['last_kara_78'] = "";
              $row['last_kara_79'] = "";
              $row['last_kara_80'] = "";

              $row['last_kara_81'] = "";
              $row['last_kara_82'] = "";
              $row['last_kara_83'] = "";
              $row['last_kara_84'] = "";
              $row['last_kara_85'] = "";
              $row['last_kara_86'] = "";
              $row['last_kara_87'] = "";
              $row['last_kara_88'] = "";
              $row['last_kara_89'] = "";
              $row['last_kara_90'] = "";

              $row['last_kara_91'] = "";
              $row['last_kara_92'] = "";
              $row['last_kara_93'] = "";
              $row['last_kara_94'] = "";
              $row['last_kara_95'] = "";
              $row['last_kara_96'] = "";
              $row['last_kara_97'] = "";
              $row['last_kara_98'] = "";
              $row['last_kara_99'] = "";
              $row['last_kara_100'] = "";
              //=========================== 100
              $row['last_kara_101'] = "";
              $row['last_kara_102'] = "";
              $row['last_kara_103'] = "";
              $row['last_kara_104'] = "";
              $row['last_kara_105'] = "";
              $row['last_kara_106'] = "";
              $row['last_kara_107'] = "";
              $row['last_kara_108'] = "";
              $row['last_kara_109'] = "";
              $row['last_kara_110'] = "";

              $row['last_kara_111'] = "";
              $row['last_kara_112'] = "";
              $row['last_kara_113'] = "";
              $row['last_kara_114'] = "";
              $row['last_kara_115'] = "";
              $row['last_kara_116'] = "";
              $row['last_kara_117'] = "";
              $row['last_kara_118'] = "";
              $row['last_kara_119'] = "";
              $row['last_kara_120'] = "";

              $row['last_kara_121'] = "";
              $row['last_kara_122'] = "";
              $row['last_kara_123'] = "";
              $row['last_kara_124'] = "";
              $row['last_kara_125'] = "";
              $row['last_kara_126'] = "";
              $row['last_kara_127'] = "";
              $row['last_kara_128'] = "";
              $row['last_kara_129'] = "";
              $row['last_kara_130'] = "";

              $row['last_kara_131'] = "";
              $row['last_kara_132'] = "";
              $row['last_kara_133'] = "";
              $row['last_kara_134'] = "";
              $row['last_kara_135'] = "";
              $row['last_kara_136'] = "";
              $row['last_kara_137'] = "";
              $row['last_kara_138'] = "";
              $row['last_kara_139'] = "";
              $row['last_kara_140'] = "";

              $row['last_kara_141'] = "";
              $row['last_kara_142'] = "";
              $row['last_kara_143'] = "";
              $row['last_kara_144'] = "";
              $row['last_kara_145'] = "";
              $row['last_kara_146'] = "";
              $row['last_kara_147'] = "";
              $row['last_kara_148'] = "";
              $row['last_kara_149'] = "";
              $row['last_kara_150'] = "";
              //============================= 150
              $row['last_kara_151'] = "";
              $row['last_kara_152'] = "";
              $row['last_kara_153'] = "";
              $row['last_kara_154'] = "";
              $row['last_kara_155'] = "";
              $row['last_kara_156'] = "";
              $row['last_kara_157'] = "";
              $row['last_kara_158'] = "";
              $row['last_kara_159'] = "";
              $row['last_kara_160'] = "";

              $row['last_kara_161'] = "";
              $row['last_kara_162'] = "";
              $row['last_kara_163'] = "";
              $row['last_kara_164'] = "";


              //============================ 165

              /********************************************************************** */
              //*********************  ２次元配列へ格納 ******************************
              /********************************************************************** */
              $return[] = array(
                'employee_id' => $row['employee_id'], // 項目１： 管理番号 〇
                'user_name' => $row['user_name'], // 項目２： 社員氏名　〇
                'furi_name' => $row['furi_name'], // 項目３： フリガナ　〇
                'sex' => $row['sex'], // 項目４： 性別　〇
                'wareki' => $wareki_r, //　項目５： 和暦　〇

                'birthday' => $Seireki_r, //　項目６： 西暦　〇

                /*  
                   'creation_time' => $row['creation_time'],
                   'data_Flg' => $row['data_Flg'],
                   */

                'kara_01' => $row['kara_01'], //  項目７：　入社区分　
                'kara_02' => $row['kara_02'], //　項目８：　入社年月日
                'kara_03' => $row['kara_03'], //  項目９：　入社年月日（西暦）
                'kara_04' => $row['kara_04'], //  項目１０：勤続年数　
                'kara_05' => $row['kara_05'], //  項目１１：勤続年数_月数

                'syasin_K' => $syasin_K, // 項目１２： ,社員区分　〇

                'kara_07' => $row['kara_07'], // 項目13： 役職
                'kara_08' => $row['kara_08'], // 項目14： 学歴

                'syozoku_b' => $syozoku_b, // 項目15： 所属部門　〇

                'kara_10' => $row['kara_10'], // 項目16：職種

                'zip' => $row['zip'], // 項目 17 ：　郵便番号　〇
                'address_01' => $row['address_01'], // 項目18 ： 住所１　〇

                'kara_11' => $row['kara_11'], // 空　カラム 19  住所２
                'kara_12' => $row['kara_12'], // 空　カラム 20  住所（フリガナ）１
                'kara_13' => $row['kara_13'], // 空　カラム 21  住所（フリガナ）２

                'tel' => $row['tel'],   // 項目22 電話番号　〇
                'email' => $row['email'],   // 項目23: email　〇

                'higaitou_01' => $row['higaitou_01'],   // 項目24:障害区分  非該当
                'higaitou_02' => $row['higaitou_02'],   // 項目25:寡婦  非該当
                'higaitou_03' => $row['higaitou_03'],   // 項目26:寡夫 非該当
                'higaitou_04' => $row['higaitou_04'],   // 項目27: 非該当
                'higaitou_05' => $row['higaitou_05'],   // 項目28: 非該当
                'higaitou_06' => $row['higaitou_06'],   // 項目29: 非該当

                'kara_14' => $row['kara_14'], // 項目30:退職理由 空　カラム , 14
                'kara_15' => $row['kara_15'], // 項目31:退職年月日 空　カラム , 15
                'kara_16' => $row['kara_16'], // 項目32:退職年月日 空　カラム , 16

                'kubun_01' => $row['kubun_01'], // 定型区分 01, 項目33:給与区分:月給
                'kubun_02' => $row['kubun_02'], // 定型区分 02, 項目34:税表区分:月額甲欄
                'kubun_03' => $row['kubun_03'], // 定型区分 03, 項目35:年末調整対象:する
                'kubun_04' => $row['kubun_04'], // 定型区分 04, 項目36:支払形態:振込
                'kubun_05' => $row['kubun_05'], // 定型区分 05, 項目37:支給日パターン:サンプル
                'kubun_06' => $row['kubun_06'], // 定型区分 06, 項目38:給与明細書パターン:社員パターン
                'kubun_07' => $row['kubun_07'], // 定型区分 07, 項目39:賞与明細書パターン:賞与サンプル
                'kubun_08' => $row['kubun_08'], // 定型区分 08, 項目40:健康保険対象
                'kubun_09' => $row['kubun_09'], // 定型区分 09, 項目41:介護保険対象
                'kubun_10' => $row['kubun_10'], // 定型区分 10, 項目42:健保月額（千円）:220
                'kubun_10_02' => $row['kubun_10_02'], // 項目43:健保証番号（協会） 367

                'kara_17' => $row['kara_17'], // 空　カラム , 項目44:健保証番号（組合）
                'kara_18' => $row['kara_18'], // 空　カラム , 項目45:健保・厚年　資格取得年月日

                'kubun_11' => $row['kubun_11'], // 定型区分 11 項目46:厚生年金対象, 空欄

                'kubun_12' => $row['kubun_12'], // 定型区分 12 項目47:70歳以上被用者:非該当
                'kubun_13' => $row['kubun_13'], // 定型区分 13 項目48:保険者種別:女子
                'kubun_14' => $row['kubun_14'], // 定型区分 14 項目49:厚年月額（千円）

                'kara_19' => $row['kara_19'], // 空　カラム , 19  項目50:基礎年金番号１
                'kara_20' => $row['kara_20'], // 空　カラム , 20  項目51:基礎年金番号２

                'kubun_15' => $row['kubun_15'], // 定型区分 15 項目52:短時間労働者（3/4未満）:対象外
                'kubun_16' => $row['kubun_16'], // 定型区分 16 項目53:労災保険対象:対象
                'kubun_17' => $row['kubun_17'], // 定型区分 17 項目54:雇用保険対象:対象
                'kubun_18' => $row['kubun_18'], // 定型区分 18 項目55:労働保険用区分:常用
                'kubun_19' => $row['kubun_19'], // 定型区分 19 項目56:雇用保険被保険者番号１:5029
                'kubun_20' => $row['kubun_20'], // 定型区分 20 項目57:雇用保険被保険者番号２:530842
                'kubun_21' => $row['kubun_21'], // 定型区分 21 項目58:雇用保険被保険者番号３:4

                'kara_21' => $row['kara_21'], // 空　カラム , 21 項目59:労働保険　資格取得年月日:


                // 配偶者ある　、なし
                'spouse' => $row['spouse'], // 項目60:配偶者:なし

                'kara_22' => $row['kara_22'], // 空　カラム , 22 項目61:配偶者氏名:
                'kara_23' => $row['kara_23'], // 空　カラム , 23 項目62:配偶者フリガナ:
                'kara_24' => $row['kara_24'], // 空　カラム , 24 項目63:配偶者性別:

                'kubun_22' => $row['kubun_22'], // 定型区分 22 項目64:配偶者生年月日:

                'kara_25' => $row['kara_25'], // 空　カラム , 25 項目65:配偶者生年月日（西暦）
                //  'kara_26' => $row['kara_26'], // 空　カラム , 26

                'higaitou_07' => $row['higaitou_07'], // 項目66:源泉控除対象配偶者:非該当
                'higaitou_08' => $row['higaitou_08'], // 項目67:配偶者老人:非該当
                'higaitou_09' => $row['higaitou_09'], // 項目68:配偶者障害者:非該当
                'higaitou_10' => $row['higaitou_10'], // 項目69:配偶者同居:非同居
                'higaitou_11' => $row['higaitou_11'], // 項目70:配偶者非居住者:非該当

                //　※必須 扶養人数 dependents_num
                'dependents_num' => $row['dependents_num'], // 項目71:扶養控除対象人数:0

                // 165
                'last_kara_00' => $row['last_kara_0'],
                'last_kara_01' => $row['last_kara_1'],
                'last_kara_02' => $row['last_kara_2'],
                'last_kara_03' => $row['last_kara_3'],
                'last_kara_04' => $row['last_kara_4'],
                'last_kara_05' => $row['last_kara_5'],
                'last_kara_06' => $row['last_kara_6'],
                'last_kara_07' => $row['last_kara_7'],
                'last_kara_08' => $row['last_kara_8'],
                'last_kara_09' => $row['last_kara_9'],
                'last_kara_10' => $row['last_kara_10'],
                'last_kara_11' => $row['last_kara_11'],
                'last_kara_12' => $row['last_kara_12'],
                'last_kara_13' => $row['last_kara_13'],
                'last_kara_14' => $row['last_kara_14'],
                'last_kara_15' => $row['last_kara_15'],
                'last_kara_16' => $row['last_kara_16'],
                'last_kara_17' => $row['last_kara_17'],
                'last_kara_18' => $row['last_kara_18'],
                'last_kara_19' => $row['last_kara_19'],
                'last_kara_20' => $row['last_kara_20'],
                'last_kara_21' => $row['last_kara_21'],
                'last_kara_22' => $row['last_kara_22'],
                'last_kara_23' => $row['last_kara_23'],
                'last_kara_24' => $row['last_kara_24'],
                'last_kara_25' => $row['last_kara_25'],
                'last_kara_26' => $row['last_kara_26'],
                'last_kara_27' => $row['last_kara_27'],
                'last_kara_28' => $row['last_kara_28'],
                'last_kara_29' => $row['last_kara_29'],
                'last_kara_30' => $row['last_kara_30'],
                'last_kara_31' => $row['last_kara_31'],
                'last_kara_32' => $row['last_kara_32'],
                'last_kara_33' => $row['last_kara_33'],
                'last_kara_34' => $row['last_kara_34'],
                'last_kara_35' => $row['last_kara_35'],
                'last_kara_36' => $row['last_kara_36'],
                'last_kara_37' => $row['last_kara_37'],
                'last_kara_38' => $row['last_kara_38'],
                'last_kara_39' => $row['last_kara_39'],
                'last_kara_40' => $row['last_kara_40'],
                'last_kara_41' => $row['last_kara_41'],
                'last_kara_42' => $row['last_kara_42'],
                'last_kara_43' => $row['last_kara_43'],
                'last_kara_44' => $row['last_kara_44'],
                'last_kara_45' => $row['last_kara_45'],
                'last_kara_46' => $row['last_kara_46'],
                'last_kara_47' => $row['last_kara_47'],
                'last_kara_48' => $row['last_kara_48'],
                'last_kara_49' => $row['last_kara_49'],
                'last_kara_50' => $row['last_kara_50'],
                'last_kara_51' => $row['last_kara_51'],
                'last_kara_52' => $row['last_kara_52'],
                'last_kara_53' => $row['last_kara_53'],
                'last_kara_54' => $row['last_kara_54'],
                'last_kara_55' => $row['last_kara_55'],
                'last_kara_56' => $row['last_kara_56'],
                'last_kara_57' => $row['last_kara_57'],
                'last_kara_58' => $row['last_kara_58'],
                'last_kara_59' => $row['last_kara_59'],
                'last_kara_60' => $row['last_kara_60'],
                'last_kara_61' => $row['last_kara_61'],
                'last_kara_62' => $row['last_kara_62'],
                'last_kara_63' => $row['last_kara_63'],
                'last_kara_64' => $row['last_kara_64'],
                'last_kara_65' => $row['last_kara_65'],
                'last_kara_66' => $row['last_kara_66'],
                'last_kara_67' => $row['last_kara_67'],
                'last_kara_68' => $row['last_kara_68'],
                'last_kara_69' => $row['last_kara_69'],
                'last_kara_70' => $row['last_kara_70'],
                'last_kara_71' => $row['last_kara_71'],
                'last_kara_72' => $row['last_kara_72'],
                'last_kara_73' => $row['last_kara_73'],
                'last_kara_74' => $row['last_kara_74'],
                'last_kara_75' => $row['last_kara_75'],
                'last_kara_76' => $row['last_kara_76'],
                'last_kara_77' => $row['last_kara_77'],
                'last_kara_78' => $row['last_kara_78'],
                'last_kara_79' => $row['last_kara_79'],
                'last_kara_80' => $row['last_kara_80'],
                'last_kara_81' => $row['last_kara_81'],
                'last_kara_82' => $row['last_kara_82'],
                'last_kara_83' => $row['last_kara_83'],
                'last_kara_84' => $row['last_kara_84'],
                'last_kara_85' => $row['last_kara_85'],
                'last_kara_86' => $row['last_kara_86'],
                'last_kara_87' => $row['last_kara_87'],
                'last_kara_88' => $row['last_kara_88'],
                'last_kara_89' => $row['last_kara_89'],
                'last_kara_90' => $row['last_kara_90'],
                'last_kara_91' => $row['last_kara_91'],
                'last_kara_92' => $row['last_kara_92'],
                'last_kara_93' => $row['last_kara_93'],
                'last_kara_94' => $row['last_kara_94'],
                'last_kara_95' => $row['last_kara_95'],
                'last_kara_96' => $row['last_kara_96'],
                'last_kara_97' => $row['last_kara_97'],
                'last_kara_98' => $row['last_kara_98'],
                'last_kara_99' => $row['last_kara_99'],
                'last_kara_100' => $row['last_kara_100'],
                'last_kara_101' => $row['last_kara_101'],
                'last_kara_102' => $row['last_kara_102'],
                'last_kara_103' => $row['last_kara_103'],
                'last_kara_104' => $row['last_kara_104'],
                'last_kara_105' => $row['last_kara_105'],
                'last_kara_106' => $row['last_kara_106'],
                'last_kara_107' => $row['last_kara_107'],
                'last_kara_108' => $row['last_kara_108'],
                'last_kara_109' => $row['last_kara_109'],
                'last_kara_110' => $row['last_kara_110'],
                'last_kara_111' => $row['last_kara_111'],
                'last_kara_112' => $row['last_kara_112'],
                'last_kara_113' => $row['last_kara_113'],
                'last_kara_114' => $row['last_kara_114'],
                'last_kara_115' => $row['last_kara_115'],
                'last_kara_116' => $row['last_kara_116'],
                'last_kara_117' => $row['last_kara_117'],
                'last_kara_118' => $row['last_kara_118'],
                'last_kara_119' => $row['last_kara_119'],
                'last_kara_120' => $row['last_kara_120'],
                'last_kara_121' => $row['last_kara_121'],
                'last_kara_122' => $row['last_kara_122'],
                'last_kara_123' => $row['last_kara_123'],
                'last_kara_124' => $row['last_kara_124'],
                'last_kara_125' => $row['last_kara_125'],
                'last_kara_126' => $row['last_kara_126'],
                'last_kara_127' => $row['last_kara_127'],
                'last_kara_128' => $row['last_kara_128'],
                'last_kara_129' => $row['last_kara_129'],
                'last_kara_130' => $row['last_kara_130'],
                'last_kara_131' => $row['last_kara_131'],
                'last_kara_132' => $row['last_kara_132'],
                'last_kara_133' => $row['last_kara_133'],
                'last_kara_134' => $row['last_kara_134'],
                'last_kara_135' => $row['last_kara_135'],
                'last_kara_136' => $row['last_kara_136'],
                'last_kara_137' => $row['last_kara_137'],
                'last_kara_138' => $row['last_kara_138'],
                'last_kara_139' => $row['last_kara_139'],
                'last_kara_140' => $row['last_kara_140'],
                'last_kara_141' => $row['last_kara_141'],
                'last_kara_142' => $row['last_kara_142'],
                'last_kara_143' => $row['last_kara_143'],
                'last_kara_144' => $row['last_kara_144'],
                'last_kara_145' => $row['last_kara_145'],
                'last_kara_146' => $row['last_kara_146'],
                'last_kara_147' => $row['last_kara_147'],
                'last_kara_148' => $row['last_kara_148'],
                'last_kara_149' => $row['last_kara_149'],
                'last_kara_150' => $row['last_kara_150'],
                'last_kara_151' => $row['last_kara_151'],
                'last_kara_152' => $row['last_kara_152'],
                'last_kara_153' => $row['last_kara_153'],
                'last_kara_154' => $row['last_kara_154'],
                'last_kara_155' => $row['last_kara_155'],
                'last_kara_156' => $row['last_kara_156'],
                'last_kara_157' => $row['last_kara_157'],
                'last_kara_158' => $row['last_kara_158'],
                'last_kara_159' => $row['last_kara_159'],
                'last_kara_160' => $row['last_kara_160'],
                'last_kara_161' => $row['last_kara_161'],
                'last_kara_162' => $row['last_kara_162'],
                'last_kara_163' => $row['last_kara_163'],
                'last_kara_164' => $row['last_kara_164'],

              );

              // bank用　2次元配列　$return_bank []

            }

            // ファイルへ挿入 （.csv）
            foreach ($return as $item) {
              $file->fputcsv($item);
            }



            //================= CSV ファイル作成完了 フラグ
            $csv_file_ok_FLG = "1";

            // csv セレクト選択ボックス 有り OK 
            $export_output_err = "0";
          } //=== END if


          //==============================================
          //*************** ファイルのダウンロード処理 */
          //==============================================
          //  t_download($protocol);

          //===================================
          // =========== 現在のフルURL 取得
          //===================================
          /*
      if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {

        $protocol = 'https://';
      } else {

        $protocol = 'http://';
      }

      // ./files の . を削除
      $new_create_csv = mb_substr($create_csv, 1);

      $protocol .= $_SERVER["HTTP_HOST"] . '/job_recruit/csv' . $new_create_csv;
    
     


      // ファイルタイプを指定
      header('Content-Type: application/octet-stream');

      // ファイルサイズを取得し、ダウンロードの進捗を表示
      // header('Content-Length: '.filesize($protocol));

      // ファイルのダウンロード、リネームを指示
      header('Content-Disposition: attachment; filename="' . $file_name . '"');

      ob_clean();  //追記
      flush();     //追記

      // ファイルを読み込みダウンロードを実行
      readfile($protocol);

      //    exit;
      */
        } catch (PDOException $e) {

          //   print('Error:'.$e->getMessage());

          // (トランザクション) ロールバック
          //       $pdo->rollBack();

          // ************** エラー処理 ***************
          $csv_file_ok_FLG = "2";
        } finally {

          $pdo = null;
        }


        try {

          //=====================================================
          //=====================================================
          //================== 銀行用　CSV 作成
          //=====================================================
          //=====================================================

          //=== CSV ファイル名  => アルバイト管理_2021-10-12_121212.csv
          $file_name_02 = "GinkouJ_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
            "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
            $get_now_arr['minute'] . $get_now_arr['second'] . ".csv";

          // CSV ファイル名 2 => file_name_02

          //=== ファイル名の文字コード変換  
          $file_name_02 = mb_convert_encoding($file_name_02, "SJIS", "UTF-8");

          //=== ファイルパス
          $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];

          // 新ファイル
          $create_csv_02 = $dirpath . "/" . $file_name_02;

          // PDO オブジェクト
          $pdo = new PDO($dsn, $user, $password);

          // SQL
          $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
                  WHERE creation_time BETWEEN ? AND ? ORDER BY user_id DESC");

          // CSV エクスポート 開始日
          $stmt->bindValue(
            1,
            $date_target,
            PDO::PARAM_STR
          );

          // CSV エクスポート 終了日
          $stmt->bindValue(
            2,
            $date_target_02,
            PDO::PARAM_STR
          );

          // SQL 実行 
          $res = $stmt->execute();

          if (touch($create_csv_02)) {
            $file_02 = new SplFileObject($create_csv_02, "w");

            // 出力する CSV に　ヘッダーを書きこむ
            $file_02->fputcsv($export_header_02);
            // ============= END if

            //====================== 変数宣言

            $Siharai_M_Number; // 支払元登録No
            $bank_siten_name_kana = "ｼﾃﾝﾌﾘｶﾞﾅ";   // 支店名　カナ
            $bank_kamoku = ""; //   預金種目

            $teigaku_F = "0"; // 定額振込

            $koumoku_Last; // デフォルト 0

            //======= 日付選択　データ　取得
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


              // 社員氏名
              $row['user_name'] = mb_convert_encoding($row['user_name'], "SJIS-win", "auto");

              // 社員番号 
              //  ========= 0 パディング （６桁）
              $row['employee_id'] = sprintf('%06d', $row['employee_id']);
              $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");

              // 金融機関コード 	bank_code （４桁）
              $row['bank_code'] = sprintf('%04d', $row['bank_code']);
              $row['bank_code'] = mb_convert_encoding($row['bank_code'], "SJIS-win", "auto");

              // 金融機関名 bank_name
              $row['bank_name'] = mb_convert_encoding($row['bank_name'], "SJIS-win", "auto");
              // 金融機関フリガナ bank_name_kana


              $row['bank_name_kana'] = mb_convert_encoding($row['bank_name_kana'], "SJIS-win", "auto");


              // 支店コード bank_siten_code （３桁）
              $row['bank_code'] = sprintf('%03d', $row['bank_code']);
              $row['bank_siten_code'] = mb_convert_encoding($row['bank_siten_code'], "SJIS-win", "auto");

              // 支店名 bank_siten_name 
              $row['bank_siten_name'] = mb_convert_encoding($row['bank_siten_name'], "SJIS-win", "auto");


              // 預金種目  0:普通 , 1:当座 bank_kamoku
              $row['bank_kamoku'] = mb_convert_encoding($row['bank_kamoku'], "SJIS-win", "auto");
              if (strcmp($row['bank_kamoku'], "0") == 0) {
                $bank_kamoku = "普通";
                $bank_kamoku = mb_convert_encoding($bank_kamoku, "SJIS-win", "auto");
              } else {
                $bank_kamoku = "当座";
                $bank_kamoku = mb_convert_encoding($bank_kamoku, "SJIS-win", "auto");
              }

              // 口座番号 kouzzz_number
              $row['kouzzz_number'] = mb_convert_encoding($row['kouzzz_number'], "SJIS-win", "auto");

              // 支払元登録No
              $Siharai_M_Number = "1";

              // 支店フリガナ ※カラムなし $bank_siten_name_kana
              $bank_siten_name_kana = "ｼﾃﾝﾌﾘｶﾞﾅ";
              $bank_siten_name_kana = mb_convert_encoding($bank_siten_name_kana, "SJIS-win", "auto");

              // 振込手数料 空欄
              //=== 空文字
              $row['kara_01'] = ""; // 空 項目 ,   // 振込手数料

              $teigaku_F = "";
              $teigaku_F = mb_convert_encoding($teigaku_F, "SJIS-win", "auto"); // 定額振込

              //===  10 空欄 ===
              $row['kara_02'] = ""; // 空 項目 ,   //   振込先 1　金額,
              $row['kara_03'] = ""; // 空 項目 ,   // 振込先2　金融機関コード,
              $row['kara_04'] = ""; // 空 項目 ,   // 振込先2　金融機関名,
              $row['kara_05'] = ""; // 空 項目 ,   // 振込先2　金融機関フリガナ,
              $row['kara_06'] = ""; // 空 項目 ,   // 振込先2　支店コード,
              $row['kara_07'] = ""; // 空 項目 ,   // 振込先2  支店名,
              $row['kara_08'] = ""; // 空 項目 ,  振込先2   支店フリガナ
              $row['kara_09'] = ""; // 空 項目 ,   // 振込先2　預金種目,
              $row['kara_10'] = ""; // 空 項目 ,   // 振込先2　口座番号,
              $row['kara_11'] = ""; // 空 項目 ,   // 振込先2　振込手数料,
              $row['kara_12'] = ""; // 空 項目 ,   // 振込先2　定額振込,

              // 振込先2, ■■■必須■■■ デフォルト 0  項目ラスト
              $koumoku_Last = "";

              $return_bank[] = array(
                'employee_id' => $row['employee_id'], // 項目 ： 社員番号
                'user_name' => $row['user_name'], // 項目 ： 社員氏名

                // 支払元登録No  デフォルト: 1
                'Siharai_M_Number' => $Siharai_M_Number,

                'bank_code' => $row['bank_code'], // 金融機関コード
                'bank_name' => $row['bank_name'], // 金融機関名
                'bank_name_kana' => $row['bank_name_kana'], // 金融機関フリガナ

                'bank_siten_code' => $row['bank_siten_code'], // 支店コード
                'bank_siten_name' => $row['bank_siten_name'], // 支店名
                'bank_siten_name_kana' => $bank_siten_name_kana, // 支店フリガナ

                'bank_kamoku' =>  $bank_kamoku, // 預金種目
                'kouzzz_number' => $row['kouzzz_number'], // 口座番号

                'kara_01' => $row['kara_01'], // 振込手数料

                'teigaku_F' => $teigaku_F, // 定額振込

                'kara_02' => $row['kara_02'], // 空　カラム , 2
                'kara_03' => $row['kara_03'], // 空　カラム , 3
                'kara_04' => $row['kara_04'], // 空　カラム , 4
                'kara_05' => $row['kara_05'], // 空　カラム , 5
                'kara_06' => $row['kara_06'], // 空　カラム , 6
                'kara_07' => $row['kara_07'], // 空　カラム , 7
                'kara_08' => $row['kara_08'], // 空　カラム , 8
                'kara_09' => $row['kara_09'], // 空　カラム , 9
                'kara_10' => $row['kara_10'], // 空　カラム , 10
                'kara_11' => $row['kara_11'], // 空　カラム , 10
                'kara_12' => $row['kara_12'], // 空　カラム , 10

                'koumoku_Last' => $koumoku_Last, // 項目ラスト 

              );
            }


            // ファイルへ挿入 （.csv）
            foreach ($return_bank as $item) {
              $file_02->fputcsv($item);
            }
          } //======= END if 




          //===================================
          // =========== 現在のフルURL 取得   ファイルダウンロード処理
          //===================================

          /*
      if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {

        $protocol_02 = 'https://';
      } else {

        $protocol_02 = 'http://';
      }

     
      $new_create_csv_02 = mb_substr($create_csv_02, 1);
      $protocol_02 .= $_SERVER["HTTP_HOST"] . '/job_recruit/csv' . $new_create_csv_02;

      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="' . $file_name_02 . '"');

      ob_clean();  
      flush();     

      readfile($protocol_02);
      exit;
      */


          //==================================================================
          //================= アップデート 処理 データ処理フラグを データ作成済みにする
          //==================================================================

          $stmt = $pdo->prepare("UPDATE User_Info_Table SET data_Flg = '1' 
            WHERE creation_time BETWEEN ? AND ?");

          // CSV エクスポート 開始日
          $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

          // CSV エクスポート 終了日
          $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);

          // SQL 実行 
          $res = $stmt->execute();
        } catch (PDOException $e) {

          // ************** エラー処理 ***************
          $csv_file_ok_FLG = "2";
        } finally {

          $pdo = null;

          // ページリロード
          header("Location: " . $_SERVER['PHP_SELF']);

          exit;
        }
      }
    }



    //==============================================================================
    //==============================================================================
    //=======  未出力ファイル　チェック (デフォルト)   download_btn CSV ダウンロード処理 
    //===============================================================================
    //==============================================================================

  } else if ($export_output === "csv" && isset($_POST['uninput_file'])) {


    //============ 未出力 ファイル名 CSV , Mcsv_baito_kanri_

    // CSV ファイル名  => 未出力_アルバイト管理_2021-10-12_121212.csv
    $file_name = "Mcsv_baito_kanri_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
      "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
      $get_now_arr['minute'] . $get_now_arr['second'] . ".csv";

    // ファイル名の文字コード変換  
    //  $file_name = mb_convert_encoding($file_name, "SJIS", "auto");
    $file_name = mb_convert_encoding($file_name, "SJIS", "UTF-8");

    /*
    $export_csv_title = [
      "名前", "ふりがな", "年齢","メールアドレス", "性別", "登録日"
    ]; //DBテーブルのヘッダー項目
    */

    // ======= CSV ファイル　ヘッダータイトル　作成
    //============= 項目 237 
    $export_csv_title = [
      "社員番号",
      "社員氏名",
      "フリガナ",
      "性別",
      "生年月日",
      "生年月日（西暦）",
      "入社区分",
      "入社年月日",
      "入社年月日（西暦）",
      "勤続年数",
      "勤続年数_月数",
      "社員区分",
      "役職",
      "学歴",
      "所属部門",
      "職種",
      "郵便番号",
      "住所１",
      "住所２",
      "住所（フリガナ）１",
      "住所（フリガナ）２",
      "電話番号",
      "メールアドレス",
      "障害区分",
      "寡婦",
      "寡夫",
      "勤労学生",
      "災害者",
      "外国人",
      "退職理由",
      "退職年月日",
      "退職年月日（西暦）",
      "給与区分",
      "税表区分",
      "年末調整対象",
      "支払形態",
      "支給日パターン",
      "給与明細書パターン",
      "賞与明細書パターン",
      "健康保険対象",
      "介護保険対象",
      "健保月額（千円）",
      "健保証番号（協会）",
      "健保証番号（組合）",
      "健保・厚年　資格取得年月日",
      "厚生年金対象",
      "70歳以上被用者",
      "保険者種別",
      "厚年月額（千円）",
      "基礎年金番号１",
      "基礎年金番号２",
      "短時間労働者（3/4未満）",
      "労災保険対象",
      "雇用保険対象",
      "労働保険用区分",
      "雇用保険被保険者番号１",
      "雇用保険被保険者番号２",
      "雇用保険被保険者番号３",
      "労働保険　資格取得年月日",
      "配偶者",
      "配偶者氏名",
      "配偶者フリガナ",
      "配偶者性別",
      "配偶者生年月日",
      "配偶者生年月日（西暦）",
      "源泉控除対象配偶者",
      "配偶者老人",
      "配偶者障害者",
      "配偶者同居",
      "配偶者非居住者",
      "扶養控除対象人数",
      "扶養親族_扶養親族名_1",
      "扶養親族_フリガナ_1",
      "扶養親族_続柄_1",
      "扶養親族_生年月日_1",
      "扶養親族_生年月日（西暦）_1",
      "扶養親族_扶養区分_1",
      "扶養親族_障害者区分_1",
      "扶養親族_同居区分_1",
      "扶養親族_同居老親等区分_1",
      "扶養親族_非居住者区分_1",
      "扶養親族_控除計算区分_1",
      "扶養親族_扶養親族名_2",
      "扶養親族_フリガナ_2",
      "扶養親族_続柄_2",
      "扶養親族_生年月日_2",
      "扶養親族_生年月日（西暦）_2",
      "扶養親族_扶養区分_2",
      "扶養親族_障害者区分_2",
      "扶養親族_同居区分_2",
      "扶養親族_同居老親等区分_2",
      "扶養親族_非居住者区分_2",
      "扶養親族_控除計算区分_2",
      "扶養親族_扶養親族名_3",
      "扶養親族_フリガナ_3",
      "扶養親族_続柄_3",
      "扶養親族_生年月日_3",
      "扶養親族_生年月日（西暦）_3",
      "扶養親族_扶養区分_3",
      "扶養親族_障害者区分_3",
      "扶養親族_同居区分_3",
      "扶養親族_同居老親等区分_3",
      "扶養親族_非居住者区分_3",
      "扶養親族_控除計算区分_3",
      "扶養親族_扶養親族名_4",
      "扶養親族_フリガナ_4",
      "扶養親族_続柄_4",
      "扶養親族_生年月日_4",
      "扶養親族_生年月日（西暦）_4",
      "扶養親族_扶養区分_4",
      "扶養親族_障害者区分_4",
      "扶養親族_同居区分_4",
      "扶養親族_同居老親等区分_4",
      "扶養親族_非居住者区分_4",
      "扶養親族_控除計算区分_4",
      "扶養親族_扶養親族名_5",
      "扶養親族_フリガナ_5",
      "扶養親族_続柄_5",
      "扶養親族_生年月日_5",
      "扶養親族_生年月日（西暦）_5",
      "扶養親族_扶養区分_5",
      "扶養親族_障害者区分_5",
      "扶養親族_同居区分_5",
      "扶養親族_同居老親等区分_5",
      "扶養親族_非居住者区分_5",
      "扶養親族_控除計算区分_5",
      "扶養親族_扶養親族名_6",
      "扶養親族_フリガナ_6",
      "扶養親族_続柄_6",
      "扶養親族_生年月日_6",
      "扶養親族_生年月日（西暦）_6",
      "扶養親族_扶養区分_6",
      "扶養親族_障害者区分_6",
      "扶養親族_同居区分_6",
      "扶養親族_同居老親等区分_6",
      "扶養親族_非居住者区分_6",
      "扶養親族_控除計算区分_6",
      "扶養親族_扶養親族名_7",
      "扶養親族_フリガナ_7",
      "扶養親族_続柄_7",
      "扶養親族_生年月日_7",
      "扶養親族_生年月日（西暦）_7",
      "扶養親族_扶養区分_7",
      "扶養親族_障害者区分_7",
      "扶養親族_同居区分_7",
      "扶養親族_同居老親等区分_7",
      "扶養親族_非居住者区分_7",
      "扶養親族_控除計算区分_7",
      "扶養親族_扶養親族名_8",
      "扶養親族_フリガナ_8",
      "扶養親族_続柄_8",
      "扶養親族_生年月日_8",
      "扶養親族_生年月日（西暦）_8",
      "扶養親族_扶養区分_8",
      "扶養親族_障害者区分_8",
      "扶養親族_同居区分_8",
      "扶養親族_同居老親等区分_8",
      "扶養親族_非居住者区分_8",
      "扶養親族_控除計算区分_8",
      "扶養親族_扶養親族名_9",
      "扶養親族_フリガナ_9",
      "扶養親族_続柄_9",
      "扶養親族_生年月日_9",
      "扶養親族_生年月日（西暦）_9",
      "扶養親族_扶養区分_9",
      "扶養親族_障害者区分_9",
      "扶養親族_同居区分_9",
      "扶養親族_同居老親等区分_9",
      "扶養親族_非居住者区分_9",
      "扶養親族_控除計算区分_9",
      "扶養親族_扶養親族名_10",
      "扶養親族_フリガナ_10",
      "扶養親族_続柄_10",
      "扶養親族_生年月日_10",
      "扶養親族_生年月日（西暦）_10",
      "扶養親族_扶養区分_10",
      "扶養親族_障害者区分_10",
      "扶養親族_同居区分_10",
      "扶養親族_同居老親等区分_10",
      "扶養親族_非居住者区分_10",
      "扶養親族_控除計算区分_10",
      "扶養親族_扶養親族名_11",
      "扶養親族_フリガナ_11",
      "扶養親族_続柄_11",
      "扶養親族_生年月日_11",
      "扶養親族_生年月日（西暦）_11",
      "扶養親族_扶養区分_11",
      "扶養親族_障害者区分_11",
      "扶養親族_同居区分_11",
      "扶養親族_同居老親等区分_11",
      "扶養親族_非居住者区分_11",
      "扶養親族_控除計算区分_11",
      "扶養親族_扶養親族名_12",
      "扶養親族_フリガナ_12",
      "扶養親族_続柄_12",
      "扶養親族_生年月日_12",
      "扶養親族_生年月日（西暦）_12",
      "扶養親族_扶養区分_12",
      "扶養親族_障害者区分_12",
      "扶養親族_同居区分_12",
      "扶養親族_同居老親等区分_12",
      "扶養親族_非居住者区分_12",
      "扶養親族_控除計算区分_12",
      "扶養親族_扶養親族名_13",
      "扶養親族_フリガナ_13",
      "扶養親族_続柄_13",
      "扶養親族_生年月日_13",
      "扶養親族_生年月日（西暦）_13",
      "扶養親族_扶養区分_13",
      "扶養親族_障害者区分_13",
      "扶養親族_同居区分_13",
      "扶養親族_同居老親等区分_13",
      "扶養親族_非居住者区分_13",
      "扶養親族_控除計算区分_13",
      "扶養親族_扶養親族名_14",
      "扶養親族_フリガナ_14",
      "扶養親族_続柄_14",
      "扶養親族_生年月日_14",
      "扶養親族_生年月日（西暦）_14",
      "扶養親族_扶養区分_14",
      "扶養親族_障害者区分_14",
      "扶養親族_同居区分_14",
      "扶養親族_同居老親等区分_14",
      "扶養親族_非居住者区分_14",
      "扶養親族_控除計算区分_14",
      "扶養親族_扶養親族名_15",
      "扶養親族_フリガナ_15",
      "扶養親族_続柄_15",
      "扶養親族_生年月日_15",
      "扶養親族_生年月日（西暦）_15",
      "扶養親族_扶養区分_15",
      "扶養親族_障害者区分_15",
      "扶養親族_同居区分_15",
      "扶養親族_同居老親等区分_15",
      "扶養親族_非居住者区分_15",
      "扶養親族_控除計算区分_15",
    ];

    $export_header = [];

    foreach ($export_csv_title as $key => $val) {

      $export_header[] = mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
    }



    $export_header_02 = [];

    $export_csv_title_02 = [];

    $export_csv_title_02 = [
      '社員番号',
      '社員氏名',
      '支払元登録No',
      '振込先①　金融機関コード',
      '振込先①　金融機関名',
      '振込先①　金融機関フリガナ',
      '振込先①　支店コード',
      '振込先①　支店名',
      '振込先①　支店フリガナ',
      '振込先①　預金種目',
      '振込先①　口座番号',
      '振込先①　振込手数料',
      '振込先①　定額振込',
      '振込先①　金額',
      '振込先②　金融機関コード',
      '振込先②　金融機関名',
      '振込先②　金融機関フリガナ',
      '振込先②　支店コード',
      '振込先②　支店名',
      '振込先②　支店フリガナ',
      '振込先②　預金種目',
      '振込先②　口座番号',
      '振込先②　振込手数料',
      '振込先②　定額振込',
      '振込先②　金額'
    ];

    foreach ($export_csv_title_02 as $key => $val) {

      $export_header_02[] = mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
    }


    //================================  
    //================== 接続情報
    //================================  
    $dsn = '';
    $user = '';
    $password = '';

    // 絞り込み　結果　格納配列
    $retunr_output_csv = []; // CSV

    try {

      // PDO オブジェクト作成
      $pdo = new PDO($dsn, $user, $password);

      $stmt = $pdo->prepare("SELECT * from User_Info_Table WHERE data_Flg = '0'");

      // SQL 実行
      $res = $stmt->execute();


      // 絞り込み　結果　格納配列
      $retunr_tmp = [];
      $return = [];

      // ファルダがなかったら、フォルダを作成  形式 files_20211025
      $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];
      if (!file_exists($dirpath)) {
        mkdir($dirpath, 0777);

        //chmod関数で「hoge」ディレクトリのパーミッションを「0777」にする
        //   chmod($dirpath, 0777);
      }

      //====== ファイルの保存
      // ファイル生成場所
      $create_csv = $dirpath . "/" . $file_name;


      if (touch($create_csv)) {
        $file = new SplFileObject($create_csv, "w");

        // 出力する CSV に　ヘッダーを書きこむ
        $file->fputcsv($export_header);

        //====================================================== 
        //=========================== CSV 用　変数宣言
        //====================================================== 
        $sex = ""; // 性別
        $wareki = ""; // 和暦の年
        $wareki_r = ""; // 和暦 年月日 出力用
        $month_r = "";
        $day_r = ""; // 日付　日にち

        $Seireki_r = ""; // 西暦出力用

        $hokensya_K = ""; // 保険者種別  男子、女子
        $syasin_K = ""; // 社員区分
        $syozoku_b = ""; // 所属部門


        //======= 日付選択　データ　取得
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


          // ============ 文字化け対策 ============
          $row['user_name'] = mb_convert_encoding($row['user_name'], "SJIS-win", "auto"); // 社員氏名


          //=== ふりがな　を　カナ　へ変換
          $row['furi_name'] = mb_convert_kana($row['furi_name'], "h", "UTF-8"); // フリガナ
          $row['furi_name'] = mb_convert_encoding($row['furi_name'], "SJIS-win", "auto");

          // メールアドレス
          $row['email'] = mb_convert_encoding($row['email'], "SJIS-win", "auto");

          // 配偶者 spouse
          if (strcmp($row['spouse'], "0") == 0) {
            $row['spouse'] = "なし";
            $row['spouse'] = mb_convert_encoding($row['spouse'], "SJIS-win", "auto");
          } else {
            $row['spouse'] = "あり";
            $row['spouse'] = mb_convert_encoding($row['spouse'], "SJIS-win", "auto");
          }


          // 扶養人数 	dependents_num
          $row['dependents_num'] = mb_convert_encoding($row['dependents_num'], "SJIS-win", "auto");

          $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto"); // 0 => 男性 , 1 => 女性

          //=== 性別
          if (strcmp($row['sex'], "0") == 0) {
            $row['sex'] = "男"; // 保険者種別
            $hokensya_K = ""; // 保険者種別

            $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
            $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
          } else {
            $row['sex'] = "女"; // 保険者種別
            $hokensya_K = ""; // 保険者種別

            $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
            $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
          }

          //=== 生年月日　（西暦 => 和暦　変換）
          $row['birthday'] = mb_convert_encoding($row['birthday'], "SJIS-win", "auto");

          // 年
          $year = mb_substr($row['birthday'], 0, 4); // 西暦　取り出し 1900

          // 和暦変換用 function  Wareki_Parse
          $wareki = Wareki_Parse($year);

          // 月
          $month = mb_substr($row['birthday'], 5, 2);

          if (strpos(
            $month,
            '-'
          ) !== false) {
            // - ハイフン が含まれている場合 , 0　パディング
            $month = str_replace("-", "", $month);
            $month_r = "0" . $month;
          } else {
            $month_r = $month;
          }


          // 日
          $day = mb_substr($row['birthday'], 7, 2);
          // 文字列の長さ取得  1,  10
          $day_num = mb_strlen($day);

          if ($day_num == 1) {
            $day_r = "0" . $day;
          } else if (strpos($day, '-') !== false) {
            $day = str_replace("-", "", $day);
            $day_r = $day;
          } else {
            $day_r = $day;
          }

          $wareki_r = $wareki . $month_r . "月" . $day_r . "日";
          $wareki_r = mb_convert_encoding($wareki_r, "SJIS-win", "auto");

          // 西暦
          $Seireki_r = $year . "年" . $month_r . "月" . $day_r . "日";
          $Seireki_r = mb_convert_encoding($Seireki_r, "SJIS-win", "auto");

          //=== 郵便番号 zip  （-） をつける
          $row['zip'] = substr($row['zip'], 0, 3) . "-" . substr($row['zip'], 3);
          $row['zip'] = mb_convert_encoding($row['zip'], "SJIS-win", "auto");

          //=== 住所１ address_01
          $row['address_01'] = mb_convert_encoding($row['address_01'], "SJIS-win", "auto");
          //=== 電話番号 tel

          // 電話番号 （-） ハイフン付加
          $row['tel'] = substr($row['tel'], 0, 3) . "-" . substr($row['tel'], 3, 4) . "-" . substr($row['tel'], 7);
          $row['tel'] = mb_convert_encoding($row['tel'], "SJIS-win", "auto");


          $row['creation_time'] = mb_convert_encoding($row['creation_time'], "SJIS-win", "auto");
          $row['data_Flg'] = mb_convert_encoding($row['data_Flg'], "SJIS-win", "auto");

          // 社員番号 employee_id （６桁）
          $row['employee_id'] = sprintf('%06d', $row['employee_id']);
          $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");

          //=== 空文字
          $row['kara_01'] = ""; // 空 項目 ,   // 項目7: 入社区分
          $row['kara_02'] = ""; // 空 項目 ,   // 項目8: 入社年月日
          $row['kara_03'] = ""; // 空 項目 ,   // 項目9: 入社年月日（西暦）
          $row['kara_04'] = ""; // 空 項目 ,   // 項目10: 勤続年数
          $row['kara_05'] = ""; // 空 項目 ,   // 項目11: 勤続年数_月数

          $syasin_K = "アルバイト"; // 空 項目 ,   // 項目12: 社員区分 => アルバイト
          $syasin_K = mb_convert_encoding($syasin_K, "SJIS-win", "auto");


          $row['kara_07'] = ""; // 空 項目 ,   // 項目13: 役職
          $row['kara_08'] = ""; // 空 項目 ,   // 項目14: 学歴

          //=============== 2021_12_21 値変更
          //     $syozoku_b = "003";                 // 項目15: 所属部門
          $syozoku_b = "アルバイト";                 // 項目15: 所属部門
          $syozoku_b = mb_convert_encoding($syozoku_b, "SJIS-win", "auto");

          $row['kara_09'] = ""; // 空 項目 ,   // 
          $row['kara_10'] = ""; // 空 項目 ,   // 職種

          $row['kara_11'] = ""; // 空 項目 ,  // 住所 2 
          $row['kara_12'] = ""; // 空 項目 ,  // 住所 （フリガナ）１ ※入れたい 
          $row['kara_13'] = ""; // 空 項目 ,  // 住所 （フリガナ）２

          //=== 非該当 Start
          $row['higaitou_01'] = "非該当";
          $row['higaitou_02'] = "非該当";
          $row['higaitou_03'] = "非該当";
          $row['higaitou_04'] = "非該当";
          $row['higaitou_05'] = "非該当";
          $row['higaitou_06'] = "非該当";
          // エンコード
          $row['higaitou_01'] = mb_convert_encoding($row['higaitou_01'], "SJIS-win", "auto");
          $row['higaitou_02'] = mb_convert_encoding($row['higaitou_02'], "SJIS-win", "auto");
          $row['higaitou_03'] = mb_convert_encoding($row['higaitou_03'], "SJIS-win", "auto");
          $row['higaitou_04'] = mb_convert_encoding($row['higaitou_04'], "SJIS-win", "auto");
          $row['higaitou_05'] = mb_convert_encoding($row['higaitou_05'], "SJIS-win", "auto");
          $row['higaitou_06'] = mb_convert_encoding($row['higaitou_06'], "SJIS-win", "auto");
          //=== 非該当 End

          //=== 空文字
          $row['kara_14'] = "";  // 退職理由
          $row['kara_15'] = "";  // 退職年月日
          $row['kara_16'] = "";  // 退職年月日（西暦）

          //=== 定型　区分　
          $row['kubun_01'] =  "月給"; // 給与区分
          $row['kubun_02'] =  "月額甲欄"; // 税表区分
          $row['kubun_03'] =  "する"; // 年末調整対象 
          $row['kubun_04'] =  "振込"; // 支払形態
          $row['kubun_05'] =  "サンプル"; // 支給日パターン

          //=============== 2021_12_21 値変更
          //   $row['kubun_06'] =  "社員パターン"; // 給与明細書パターン
          $row['kubun_06'] =  ""; // 給与明細書パターン

          //=============== 2021_12_21 値変更
          //  $row['kubun_07'] =  "賞与サンプル"; // 賞与明細書パターン
          $row['kubun_07'] =  ""; // 賞与明細書パターン


          $row['kubun_08'] =  "";   // 健康保険対象
          $row['kubun_09'] =  "";   //介護保険対象

          //=============== 2021_12_21 値変更
          //  $row['kubun_10'] =  "220";   //  健保月額（千円）
          $row['kubun_10'] =  "";   //  健保月額（千円）

          //=============== 2021_12_21 値変更
          //  $row['kubun_10_02'] = "367"; // 健保証番号
          $row['kubun_10_02'] = ""; // 健保証番号

          // エンコード
          $row['kubun_01'] = mb_convert_encoding($row['kubun_01'], "SJIS-win", "auto");
          $row['kubun_02'] = mb_convert_encoding($row['kubun_02'], "SJIS-win", "auto");
          $row['kubun_03'] = mb_convert_encoding($row['kubun_03'], "SJIS-win", "auto");
          $row['kubun_04'] = mb_convert_encoding($row['kubun_04'], "SJIS-win", "auto");
          $row['kubun_05'] = mb_convert_encoding($row['kubun_05'], "SJIS-win", "auto");
          $row['kubun_06'] = mb_convert_encoding($row['kubun_06'], "SJIS-win", "auto");
          $row['kubun_07'] = mb_convert_encoding($row['kubun_07'], "SJIS-win", "auto");
          $row['kubun_08'] = mb_convert_encoding($row['kubun_08'], "SJIS-win", "auto");
          $row['kubun_09'] = mb_convert_encoding($row['kubun_09'], "SJIS-win", "auto");
          $row['kubun_10'] = mb_convert_encoding($row['kubun_10'], "SJIS-win", "auto");
          //=== 定型　区分 END

          //=== 空文字
          $row['kara_17'] = "";   // 基礎年金番号１
          $row['kara_18'] = "";   // 基礎年金番号２

          //=== 定型　区分　
          $row['kubun_11'] = ""; // 厚生年金対象 , 対象  空欄

          $row['kubun_12'] = "非該当"; // 70歳以上被用者
          $row['kubun_13'] = $hokensya_K; // 保険者種別 , 男子、女子
          $row['kubun_14'] = ""; // 項目49:厚年月額（千円） 空欄
          // エンコード
          $row['kubun_11'] = mb_convert_encoding($row['kubun_11'], "SJIS-win", "auto");
          $row['kubun_12'] = mb_convert_encoding($row['kubun_12'], "SJIS-win", "auto");
          $row['kubun_13'] = mb_convert_encoding($row['kubun_13'], "SJIS-win", "auto");
          $row['kubun_14'] = mb_convert_encoding($row['kubun_14'], "SJIS-win", "auto");
          //=== 定型　区分　END

          $row['kara_19'] = ""; // 基礎年金番号１
          $row['kara_20'] = ""; // 基礎年金番号２

          //=== 定型区分
          $row['kubun_15'] =  "対象外";  // 短時間労働者（3/4未満）
          $row['kubun_16'] =  "対象";  // 労災保険対象
          $row['kubun_17'] =  "対象";  // 雇用保険対象
          $row['kubun_18'] =  "常用";  // 労働保険用区分

          //=============== 2021_12_21 値変更
          //  $row['kubun_19'] =  "5029";  // 雇用保険被保険者番号１
          $row['kubun_19'] =  "";  // 雇用保険被保険者番号１

          //=============== 2021_12_21 値変更
          //  $row['kubun_20'] =  "530842";  // 雇用保険被保険者番号 2
          $row['kubun_20'] =  "";  // 雇用保険被保険者番号 2

          //=============== 2021_12_21 値変更
          //  $row['kubun_21'] =  "4";  // 雇用保険被保険者番号 3
          $row['kubun_21'] =  "";  // 雇用保険被保険者番号 3

          // エンコード
          $row['kubun_15'] = mb_convert_encoding($row['kubun_15'], "SJIS-win", "auto");
          $row['kubun_16'] = mb_convert_encoding($row['kubun_16'], "SJIS-win", "auto");
          $row['kubun_17'] = mb_convert_encoding($row['kubun_17'], "SJIS-win", "auto");
          $row['kubun_18'] = mb_convert_encoding($row['kubun_18'], "SJIS-win", "auto");
          $row['kubun_19'] = mb_convert_encoding($row['kubun_19'], "SJIS-win", "auto");
          $row['kubun_20'] = mb_convert_encoding($row['kubun_20'], "SJIS-win", "auto");
          $row['kubun_21'] = mb_convert_encoding($row['kubun_21'], "SJIS-win", "auto");

          //=== 空文字
          $row['kara_22'] = ""; // 労働保険　資格取得年月日

          $row['kara_23'] = "";   // 
          $row['kara_24'] = "";   //  配偶者性別 ※ 2021_12_07 修正

          $row['kubun_22'] =  "";  // 配偶者生年月日
          $row['kubun_22'] = mb_convert_encoding($row['kubun_22'], "SJIS-win", "auto");

          $row['kara_25'] = "";  //  配偶者生年月日（西暦）
          //       $row['kara_26'] = "";  // 

          $row['higaitou_07'] = "非該当";   // 源泉控除対象配偶者
          $row['higaitou_08'] = "非該当";   // 配偶者老人
          $row['higaitou_09'] = "非該当";   // 配偶者障害者
          $row['higaitou_10'] = "非同居";   // 配偶者同居  : 非同居
          $row['higaitou_11'] = "非該当";   // 配偶者非居住者

          $row['higaitou_07'] = mb_convert_encoding($row['higaitou_07'], "SJIS-win", "auto");
          $row['higaitou_08'] = mb_convert_encoding($row['higaitou_08'], "SJIS-win", "auto");
          $row['higaitou_09'] = mb_convert_encoding($row['higaitou_09'], "SJIS-win", "auto");
          $row['higaitou_10'] = mb_convert_encoding($row['higaitou_10'], "SJIS-win", "auto");
          $row['higaitou_11'] = mb_convert_encoding($row['higaitou_11'], "SJIS-win", "auto");

          // =========　※必須 扶養控除対象人数
          $row['dependents_num'] = mb_convert_encoding($row['dependents_num'], "SJIS-win", "auto");

          //======== 165 の　空白行
          $row['last_kara_01'] = "";
          $row['last_kara_02'] = "";
          $row['last_kara_03'] = "";
          $row['last_kara_04'] = "";
          $row['last_kara_05'] = "";
          $row['last_kara_06'] = "";
          $row['last_kara_07'] = "";
          $row['last_kara_08'] = "";
          $row['last_kara_09'] = "";
          $row['last_kara_10'] = "";

          $row['last_kara_11'] = "";
          $row['last_kara_12'] = "";
          $row['last_kara_13'] = "";
          $row['last_kara_14'] = "";
          $row['last_kara_15'] = "";
          $row['last_kara_16'] = "";
          $row['last_kara_17'] = "";
          $row['last_kara_18'] = "";
          $row['last_kara_19'] = "";
          $row['last_kara_20'] = "";

          $row['last_kara_21'] = "";
          $row['last_kara_22'] = "";
          $row['last_kara_23'] = "";
          $row['last_kara_24'] = "";
          $row['last_kara_25'] = "";
          $row['last_kara_26'] = "";
          $row['last_kara_27'] = "";
          $row['last_kara_28'] = "";
          $row['last_kara_29'] = "";
          $row['last_kara_30'] = "";

          $row['last_kara_31'] = "";
          $row['last_kara_32'] = "";
          $row['last_kara_33'] = "";
          $row['last_kara_34'] = "";
          $row['last_kara_35'] = "";
          $row['last_kara_36'] = "";
          $row['last_kara_37'] = "";
          $row['last_kara_38'] = "";
          $row['last_kara_39'] = "";
          $row['last_kara_40'] = "";

          $row['last_kara_41'] = "";
          $row['last_kara_42'] = "";
          $row['last_kara_43'] = "";
          $row['last_kara_44'] = "";
          $row['last_kara_45'] = "";
          $row['last_kara_46'] = "";
          $row['last_kara_47'] = "";
          $row['last_kara_48'] = "";
          $row['last_kara_49'] = "";
          $row['last_kara_50'] = "";
          //============================ 50
          $row['last_kara_51'] = "";
          $row['last_kara_52'] = "";
          $row['last_kara_53'] = "";
          $row['last_kara_54'] = "";
          $row['last_kara_55'] = "";
          $row['last_kara_56'] = "";
          $row['last_kara_57'] = "";
          $row['last_kara_58'] = "";
          $row['last_kara_59'] = "";
          $row['last_kara_60'] = "";

          $row['last_kara_61'] = "";
          $row['last_kara_62'] = "";
          $row['last_kara_63'] = "";
          $row['last_kara_64'] = "";
          $row['last_kara_65'] = "";
          $row['last_kara_66'] = "";
          $row['last_kara_67'] = "";
          $row['last_kara_68'] = "";
          $row['last_kara_69'] = "";
          $row['last_kara_70'] = "";

          $row['last_kara_71'] = "";
          $row['last_kara_72'] = "";
          $row['last_kara_73'] = "";
          $row['last_kara_74'] = "";
          $row['last_kara_75'] = "";
          $row['last_kara_76'] = "";
          $row['last_kara_77'] = "";
          $row['last_kara_78'] = "";
          $row['last_kara_79'] = "";
          $row['last_kara_80'] = "";

          $row['last_kara_81'] = "";
          $row['last_kara_82'] = "";
          $row['last_kara_83'] = "";
          $row['last_kara_84'] = "";
          $row['last_kara_85'] = "";
          $row['last_kara_86'] = "";
          $row['last_kara_87'] = "";
          $row['last_kara_88'] = "";
          $row['last_kara_89'] = "";
          $row['last_kara_90'] = "";

          $row['last_kara_91'] = "";
          $row['last_kara_92'] = "";
          $row['last_kara_93'] = "";
          $row['last_kara_94'] = "";
          $row['last_kara_95'] = "";
          $row['last_kara_96'] = "";
          $row['last_kara_97'] = "";
          $row['last_kara_98'] = "";
          $row['last_kara_99'] = "";
          $row['last_kara_100'] = "";
          //=========================== 100
          $row['last_kara_101'] = "";
          $row['last_kara_102'] = "";
          $row['last_kara_103'] = "";
          $row['last_kara_104'] = "";
          $row['last_kara_105'] = "";
          $row['last_kara_106'] = "";
          $row['last_kara_107'] = "";
          $row['last_kara_108'] = "";
          $row['last_kara_109'] = "";
          $row['last_kara_110'] = "";

          $row['last_kara_111'] = "";
          $row['last_kara_112'] = "";
          $row['last_kara_113'] = "";
          $row['last_kara_114'] = "";
          $row['last_kara_115'] = "";
          $row['last_kara_116'] = "";
          $row['last_kara_117'] = "";
          $row['last_kara_118'] = "";
          $row['last_kara_119'] = "";
          $row['last_kara_120'] = "";

          $row['last_kara_121'] = "";
          $row['last_kara_122'] = "";
          $row['last_kara_123'] = "";
          $row['last_kara_124'] = "";
          $row['last_kara_125'] = "";
          $row['last_kara_126'] = "";
          $row['last_kara_127'] = "";
          $row['last_kara_128'] = "";
          $row['last_kara_129'] = "";
          $row['last_kara_130'] = "";

          $row['last_kara_131'] = "";
          $row['last_kara_132'] = "";
          $row['last_kara_133'] = "";
          $row['last_kara_134'] = "";
          $row['last_kara_135'] = "";
          $row['last_kara_136'] = "";
          $row['last_kara_137'] = "";
          $row['last_kara_138'] = "";
          $row['last_kara_139'] = "";
          $row['last_kara_140'] = "";

          $row['last_kara_141'] = "";
          $row['last_kara_142'] = "";
          $row['last_kara_143'] = "";
          $row['last_kara_144'] = "";
          $row['last_kara_145'] = "";
          $row['last_kara_146'] = "";
          $row['last_kara_147'] = "";
          $row['last_kara_148'] = "";
          $row['last_kara_149'] = "";
          $row['last_kara_150'] = "";
          //============================= 150
          $row['last_kara_151'] = "";
          $row['last_kara_152'] = "";
          $row['last_kara_153'] = "";
          $row['last_kara_154'] = "";
          $row['last_kara_155'] = "";
          $row['last_kara_156'] = "";
          $row['last_kara_157'] = "";
          $row['last_kara_158'] = "";
          $row['last_kara_159'] = "";
          $row['last_kara_160'] = "";

          $row['last_kara_161'] = "";
          $row['last_kara_162'] = "";
          $row['last_kara_163'] = "";
          $row['last_kara_164'] = "";


          //============================ 165

          /********************************************************************** */
          //*********************  ２次元配列へ格納 ******************************
          /********************************************************************** */
          $retunr_output_csv[] = array(
            'employee_id' => $row['employee_id'], // 項目１： 管理番号 〇
            'user_name' => $row['user_name'], // 項目２： 社員氏名　〇
            'furi_name' => $row['furi_name'], // 項目３： フリガナ　〇
            'sex' => $row['sex'], // 項目４： 性別　〇
            'wareki' => $wareki_r, //　項目５： 和暦　〇

            'birthday' => $Seireki_r, //　項目６： 西暦　〇

            /*  
              'creation_time' => $row['creation_time'],
              'data_Flg' => $row['data_Flg'],
              */

            'kara_01' => $row['kara_01'], //  項目７：　入社区分　
            'kara_02' => $row['kara_02'], //　項目８：　入社年月日
            'kara_03' => $row['kara_03'], //  項目９：　入社年月日（西暦）
            'kara_04' => $row['kara_04'], //  項目１０：勤続年数　
            'kara_05' => $row['kara_05'], //  項目１１：勤続年数_月数

            'syasin_K' => $syasin_K, // 項目１２： ,社員区分　〇

            'kara_07' => $row['kara_07'], // 項目13： 役職
            'kara_08' => $row['kara_08'], // 項目14： 学歴

            'syozoku_b' => $syozoku_b, // 項目15： 所属部門　〇

            'kara_10' => $row['kara_10'], // 項目16：職種

            'zip' => $row['zip'], // 項目 17 ：　郵便番号　〇
            'address_01' => $row['address_01'], // 項目18 ： 住所１　〇

            'kara_11' => $row['kara_11'], // 空　カラム 19  住所２
            'kara_12' => $row['kara_12'], // 空　カラム 20  住所（フリガナ）１
            'kara_13' => $row['kara_13'], // 空　カラム 21  住所（フリガナ）２

            'tel' => $row['tel'],   // 項目22 電話番号　〇
            'email' => $row['email'],   // 項目23: email　〇

            'higaitou_01' => $row['higaitou_01'],   // 項目24:障害区分  非該当
            'higaitou_02' => $row['higaitou_02'],   // 項目25:寡婦  非該当
            'higaitou_03' => $row['higaitou_03'],   // 項目26:寡夫 非該当
            'higaitou_04' => $row['higaitou_04'],   // 項目27: 非該当
            'higaitou_05' => $row['higaitou_05'],   // 項目28: 非該当
            'higaitou_06' => $row['higaitou_06'],   // 項目29: 非該当

            'kara_14' => $row['kara_14'], // 項目30:退職理由 空　カラム , 14
            'kara_15' => $row['kara_15'], // 項目31:退職年月日 空　カラム , 15
            'kara_16' => $row['kara_16'], // 項目32:退職年月日 空　カラム , 16

            'kubun_01' => $row['kubun_01'], // 定型区分 01, 項目33:給与区分:月給
            'kubun_02' => $row['kubun_02'], // 定型区分 02, 項目34:税表区分:月額甲欄
            'kubun_03' => $row['kubun_03'], // 定型区分 03, 項目35:年末調整対象:する
            'kubun_04' => $row['kubun_04'], // 定型区分 04, 項目36:支払形態:振込
            'kubun_05' => $row['kubun_05'], // 定型区分 05, 項目37:支給日パターン:サンプル
            'kubun_06' => $row['kubun_06'], // 定型区分 06, 項目38:給与明細書パターン:社員パターン
            'kubun_07' => $row['kubun_07'], // 定型区分 07, 項目39:賞与明細書パターン:賞与サンプル
            'kubun_08' => $row['kubun_08'], // 定型区分 08, 項目40:健康保険対象
            'kubun_09' => $row['kubun_09'], // 定型区分 09, 項目41:介護保険対象
            'kubun_10' => $row['kubun_10'], // 定型区分 10, 項目42:健保月額（千円）:220
            'kubun_10_02' => $row['kubun_10_02'], // 項目43:健保証番号（協会） 367

            'kara_17' => $row['kara_17'], // 空　カラム , 項目44:健保証番号（組合）
            'kara_18' => $row['kara_18'], // 空　カラム , 項目45:健保・厚年　資格取得年月日

            'kubun_11' => $row['kubun_11'], // 定型区分 11 項目46:厚生年金対象, 空欄

            'kubun_12' => $row['kubun_12'], // 定型区分 12 項目47:70歳以上被用者:非該当
            'kubun_13' => $row['kubun_13'], // 定型区分 13 項目48:保険者種別:女子
            'kubun_14' => $row['kubun_14'], // 定型区分 14 項目49:厚年月額（千円）

            'kara_19' => $row['kara_19'], // 空　カラム , 19  項目50:基礎年金番号１
            'kara_20' => $row['kara_20'], // 空　カラム , 20  項目51:基礎年金番号２

            'kubun_15' => $row['kubun_15'], // 定型区分 15 項目52:短時間労働者（3/4未満）:対象外
            'kubun_16' => $row['kubun_16'], // 定型区分 16 項目53:労災保険対象:対象
            'kubun_17' => $row['kubun_17'], // 定型区分 17 項目54:雇用保険対象:対象
            'kubun_18' => $row['kubun_18'], // 定型区分 18 項目55:労働保険用区分:常用
            'kubun_19' => $row['kubun_19'], // 定型区分 19 項目56:雇用保険被保険者番号１:5029
            'kubun_20' => $row['kubun_20'], // 定型区分 20 項目57:雇用保険被保険者番号２:530842
            'kubun_21' => $row['kubun_21'], // 定型区分 21 項目58:雇用保険被保険者番号３:4

            'kara_21' => $row['kara_21'], // 空　カラム , 21 項目59:労働保険　資格取得年月日:


            // 配偶者ある　、なし
            'spouse' => $row['spouse'], // 項目60:配偶者:なし

            'kara_22' => $row['kara_22'], // 空　カラム , 22 項目61:配偶者氏名:
            'kara_23' => $row['kara_23'], // 空　カラム , 23 項目62:配偶者フリガナ:
            'kara_24' => $row['kara_24'], // 空　カラム , 24 項目63:配偶者性別:

            'kubun_22' => $row['kubun_22'], // 定型区分 22 項目64:配偶者生年月日:

            'kara_25' => $row['kara_25'], // 空　カラム , 25 項目65:配偶者生年月日（西暦）
            //  'kara_26' => $row['kara_26'], // 空　カラム , 26

            'higaitou_07' => $row['higaitou_07'], // 項目66:源泉控除対象配偶者:非該当
            'higaitou_08' => $row['higaitou_08'], // 項目67:配偶者老人:非該当
            'higaitou_09' => $row['higaitou_09'], // 項目68:配偶者障害者:非該当
            'higaitou_10' => $row['higaitou_10'], // 項目69:配偶者同居:非同居
            'higaitou_11' => $row['higaitou_11'], // 項目70:配偶者非居住者:非該当

            //　※必須 扶養人数 dependents_num
            'dependents_num' => $row['dependents_num'], // 項目71:扶養控除対象人数:0

            // 165
            'last_kara_00' => $row['last_kara_0'],
            'last_kara_01' => $row['last_kara_1'],
            'last_kara_02' => $row['last_kara_2'],
            'last_kara_03' => $row['last_kara_3'],
            'last_kara_04' => $row['last_kara_4'],
            'last_kara_05' => $row['last_kara_5'],
            'last_kara_06' => $row['last_kara_6'],
            'last_kara_07' => $row['last_kara_7'],
            'last_kara_08' => $row['last_kara_8'],
            'last_kara_09' => $row['last_kara_9'],
            'last_kara_10' => $row['last_kara_10'],
            'last_kara_11' => $row['last_kara_11'],
            'last_kara_12' => $row['last_kara_12'],
            'last_kara_13' => $row['last_kara_13'],
            'last_kara_14' => $row['last_kara_14'],
            'last_kara_15' => $row['last_kara_15'],
            'last_kara_16' => $row['last_kara_16'],
            'last_kara_17' => $row['last_kara_17'],
            'last_kara_18' => $row['last_kara_18'],
            'last_kara_19' => $row['last_kara_19'],
            'last_kara_20' => $row['last_kara_20'],
            'last_kara_21' => $row['last_kara_21'],
            'last_kara_22' => $row['last_kara_22'],
            'last_kara_23' => $row['last_kara_23'],
            'last_kara_24' => $row['last_kara_24'],
            'last_kara_25' => $row['last_kara_25'],
            'last_kara_26' => $row['last_kara_26'],
            'last_kara_27' => $row['last_kara_27'],
            'last_kara_28' => $row['last_kara_28'],
            'last_kara_29' => $row['last_kara_29'],
            'last_kara_30' => $row['last_kara_30'],
            'last_kara_31' => $row['last_kara_31'],
            'last_kara_32' => $row['last_kara_32'],
            'last_kara_33' => $row['last_kara_33'],
            'last_kara_34' => $row['last_kara_34'],
            'last_kara_35' => $row['last_kara_35'],
            'last_kara_36' => $row['last_kara_36'],
            'last_kara_37' => $row['last_kara_37'],
            'last_kara_38' => $row['last_kara_38'],
            'last_kara_39' => $row['last_kara_39'],
            'last_kara_40' => $row['last_kara_40'],
            'last_kara_41' => $row['last_kara_41'],
            'last_kara_42' => $row['last_kara_42'],
            'last_kara_43' => $row['last_kara_43'],
            'last_kara_44' => $row['last_kara_44'],
            'last_kara_45' => $row['last_kara_45'],
            'last_kara_46' => $row['last_kara_46'],
            'last_kara_47' => $row['last_kara_47'],
            'last_kara_48' => $row['last_kara_48'],
            'last_kara_49' => $row['last_kara_49'],
            'last_kara_50' => $row['last_kara_50'],
            'last_kara_51' => $row['last_kara_51'],
            'last_kara_52' => $row['last_kara_52'],
            'last_kara_53' => $row['last_kara_53'],
            'last_kara_54' => $row['last_kara_54'],
            'last_kara_55' => $row['last_kara_55'],
            'last_kara_56' => $row['last_kara_56'],
            'last_kara_57' => $row['last_kara_57'],
            'last_kara_58' => $row['last_kara_58'],
            'last_kara_59' => $row['last_kara_59'],
            'last_kara_60' => $row['last_kara_60'],
            'last_kara_61' => $row['last_kara_61'],
            'last_kara_62' => $row['last_kara_62'],
            'last_kara_63' => $row['last_kara_63'],
            'last_kara_64' => $row['last_kara_64'],
            'last_kara_65' => $row['last_kara_65'],
            'last_kara_66' => $row['last_kara_66'],
            'last_kara_67' => $row['last_kara_67'],
            'last_kara_68' => $row['last_kara_68'],
            'last_kara_69' => $row['last_kara_69'],
            'last_kara_70' => $row['last_kara_70'],
            'last_kara_71' => $row['last_kara_71'],
            'last_kara_72' => $row['last_kara_72'],
            'last_kara_73' => $row['last_kara_73'],
            'last_kara_74' => $row['last_kara_74'],
            'last_kara_75' => $row['last_kara_75'],
            'last_kara_76' => $row['last_kara_76'],
            'last_kara_77' => $row['last_kara_77'],
            'last_kara_78' => $row['last_kara_78'],
            'last_kara_79' => $row['last_kara_79'],
            'last_kara_80' => $row['last_kara_80'],
            'last_kara_81' => $row['last_kara_81'],
            'last_kara_82' => $row['last_kara_82'],
            'last_kara_83' => $row['last_kara_83'],
            'last_kara_84' => $row['last_kara_84'],
            'last_kara_85' => $row['last_kara_85'],
            'last_kara_86' => $row['last_kara_86'],
            'last_kara_87' => $row['last_kara_87'],
            'last_kara_88' => $row['last_kara_88'],
            'last_kara_89' => $row['last_kara_89'],
            'last_kara_90' => $row['last_kara_90'],
            'last_kara_91' => $row['last_kara_91'],
            'last_kara_92' => $row['last_kara_92'],
            'last_kara_93' => $row['last_kara_93'],
            'last_kara_94' => $row['last_kara_94'],
            'last_kara_95' => $row['last_kara_95'],
            'last_kara_96' => $row['last_kara_96'],
            'last_kara_97' => $row['last_kara_97'],
            'last_kara_98' => $row['last_kara_98'],
            'last_kara_99' => $row['last_kara_99'],
            'last_kara_100' => $row['last_kara_100'],
            'last_kara_101' => $row['last_kara_101'],
            'last_kara_102' => $row['last_kara_102'],
            'last_kara_103' => $row['last_kara_103'],
            'last_kara_104' => $row['last_kara_104'],
            'last_kara_105' => $row['last_kara_105'],
            'last_kara_106' => $row['last_kara_106'],
            'last_kara_107' => $row['last_kara_107'],
            'last_kara_108' => $row['last_kara_108'],
            'last_kara_109' => $row['last_kara_109'],
            'last_kara_110' => $row['last_kara_110'],
            'last_kara_111' => $row['last_kara_111'],
            'last_kara_112' => $row['last_kara_112'],
            'last_kara_113' => $row['last_kara_113'],
            'last_kara_114' => $row['last_kara_114'],
            'last_kara_115' => $row['last_kara_115'],
            'last_kara_116' => $row['last_kara_116'],
            'last_kara_117' => $row['last_kara_117'],
            'last_kara_118' => $row['last_kara_118'],
            'last_kara_119' => $row['last_kara_119'],
            'last_kara_120' => $row['last_kara_120'],
            'last_kara_121' => $row['last_kara_121'],
            'last_kara_122' => $row['last_kara_122'],
            'last_kara_123' => $row['last_kara_123'],
            'last_kara_124' => $row['last_kara_124'],
            'last_kara_125' => $row['last_kara_125'],
            'last_kara_126' => $row['last_kara_126'],
            'last_kara_127' => $row['last_kara_127'],
            'last_kara_128' => $row['last_kara_128'],
            'last_kara_129' => $row['last_kara_129'],
            'last_kara_130' => $row['last_kara_130'],
            'last_kara_131' => $row['last_kara_131'],
            'last_kara_132' => $row['last_kara_132'],
            'last_kara_133' => $row['last_kara_133'],
            'last_kara_134' => $row['last_kara_134'],
            'last_kara_135' => $row['last_kara_135'],
            'last_kara_136' => $row['last_kara_136'],
            'last_kara_137' => $row['last_kara_137'],
            'last_kara_138' => $row['last_kara_138'],
            'last_kara_139' => $row['last_kara_139'],
            'last_kara_140' => $row['last_kara_140'],
            'last_kara_141' => $row['last_kara_141'],
            'last_kara_142' => $row['last_kara_142'],
            'last_kara_143' => $row['last_kara_143'],
            'last_kara_144' => $row['last_kara_144'],
            'last_kara_145' => $row['last_kara_145'],
            'last_kara_146' => $row['last_kara_146'],
            'last_kara_147' => $row['last_kara_147'],
            'last_kara_148' => $row['last_kara_148'],
            'last_kara_149' => $row['last_kara_149'],
            'last_kara_150' => $row['last_kara_150'],
            'last_kara_151' => $row['last_kara_151'],
            'last_kara_152' => $row['last_kara_152'],
            'last_kara_153' => $row['last_kara_153'],
            'last_kara_154' => $row['last_kara_154'],
            'last_kara_155' => $row['last_kara_155'],
            'last_kara_156' => $row['last_kara_156'],
            'last_kara_157' => $row['last_kara_157'],
            'last_kara_158' => $row['last_kara_158'],
            'last_kara_159' => $row['last_kara_159'],
            'last_kara_160' => $row['last_kara_160'],
            'last_kara_161' => $row['last_kara_161'],
            'last_kara_162' => $row['last_kara_162'],
            'last_kara_163' => $row['last_kara_163'],
            'last_kara_164' => $row['last_kara_164'],

          );

          // bank用　2次元配列　$return_bank []

        }

        // ファイルへ挿入 （.csv）
        foreach ($retunr_output_csv as $item) {
          $file->fputcsv($item);
        }


        //================= CSV ファイル作成完了 フラグ
        $csv_file_ok_FLG = "1";

        // csv セレクト選択ボックス 有り OK 
        $export_output_err = "0";
      } //=== END if


      //. $_SERVER["REQUEST_URI"];

      //==============================================
      //*************** ファイルのダウンロード処理 */
      //==============================================
      //  t_download($protocol);


      //===================================
      // =========== 現在のフルURL 取得
      //===================================

      /*
      if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {

        $protocol = 'https://';
      } else {

        $protocol = 'http://';
      }

      // ./files の . を削除
      $new_create_csv = mb_substr($create_csv, 1);

      $protocol .= $_SERVER["HTTP_HOST"] . '/job_recruit/csv' . $new_create_csv;
   

      // ファイルタイプを指定
      header('Content-Type: application/octet-stream');
      // ファイルサイズを取得し、ダウンロードの進捗を表示
      //       header('Content-Length: '.filesize($protocol));
      // ファイルのダウンロード、リネームを指示


      header('Content-Disposition: attachment; filename="' . $file_name . '"');

      ob_clean();  //追記
      flush();     //追記

      // ファイルを読み込みダウンロードを実行
      readfile($protocol);
      //   exit;
      */
    } catch (PDOException $e) {

      $e->getMessage();
    } finally {

      $pdo = null;
    } //========================= END try 



    try {


      //=====================================================
      //=====================================================
      //================== 銀行用　CSV 作成
      //=====================================================
      //=====================================================

      //=== CSV ファイル名  => アルバイト管理_2021-10-12_121212.csv
      $file_name_02 = "Mcsv_GinkouJ_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
        "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
        $get_now_arr['minute'] . $get_now_arr['second'] . ".csv";

      // CSV ファイル名 2 => file_name_02

      //=== ファイル名の文字コード変換  
      $file_name_02 = mb_convert_encoding($file_name_02, "SJIS", "UTF-8");

      //=== ファイルパス
      $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];

      // 新ファイル
      $create_csv_02 = $dirpath . "/" . $file_name_02;

      // PDO オブジェクト作成
      $pdo = new PDO($dsn, $user, $password);

      // SQL
      $stmt = $pdo->prepare("SELECT * from User_Info_Table WHERE data_Flg = '0'");

      // SQL 実行 
      $res = $stmt->execute();

      if (touch($create_csv_02)) {
        $file_02 = new SplFileObject($create_csv_02, "w");

        // 出力する CSV に　ヘッダーを書きこむ
        $file_02->fputcsv($export_header_02);
        // ============= END if

        //====================== 変数宣言

        $Siharai_M_Number; // 支払元登録No
        $bank_siten_name_kana = "ｼﾃﾝフリガナ";   // 支店名　カナ
        $bank_kamoku = ""; //   預金種目

        $teigaku_F = "0"; // 定額振込

        $koumoku_Last; // デフォルト 0

        //======= 日付選択　データ　取得
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


          // 社員氏名
          $row['user_name'] = mb_convert_encoding($row['user_name'], "SJIS-win", "auto");

          // 社員番号　（６桁）
          $row['employee_id'] = sprintf('%06d', $row['employee_id']);
          $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");


          // 金融機関コード 	bank_code　（４桁）
          $row['bank_code'] = sprintf('%04d', $row['bank_code']);
          $row['bank_code'] = mb_convert_encoding($row['bank_code'], "SJIS-win", "auto");

          // 金融機関名 bank_name
          $row['bank_name'] = mb_convert_encoding($row['bank_name'], "SJIS-win", "auto");

          // 金融機関フリガナ bank_name_kana
          $row['bank_name_kana'] = mb_convert_encoding($row['bank_name_kana'], "SJIS-win", "auto");

          // 支店コード bank_siten_code
          $row['bank_siten_code'] = sprintf('%03d', $row['bank_siten_code']);
          $row['bank_siten_code'] = mb_convert_encoding($row['bank_siten_code'], "SJIS-win", "auto");

          // 支店名 bank_siten_name 
          $row['bank_siten_name'] = mb_convert_encoding($row['bank_siten_name'], "SJIS-win", "auto");


          // 預金種目  0:普通 , 1:当座 bank_kamoku
          $row['bank_kamoku'] = mb_convert_encoding($row['bank_kamoku'], "SJIS-win", "auto");
          if (strcmp($row['bank_kamoku'], "0") == 0) {
            $bank_kamoku = "普通";
            $bank_kamoku = mb_convert_encoding($bank_kamoku, "SJIS-win", "auto");
          } else {
            $bank_kamoku = "当座";
            $bank_kamoku = mb_convert_encoding($bank_kamoku, "SJIS-win", "auto");
          }

          // 口座番号 kouzzz_number
          $row['kouzzz_number'] = mb_convert_encoding($row['kouzzz_number'], "SJIS-win", "auto");

          // 支払元登録No
          $Siharai_M_Number = "1";

          // 支店フリガナ ※カラムなし $bank_siten_name_kana
          $bank_siten_name_kana = "ｼﾃﾝﾌﾘｶﾞﾅ";
          $bank_siten_name_kana = mb_convert_encoding($bank_siten_name_kana, "SJIS-win", "auto");

          // 振込手数料 空欄
          //=== 空文字
          $row['kara_01'] = ""; // 空 項目 ,   // 振込手数料

          $teigaku_F = "";
          $teigaku_F = mb_convert_encoding($teigaku_F, "SJIS-win", "auto"); // 定額振込

          //===  10 空欄 ===
          $row['kara_02'] = ""; // 空 項目 ,   //   振込先 1　金額,
          $row['kara_03'] = ""; // 空 項目 ,   // 振込先2　金融機関コード,
          $row['kara_04'] = ""; // 空 項目 ,   // 振込先2　金融機関名,
          $row['kara_05'] = ""; // 空 項目 ,   // 振込先2　金融機関フリガナ,
          $row['kara_06'] = ""; // 空 項目 ,   // 振込先2　支店コード,
          $row['kara_07'] = ""; // 空 項目 ,   // 振込先2  支店名,
          $row['kara_08'] = ""; // 空 項目 ,  振込先2   支店フリガナ
          $row['kara_09'] = ""; // 空 項目 ,   // 振込先2　預金種目,
          $row['kara_10'] = ""; // 空 項目 ,   // 振込先2　口座番号,
          $row['kara_11'] = ""; // 空 項目 ,   // 振込先2　振込手数料,
          $row['kara_12'] = ""; // 空 項目 ,   // 振込先2　定額振込,

          // 振込先2, ■■■必須■■■ デフォルト 0  項目ラスト
          $koumoku_Last = "";

          $return_bank_all[] = array(
            'employee_id' => $row['employee_id'], // 項目 ： 社員番号
            'user_name' => $row['user_name'], // 項目 ： 社員氏名

            // 支払元登録No  デフォルト: 1
            'Siharai_M_Number' => $Siharai_M_Number,

            'bank_code' => $row['bank_code'], // 金融機関コード
            'bank_name' => $row['bank_name'], // 金融機関名
            'bank_name_kana' => $row['bank_name_kana'], // 金融機関フリガナ

            'bank_siten_code' => $row['bank_siten_code'], // 支店コード
            'bank_siten_name' => $row['bank_siten_name'], // 支店名
            'bank_siten_name_kana' => $bank_siten_name_kana, // 支店フリガナ

            'bank_kamoku' =>  $bank_kamoku, // 預金種目
            'kouzzz_number' => $row['kouzzz_number'], // 口座番号

            'kara_01' => $row['kara_01'], // 振込手数料

            'teigaku_F' => $teigaku_F, // 定額振込

            'kara_02' => $row['kara_02'], // 空　カラム , 2
            'kara_03' => $row['kara_03'], // 空　カラム , 3
            'kara_04' => $row['kara_04'], // 空　カラム , 4
            'kara_05' => $row['kara_05'], // 空　カラム , 5
            'kara_06' => $row['kara_06'], // 空　カラム , 6
            'kara_07' => $row['kara_07'], // 空　カラム , 7
            'kara_08' => $row['kara_08'], // 空　カラム , 8
            'kara_09' => $row['kara_09'], // 空　カラム , 9
            'kara_10' => $row['kara_10'], // 空　カラム , 10
            'kara_11' => $row['kara_11'], // 空　カラム , 10
            'kara_12' => $row['kara_12'], // 空　カラム , 10

            'koumoku_Last' => $koumoku_Last, // 項目ラスト 

          );
        }


        // ファイルへ挿入 （.csv）
        foreach ($return_bank_all as $item) {
          $file_02->fputcsv($item);
        }
      } //======= END if 





      // ********* フラグ処理
      $csv_file_ok_FLG = "0"; // デフォルト

      $excel_file_FLG = "1"; // Excel　作成完了

      //==================================================================
      //================= アップデート 処理 データ処理フラグを データ作成済みにする
      //==================================================================
      $stmt = $pdo->prepare("UPDATE User_Info_Table SET data_Flg = '1' 
                        WHERE data_Flg = ? ");

      $stmt->bindValue(
        1,
        '0',
        PDO::PARAM_STR
      );

      // SQL 実行 
      $res = $stmt->execute();

      exit;
    } catch (PDOException $e) {

      $e->getMessage();
    } finally {

      $pdo = null;

      // ページリロード
      header("Location: " . $_SERVER['PHP_SELF']);

      exit;
    } //========================= END try 



    //==============================================================================
    //==============================================================================
    //=======  未出力ファイル　チェック (デフォルト)    Excel ダウンロード処理 
    //===============================================================================
    //==============================================================================

  } else if ($export_output === "excel" && strcmp($check_box_FLG, "1") == 0) {


    // Excel出力 ファイル名  => 未出力_アルバイト管理_2021-10-12_121212.xlsx
    $file_name_excel = "M_baito_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
      "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
      $get_now_arr['minute'] . $get_now_arr['second'] . ".xlsx";

    // ファイル名の文字コード変換  
    //  $file_name_excel = mb_convert_encoding($file_name_excel, "SJIS", "UTF-8");


    // Excel出力 ファイル名  => アルバイト管理_2021-10-12_121212.xlsx
    $file_name_excel_bank = "M_ginkou_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
      "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
      $get_now_arr['minute'] . $get_now_arr['second'] . ".xlsx";

    // ファイル名の文字コード変換  
    //  $file_name_excel_bank = mb_convert_encoding($file_name_excel, "SJIS", "UTF-8");


    //================================  
    //================== 接続情報
    //================================  
    $dsn = '';
    $user = '';
    $password = '';


    $retunr_output_excel = []; // Excel

    try {

      // PDO オブジェクト作成
      $pdo = new PDO($dsn, $user, $password);

      $stmt = $pdo->prepare("SELECT * from User_Info_Table WHERE data_Flg = '0'");

      // SQL 実行
      $res = $stmt->execute();

      // ファルダがなかったら、フォルダを作成  形式 files_20211025
      $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];
      if (!file_exists($dirpath)) {
        mkdir($dirpath, 0777);

        //chmod関数で「hoge」ディレクトリのパーミッションを「0777」にする
        //   chmod($dirpath, 0777);
      }

      //====== ファイルの保存
      // ファイル生成場所
      $create_excel = $dirpath . "/" . $file_name_excel;


      //====================================================== 
      //=========================== Excel 用　変数宣言
      //====================================================== 
      $sex = ""; // 性別
      $wareki = ""; // 和暦の年
      $wareki_r = ""; // 和暦 年月日 出力用
      $month_r = "";
      $day_r = ""; // 日付　日にち

      $Seireki_r = ""; // 西暦出力用

      $hokensya_K = ""; // 保険者種別  男子、女子
      $syasin_K = ""; // 社員区分
      $syozoku_b = ""; // 所属部門


      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        //=== 項目 1 employee_id 社員番号  000101 　６桁に　?パディング
        $row['employee_id'] = sprintf('%06d', $row['employee_id']);
        //       $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");

        //=== 項目 2
        $row['user_name'] = mb_convert_kana($row['user_name'], "h", "UTF-8"); // 社員氏名

        //=== 項目 3 ふりがな　を　カナ　へ変換
        $row['furi_name'] = mb_convert_kana($row['furi_name'], "h", "UTF-8"); // フリガナ
        //      $row['furi_name'] = mb_convert_encoding($row['furi_name'], "SJIS-win", "auto");

        // メールアドレス
        //      $row['email'] = mb_convert_encoding($row['email'], "SJIS-win", "auto");

        // 配偶者 spouse
        //      $row['spouse'] = mb_convert_encoding($row['spouse'], "SJIS-win", "auto");

        if (strcmp($row['spouse'], "0") == 0) {
          $row['spouse'] = "なし";
        } else {
          $row['spouse'] = "あり";
        }

        // 扶養人数 	dependents_num
        //      $row['dependents_num'] = mb_convert_encoding($row['dependents_num'], "SJIS-win", "auto");

        //=== 項目 4

        //=== 性別
        if (strcmp($row['sex'], "0")) {
          $row['sex'] = "男"; // 保険者種別
          $hokensya_K = ""; // 保険者種別

          //        $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
          //     $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
        } else {
          $row['sex'] = "女"; // 保険者種別
          $hokensya_K = ""; // 保険者種別

          //       $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
          //      $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
        }

        // === 項目 5

        //=== 生年月日　（西暦 => 和暦　変換）
        //        $row['birthday'] = mb_convert_encoding($row['birthday'], "SJIS-win", "auto");

        // 年
        $year = mb_substr($row['birthday'], 0, 4); // 西暦　取り出し 1900

        // 和暦変換用 function  Wareki_Parse
        $wareki = Wareki_Parse($year);

        // 月
        $month = mb_substr($row['birthday'], 5, 2);

        if (strpos($month, '-') !== false) {
          // - ハイフン が含まれている場合 , 0　パディング
          $month = str_replace("-", "", $month);
          $month_r = "0" . $month;
        } else {
          $month_r = $month;
        }


        // 日
        $day = mb_substr($row['birthday'], 7, 2);
        // 文字列の長さ取得  1,  10
        $day_num = mb_strlen($day);

        if ($day_num == 1) {
          $day_r = "0" . $day;
        } else if (
          strpos(
            $day,
            '-'
          ) !== false
        ) {
          $day = str_replace("-", "", $day);
          $day_r = $day;
        } else {
          $day_r = $day;
        }

        // 和暦
        $wareki_r = $wareki . $month_r . "月" . $day_r . "日";
        //      $wareki_r = mb_convert_encoding($wareki_r, "SJIS-win", "auto");

        //=== 項目6  西暦  
        $Seireki_r = $year . "年" . $month_r . "月" . $day_r . "日";
        //        $Seireki_r = mb_convert_encoding($Seireki_r, "SJIS-win", "auto");


        //===  郵便番号 zip
        //=== 郵便番号 zip  （-） をつける
        $row['zip'] = substr($row['zip'], 0, 3) . "-" . substr($row['zip'], 3);
        //      $row['zip'] = mb_convert_encoding($row['zip'], "SJIS-win", "auto");

        //=== 住所１ address_01
        //      $row['address_01'] = mb_convert_encoding($row['address_01'], "SJIS-win", "auto");

        //=== 電話番号 tel
        $row['tel'] = substr($row['tel'], 0, 3) . "-" . substr($row['tel'], 3, 4) . "-" . substr($row['tel'], 7);

        //       $row['creation_time'] = mb_convert_encoding($row['creation_time'], "SJIS-win", "auto");
        //      $row['data_Flg'] = mb_convert_encoding($row['data_Flg'], "SJIS-win", "auto");

        //=== 社員番号
        //       $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");


        //=== 社員区分
        $syasin_K = "アルバイト";
        //       $syasin_K = mb_convert_encoding($syasin_K, "SJIS-win", "auto");

        //=== 所属部門

        //============================= 20221_12_21 値変更
        //  $syozoku_b = "003";
        $syozoku_b = "アルバイト";

        //       $syozoku_b = mb_convert_encoding($syozoku_b, "SJIS-win", "auto");

        // ======================== フォームから取得以外の項目
        $row['koumoku_01'] = ""; // 項目7:入社区分  （空）
        $row['koumoku_02'] = ""; // 項目8:入社年月日　（空）
        $row['koumoku_03'] = ""; // 項目9:入社年月日（西暦）　（空）
        $row['koumoku_04'] = ""; // 項目10:勤続年数　（空）
        $row['koumoku_05'] = ""; // 項目11 勤続年数_月数　（空）

        $row['koumoku_06'] = ""; // 項目13:役職 　（空）
        $row['koumoku_07'] = ""; // 項目14:学歴: （空）
        $row['koumoku_08'] = ""; // 項目16:職種: （空）
        $row['koumoku_09'] = ""; // 項目19:住所２
        $row['koumoku_10'] = ""; // 項目20:住所（フリガナ）１

        $row['koumoku_11'] = ""; // 項目21:住所（フリガナ）２
        $row['koumoku_12'] = "非該当"; // 項目24:障害区分:非該当
        $row['koumoku_13'] = "非該当"; // 項目25:寡婦:非該当
        $row['koumoku_14'] = "非該当"; // 項目26:寡夫:非該当
        $row['koumoku_15'] = "非該当"; // 項目27:勤労学生:非該当

        $row['koumoku_16'] = "非該当"; // 項目28:災害者:非該当
        $row['koumoku_17'] = "非該当"; // 項目29:外国人:非該当
        $row['koumoku_18'] = ""; // 項目30:退職理由: （空）
        $row['koumoku_19'] = ""; // 項目31:退職年月日: （空）
        $row['koumoku_20'] = ""; // 項目32:退職年月日（西暦）: （空）

        $row['koumoku_21'] = "月給"; // 項目33:給与区分:月給
        $row['koumoku_22'] = "月額甲欄"; // 項目34:税表区分:月額甲欄
        $row['koumoku_23'] = "する"; // 項目35:年末調整対象:する
        $row['koumoku_24'] = "振込"; // 項目36:支払形態:振込
        $row['koumoku_25'] = "サンプル"; // 項目37:支給日パターン:サンプル

        //============================= 20221_12_21 値変更
        // $row['koumoku_26'] = "社員パターン"; // 項目38:給与明細書パターン:社員パターン
        $row['koumoku_26'] = ""; // 項目38:給与明細書パターン:社員パターン

        //============================= 20221_12_21 値変更
        //  $row['koumoku_27'] = "賞与サンプル"; // 項目39:賞与明細書パターン:賞与サンプル
        $row['koumoku_27'] = ""; // 項目39:賞与明細書パターン:賞与サンプル

        $row['koumoku_28'] = ""; // 項目40:健康保険対象: （空）
        $row['koumoku_29'] = ""; // 項目41:介護保険対象: （空）

        //============================= 20221_12_21 値変更
        //  $row['koumoku_30'] = "220"; // 項目42:健保月額（千円）:220
        $row['koumoku_30'] = ""; // 項目42:健保月額（千円）:220

        //============================= 20221_12_21 値変更
        //  $row['koumoku_31'] = "367"; // 項目43:健保証番号（協会）367
        $row['koumoku_31'] = ""; // 項目43:健保証番号（協会）367

        $row['koumoku_32'] = ""; // 項目44:健保証番号（組合）: （空）
        $row['koumoku_33'] = ""; // 項目45:健保・厚年　資格取得年月日: （空）
        $row['koumoku_34'] = ""; // 項目46:厚生年金対象: （空）
        $row['koumoku_35'] = "非該当"; // 項目47:70歳以上被用者:非該当

        // $hokensya_K  項目48:保険者種別:女子　△△△ 変数当てはめ △△△
        $row['koumoku_37'] = ""; // 項目49:厚年月額（千円）: （空）
        $row['koumoku_38'] = ""; // 項目50:基礎年金番号１: （空）
        $row['koumoku_39'] = ""; // 項目51:基礎年金番号２:対象
        $row['koumoku_40'] = "対象外"; // 項目52:短時間労働者（3/4未満）:対象外

        $row['koumoku_41'] = "対象"; // 項目53:労災保険対象:対象

        //============================= 20221_12_21 値変更
        //  $row['koumoku_42'] = "対象"; // 項目54:雇用保険対象:対象
        $row['koumoku_42'] = ""; // 項目54:雇用保険対象:対象

        //============================= 20221_12_21 値変更
        $row['koumoku_43'] = "常用"; // 項目55:労働保険用区分:常用
        //  $row['koumoku_43'] = "常用"; // 項目55:労働保険用区分:常用

        //============================= 20221_12_21 値変更
        //  $row['koumoku_44'] = ""; // 項目56:雇用保険被保険者番号１:5029
        $row['koumoku_44'] = "5029"; // 項目56:雇用保険被保険者番号１:5029

        //============================= 20221_12_21 値変更
        //  $row['koumoku_45'] = ""; // 項目57:雇用保険被保険者番号２:530842
        $row['koumoku_45'] = "530842"; // 項目57:雇用保険被保険者番号２:530842

        //============================= 20221_12_21 値変更
        //  $row['koumoku_46'] = ""; // 項目58:雇用保険被保険者番号３:4
        $row['koumoku_46'] = "4"; // 項目58:雇用保険被保険者番号３:4


        $row['koumoku_47'] = ""; // 項目59:労働保険　資格取得年月日: （空）
        // 項目60:配偶者:なし　　＝＞　$row['spouse']
        $row['koumoku_49'] = ""; // 項目61:配偶者氏名: （空）
        $row['koumoku_50'] = ""; // 項目62:配偶者フリガナ: （空）

        $row['koumoku_51'] = ""; // 項目63:配偶者性別: （空）
        $row['koumoku_52'] = ""; // 項目64:配偶者生年月日: （空）
        $row['koumoku_53'] = ""; // 項目65:配偶者生年月日（西暦）: （空）
        $row['koumoku_54'] = "非該当"; // 項目66:源泉控除対象配偶者:非該当
        $row['koumoku_55'] = "非該当"; // 項目67:配偶者老人:非該当

        $row['koumoku_56'] = "非該当"; // 項目68:配偶者老人:非該当
        $row['koumoku_57'] = "非同居"; // 項目69:配偶者老人:非同居
        $row['koumoku_58'] = "非該当"; // 項目70:配偶者老人:非該当
        //     $row['koumoku_59'] = ""; // 項目71:扶養控除対象人数:0



        //======== 文字ばけ対策
        /*
        $row['koumoku_01'] = mb_convert_encoding($row['koumoku_01'], "SJIS-win", "auto");
        $row['koumoku_02'] = mb_convert_encoding($row['koumoku_02'], "SJIS-win", "auto");
        $row['koumoku_03'] = mb_convert_encoding($row['koumoku_03'], "SJIS-win", "auto");
        $row['koumoku_04'] = mb_convert_encoding($row['koumoku_04'], "SJIS-win", "auto");
        $row['koumoku_05'] = mb_convert_encoding($row['koumoku_05'], "SJIS-win", "auto");
        $row['koumoku_06'] = mb_convert_encoding($row['koumoku_06'], "SJIS-win", "auto");
        $row['koumoku_07'] = mb_convert_encoding($row['koumoku_07'], "SJIS-win", "auto");
        $row['koumoku_08'] = mb_convert_encoding($row['koumoku_08'], "SJIS-win", "auto");
        $row['koumoku_09'] = mb_convert_encoding($row['koumoku_09'], "SJIS-win", "auto");
        $row['koumoku_10'] = mb_convert_encoding($row['koumoku_10'], "SJIS-win", "auto");

        $row['koumoku_11'] = mb_convert_encoding($row['koumoku_11'], "SJIS-win", "auto");
        $row['koumoku_12'] = mb_convert_encoding($row['koumoku_12'], "SJIS-win", "auto");
        $row['koumoku_13'] = mb_convert_encoding($row['koumoku_13'], "SJIS-win", "auto");
        $row['koumoku_14'] = mb_convert_encoding($row['koumoku_14'], "SJIS-win", "auto");
        $row['koumoku_15'] = mb_convert_encoding($row['koumoku_15'], "SJIS-win", "auto");
        $row['koumoku_16'] = mb_convert_encoding($row['koumoku_16'], "SJIS-win", "auto");
        $row['koumoku_17'] = mb_convert_encoding($row['koumoku_17'], "SJIS-win", "auto");
        $row['koumoku_18'] = mb_convert_encoding($row['koumoku_18'], "SJIS-win", "auto");
        $row['koumoku_19'] = mb_convert_encoding($row['koumoku_19'], "SJIS-win", "auto");
        $row['koumoku_20'] = mb_convert_encoding($row['koumoku_20'], "SJIS-win", "auto");

        $row['koumoku_21'] = mb_convert_encoding($row['koumoku_21'], "SJIS-win", "auto");
        $row['koumoku_22'] = mb_convert_encoding($row['koumoku_22'], "SJIS-win", "auto");
        $row['koumoku_23'] = mb_convert_encoding($row['koumoku_23'], "SJIS-win", "auto");
        $row['koumoku_24'] = mb_convert_encoding($row['koumoku_24'], "SJIS-win", "auto");
        $row['koumoku_25'] = mb_convert_encoding($row['koumoku_25'], "SJIS-win", "auto");
        $row['koumoku_26'] = mb_convert_encoding($row['koumoku_26'], "SJIS-win", "auto");
        $row['koumoku_27'] = mb_convert_encoding($row['koumoku_27'], "SJIS-win", "auto");
        $row['koumoku_28'] = mb_convert_encoding($row['koumoku_28'], "SJIS-win", "auto");
        $row['koumoku_29'] = mb_convert_encoding($row['koumoku_29'], "SJIS-win", "auto");
        $row['koumoku_30'] = mb_convert_encoding($row['koumoku_30'], "SJIS-win", "auto");

        $row['koumoku_31'] = mb_convert_encoding($row['koumoku_31'], "SJIS-win", "auto");
        $row['koumoku_32'] = mb_convert_encoding($row['koumoku_32'], "SJIS-win", "auto");
        $row['koumoku_33'] = mb_convert_encoding($row['koumoku_33'], "SJIS-win", "auto");
        $row['koumoku_34'] = mb_convert_encoding($row['koumoku_34'], "SJIS-win", "auto");
        $row['koumoku_35'] = mb_convert_encoding($row['koumoku_35'], "SJIS-win", "auto");
        //        $row['koumoku_36'] = mb_convert_encoding($row['koumoku_36'], "SJIS-win", "auto");
        $row['koumoku_37'] = mb_convert_encoding($row['koumoku_37'], "SJIS-win", "auto");
        $row['koumoku_38'] = mb_convert_encoding($row['koumoku_38'], "SJIS-win", "auto");
        $row['koumoku_39'] = mb_convert_encoding($row['koumoku_39'], "SJIS-win", "auto");
        $row['koumoku_40'] = mb_convert_encoding($row['koumoku_40'], "SJIS-win", "auto");

        $row['koumoku_41'] = mb_convert_encoding($row['koumoku_41'], "SJIS-win", "auto");
        $row['koumoku_42'] = mb_convert_encoding($row['koumoku_42'], "SJIS-win", "auto");
        $row['koumoku_43'] = mb_convert_encoding($row['koumoku_43'], "SJIS-win", "auto");
        $row['koumoku_44'] = mb_convert_encoding($row['koumoku_44'], "SJIS-win", "auto");
        $row['koumoku_45'] = mb_convert_encoding($row['koumoku_45'], "SJIS-win", "auto");
        $row['koumoku_46'] = mb_convert_encoding($row['koumoku_46'], "SJIS-win", "auto");
        $row['koumoku_47'] = mb_convert_encoding($row['koumoku_47'], "SJIS-win", "auto");
        //      $row['koumoku_48'] = mb_convert_encoding($row['koumoku_48'], "SJIS-win", "auto");
        $row['koumoku_49'] = mb_convert_encoding($row['koumoku_49'], "SJIS-win", "auto");
        $row['koumoku_50'] = mb_convert_encoding($row['koumoku_50'], "SJIS-win", "auto");

        $row['koumoku_51'] = mb_convert_encoding($row['koumoku_51'], "SJIS-win", "auto");
        $row['koumoku_52'] = mb_convert_encoding($row['koumoku_52'], "SJIS-win", "auto");
        $row['koumoku_53'] = mb_convert_encoding($row['koumoku_53'], "SJIS-win", "auto");
        $row['koumoku_54'] = mb_convert_encoding($row['koumoku_54'], "SJIS-win", "auto");
        $row['koumoku_55'] = mb_convert_encoding($row['koumoku_55'], "SJIS-win", "auto");

        $row['koumoku_56'] = mb_convert_encoding($row['koumoku_56'], "SJIS-win", "auto");
        $row['koumoku_57'] = mb_convert_encoding($row['koumoku_57'], "SJIS-win", "auto");
        $row['koumoku_58'] = mb_convert_encoding($row['koumoku_58'], "SJIS-win", "auto");
        */

        //======== 165 の　空白行
        $row['last_kara_01'] = "";
        $row['last_kara_02'] = "";
        $row['last_kara_03'] = "";
        $row['last_kara_04'] = "";
        $row['last_kara_05'] = "";
        $row['last_kara_06'] = "";
        $row['last_kara_07'] = "";
        $row['last_kara_08'] = "";
        $row['last_kara_09'] = "";
        $row['last_kara_10'] = "";

        $row['last_kara_11'] = "";
        $row['last_kara_12'] = "";
        $row['last_kara_13'] = "";
        $row['last_kara_14'] = "";
        $row['last_kara_15'] = "";
        $row['last_kara_16'] = "";
        $row['last_kara_17'] = "";
        $row['last_kara_18'] = "";
        $row['last_kara_19'] = "";
        $row['last_kara_20'] = "";

        $row['last_kara_21'] = "";
        $row['last_kara_22'] = "";
        $row['last_kara_23'] = "";
        $row['last_kara_24'] = "";
        $row['last_kara_25'] = "";
        $row['last_kara_26'] = "";
        $row['last_kara_27'] = "";
        $row['last_kara_28'] = "";
        $row['last_kara_29'] = "";
        $row['last_kara_30'] = "";

        $row['last_kara_31'] = "";
        $row['last_kara_32'] = "";
        $row['last_kara_33'] = "";
        $row['last_kara_34'] = "";
        $row['last_kara_35'] = "";
        $row['last_kara_36'] = "";
        $row['last_kara_37'] = "";
        $row['last_kara_38'] = "";
        $row['last_kara_39'] = "";
        $row['last_kara_40'] = "";

        $row['last_kara_41'] = "";
        $row['last_kara_42'] = "";
        $row['last_kara_43'] = "";
        $row['last_kara_44'] = "";
        $row['last_kara_45'] = "";
        $row['last_kara_46'] = "";
        $row['last_kara_47'] = "";
        $row['last_kara_48'] = "";
        $row['last_kara_49'] = "";
        $row['last_kara_50'] = "";
        //============================ 50
        $row['last_kara_51'] = "";
        $row['last_kara_52'] = "";
        $row['last_kara_53'] = "";
        $row['last_kara_54'] = "";
        $row['last_kara_55'] = "";
        $row['last_kara_56'] = "";
        $row['last_kara_57'] = "";
        $row['last_kara_58'] = "";
        $row['last_kara_59'] = "";
        $row['last_kara_60'] = "";

        $row['last_kara_61'] = "";
        $row['last_kara_62'] = "";
        $row['last_kara_63'] = "";
        $row['last_kara_64'] = "";
        $row['last_kara_65'] = "";
        $row['last_kara_66'] = "";
        $row['last_kara_67'] = "";
        $row['last_kara_68'] = "";
        $row['last_kara_69'] = "";
        $row['last_kara_70'] = "";

        $row['last_kara_71'] = "";
        $row['last_kara_72'] = "";
        $row['last_kara_73'] = "";
        $row['last_kara_74'] = "";
        $row['last_kara_75'] = "";
        $row['last_kara_76'] = "";
        $row['last_kara_77'] = "";
        $row['last_kara_78'] = "";
        $row['last_kara_79'] = "";
        $row['last_kara_80'] = "";

        $row['last_kara_81'] = "";
        $row['last_kara_82'] = "";
        $row['last_kara_83'] = "";
        $row['last_kara_84'] = "";
        $row['last_kara_85'] = "";
        $row['last_kara_86'] = "";
        $row['last_kara_87'] = "";
        $row['last_kara_88'] = "";
        $row['last_kara_89'] = "";
        $row['last_kara_90'] = "";

        $row['last_kara_91'] = "";
        $row['last_kara_92'] = "";
        $row['last_kara_93'] = "";
        $row['last_kara_94'] = "";
        $row['last_kara_95'] = "";
        $row['last_kara_96'] = "";
        $row['last_kara_97'] = "";
        $row['last_kara_98'] = "";
        $row['last_kara_99'] = "";
        $row['last_kara_100'] = "";
        //=========================== 100
        $row['last_kara_101'] = "";
        $row['last_kara_102'] = "";
        $row['last_kara_103'] = "";
        $row['last_kara_104'] = "";
        $row['last_kara_105'] = "";
        $row['last_kara_106'] = "";
        $row['last_kara_107'] = "";
        $row['last_kara_108'] = "";
        $row['last_kara_109'] = "";
        $row['last_kara_110'] = "";

        $row['last_kara_111'] = "";
        $row['last_kara_112'] = "";
        $row['last_kara_113'] = "";
        $row['last_kara_114'] = "";
        $row['last_kara_115'] = "";
        $row['last_kara_116'] = "";
        $row['last_kara_117'] = "";
        $row['last_kara_118'] = "";
        $row['last_kara_119'] = "";
        $row['last_kara_120'] = "";

        $row['last_kara_121'] = "";
        $row['last_kara_122'] = "";
        $row['last_kara_123'] = "";
        $row['last_kara_124'] = "";
        $row['last_kara_125'] = "";
        $row['last_kara_126'] = "";
        $row['last_kara_127'] = "";
        $row['last_kara_128'] = "";
        $row['last_kara_129'] = "";
        $row['last_kara_130'] = "";

        $row['last_kara_131'] = "";
        $row['last_kara_132'] = "";
        $row['last_kara_133'] = "";
        $row['last_kara_134'] = "";
        $row['last_kara_135'] = "";
        $row['last_kara_136'] = "";
        $row['last_kara_137'] = "";
        $row['last_kara_138'] = "";
        $row['last_kara_139'] = "";
        $row['last_kara_140'] = "";

        $row['last_kara_141'] = "";
        $row['last_kara_142'] = "";
        $row['last_kara_143'] = "";
        $row['last_kara_144'] = "";
        $row['last_kara_145'] = "";
        $row['last_kara_146'] = "";
        $row['last_kara_147'] = "";
        $row['last_kara_148'] = "";
        $row['last_kara_149'] = "";
        $row['last_kara_150'] = "";
        //============================= 150
        $row['last_kara_151'] = "";
        $row['last_kara_152'] = "";
        $row['last_kara_153'] = "";
        $row['last_kara_154'] = "";
        $row['last_kara_155'] = "";
        $row['last_kara_156'] = "";
        $row['last_kara_157'] = "";
        $row['last_kara_158'] = "";
        $row['last_kara_159'] = "";
        $row['last_kara_160'] = "";

        $row['last_kara_161'] = "";
        $row['last_kara_162'] = "";
        $row['last_kara_163'] = "";
        $row['last_kara_164'] = "";

        // ==============================================================
        // ====================================== 配列　挿入
        // ==============================================================
        $retunr_excel[] = array(

          'employee_id' => $row['employee_id'], // 項目 1 employee_id 社員番号
          'user_name' => $row['user_name'],   // 項目 2 社員氏名
          'furi_name' => $row['furi_name'],  // 項目 3  フリガナ
          'sex' => $row['sex'], // 項目 4  性別
          'wareki_r' => $wareki_r,  // 項目 5 生年月日（和暦）

          'Seireki_r' => $Seireki_r, // 項目 6 生年月日（西暦）
          'koumoku_01' => $row['koumoku_01'], // 項目7:入社区分
          'koumoku_02' => $row['koumoku_02'], // 項目8:入社年月日
          'koumoku_03' => $row['koumoku_03'], // 項目9:入社年月日（西暦）
          'koumoku_04' => $row['koumoku_04'], // 項目10:勤続年数

          'koumoku_05' => $row['koumoku_05'], // 項目11 勤続年数_月数
          'syasin_K' => $syasin_K, // 項目 12 社員区分
          'koumoku_06' => $row['koumoku_06'], // 項目13:役職 　（空）
          'koumoku_07' => $row['koumoku_07'], // 項目14:学歴: （空）

          'syozoku_b' => $syozoku_b, //  項目15:所属部門  

          'koumoku_08' => $row['koumoku_08'], // 項目16 : 職種
          'zip' => $row['zip'],   // 項目17　郵便番号
          'address_01' => $row['address_01'],  // 項目18 住所　１
          'koumoku_09' => $row['koumoku_09'],  // 項目19:住所２: （空）
          'koumoku_10' => $row['koumoku_10'],  // 項目20:住所（フリガナ）１: （空）

          'koumoku_11' => $row['koumoku_11'],  // 項目21:住所（フリガナ）２: （空）
          'tel' => $row['tel'],  // 項目22:電話番号:090-1111-2222
          'email' => $row['email'], // 項目23:メールアドレス
          'koumoku_12' => $row['koumoku_12'], // 項目24:障害区分:非該当
          'koumoku_13' => $row['koumoku_13'], // 項目25:寡婦:非該当

          'koumoku_14' => $row['koumoku_14'], // 項目26:寡夫:非該当
          'koumoku_15' => $row['koumoku_15'], // 項目27:勤労学生:非該当
          'koumoku_16' => $row['koumoku_16'], // 項目28:災害者:非該当
          'koumoku_17' => $row['koumoku_17'], // 項目29:外国人:非該当
          'koumoku_18' => $row['koumoku_18'], // 項目30:退職理由: （空）
          // 30 ?
          'koumoku_19' => $row['koumoku_19'], // 項目31:退職年月日: （空）
          'koumoku_20' => $row['koumoku_20'], // 項目32:退職年月日（西暦）: （空）
          'koumoku_21' => $row['koumoku_21'], // 項目33:給与区分:月給
          'koumoku_22' => $row['koumoku_22'], // 項目34:税表区分:月額甲欄
          'koumoku_23' => $row['koumoku_23'], // 項目35:年末調整対象:する
          // 35
          'koumoku_24' => $row['koumoku_24'], // 項目36:支払形態:振込
          'koumoku_25' => $row['koumoku_25'], // 項目37:支給日パターン:サンプル
          'koumoku_26' => $row['koumoku_26'], // 項目38:給与明細書パターン:社員パターン
          'koumoku_27' => $row['koumoku_27'], // 項目39:賞与明細書パターン:賞与サンプル
          'koumoku_28' => $row['koumoku_28'], // 項目40:健康保険対象: （空）
          // 40
          'koumoku_29' => $row['koumoku_29'], // 項目41:介護保険対象: （空）
          'koumoku_30' => $row['koumoku_30'], // 項目42:健保月額（千円）:220
          'koumoku_31' => $row['koumoku_31'], // 項目43:健保証番号（協会）:367
          'koumoku_32' => $row['koumoku_32'], // 項目44:健保証番号（組合）: （空）
          'koumoku_33' => $row['koumoku_33'], // 項目45:健保・厚年　資格取得年月日: （空）
          // 45
          'koumoku_34' => $row['koumoku_34'], // 項目46:厚生年金対象: （空）
          'koumoku_35' => $row['koumoku_35'], // 項目47:70歳以上被用者:非該当

          // $hokensya_K　当てはめ
          'hokensya_K' => $hokensya_K, // 項目48:保険者種別:女子  $hokensya_K

          'koumoku_37' => $row['koumoku_36'], // 項目49:厚年月額（千円）: （空）
          'koumoku_38' => $row['koumoku_37'], // 項目50:基礎年金番号１: （空）
          // 50
          'koumoku_39' => $row['koumoku_39'], // 項目51:基礎年金番号２:対象
          'koumoku_40' => $row['koumoku_40'], // 項目52:短時間労働者（3/4未満）:対象外
          'koumoku_41' => $row['koumoku_41'], // 項目53:労災保険対象:対象
          'koumoku_42' => $row['koumoku_42'], // 項目54:雇用保険対象:対象
          'koumoku_43' => $row['koumoku_43'], // 項目55:労働保険用区分:常用
          // 55
          'koumoku_44' => $row['koumoku_44'], // 項目56:雇用保険被保険者番号１:5029
          'koumoku_45' => $row['koumoku_45'], // 項目57:雇用保険被保険者番号２:530842
          'koumoku_46' => $row['koumoku_46'], // 項目58:雇用保険被保険者番号３:4
          'koumoku_47' => $row['koumoku_47'], // 項目59:労働保険　資格取得年月日: （空）
          'spouse' => $row['spouse'], // 項目60:配偶者:　フォームから値取得
          // 60
          'koumoku_49' => $row['koumoku_49'], // 項目61:配偶者氏名: （空）
          'koumoku_50' => $row['koumoku_50'], // 項目62:配偶者フリガナ: （空）
          'koumoku_51' => $row['koumoku_51'], // 項目63:配偶者性別: （空）
          'koumoku_52' => $row['koumoku_52'], // 項目64:配偶者生年月日: （空）
          'koumoku_53' => $row['koumoku_53'], // 項目65:配偶者生年月日（西暦）: （空）
          // 65
          'koumoku_54' => $row['koumoku_54'], // 項目66:源泉控除対象配偶者:非該当
          'koumoku_55' => $row['koumoku_55'], // 項目67:配偶者老人:非該当
          'koumoku_56' => $row['koumoku_56'], // 項目68:配偶者障害者:非該当
          'koumoku_57' => $row['koumoku_57'], // 項目69:配偶者同居:非同居
          'koumoku_58' => $row['koumoku_58'], // 項目70:配偶者非居住者:非該当

          // 項目71:扶養控除対象人数:  フォームから取得
          'dependents_num' => $row['dependents_num'],

          // 165
          'last_kara_00' => $row['last_kara_0'],
          'last_kara_01' => $row['last_kara_1'],
          'last_kara_02' => $row['last_kara_2'],
          'last_kara_03' => $row['last_kara_3'],
          'last_kara_04' => $row['last_kara_4'],
          'last_kara_05' => $row['last_kara_5'],
          'last_kara_06' => $row['last_kara_6'],
          'last_kara_07' => $row['last_kara_7'],
          'last_kara_08' => $row['last_kara_8'],
          'last_kara_09' => $row['last_kara_9'],
          'last_kara_10' => $row['last_kara_10'],
          'last_kara_11' => $row['last_kara_11'],
          'last_kara_12' => $row['last_kara_12'],
          'last_kara_13' => $row['last_kara_13'],
          'last_kara_14' => $row['last_kara_14'],
          'last_kara_15' => $row['last_kara_15'],
          'last_kara_16' => $row['last_kara_16'],
          'last_kara_17' => $row['last_kara_17'],
          'last_kara_18' => $row['last_kara_18'],
          'last_kara_19' => $row['last_kara_19'],
          'last_kara_20' => $row['last_kara_20'],
          'last_kara_21' => $row['last_kara_21'],
          'last_kara_22' => $row['last_kara_22'],
          'last_kara_23' => $row['last_kara_23'],
          'last_kara_24' => $row['last_kara_24'],
          'last_kara_25' => $row['last_kara_25'],
          'last_kara_26' => $row['last_kara_26'],
          'last_kara_27' => $row['last_kara_27'],
          'last_kara_28' => $row['last_kara_28'],
          'last_kara_29' => $row['last_kara_29'],
          'last_kara_30' => $row['last_kara_30'],
          'last_kara_31' => $row['last_kara_31'],
          'last_kara_32' => $row['last_kara_32'],
          'last_kara_33' => $row['last_kara_33'],
          'last_kara_34' => $row['last_kara_34'],
          'last_kara_35' => $row['last_kara_35'],
          'last_kara_36' => $row['last_kara_36'],
          'last_kara_37' => $row['last_kara_37'],
          'last_kara_38' => $row['last_kara_38'],
          'last_kara_39' => $row['last_kara_39'],
          'last_kara_40' => $row['last_kara_40'],
          'last_kara_41' => $row['last_kara_41'],
          'last_kara_42' => $row['last_kara_42'],
          'last_kara_43' => $row['last_kara_43'],
          'last_kara_44' => $row['last_kara_44'],
          'last_kara_45' => $row['last_kara_45'],
          'last_kara_46' => $row['last_kara_46'],
          'last_kara_47' => $row['last_kara_47'],
          'last_kara_48' => $row['last_kara_48'],
          'last_kara_49' => $row['last_kara_49'],
          'last_kara_50' => $row['last_kara_50'],
          'last_kara_51' => $row['last_kara_51'],
          'last_kara_52' => $row['last_kara_52'],
          'last_kara_53' => $row['last_kara_53'],
          'last_kara_54' => $row['last_kara_54'],
          'last_kara_55' => $row['last_kara_55'],
          'last_kara_56' => $row['last_kara_56'],
          'last_kara_57' => $row['last_kara_57'],
          'last_kara_58' => $row['last_kara_58'],
          'last_kara_59' => $row['last_kara_59'],
          'last_kara_60' => $row['last_kara_60'],
          'last_kara_61' => $row['last_kara_61'],
          'last_kara_62' => $row['last_kara_62'],
          'last_kara_63' => $row['last_kara_63'],
          'last_kara_64' => $row['last_kara_64'],
          'last_kara_65' => $row['last_kara_65'],
          'last_kara_66' => $row['last_kara_66'],
          'last_kara_67' => $row['last_kara_67'],
          'last_kara_68' => $row['last_kara_68'],
          'last_kara_69' => $row['last_kara_69'],
          'last_kara_70' => $row['last_kara_70'],
          'last_kara_71' => $row['last_kara_71'],
          'last_kara_72' => $row['last_kara_72'],
          'last_kara_73' => $row['last_kara_73'],
          'last_kara_74' => $row['last_kara_74'],
          'last_kara_75' => $row['last_kara_75'],
          'last_kara_76' => $row['last_kara_76'],
          'last_kara_77' => $row['last_kara_77'],
          'last_kara_78' => $row['last_kara_78'],
          'last_kara_79' => $row['last_kara_79'],
          'last_kara_80' => $row['last_kara_80'],
          'last_kara_81' => $row['last_kara_81'],
          'last_kara_82' => $row['last_kara_82'],
          'last_kara_83' => $row['last_kara_83'],
          'last_kara_84' => $row['last_kara_84'],
          'last_kara_85' => $row['last_kara_85'],
          'last_kara_86' => $row['last_kara_86'],
          'last_kara_87' => $row['last_kara_87'],
          'last_kara_88' => $row['last_kara_88'],
          'last_kara_89' => $row['last_kara_89'],
          'last_kara_90' => $row['last_kara_90'],
          'last_kara_91' => $row['last_kara_91'],
          'last_kara_92' => $row['last_kara_92'],
          'last_kara_93' => $row['last_kara_93'],
          'last_kara_94' => $row['last_kara_94'],
          'last_kara_95' => $row['last_kara_95'],
          'last_kara_96' => $row['last_kara_96'],
          'last_kara_97' => $row['last_kara_97'],
          'last_kara_98' => $row['last_kara_98'],
          'last_kara_99' => $row['last_kara_99'],
          'last_kara_100' => $row['last_kara_100'],
          'last_kara_101' => $row['last_kara_101'],
          'last_kara_102' => $row['last_kara_102'],
          'last_kara_103' => $row['last_kara_103'],
          'last_kara_104' => $row['last_kara_104'],
          'last_kara_105' => $row['last_kara_105'],
          'last_kara_106' => $row['last_kara_106'],
          'last_kara_107' => $row['last_kara_107'],
          'last_kara_108' => $row['last_kara_108'],
          'last_kara_109' => $row['last_kara_109'],
          'last_kara_110' => $row['last_kara_110'],
          'last_kara_111' => $row['last_kara_111'],
          'last_kara_112' => $row['last_kara_112'],
          'last_kara_113' => $row['last_kara_113'],
          'last_kara_114' => $row['last_kara_114'],
          'last_kara_115' => $row['last_kara_115'],
          'last_kara_116' => $row['last_kara_116'],
          'last_kara_117' => $row['last_kara_117'],
          'last_kara_118' => $row['last_kara_118'],
          'last_kara_119' => $row['last_kara_119'],
          'last_kara_120' => $row['last_kara_120'],
          'last_kara_121' => $row['last_kara_121'],
          'last_kara_122' => $row['last_kara_122'],
          'last_kara_123' => $row['last_kara_123'],
          'last_kara_124' => $row['last_kara_124'],
          'last_kara_125' => $row['last_kara_125'],
          'last_kara_126' => $row['last_kara_126'],
          'last_kara_127' => $row['last_kara_127'],
          'last_kara_128' => $row['last_kara_128'],
          'last_kara_129' => $row['last_kara_129'],
          'last_kara_130' => $row['last_kara_130'],
          'last_kara_131' => $row['last_kara_131'],
          'last_kara_132' => $row['last_kara_132'],
          'last_kara_133' => $row['last_kara_133'],
          'last_kara_134' => $row['last_kara_134'],
          'last_kara_135' => $row['last_kara_135'],
          'last_kara_136' => $row['last_kara_136'],
          'last_kara_137' => $row['last_kara_137'],
          'last_kara_138' => $row['last_kara_138'],
          'last_kara_139' => $row['last_kara_139'],
          'last_kara_140' => $row['last_kara_140'],
          'last_kara_141' => $row['last_kara_141'],
          'last_kara_142' => $row['last_kara_142'],
          'last_kara_143' => $row['last_kara_143'],
          'last_kara_144' => $row['last_kara_144'],
          'last_kara_145' => $row['last_kara_145'],
          'last_kara_146' => $row['last_kara_146'],
          'last_kara_147' => $row['last_kara_147'],
          'last_kara_148' => $row['last_kara_148'],
          'last_kara_149' => $row['last_kara_149'],
          'last_kara_150' => $row['last_kara_150'],
          'last_kara_151' => $row['last_kara_151'],
          'last_kara_152' => $row['last_kara_152'],
          'last_kara_153' => $row['last_kara_153'],
          'last_kara_154' => $row['last_kara_154'],
          'last_kara_155' => $row['last_kara_155'],
          'last_kara_156' => $row['last_kara_156'],
          'last_kara_157' => $row['last_kara_157'],
          'last_kara_158' => $row['last_kara_158'],
          'last_kara_159' => $row['last_kara_159'],
          'last_kara_160' => $row['last_kara_160'],
          'last_kara_161' => $row['last_kara_161'],
          'last_kara_162' => $row['last_kara_162'],
          'last_kara_163' => $row['last_kara_163'],
          'last_kara_164' => $row['last_kara_164'],


        );
      }


      if (!empty($retunr_excel)) {

        $excel_file_err = 0;

        // Spreadsheetオブジェクト生成
        $objSpreadsheet = new Spreadsheet();
        // シート設定
        $objSheet = $objSpreadsheet->getActiveSheet();

        // Spreadsheetオブジェクト生成
        $objSpreadsheet = new Spreadsheet();
        // シート設定
        $objSheet = $objSpreadsheet->getActiveSheet();

        // ヘッダー項目
        // 横方向の配列データ
        $arrX = array(
          '社員番号', // 項目0
          '社員氏名', // 項目1
          'フリガナ', // 項目2
          '性別', // 項目3
          '生年月日', // 項目4
          '生年月日（西暦）', // 項目5
          '入社区分', // 項目6
          '入社年月日', // 項目7
          '入社年月日（西暦）', // 項目8
          '勤続年数', // 項目9
          '勤続年数_月数', // 項目10
          '社員区分', // 項目11
          '役職', // 項目12
          '学歴', // 項目13
          '所属部門', // 項目14
          '職種', // 項目15
          '郵便番号', // 項目16
          '住所１', // 項目17
          '住所２', // 項目18
          '住所（フリガナ）１', // 項目19
          '住所（フリガナ）２', // 項目20
          '電話番号', // 項目21
          'メールアドレス', // 項目22
          '障害区分', // 項目23
          '寡婦', // 項目24
          '寡夫', // 項目25
          '勤労学生', // 項目26
          '災害者', // 項目27
          '外国人', // 項目28
          '退職理由', // 項目29
          '退職年月日', // 項目30
          '退職年月日（西暦）', // 項目31
          '給与区分', // 項目32
          '税表区分', // 項目33
          '年末調整対象', // 項目34
          '支払形態', // 項目35
          '支給日パターン', // 項目36
          '給与明細書パターン', // 項目37
          '賞与明細書パターン', // 項目38
          '健康保険対象', // 項目39
          '介護保険対象', // 項目40
          '健保月額（千円）', // 項目41
          '健保証番号（協会）', // 項目42
          '健保証番号（組合）', // 項目43
          '健保・厚年　資格取得年月日', // 項目44
          '厚生年金対象', // 項目45
          '70歳以上被用者', // 項目46
          '保険者種別', // 項目47
          '厚年月額（千円）', // 項目48
          '基礎年金番号１', // 項目49
          '基礎年金番号２', // 項目50
          '短時間労働者（3/4未満）', // 項目51
          '労災保険対象', // 項目52
          '雇用保険対象', // 項目53
          '労働保険用区分', // 項目54
          '雇用保険被保険者番号１', // 項目55
          '雇用保険被保険者番号２', // 項目56
          '雇用保険被保険者番号３', // 項目57
          '労働保険　資格取得年月日', // 項目58
          '配偶者', // 項目59
          '配偶者氏名', // 項目60
          '配偶者フリガナ', // 項目61
          '配偶者性別', // 項目62
          '配偶者生年月日', // 項目63
          '配偶者生年月日（西暦）', // 項目64
          '源泉控除対象配偶者', // 項目65
          '配偶者老人', // 項目66
          '配偶者障害者', // 項目67
          '配偶者同居', // 項目68
          '配偶者非居住者', // 項目69
          '扶養控除対象人数', // 項目70
          '扶養親族_扶養親族名_1', // 項目71
          '扶養親族_フリガナ_1', // 項目72
          '扶養親族_続柄_1', // 項目73
          '扶養親族_生年月日_1', // 項目74
          '扶養親族_生年月日（西暦）_1', // 項目75
          '扶養親族_扶養区分_1', // 項目76
          '扶養親族_障害者区分_1', // 項目77
          '扶養親族_同居区分_1', // 項目78
          '扶養親族_同居老親等区分_1', // 項目79
          '扶養親族_非居住者区分_1', // 項目80
          '扶養親族_控除計算区分_1', // 項目81
          '扶養親族_扶養親族名_2', // 項目82
          '扶養親族_フリガナ_2', // 項目83
          '扶養親族_続柄_2', // 項目84
          '扶養親族_生年月日_2', // 項目85
          '扶養親族_生年月日（西暦）_2', // 項目86
          '扶養親族_扶養区分_2', // 項目87
          '扶養親族_障害者区分_2', // 項目88
          '扶養親族_同居区分_2', // 項目89
          '扶養親族_同居老親等区分_2', // 項目90
          '扶養親族_非居住者区分_2', // 項目91
          '扶養親族_控除計算区分_2', // 項目92
          '扶養親族_扶養親族名_3', // 項目93
          '扶養親族_フリガナ_3', // 項目94
          '扶養親族_続柄_3', // 項目95
          '扶養親族_生年月日_3', // 項目96
          '扶養親族_生年月日（西暦）_3', // 項目97
          '扶養親族_扶養区分_3', // 項目98
          '扶養親族_障害者区分_3', // 項目99
          '扶養親族_同居区分_3', // 項目100
          '扶養親族_同居老親等区分_3', // 項目101
          '扶養親族_非居住者区分_3', // 項目102
          '扶養親族_控除計算区分_3', // 項目103
          '扶養親族_扶養親族名_4', // 項目104
          '扶養親族_フリガナ_4', // 項目105
          '扶養親族_続柄_4', // 項目106
          '扶養親族_生年月日_4', // 項目107
          '扶養親族_生年月日（西暦）_4', // 項目108
          '扶養親族_扶養区分_4', // 項目109
          '扶養親族_障害者区分_4', // 項目110
          '扶養親族_同居区分_4', // 項目111
          '扶養親族_同居老親等区分_4', // 項目112
          '扶養親族_非居住者区分_4', // 項目113
          '扶養親族_控除計算区分_4', // 項目114
          '扶養親族_扶養親族名_5', // 項目115
          '扶養親族_フリガナ_5', // 項目116
          '扶養親族_続柄_5', // 項目117
          '扶養親族_生年月日_5', // 項目118
          '扶養親族_生年月日（西暦）_5', // 項目119
          '扶養親族_扶養区分_5', // 項目120
          '扶養親族_障害者区分_5', // 項目121
          '扶養親族_同居区分_5', // 項目122
          '扶養親族_同居老親等区分_5', // 項目123
          '扶養親族_非居住者区分_5', // 項目124
          '扶養親族_控除計算区分_5', // 項目125
          '扶養親族_扶養親族名_6', // 項目126
          '扶養親族_フリガナ_6', // 項目127
          '扶養親族_続柄_6', // 項目128
          '扶養親族_生年月日_6', // 項目129
          '扶養親族_生年月日（西暦）_6', // 項目130
          '扶養親族_扶養区分_6', // 項目131
          '扶養親族_障害者区分_6', // 項目132
          '扶養親族_同居区分_6', // 項目133
          '扶養親族_同居老親等区分_6', // 項目134
          '扶養親族_非居住者区分_6', // 項目135
          '扶養親族_控除計算区分_6', // 項目136
          '扶養親族_扶養親族名_7', // 項目137
          '扶養親族_フリガナ_7', // 項目138
          '扶養親族_続柄_7', // 項目139
          '扶養親族_生年月日_7', // 項目140
          '扶養親族_生年月日（西暦）_7', // 項目141
          '扶養親族_扶養区分_7', // 項目142
          '扶養親族_障害者区分_7', // 項目143
          '扶養親族_同居区分_7', // 項目144
          '扶養親族_同居老親等区分_7', // 項目145
          '扶養親族_非居住者区分_7', // 項目146
          '扶養親族_控除計算区分_7', // 項目147
          '扶養親族_扶養親族名_8', // 項目148
          '扶養親族_フリガナ_8', // 項目149
          '扶養親族_続柄_8', // 項目150
          '扶養親族_生年月日_8', // 項目151
          '扶養親族_生年月日（西暦）_8', // 項目152
          '扶養親族_扶養区分_8', // 項目153
          '扶養親族_障害者区分_8', // 項目154
          '扶養親族_同居区分_8', // 項目155
          '扶養親族_同居老親等区分_8', // 項目156
          '扶養親族_非居住者区分_8', // 項目157
          '扶養親族_控除計算区分_8', // 項目158
          '扶養親族_扶養親族名_9', // 項目159
          '扶養親族_フリガナ_9', // 項目160
          '扶養親族_続柄_9', // 項目161
          '扶養親族_生年月日_9', // 項目162
          '扶養親族_生年月日（西暦）_9', // 項目163
          '扶養親族_扶養区分_9', // 項目164
          '扶養親族_障害者区分_9', // 項目165
          '扶養親族_同居区分_9', // 項目166
          '扶養親族_同居老親等区分_9', // 項目167
          '扶養親族_非居住者区分_9', // 項目168
          '扶養親族_控除計算区分_9', // 項目169
          '扶養親族_扶養親族名_10', // 項目170
          '扶養親族_フリガナ_10', // 項目171
          '扶養親族_続柄_10', // 項目172
          '扶養親族_生年月日_10', // 項目173
          '扶養親族_生年月日（西暦）_10', // 項目174
          '扶養親族_扶養区分_10', // 項目175
          '扶養親族_障害者区分_10', // 項目176
          '扶養親族_同居区分_10', // 項目177
          '扶養親族_同居老親等区分_10', // 項目178
          '扶養親族_非居住者区分_10', // 項目179
          '扶養親族_控除計算区分_10', // 項目180
          '扶養親族_扶養親族名_11', // 項目181
          '扶養親族_フリガナ_11', // 項目182
          '扶養親族_続柄_11', // 項目183
          '扶養親族_生年月日_11', // 項目184
          '扶養親族_生年月日（西暦）_11', // 項目185
          '扶養親族_扶養区分_11', // 項目186
          '扶養親族_障害者区分_11', // 項目187
          '扶養親族_同居区分_11', // 項目188
          '扶養親族_同居老親等区分_11', // 項目189
          '扶養親族_非居住者区分_11', // 項目190
          '扶養親族_控除計算区分_11', // 項目191
          '扶養親族_扶養親族名_12', // 項目192
          '扶養親族_フリガナ_12', // 項目193
          '扶養親族_続柄_12', // 項目194
          '扶養親族_生年月日_12', // 項目195
          '扶養親族_生年月日（西暦）_12', // 項目196
          '扶養親族_扶養区分_12', // 項目197
          '扶養親族_障害者区分_12', // 項目198
          '扶養親族_同居区分_12', // 項目199
          '扶養親族_同居老親等区分_12', // 項目200
          '扶養親族_非居住者区分_12', // 項目201
          '扶養親族_控除計算区分_12', // 項目202
          '扶養親族_扶養親族名_13', // 項目203
          '扶養親族_フリガナ_13', // 項目204
          '扶養親族_続柄_13', // 項目205
          '扶養親族_生年月日_13', // 項目206
          '扶養親族_生年月日（西暦）_13', // 項目207
          '扶養親族_扶養区分_13', // 項目208
          '扶養親族_障害者区分_13', // 項目209
          '扶養親族_同居区分_13', // 項目210
          '扶養親族_同居老親等区分_13', // 項目211
          '扶養親族_非居住者区分_13', // 項目212
          '扶養親族_控除計算区分_13', // 項目213
          '扶養親族_扶養親族名_14', // 項目214
          '扶養親族_フリガナ_14', // 項目215
          '扶養親族_続柄_14', // 項目216
          '扶養親族_生年月日_14', // 項目217
          '扶養親族_生年月日（西暦）_14', // 項目218
          '扶養親族_扶養区分_14', // 項目219
          '扶養親族_障害者区分_14', // 項目220
          '扶養親族_同居区分_14', // 項目221
          '扶養親族_同居老親等区分_14', // 項目222
          '扶養親族_非居住者区分_14', // 項目223
          '扶養親族_控除計算区分_14', // 項目224
          '扶養親族_扶養親族名_15', // 項目225
          '扶養親族_フリガナ_15', // 項目226
          '扶養親族_続柄_15', // 項目227
          '扶養親族_生年月日_15', // 項目228
          '扶養親族_生年月日（西暦）_15', // 項目229
          '扶養親族_扶養区分_15', // 項目230
          '扶養親族_障害者区分_15', // 項目231
          '扶養親族_同居区分_15', // 項目232
          '扶養親族_同居老親等区分_15', // 項目233
          '扶養親族_非居住者区分_15', // 項目234
          '扶養親族_控除計算区分_15', // 項目235
        );

        $objSheet->fromArray(
          $arrX,      // 配列データ
          NULL,       // 配列データの中でセルに設定しないNULL値の指定
          'A1'        // 左上座標(デフォルト:"A1")
        );


        //　Excelファイルへ　データ挿入
        $objSheet->fromArray($retunr_excel, null, 'A2');

        // XLSX形式オブジェクト生成
        $objWriter = new Xlsx($objSpreadsheet);

        // ファイル書込み
        $objWriter->save($create_excel);

        //   exit();

        // ********* フラグ処理
        $csv_file_ok_FLG = "0"; // デフォルト

        $excel_file_FLG = "1"; // Excel　作成完了

      } else {

        // 指定した日付に該当の　Excelファイルがなかった場合
        //      $excel_file_err = 1;

        $excel_file_FLG = "2"; // Excel　エラー

      }
    } catch (PDOException $e) {

      //    print('Error:'.$e->getMessage());

      // (トランザクション) ロールバック
      //       $pdo->rollBack();

      // ************** エラー処理 ***************
      $csv_file_ok_FLG = "2";
    } finally {

      $pdo = null;
    } //======================================== END try ==================================


    // =========================================================================================== 
    // =========================================== 銀行用　Excel try ================================
    // ===========================================================================================
    // 絞り込み　結果　格納配列
    // $retunr_excel_bank = [];
    //========================================
    try {

      // PDO オブジェクト
      $pdo = new PDO($dsn, $user, $password);

      // SQL
      $stmt = $pdo->prepare("SELECT * from User_Info_Table WHERE data_Flg = '0'");

      // SQL 実行 
      $res = $stmt->execute();

      $idx = 0;

      // ファルダがなかったら、フォルダを作成  形式 files_20211025
      $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];

      //====== ファイルの保存
      // ファイル生成場所
      $create_excel_bank = $dirpath . "/" . $file_name_excel_bank;


      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // ============= 0 パディング ===============
        $row['employee_id'] = sprintf('%06d', $row['employee_id']);
        $row['bank_code'] = sprintf('%04d', $row['bank_code']);
        $row['bank_siten_code'] = sprintf('%03d', $row['bank_siten_code']);

        // 預金種目  0:普通 , 1:当座 bank_kamoku
        if (strcmp($row['bank_kamoku'], "0") == 0) {
          $bank_kamoku = "普通";
        } else {
          $bank_kamoku = "当座";
        }

        // 支払元登録No
        $Siharai_M_Number = "1";

        // 支店フリガナ ※カラムなし $bank_siten_name_kana
        $bank_siten_name_kana = "ｼﾃﾝﾌﾘｶﾞﾅ";

        // 振込手数料 空欄
        //=== 空文字
        $row['koumoku_01'] = ""; // 空 項目 ,   // 振込手数料

        $teigaku_F = ""; // 定額振込

        //===  10 空欄 ===
        $row['koumoku_02'] = ""; // 空 項目 ,   //   振込先 1　金額,
        $row['koumoku_03'] = ""; // 空 項目 ,   // 振込先2　金融機関コード,
        $row['koumoku_04'] = ""; // 空 項目 ,   // 振込先2　金融機関名,
        $row['koumoku_05'] = ""; // 空 項目 ,   // 振込先2　金融機関フリガナ,
        $row['koumoku_06'] = ""; // 空 項目 ,   // 振込先2　支店コード,
        $row['koumoku_07'] = ""; // 空 項目 ,   // 振込先2  支店名,
        $row['koumoku_08'] = ""; // 空 項目 ,  振込先2   支店フリガナ
        $row['koumoku_09'] = ""; // 空 項目 ,   // 振込先2　預金種目,
        $row['koumoku_10'] = ""; // 空 項目 ,   // 振込先2　口座番号,
        $row['koumoku_11'] = ""; // 空 項目 ,   // 振込先2　振込手数料,
        $row['koumoku_12'] = ""; // 空 項目 ,   // 振込先2　定額振込,

        //====== ラストカラム 「金額」
        $last_kingaku = "";

        // ==============================================================
        // ====================================== 配列　挿入
        // ==============================================================
        $retunr_excel_bank[] = array(

          'employee_id' => $row['employee_id'],  //項目_1:::社員番号
          'user_name' => $row['user_name'],      //項目_2:::社員氏名
          'Siharai_M_Number' => $Siharai_M_Number, //項目_3:::支払元登録No
          'bank_code' => $row['bank_code'], //項目_4:::振込先①　金融機関コード
          'bank_name' => $row['bank_name'],  //項目_5:::振込先①　金融機関名
          'bank_name_kana' => $row['bank_name_kana'],  //項目_6:::振込先①　金融機関フリガナ
          'bank_siten_code' => $row['bank_siten_code'], //項目_7:::振込先①　支店コード
          'bank_siten_name' => $row['bank_siten_name'], //項目_8:::振込先①　支店名
          'bank_siten_name_kana' => $bank_siten_name_kana, //項目_9:::振込先①　支店フリガナ
          'bank_kamoku' => $bank_kamoku,  //項目_10:::振込先①　預金種目
          'kouzzz_number' => $row['kouzzz_number'], //項目_11:::振込先①　口座番号
          'koumoku_01' => $row['koumoku_01'],  //項目_12:::振込先①　振込手数料
          'teigaku_F' => $teigaku_F,            //項目_13:::振込先①　定額振込
          'koumoku_01' => $row['koumoku_01'],  //項目_14:::振込先①　金額
          'koumoku_02' => $row['koumoku_02'],  //項目_15:::振込先②　金融機関コード
          'koumoku_03' => $row['koumoku_03'],  //項目_16:::振込先②　金融機関名
          'koumoku_04' => $row['koumoku_04'],  //項目_17:::振込先②　金融機関フリガナ
          'koumoku_05' => $row['koumoku_05'],  //項目_18:::振込先②　支店コード
          'koumoku_06' => $row['koumoku_06'],   //項目_19:::振込先②　支店名
          'koumoku_07' => $row['koumoku_07'], //項目_20:::振込先②　支店フリガナ
          'koumoku_08' => $row['koumoku_08'],  //項目_21:::振込先②　預金種目
          'koumoku_09' => $row['koumoku_09'],  //項目_22:::振込先②　口座番号
          'koumoku_10' => $row['koumoku_10'], //項目_23:::振込先②　振込手数料
          'koumoku_11' => $row['koumoku_11'], //項目_24:::振込先②　定額振込
          'koumoku_12' => $row['koumoku_12'],  //項目_25:::振込先②　金額
          'last_kingaku' => $last_kingaku,  // 「金額」
        );
      }

      if (!empty($retunr_excel_bank)) {

        $excel_file_err = 0;

        // Spreadsheetオブジェクト生成
        $objSpreadsheet = new Spreadsheet();
        // シート設定
        $objSheet = $objSpreadsheet->getActiveSheet();

        // Spreadsheetオブジェクト生成
        $objSpreadsheet = new Spreadsheet();
        // シート設定
        $objSheet = $objSpreadsheet->getActiveSheet();

        // ヘッダー項目
        // 横方向の配列データ
        $arrX = array(
          '社員番号',
          '社員氏名',
          '支払元登録No',
          '振込先①　金融機関コード',
          '振込先①　金融機関名',
          '振込先①　金融機関フリガナ',
          '振込先①　支店コード',
          '振込先①　支店名',
          '振込先①　支店フリガナ',
          '振込先①　預金種目',
          '振込先①　口座番号',
          '振込先①　振込手数料',
          '振込先①　定額振込',
          '振込先①　金額',
          '振込先②　金融機関コード',
          '振込先②　金融機関名',
          '振込先②　金融機関フリガナ',
          '振込先②　支店コード',
          '振込先②　支店名',
          '振込先②　支店フリガナ',
          '振込先②　預金種目',
          '振込先②　口座番号',
          '振込先②　振込手数料',
          '振込先②　定額振込',
          '振込先②　金額'
        );

        $objSheet->fromArray(
          $arrX,      // 配列データ
          NULL,       // 配列データの中でセルに設定しないNULL値の指定
          'A1'        // 左上座標(デフォルト:"A1")
        );

        //　Excelファイルへ　データ挿入
        $objSheet->fromArray($retunr_excel_bank, null, 'A2');

        // XLSX形式オブジェクト生成
        $objWriter = new Xlsx($objSpreadsheet);

        // ファイル書込み
        $objWriter->save($create_excel_bank);

        //   exit();

        // ********* フラグ処理
        $csv_file_ok_FLG = "0"; // デフォルト

        $excel_file_FLG = "1"; // Excel　作成完了

      } else {

        // 指定した日付に該当の　Excelファイルがなかった場合
        //      $excel_file_err = 1;

        $excel_file_FLG = "2"; // Excel　エラー

      }
    } catch (PDOException $e) {

      // ************** エラー処理 ***************
      $csv_file_ok_FLG = "2";
    }

    // =========================================== ====================================================
    // =========================================== 銀行用　Excel try  END ================================
    // =========================================== ====================================================




    //==================================================================
    //================= アップデート 処理 データ処理フラグを データ作成済みにする
    //==================================================================
    try {

      // PDO オブジェクト
      $pdo = new PDO($dsn, $user, $password);

      $stmt = $pdo->prepare("UPDATE User_Info_Table SET data_Flg = '1' 
                  WHERE data_Flg = '0' ");

      // CSV エクスポート 開始日
      /*
      $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

    
      $stmt->bindValue(
        2,
        $date_target_02,
        PDO::PARAM_STR
      );
      */

      // SQL 実行 
      $res = $stmt->execute();
    } catch (PDOException $e) {

      // ************** エラー処理 ***************
      $csv_file_ok_FLG = "2";
    } finally {

      $pdo = null;

      // ページリロード
      header("Location: " . $_SERVER['PHP_SELF']);

      exit;
    }

    //==============================================
    //*************** ファイルのダウンロード処理 */
    //==============================================
    //  t_download($protocol);


    //===================================
    // =========== 現在のフルURL 取得
    //===================================

    /*
    if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {

      $protocol = 'https://';
    } else {

      $protocol = 'http://';
    }

    // ./files の . を削除
    $new_create_excel = mb_substr($create_excel, 1);

    $protocol .= $_SERVER["HTTP_HOST"] . '/job_recruit/csv' . $new_create_excel;
    //. $_SERVER["REQUEST_URI"];

 


    // ファイルタイプを指定
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    // ファイルサイズを取得し、ダウンロードの進捗を表示
    //        header('Content-Length: '.filesize($protocol));
    // ファイルのダウンロード、リネームを指示
    header('Content-Disposition: attachment; filename="' . $file_name_excel . '"');

    ob_clean();  //追記
    flush();     //追記

    // ファイルを読み込みダウンロードを実行
    readfile($protocol);
    exit;
    */


    // ============================================================================
    // ============================================================================
    // =============== ************ Excel　出力 *************  日付選択
    // ============================================================================
    // ============================================================================

  } else if ($export_output === "excel" && strcmp($check_box_FLG, "0") == 0) {

    //  print("ok");


    //============================================
    //============== 修正 2022_01_13  日付が空の場合
    //============================================
    if (isset($_POST['date_target']) || isset($_POST['date_target_02'])) {

      if (empty($_POST['date_target']) && empty($_POST['date_target_02'])) {

        $display_err = "ファイル作成ができません。日付ボックスが空です。「日付を入力するか」、「未出力ファイル」にチェックをつけてください。";

        // === 日付ボックスが　２つとも　空だった場合
        $csv_FLG = "0";
      } else {


        //========== 日付のPOST データ取得
        $date_target = $_POST['date_target'];
        $date_target = str_replace("/", "-", $date_target);

        $date_target_02 = $_POST['date_target_02'];
        $date_target_02 = str_replace("/", "-", $date_target_02);







        // ==============================================================
        //====== 日付が同じなら、日付の右側ボックスを加算させる。
        // ==============================================================

        if (!empty($date_target) && !empty($date_target_02)) {

          if ($date_target === $date_target_02) {

            $tmp_date = strtotime($date_target_02);

            $date_target_02 = date("Y-m-d", strtotime("+1 day", $tmp_date));

            //        print ("日付加算" . $date_target_02);

          } else {

            $tmp_date = strtotime($date_target_02);

            $date_target_02 = date("Y-m-d", strtotime("+1 day", $tmp_date));

            //         print ("日付加算" . $date_target_02);

          }
        } // ========= END if 

        //==================== 日付プラス  1 END =========================>


        // Excel出力 ファイル名  => アルバイト管理_2021-10-12_121212.xlsx
        $file_name_excel = "R_Baito_Kanri_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
          "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
          $get_now_arr['minute'] . $get_now_arr['second'] . ".xlsx";


        // Excel出力 ファイル名  => アルバイト管理_2021-10-12_121212.xlsx
        $file_name_excel_bank = "Ginkou_Baito_Kanri_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
          "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
          $get_now_arr['minute'] . $get_now_arr['second'] . ".xlsx";

        //================================  
        //================== 接続情報
        //================================  
        $dsn = '';
        $user = '';
        $password = '';

        // 絞り込み　結果　格納配列
        $retunr_excel = [];

        try {

          // PDO オブジェクト
          $pdo = new PDO($dsn, $user, $password);

          // SQL
          $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
            WHERE creation_time BETWEEN ? AND ? ORDER BY user_id DESC");

          // CSV エクスポート 開始日
          $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

          // CSV エクスポート 終了日
          $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);

          // SQL 実行 
          $res = $stmt->execute();

          $idx = 0;

          // ファルダがなかったら、フォルダを作成  形式 files_20211025
          $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];
          if (!file_exists($dirpath)) {
            mkdir($dirpath, 0777);

            //chmod関数で「hoge」ディレクトリのパーミッションを「0777」にする
            // chmod($dirpath, 0777);
          }



          //====== ファイルの保存
          // ファイル生成場所
          $create_excel = $dirpath . "/" . $file_name_excel;

          //====================================================== 
          //=========================== Excel 用　変数宣言
          //====================================================== 
          $sex = ""; // 性別
          $wareki = ""; // 和暦の年
          $wareki_r = ""; // 和暦 年月日 出力用
          $month_r = "";
          $day_r = ""; // 日付　日にち

          $Seireki_r = ""; // 西暦出力用

          $hokensya_K = ""; // 保険者種別  男子、女子
          $syasin_K = ""; // 社員区分
          $syozoku_b = ""; // 所属部門


          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            //=== 項目 1 employee_id 社員番号  000101 　６桁に　?パディング
            $row['employee_id'] = sprintf('%06d', $row['employee_id']);
            //       $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");

            //=== 項目 2
            $row['user_name'] = mb_convert_kana($row['user_name'], "h", "UTF-8"); // 社員氏名

            //=== 項目 3 ふりがな　を　カナ　へ変換
            $row['furi_name'] = mb_convert_kana($row['furi_name'], "h", "UTF-8"); // フリガナ
            //      $row['furi_name'] = mb_convert_encoding($row['furi_name'], "SJIS-win", "auto");

            // メールアドレス
            //      $row['email'] = mb_convert_encoding($row['email'], "SJIS-win", "auto");

            // 配偶者 spouse
            //      $row['spouse'] = mb_convert_encoding($row['spouse'], "SJIS-win", "auto");
            if (strcmp($row['spouse'], "0") == 0) {
              $row['spouse'] = "なし";
            } else {
              $row['spouse'] = "あり";
            }

            // 扶養人数 	dependents_num
            //      $row['dependents_num'] = mb_convert_encoding($row['dependents_num'], "SJIS-win", "auto");

            //=== 項目 4

            //=== 性別
            if (strcmp($row['sex'], "0") == 0) {
              $row['sex'] = "男"; // 保険者種別
              $hokensya_K = ""; // 保険者種別

              //        $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
              //     $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
            } else {
              $row['sex'] = "女"; // 保険者種別
              $hokensya_K = ""; // 保険者種別

              //       $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
              //      $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
            }

            // === 項目 5

            //=== 生年月日　（西暦 => 和暦　変換）
            //        $row['birthday'] = mb_convert_encoding($row['birthday'], "SJIS-win", "auto");

            // 年
            $year = mb_substr($row['birthday'], 0, 4); // 西暦　取り出し 1900

            // 和暦変換用 function  Wareki_Parse
            $wareki = Wareki_Parse($year);

            // 月
            $month = mb_substr($row['birthday'], 5, 2);

            if (strpos($month, '-') !== false) {
              // - ハイフン が含まれている場合 , 0　パディング
              $month = str_replace("-", "", $month);
              $month_r = "0" . $month;
            } else {
              $month_r = $month;
            }


            // 日
            $day = mb_substr(
              $row['birthday'],
              7,
              2
            );
            // 文字列の長さ取得  1,  10
            $day_num = mb_strlen($day);

            if ($day_num == 1) {
              $day_r = "0" . $day;
            } else if (strpos(
              $day,
              '-'
            ) !== false) {
              $day = str_replace("-", "", $day);
              $day_r = $day;
            } else {
              $day_r = $day;
            }

            // 和暦
            $wareki_r = $wareki . $month_r . "月" . $day_r . "日";
            //      $wareki_r = mb_convert_encoding($wareki_r, "SJIS-win", "auto");

            //=== 項目6  西暦  
            $Seireki_r = $year . "年" . $month_r . "月" . $day_r . "日";
            //        $Seireki_r = mb_convert_encoding($Seireki_r, "SJIS-win", "auto");


            //===  郵便番号 zip
            //=== 郵便番号 zip  （-） をつける
            $row['zip'] = substr($row['zip'], 0, 3) . "-" . substr($row['zip'], 3);
            //      $row['zip'] = mb_convert_encoding($row['zip'], "SJIS-win", "auto");

            //=== 住所１ address_01
            //      $row['address_01'] = mb_convert_encoding($row['address_01'], "SJIS-win", "auto");

            //=== 電話番号 tel
            $row['tel'] = substr($row['tel'], 0, 3) . "-" . substr($row['tel'], 3, 4) . "-" . substr($row['tel'], 7);

            //       $row['creation_time'] = mb_convert_encoding($row['creation_time'], "SJIS-win", "auto");
            //      $row['data_Flg'] = mb_convert_encoding($row['data_Flg'], "SJIS-win", "auto");

            //=== 社員番号
            //       $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");


            //=== 社員区分
            $syasin_K = "アルバイト";
            //       $syasin_K = mb_convert_encoding($syasin_K, "SJIS-win", "auto");

            //=== 所属部門
            //============================= 20221_12_21 値変更
            // $syozoku_b = "003";
            $syozoku_b = "アルバイト";

            //       $syozoku_b = mb_convert_encoding($syozoku_b, "SJIS-win", "auto");

            // ======================== フォームから取得以外の項目
            $row['koumoku_01'] = ""; // 項目7:入社区分  （空）
            $row['koumoku_02'] = ""; // 項目8:入社年月日　（空）
            $row['koumoku_03'] = ""; // 項目9:入社年月日（西暦）　（空）
            $row['koumoku_04'] = ""; // 項目10:勤続年数　（空）
            $row['koumoku_05'] = ""; // 項目11 勤続年数_月数　（空）

            $row['koumoku_06'] = ""; // 項目13:役職 　（空）
            $row['koumoku_07'] = ""; // 項目14:学歴: （空）
            $row['koumoku_08'] = ""; // 項目16:職種: （空）
            $row['koumoku_09'] = ""; // 項目19:住所２
            $row['koumoku_10'] = ""; // 項目20:住所（フリガナ）１

            $row['koumoku_11'] = ""; // 項目21:住所（フリガナ）２
            $row['koumoku_12'] = "非該当"; // 項目24:障害区分:非該当
            $row['koumoku_13'] = "非該当"; // 項目25:寡婦:非該当
            $row['koumoku_14'] = "非該当"; // 項目26:寡夫:非該当
            $row['koumoku_15'] = "非該当"; // 項目27:勤労学生:非該当

            $row['koumoku_16'] = "非該当"; // 項目28:災害者:非該当
            $row['koumoku_17'] = "非該当"; // 項目29:外国人:非該当
            $row['koumoku_18'] = ""; // 項目30:退職理由: （空）
            $row['koumoku_19'] = ""; // 項目31:退職年月日: （空）
            $row['koumoku_20'] = ""; // 項目32:退職年月日（西暦）: （空）

            $row['koumoku_21'] = "非該当"; // 項目33:給与区分:月給
            $row['koumoku_22'] = "非該当"; // 項目34:税表区分:月額甲欄
            $row['koumoku_23'] = ""; // 項目35:年末調整対象:する
            $row['koumoku_24'] = "振込"; // 項目36:支払形態:振込
            $row['koumoku_25'] = "サンプル"; // 項目37:支給日パターン:サンプル

            //============================= 20221_12_21 値変更
            //  $row['koumoku_26'] = "社員パターン"; // 項目38:給与明細書パターン:社員パターン
            $row['koumoku_26'] = ""; // 項目38:給与明細書パターン:社員パターン

            //============================= 20221_12_21 値変更
            //  $row['koumoku_27'] = "賞与サンプル"; // 項目39:賞与明細書パターン:賞与サンプル
            $row['koumoku_27'] = ""; // 項目39:賞与明細書パターン:賞与サンプル

            $row['koumoku_28'] = ""; // 項目40:健康保険対象: （空）
            $row['koumoku_29'] = ""; // 項目41:介護保険対象: （空）

            //============================= 20221_12_21 値変更
            //  $row['koumoku_30'] = "220"; // 項目42:健保月額（千円）:220
            $row['koumoku_30'] = ""; // 項目42:健保月額（千円）:220

            //============================= 20221_12_21 値変更
            //  $row['koumoku_31'] = "367"; // 項目43:健保証番号（協会）367
            $row['koumoku_31'] = ""; // 項目43:健保証番号（協会）367


            $row['koumoku_32'] = ""; // 項目44:健保証番号（組合）: （空）
            $row['koumoku_33'] = ""; // 項目45:健保・厚年　資格取得年月日: （空）
            $row['koumoku_34'] = ""; // 項目46:厚生年金対象: （空）
            $row['koumoku_35'] = "非該当"; // 項目47:70歳以上被用者:非該当

            // $hokensya_K  項目48:保険者種別:女子　△△△ 変数当てはめ △△△
            $row['koumoku_37'] = ""; // 項目49:厚年月額（千円）: （空）
            $row['koumoku_38'] = ""; // 項目50:基礎年金番号１: （空）
            $row['koumoku_39'] = ""; // 項目51:基礎年金番号２:対象
            $row['koumoku_40'] = "対象外"; // 項目52:短時間労働者（3/4未満）:対象外

            $row['koumoku_41'] = "対象"; // 項目53:労災保険対象:対象

            //============================= 20221_12_21 値変更
            //  $row['koumoku_42'] = "対象"; // 項目54:雇用保険対象:対象
            $row['koumoku_42'] = ""; // 項目54:雇用保険対象:対象

            $row['koumoku_43'] = "常用"; // 項目55:労働保険用区分:常用

            //============================= 20221_12_21 値変更
            //  $row['koumoku_44'] = "5029"; // 項目56:雇用保険被保険者番号１:5029
            $row['koumoku_44'] = ""; // 項目56:雇用保険被保険者番号１:5029

            //============================= 20221_12_21 値変更
            //  $row['koumoku_45'] = "530842"; // 項目57:雇用保険被保険者番号２:530842
            $row['koumoku_45'] = ""; // 項目57:雇用保険被保険者番号２:530842

            //============================= 20221_12_21 値変更
            //  $row['koumoku_46'] = "4"; // 項目58:雇用保険被保険者番号３:4
            $row['koumoku_46'] = ""; // 項目58:雇用保険被保険者番号３:4


            $row['koumoku_47'] = ""; // 項目59:労働保険　資格取得年月日: （空）
            // 項目60:配偶者:なし　　＝＞　$row['spouse']
            $row['koumoku_49'] = ""; // 項目61:配偶者氏名: （空）
            $row['koumoku_50'] = ""; // 項目62:配偶者フリガナ: （空）

            $row['koumoku_51'] = ""; // 項目63:配偶者性別: （空）
            $row['koumoku_52'] = ""; // 項目64:配偶者生年月日: （空）
            $row['koumoku_53'] = ""; // 項目65:配偶者生年月日（西暦）: （空）
            $row['koumoku_54'] = "非該当"; // 項目66:源泉控除対象配偶者:非該当
            $row['koumoku_55'] = "非該当"; // 項目67:配偶者老人:非該当

            $row['koumoku_56'] = "非該当"; // 項目68:配偶者老人:非該当
            $row['koumoku_57'] = "非同居"; // 項目69:配偶者老人:非同居
            $row['koumoku_58'] = "非該当"; // 項目70:配偶者老人:非該当
            //     $row['koumoku_59'] = ""; // 項目71:扶養控除対象人数:0

            // 項目71:扶養控除対象人数:  フォームから取得


            //======== 165 の　空白行
            $row['last_kara_01'] = "";
            $row['last_kara_02'] = "";
            $row['last_kara_03'] = "";
            $row['last_kara_04'] = "";
            $row['last_kara_05'] = "";
            $row['last_kara_06'] = "";
            $row['last_kara_07'] = "";
            $row['last_kara_08'] = "";
            $row['last_kara_09'] = "";
            $row['last_kara_10'] = "";

            $row['last_kara_11'] = "";
            $row['last_kara_12'] = "";
            $row['last_kara_13'] = "";
            $row['last_kara_14'] = "";
            $row['last_kara_15'] = "";
            $row['last_kara_16'] = "";
            $row['last_kara_17'] = "";
            $row['last_kara_18'] = "";
            $row['last_kara_19'] = "";
            $row['last_kara_20'] = "";

            $row['last_kara_21'] = "";
            $row['last_kara_22'] = "";
            $row['last_kara_23'] = "";
            $row['last_kara_24'] = "";
            $row['last_kara_25'] = "";
            $row['last_kara_26'] = "";
            $row['last_kara_27'] = "";
            $row['last_kara_28'] = "";
            $row['last_kara_29'] = "";
            $row['last_kara_30'] = "";

            $row['last_kara_31'] = "";
            $row['last_kara_32'] = "";
            $row['last_kara_33'] = "";
            $row['last_kara_34'] = "";
            $row['last_kara_35'] = "";
            $row['last_kara_36'] = "";
            $row['last_kara_37'] = "";
            $row['last_kara_38'] = "";
            $row['last_kara_39'] = "";
            $row['last_kara_40'] = "";

            $row['last_kara_41'] = "";
            $row['last_kara_42'] = "";
            $row['last_kara_43'] = "";
            $row['last_kara_44'] = "";
            $row['last_kara_45'] = "";
            $row['last_kara_46'] = "";
            $row['last_kara_47'] = "";
            $row['last_kara_48'] = "";
            $row['last_kara_49'] = "";
            $row['last_kara_50'] = "";
            //============================ 50
            $row['last_kara_51'] = "";
            $row['last_kara_52'] = "";
            $row['last_kara_53'] = "";
            $row['last_kara_54'] = "";
            $row['last_kara_55'] = "";
            $row['last_kara_56'] = "";
            $row['last_kara_57'] = "";
            $row['last_kara_58'] = "";
            $row['last_kara_59'] = "";
            $row['last_kara_60'] = "";

            $row['last_kara_61'] = "";
            $row['last_kara_62'] = "";
            $row['last_kara_63'] = "";
            $row['last_kara_64'] = "";
            $row['last_kara_65'] = "";
            $row['last_kara_66'] = "";
            $row['last_kara_67'] = "";
            $row['last_kara_68'] = "";
            $row['last_kara_69'] = "";
            $row['last_kara_70'] = "";

            $row['last_kara_71'] = "";
            $row['last_kara_72'] = "";
            $row['last_kara_73'] = "";
            $row['last_kara_74'] = "";
            $row['last_kara_75'] = "";
            $row['last_kara_76'] = "";
            $row['last_kara_77'] = "";
            $row['last_kara_78'] = "";
            $row['last_kara_79'] = "";
            $row['last_kara_80'] = "";

            $row['last_kara_81'] = "";
            $row['last_kara_82'] = "";
            $row['last_kara_83'] = "";
            $row['last_kara_84'] = "";
            $row['last_kara_85'] = "";
            $row['last_kara_86'] = "";
            $row['last_kara_87'] = "";
            $row['last_kara_88'] = "";
            $row['last_kara_89'] = "";
            $row['last_kara_90'] = "";

            $row['last_kara_91'] = "";
            $row['last_kara_92'] = "";
            $row['last_kara_93'] = "";
            $row['last_kara_94'] = "";
            $row['last_kara_95'] = "";
            $row['last_kara_96'] = "";
            $row['last_kara_97'] = "";
            $row['last_kara_98'] = "";
            $row['last_kara_99'] = "";
            $row['last_kara_100'] = "";
            //=========================== 100
            $row['last_kara_101'] = "";
            $row['last_kara_102'] = "";
            $row['last_kara_103'] = "";
            $row['last_kara_104'] = "";
            $row['last_kara_105'] = "";
            $row['last_kara_106'] = "";
            $row['last_kara_107'] = "";
            $row['last_kara_108'] = "";
            $row['last_kara_109'] = "";
            $row['last_kara_110'] = "";

            $row['last_kara_111'] = "";
            $row['last_kara_112'] = "";
            $row['last_kara_113'] = "";
            $row['last_kara_114'] = "";
            $row['last_kara_115'] = "";
            $row['last_kara_116'] = "";
            $row['last_kara_117'] = "";
            $row['last_kara_118'] = "";
            $row['last_kara_119'] = "";
            $row['last_kara_120'] = "";

            $row['last_kara_121'] = "";
            $row['last_kara_122'] = "";
            $row['last_kara_123'] = "";
            $row['last_kara_124'] = "";
            $row['last_kara_125'] = "";
            $row['last_kara_126'] = "";
            $row['last_kara_127'] = "";
            $row['last_kara_128'] = "";
            $row['last_kara_129'] = "";
            $row['last_kara_130'] = "";

            $row['last_kara_131'] = "";
            $row['last_kara_132'] = "";
            $row['last_kara_133'] = "";
            $row['last_kara_134'] = "";
            $row['last_kara_135'] = "";
            $row['last_kara_136'] = "";
            $row['last_kara_137'] = "";
            $row['last_kara_138'] = "";
            $row['last_kara_139'] = "";
            $row['last_kara_140'] = "";

            $row['last_kara_141'] = "";
            $row['last_kara_142'] = "";
            $row['last_kara_143'] = "";
            $row['last_kara_144'] = "";
            $row['last_kara_145'] = "";
            $row['last_kara_146'] = "";
            $row['last_kara_147'] = "";
            $row['last_kara_148'] = "";
            $row['last_kara_149'] = "";
            $row['last_kara_150'] = "";
            //============================= 150
            $row['last_kara_151'] = "";
            $row['last_kara_152'] = "";
            $row['last_kara_153'] = "";
            $row['last_kara_154'] = "";
            $row['last_kara_155'] = "";
            $row['last_kara_156'] = "";
            $row['last_kara_157'] = "";
            $row['last_kara_158'] = "";
            $row['last_kara_159'] = "";
            $row['last_kara_160'] = "";

            $row['last_kara_161'] = "";
            $row['last_kara_162'] = "";
            $row['last_kara_163'] = "";
            $row['last_kara_164'] = "";

            // ==============================================================
            // ====================================== 配列　挿入
            // ==============================================================
            $retunr_excel[] = array(

              'employee_id' => $row['employee_id'], // 項目 1 employee_id 社員番号
              'user_name' => $row['user_name'],   // 項目 2 社員氏名
              'furi_name' => $row['furi_name'],  // 項目 3  フリガナ
              'sex' => $row['sex'], // 項目 4  性別
              'wareki_r' => $wareki_r,  // 項目 5 生年月日（和暦）

              'Seireki_r' => $Seireki_r, // 項目 6 生年月日（西暦）
              'koumoku_01' => $row['koumoku_01'], // 項目7:入社区分
              'koumoku_02' => $row['koumoku_02'], // 項目8:入社年月日
              'koumoku_03' => $row['koumoku_03'], // 項目9:入社年月日（西暦）
              'koumoku_04' => $row['koumoku_04'], // 項目10:勤続年数

              'koumoku_05' => $row['koumoku_05'], // 項目11 勤続年数_月数
              'syasin_K' => $syasin_K, // 項目 12 社員区分
              'koumoku_06' => $row['koumoku_06'], // 項目13:役職 　（空）
              'koumoku_07' => $row['koumoku_07'], // 項目14:学歴: （空）
              'syozoku_b' => $row['syozoku_b'], //  項目15:所属部門  

              'koumoku_08' => $row['koumoku_08'], // 項目16 : 職種
              'zip' => $row['zip'],   // 項目17　郵便番号
              'address_01' => $row['address_01'],  // 項目18 住所　１
              'koumoku_09' => $row['koumoku_09'],  // 項目19:住所２: （空）
              'koumoku_10' => $row['koumoku_10'],  // 項目20:住所（フリガナ）１: （空）

              'koumoku_11' => $row['koumoku_11'],  // 項目21:住所（フリガナ）２: （空）
              'tel' => $row['tel'],  // 項目22:電話番号:090-1111-2222
              'email' => $row['email'], // 項目23:メールアドレス
              'koumoku_12' => $row['koumoku_12'], // 項目24:障害区分:非該当
              'koumoku_13' => $row['koumoku_13'], // 項目25:寡婦:非該当

              'koumoku_14' => $row['koumoku_14'], // 項目26:寡夫:非該当
              'koumoku_15' => $row['koumoku_15'], // 項目27:勤労学生:非該当
              'koumoku_16' => $row['koumoku_16'], // 項目28:災害者:非該当
              'koumoku_17' => $row['koumoku_17'], // 項目29:外国人:非該当
              'koumoku_18' => $row['koumoku_18'], // 項目30:退職理由: （空）
              // 30 ?
              'koumoku_19' => $row['koumoku_19'], // 項目31:退職年月日: （空）
              'koumoku_20' => $row['koumoku_20'], // 項目32:退職年月日（西暦）: （空）
              'koumoku_21' => $row['koumoku_21'], // 項目33:給与区分:月給
              'koumoku_22' => $row['koumoku_22'], // 項目34:税表区分:月額甲欄
              'koumoku_23' => $row['koumoku_23'], // 項目35:年末調整対象:する
              // 35
              'koumoku_24' => $row['koumoku_24'], // 項目36:支払形態:振込
              'koumoku_25' => $row['koumoku_25'], // 項目37:支給日パターン:サンプル
              'koumoku_26' => $row['koumoku_26'], // 項目38:給与明細書パターン:社員パターン
              'koumoku_27' => $row['koumoku_27'], // 項目39:賞与明細書パターン:賞与サンプル
              'koumoku_28' => $row['koumoku_28'], // 項目40:健康保険対象: （空）
              // 40
              'koumoku_29' => $row['koumoku_29'], // 項目41:介護保険対象: （空）
              'koumoku_30' => $row['koumoku_30'], // 項目42:健保月額（千円）:220
              'koumoku_31' => $row['koumoku_31'], // 項目43:健保証番号（協会）:367
              'koumoku_32' => $row['koumoku_32'], // 項目44:健保証番号（組合）: （空）
              'koumoku_33' => $row['koumoku_33'], // 項目45:健保・厚年　資格取得年月日: （空）
              // 45
              'koumoku_34' => $row['koumoku_34'], // 項目46:厚生年金対象: （空）
              'koumoku_35' => $row['koumoku_35'], // 項目47:70歳以上被用者:非該当

              // $hokensya_K 変数格納
              'hokensya_K' => $hokensya_K, // 項目48:保険者種別:女子

              'koumoku_37' => $row['koumoku_36'], // 項目49:厚年月額（千円）: （空）
              'koumoku_38' => $row['koumoku_37'], // 項目50:基礎年金番号１: （空）
              // 50
              'koumoku_39' => $row['koumoku_39'], // 項目51:基礎年金番号２:対象
              'koumoku_40' => $row['koumoku_40'], // 項目52:短時間労働者（3/4未満）:対象外
              'koumoku_41' => $row['koumoku_41'], // 項目53:労災保険対象:対象
              'koumoku_42' => $row['koumoku_42'], // 項目54:雇用保険対象:対象
              'koumoku_43' => $row['koumoku_43'], // 項目55:労働保険用区分:常用
              // 55
              'koumoku_44' => $row['koumoku_44'], // 項目56:雇用保険被保険者番号１:5029
              'koumoku_45' => $row['koumoku_45'], // 項目57:雇用保険被保険者番号２:530842
              'koumoku_46' => $row['koumoku_46'], // 項目58:雇用保険被保険者番号３:4
              'koumoku_47' => $row['koumoku_47'], // 項目59:労働保険　資格取得年月日: （空）
              'spouse' => $row['spouse'], // 項目60:配偶者:　フォームから値取得
              // 60
              'koumoku_49' => $row['koumoku_49'], // 項目61:配偶者氏名: （空）
              'koumoku_50' => $row['koumoku_50'], // 項目62:配偶者フリガナ: （空）
              'koumoku_51' => $row['koumoku_51'], // 項目63:配偶者性別: （空）
              'koumoku_52' => $row['koumoku_52'], // 項目64:配偶者生年月日: （空）
              'koumoku_53' => $row['koumoku_53'], // 項目65:配偶者生年月日（西暦）: （空）
              // 65
              'koumoku_54' => $row['koumoku_54'], // 項目66:源泉控除対象配偶者:非該当
              'koumoku_55' => $row['koumoku_55'], // 項目67:配偶者老人:非該当
              'koumoku_56' => $row['koumoku_56'], // 項目68:配偶者障害者:非該当
              'koumoku_57' => $row['koumoku_57'], // 項目69:配偶者同居:非同居
              'koumoku_58' => $row['koumoku_58'], // 項目70:配偶者非居住者:非該当

              // 項目71:扶養控除対象人数:  フォームから取得
              'dependents_num' => $row['dependents_num'],

              // 165
              'last_kara_00' => $row['last_kara_0'],
              'last_kara_01' => $row['last_kara_1'],
              'last_kara_02' => $row['last_kara_2'],
              'last_kara_03' => $row['last_kara_3'],
              'last_kara_04' => $row['last_kara_4'],
              'last_kara_05' => $row['last_kara_5'],
              'last_kara_06' => $row['last_kara_6'],
              'last_kara_07' => $row['last_kara_7'],
              'last_kara_08' => $row['last_kara_8'],
              'last_kara_09' => $row['last_kara_9'],
              'last_kara_10' => $row['last_kara_10'],
              'last_kara_11' => $row['last_kara_11'],
              'last_kara_12' => $row['last_kara_12'],
              'last_kara_13' => $row['last_kara_13'],
              'last_kara_14' => $row['last_kara_14'],
              'last_kara_15' => $row['last_kara_15'],
              'last_kara_16' => $row['last_kara_16'],
              'last_kara_17' => $row['last_kara_17'],
              'last_kara_18' => $row['last_kara_18'],
              'last_kara_19' => $row['last_kara_19'],
              'last_kara_20' => $row['last_kara_20'],
              'last_kara_21' => $row['last_kara_21'],
              'last_kara_22' => $row['last_kara_22'],
              'last_kara_23' => $row['last_kara_23'],
              'last_kara_24' => $row['last_kara_24'],
              'last_kara_25' => $row['last_kara_25'],
              'last_kara_26' => $row['last_kara_26'],
              'last_kara_27' => $row['last_kara_27'],
              'last_kara_28' => $row['last_kara_28'],
              'last_kara_29' => $row['last_kara_29'],
              'last_kara_30' => $row['last_kara_30'],
              'last_kara_31' => $row['last_kara_31'],
              'last_kara_32' => $row['last_kara_32'],
              'last_kara_33' => $row['last_kara_33'],
              'last_kara_34' => $row['last_kara_34'],
              'last_kara_35' => $row['last_kara_35'],
              'last_kara_36' => $row['last_kara_36'],
              'last_kara_37' => $row['last_kara_37'],
              'last_kara_38' => $row['last_kara_38'],
              'last_kara_39' => $row['last_kara_39'],
              'last_kara_40' => $row['last_kara_40'],
              'last_kara_41' => $row['last_kara_41'],
              'last_kara_42' => $row['last_kara_42'],
              'last_kara_43' => $row['last_kara_43'],
              'last_kara_44' => $row['last_kara_44'],
              'last_kara_45' => $row['last_kara_45'],
              'last_kara_46' => $row['last_kara_46'],
              'last_kara_47' => $row['last_kara_47'],
              'last_kara_48' => $row['last_kara_48'],
              'last_kara_49' => $row['last_kara_49'],
              'last_kara_50' => $row['last_kara_50'],
              'last_kara_51' => $row['last_kara_51'],
              'last_kara_52' => $row['last_kara_52'],
              'last_kara_53' => $row['last_kara_53'],
              'last_kara_54' => $row['last_kara_54'],
              'last_kara_55' => $row['last_kara_55'],
              'last_kara_56' => $row['last_kara_56'],
              'last_kara_57' => $row['last_kara_57'],
              'last_kara_58' => $row['last_kara_58'],
              'last_kara_59' => $row['last_kara_59'],
              'last_kara_60' => $row['last_kara_60'],
              'last_kara_61' => $row['last_kara_61'],
              'last_kara_62' => $row['last_kara_62'],
              'last_kara_63' => $row['last_kara_63'],
              'last_kara_64' => $row['last_kara_64'],
              'last_kara_65' => $row['last_kara_65'],
              'last_kara_66' => $row['last_kara_66'],
              'last_kara_67' => $row['last_kara_67'],
              'last_kara_68' => $row['last_kara_68'],
              'last_kara_69' => $row['last_kara_69'],
              'last_kara_70' => $row['last_kara_70'],
              'last_kara_71' => $row['last_kara_71'],
              'last_kara_72' => $row['last_kara_72'],
              'last_kara_73' => $row['last_kara_73'],
              'last_kara_74' => $row['last_kara_74'],
              'last_kara_75' => $row['last_kara_75'],
              'last_kara_76' => $row['last_kara_76'],
              'last_kara_77' => $row['last_kara_77'],
              'last_kara_78' => $row['last_kara_78'],
              'last_kara_79' => $row['last_kara_79'],
              'last_kara_80' => $row['last_kara_80'],
              'last_kara_81' => $row['last_kara_81'],
              'last_kara_82' => $row['last_kara_82'],
              'last_kara_83' => $row['last_kara_83'],
              'last_kara_84' => $row['last_kara_84'],
              'last_kara_85' => $row['last_kara_85'],
              'last_kara_86' => $row['last_kara_86'],
              'last_kara_87' => $row['last_kara_87'],
              'last_kara_88' => $row['last_kara_88'],
              'last_kara_89' => $row['last_kara_89'],
              'last_kara_90' => $row['last_kara_90'],
              'last_kara_91' => $row['last_kara_91'],
              'last_kara_92' => $row['last_kara_92'],
              'last_kara_93' => $row['last_kara_93'],
              'last_kara_94' => $row['last_kara_94'],
              'last_kara_95' => $row['last_kara_95'],
              'last_kara_96' => $row['last_kara_96'],
              'last_kara_97' => $row['last_kara_97'],
              'last_kara_98' => $row['last_kara_98'],
              'last_kara_99' => $row['last_kara_99'],
              'last_kara_100' => $row['last_kara_100'],
              'last_kara_101' => $row['last_kara_101'],
              'last_kara_102' => $row['last_kara_102'],
              'last_kara_103' => $row['last_kara_103'],
              'last_kara_104' => $row['last_kara_104'],
              'last_kara_105' => $row['last_kara_105'],
              'last_kara_106' => $row['last_kara_106'],
              'last_kara_107' => $row['last_kara_107'],
              'last_kara_108' => $row['last_kara_108'],
              'last_kara_109' => $row['last_kara_109'],
              'last_kara_110' => $row['last_kara_110'],
              'last_kara_111' => $row['last_kara_111'],
              'last_kara_112' => $row['last_kara_112'],
              'last_kara_113' => $row['last_kara_113'],
              'last_kara_114' => $row['last_kara_114'],
              'last_kara_115' => $row['last_kara_115'],
              'last_kara_116' => $row['last_kara_116'],
              'last_kara_117' => $row['last_kara_117'],
              'last_kara_118' => $row['last_kara_118'],
              'last_kara_119' => $row['last_kara_119'],
              'last_kara_120' => $row['last_kara_120'],
              'last_kara_121' => $row['last_kara_121'],
              'last_kara_122' => $row['last_kara_122'],
              'last_kara_123' => $row['last_kara_123'],
              'last_kara_124' => $row['last_kara_124'],
              'last_kara_125' => $row['last_kara_125'],
              'last_kara_126' => $row['last_kara_126'],
              'last_kara_127' => $row['last_kara_127'],
              'last_kara_128' => $row['last_kara_128'],
              'last_kara_129' => $row['last_kara_129'],
              'last_kara_130' => $row['last_kara_130'],
              'last_kara_131' => $row['last_kara_131'],
              'last_kara_132' => $row['last_kara_132'],
              'last_kara_133' => $row['last_kara_133'],
              'last_kara_134' => $row['last_kara_134'],
              'last_kara_135' => $row['last_kara_135'],
              'last_kara_136' => $row['last_kara_136'],
              'last_kara_137' => $row['last_kara_137'],
              'last_kara_138' => $row['last_kara_138'],
              'last_kara_139' => $row['last_kara_139'],
              'last_kara_140' => $row['last_kara_140'],
              'last_kara_141' => $row['last_kara_141'],
              'last_kara_142' => $row['last_kara_142'],
              'last_kara_143' => $row['last_kara_143'],
              'last_kara_144' => $row['last_kara_144'],
              'last_kara_145' => $row['last_kara_145'],
              'last_kara_146' => $row['last_kara_146'],
              'last_kara_147' => $row['last_kara_147'],
              'last_kara_148' => $row['last_kara_148'],
              'last_kara_149' => $row['last_kara_149'],
              'last_kara_150' => $row['last_kara_150'],
              'last_kara_151' => $row['last_kara_151'],
              'last_kara_152' => $row['last_kara_152'],
              'last_kara_153' => $row['last_kara_153'],
              'last_kara_154' => $row['last_kara_154'],
              'last_kara_155' => $row['last_kara_155'],
              'last_kara_156' => $row['last_kara_156'],
              'last_kara_157' => $row['last_kara_157'],
              'last_kara_158' => $row['last_kara_158'],
              'last_kara_159' => $row['last_kara_159'],
              'last_kara_160' => $row['last_kara_160'],
              'last_kara_161' => $row['last_kara_161'],
              'last_kara_162' => $row['last_kara_162'],
              'last_kara_163' => $row['last_kara_163'],
              'last_kara_164' => $row['last_kara_164'],


            );
          }


          if (!empty($retunr_excel)) {

            $excel_file_err = 0;

            // Spreadsheetオブジェクト生成
            $objSpreadsheet = new Spreadsheet();
            // シート設定
            $objSheet = $objSpreadsheet->getActiveSheet();

            // Spreadsheetオブジェクト生成
            $objSpreadsheet = new Spreadsheet();
            // シート設定
            $objSheet = $objSpreadsheet->getActiveSheet();

            // ヘッダー項目
            // 横方向の配列データ
            $arrX = array(
              '社員番号', // 項目0
              '社員氏名', // 項目1
              'フリガナ', // 項目2
              '性別', // 項目3
              '生年月日', // 項目4
              '生年月日（西暦）', // 項目5
              '入社区分', // 項目6
              '入社年月日', // 項目7
              '入社年月日（西暦）', // 項目8
              '勤続年数', // 項目9
              '勤続年数_月数', // 項目10
              '社員区分', // 項目11
              '役職', // 項目12
              '学歴', // 項目13
              '所属部門', // 項目14
              '職種', // 項目15
              '郵便番号', // 項目16
              '住所１', // 項目17
              '住所２', // 項目18
              '住所（フリガナ）１', // 項目19
              '住所（フリガナ）２', // 項目20
              '電話番号', // 項目21
              'メールアドレス', // 項目22
              '障害区分', // 項目23
              '寡婦', // 項目24
              '寡夫', // 項目25
              '勤労学生', // 項目26
              '災害者', // 項目27
              '外国人', // 項目28
              '退職理由', // 項目29
              '退職年月日', // 項目30
              '退職年月日（西暦）', // 項目31
              '給与区分', // 項目32
              '税表区分', // 項目33
              '年末調整対象', // 項目34
              '支払形態', // 項目35
              '支給日パターン', // 項目36
              '給与明細書パターン', // 項目37
              '賞与明細書パターン', // 項目38
              '健康保険対象', // 項目39
              '介護保険対象', // 項目40
              '健保月額（千円）', // 項目41
              '健保証番号（協会）', // 項目42
              '健保証番号（組合）', // 項目43
              '健保・厚年　資格取得年月日', // 項目44
              '厚生年金対象', // 項目45
              '70歳以上被用者', // 項目46
              '保険者種別', // 項目47
              '厚年月額（千円）', // 項目48
              '基礎年金番号１', // 項目49
              '基礎年金番号２', // 項目50
              '短時間労働者（3/4未満）', // 項目51
              '労災保険対象', // 項目52
              '雇用保険対象', // 項目53
              '労働保険用区分', // 項目54
              '雇用保険被保険者番号１', // 項目55
              '雇用保険被保険者番号２', // 項目56
              '雇用保険被保険者番号３', // 項目57
              '労働保険　資格取得年月日', // 項目58
              '配偶者', // 項目59
              '配偶者氏名', // 項目60
              '配偶者フリガナ', // 項目61
              '配偶者性別', // 項目62
              '配偶者生年月日', // 項目63
              '配偶者生年月日（西暦）', // 項目64
              '源泉控除対象配偶者', // 項目65
              '配偶者老人', // 項目66
              '配偶者障害者', // 項目67
              '配偶者同居', // 項目68
              '配偶者非居住者', // 項目69
              '扶養控除対象人数', // 項目70
              '扶養親族_扶養親族名_1', // 項目71
              '扶養親族_フリガナ_1', // 項目72
              '扶養親族_続柄_1', // 項目73
              '扶養親族_生年月日_1', // 項目74
              '扶養親族_生年月日（西暦）_1', // 項目75
              '扶養親族_扶養区分_1', // 項目76
              '扶養親族_障害者区分_1', // 項目77
              '扶養親族_同居区分_1', // 項目78
              '扶養親族_同居老親等区分_1', // 項目79
              '扶養親族_非居住者区分_1', // 項目80
              '扶養親族_控除計算区分_1', // 項目81
              '扶養親族_扶養親族名_2', // 項目82
              '扶養親族_フリガナ_2', // 項目83
              '扶養親族_続柄_2', // 項目84
              '扶養親族_生年月日_2', // 項目85
              '扶養親族_生年月日（西暦）_2', // 項目86
              '扶養親族_扶養区分_2', // 項目87
              '扶養親族_障害者区分_2', // 項目88
              '扶養親族_同居区分_2', // 項目89
              '扶養親族_同居老親等区分_2', // 項目90
              '扶養親族_非居住者区分_2', // 項目91
              '扶養親族_控除計算区分_2', // 項目92
              '扶養親族_扶養親族名_3', // 項目93
              '扶養親族_フリガナ_3', // 項目94
              '扶養親族_続柄_3', // 項目95
              '扶養親族_生年月日_3', // 項目96
              '扶養親族_生年月日（西暦）_3', // 項目97
              '扶養親族_扶養区分_3', // 項目98
              '扶養親族_障害者区分_3', // 項目99
              '扶養親族_同居区分_3', // 項目100
              '扶養親族_同居老親等区分_3', // 項目101
              '扶養親族_非居住者区分_3', // 項目102
              '扶養親族_控除計算区分_3', // 項目103
              '扶養親族_扶養親族名_4', // 項目104
              '扶養親族_フリガナ_4', // 項目105
              '扶養親族_続柄_4', // 項目106
              '扶養親族_生年月日_4', // 項目107
              '扶養親族_生年月日（西暦）_4', // 項目108
              '扶養親族_扶養区分_4', // 項目109
              '扶養親族_障害者区分_4', // 項目110
              '扶養親族_同居区分_4', // 項目111
              '扶養親族_同居老親等区分_4', // 項目112
              '扶養親族_非居住者区分_4', // 項目113
              '扶養親族_控除計算区分_4', // 項目114
              '扶養親族_扶養親族名_5', // 項目115
              '扶養親族_フリガナ_5', // 項目116
              '扶養親族_続柄_5', // 項目117
              '扶養親族_生年月日_5', // 項目118
              '扶養親族_生年月日（西暦）_5', // 項目119
              '扶養親族_扶養区分_5', // 項目120
              '扶養親族_障害者区分_5', // 項目121
              '扶養親族_同居区分_5', // 項目122
              '扶養親族_同居老親等区分_5', // 項目123
              '扶養親族_非居住者区分_5', // 項目124
              '扶養親族_控除計算区分_5', // 項目125
              '扶養親族_扶養親族名_6', // 項目126
              '扶養親族_フリガナ_6', // 項目127
              '扶養親族_続柄_6', // 項目128
              '扶養親族_生年月日_6', // 項目129
              '扶養親族_生年月日（西暦）_6', // 項目130
              '扶養親族_扶養区分_6', // 項目131
              '扶養親族_障害者区分_6', // 項目132
              '扶養親族_同居区分_6', // 項目133
              '扶養親族_同居老親等区分_6', // 項目134
              '扶養親族_非居住者区分_6', // 項目135
              '扶養親族_控除計算区分_6', // 項目136
              '扶養親族_扶養親族名_7', // 項目137
              '扶養親族_フリガナ_7', // 項目138
              '扶養親族_続柄_7', // 項目139
              '扶養親族_生年月日_7', // 項目140
              '扶養親族_生年月日（西暦）_7', // 項目141
              '扶養親族_扶養区分_7', // 項目142
              '扶養親族_障害者区分_7', // 項目143
              '扶養親族_同居区分_7', // 項目144
              '扶養親族_同居老親等区分_7', // 項目145
              '扶養親族_非居住者区分_7', // 項目146
              '扶養親族_控除計算区分_7', // 項目147
              '扶養親族_扶養親族名_8', // 項目148
              '扶養親族_フリガナ_8', // 項目149
              '扶養親族_続柄_8', // 項目150
              '扶養親族_生年月日_8', // 項目151
              '扶養親族_生年月日（西暦）_8', // 項目152
              '扶養親族_扶養区分_8', // 項目153
              '扶養親族_障害者区分_8', // 項目154
              '扶養親族_同居区分_8', // 項目155
              '扶養親族_同居老親等区分_8', // 項目156
              '扶養親族_非居住者区分_8', // 項目157
              '扶養親族_控除計算区分_8', // 項目158
              '扶養親族_扶養親族名_9', // 項目159
              '扶養親族_フリガナ_9', // 項目160
              '扶養親族_続柄_9', // 項目161
              '扶養親族_生年月日_9', // 項目162
              '扶養親族_生年月日（西暦）_9', // 項目163
              '扶養親族_扶養区分_9', // 項目164
              '扶養親族_障害者区分_9', // 項目165
              '扶養親族_同居区分_9', // 項目166
              '扶養親族_同居老親等区分_9', // 項目167
              '扶養親族_非居住者区分_9', // 項目168
              '扶養親族_控除計算区分_9', // 項目169
              '扶養親族_扶養親族名_10', // 項目170
              '扶養親族_フリガナ_10', // 項目171
              '扶養親族_続柄_10', // 項目172
              '扶養親族_生年月日_10', // 項目173
              '扶養親族_生年月日（西暦）_10', // 項目174
              '扶養親族_扶養区分_10', // 項目175
              '扶養親族_障害者区分_10', // 項目176
              '扶養親族_同居区分_10', // 項目177
              '扶養親族_同居老親等区分_10', // 項目178
              '扶養親族_非居住者区分_10', // 項目179
              '扶養親族_控除計算区分_10', // 項目180
              '扶養親族_扶養親族名_11', // 項目181
              '扶養親族_フリガナ_11', // 項目182
              '扶養親族_続柄_11', // 項目183
              '扶養親族_生年月日_11', // 項目184
              '扶養親族_生年月日（西暦）_11', // 項目185
              '扶養親族_扶養区分_11', // 項目186
              '扶養親族_障害者区分_11', // 項目187
              '扶養親族_同居区分_11', // 項目188
              '扶養親族_同居老親等区分_11', // 項目189
              '扶養親族_非居住者区分_11', // 項目190
              '扶養親族_控除計算区分_11', // 項目191
              '扶養親族_扶養親族名_12', // 項目192
              '扶養親族_フリガナ_12', // 項目193
              '扶養親族_続柄_12', // 項目194
              '扶養親族_生年月日_12', // 項目195
              '扶養親族_生年月日（西暦）_12', // 項目196
              '扶養親族_扶養区分_12', // 項目197
              '扶養親族_障害者区分_12', // 項目198
              '扶養親族_同居区分_12', // 項目199
              '扶養親族_同居老親等区分_12', // 項目200
              '扶養親族_非居住者区分_12', // 項目201
              '扶養親族_控除計算区分_12', // 項目202
              '扶養親族_扶養親族名_13', // 項目203
              '扶養親族_フリガナ_13', // 項目204
              '扶養親族_続柄_13', // 項目205
              '扶養親族_生年月日_13', // 項目206
              '扶養親族_生年月日（西暦）_13', // 項目207
              '扶養親族_扶養区分_13', // 項目208
              '扶養親族_障害者区分_13', // 項目209
              '扶養親族_同居区分_13', // 項目210
              '扶養親族_同居老親等区分_13', // 項目211
              '扶養親族_非居住者区分_13', // 項目212
              '扶養親族_控除計算区分_13', // 項目213
              '扶養親族_扶養親族名_14', // 項目214
              '扶養親族_フリガナ_14', // 項目215
              '扶養親族_続柄_14', // 項目216
              '扶養親族_生年月日_14', // 項目217
              '扶養親族_生年月日（西暦）_14', // 項目218
              '扶養親族_扶養区分_14', // 項目219
              '扶養親族_障害者区分_14', // 項目220
              '扶養親族_同居区分_14', // 項目221
              '扶養親族_同居老親等区分_14', // 項目222
              '扶養親族_非居住者区分_14', // 項目223
              '扶養親族_控除計算区分_14', // 項目224
              '扶養親族_扶養親族名_15', // 項目225
              '扶養親族_フリガナ_15', // 項目226
              '扶養親族_続柄_15', // 項目227
              '扶養親族_生年月日_15', // 項目228
              '扶養親族_生年月日（西暦）_15', // 項目229
              '扶養親族_扶養区分_15', // 項目230
              '扶養親族_障害者区分_15', // 項目231
              '扶養親族_同居区分_15', // 項目232
              '扶養親族_同居老親等区分_15', // 項目233
              '扶養親族_非居住者区分_15', // 項目234
              '扶養親族_控除計算区分_15', // 項目235
            );

            $objSheet->fromArray(
              $arrX,      // 配列データ
              NULL,       // 配列データの中でセルに設定しないNULL値の指定
              'A1'        // 左上座標(デフォルト:"A1")
            );


            //　Excelファイルへ　データ挿入
            $objSheet->fromArray($retunr_excel, null, 'A2');

            // XLSX形式オブジェクト生成
            $objWriter = new Xlsx($objSpreadsheet);

            // ファイル書込み
            $objWriter->save($create_excel);

            //  exit();

            // ********* フラグ処理
            $csv_file_ok_FLG = "0"; // デフォルト

            $excel_file_FLG = "1"; // Excel　作成完了

          } else {

            // 指定した日付に該当の　Excelファイルがなかった場合
            //      $excel_file_err = 1;

            $excel_file_FLG = "2"; // Excel　エラー

          }
        } catch (PDOException $e) {

          //    print('Error:'.$e->getMessage());

          // (トランザクション) ロールバック
          //       $pdo->rollBack();

          // ************** エラー処理 ***************
          $csv_file_ok_FLG = "2";
        } finally {

          $pdo = null;
        } //======================================== END try ==================================

        // =========================================================================================== 
        // =========================================== 銀行用　CSV try ================================
        // ===========================================================================================
        // 絞り込み　結果　格納配列
        $retunr_excel_bank = [];
        //========================================
        try {

          // PDO オブジェクト
          $pdo = new PDO($dsn, $user, $password);

          // SQL
          $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
            WHERE creation_time BETWEEN ? AND ? ORDER BY user_id DESC");

          // CSV エクスポート 開始日
          $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

          // CSV エクスポート 終了日
          $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);

          // SQL 実行 
          $res = $stmt->execute();

          $idx = 0;

          // ファルダがなかったら、フォルダを作成  形式 files_20211025
          $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];

          //====== ファイルの保存
          // ファイル生成場所
          $create_excel_bank = $dirpath . "/" . $file_name_excel_bank;


          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            // ============= 0 パディング ===============
            $row['employee_id'] = sprintf('%06d', $row['employee_id']);
            $row['bank_code'] = sprintf('%04d', $row['bank_code']);
            $row['bank_siten_code'] = sprintf('%03d', $row['bank_siten_code']);

            // 預金種目  0:普通 , 1:当座 bank_kamoku
            if (strcmp($row['bank_kamoku'], "0") == 0) {
              $bank_kamoku = "普通";
            } else {
              $bank_kamoku = "当座";
            }

            // 支払元登録No
            $Siharai_M_Number = "1";

            // 支店フリガナ ※カラムなし $bank_siten_name_kana
            $bank_siten_name_kana = "ｼﾃﾝﾌﾘｶﾞﾅ";

            // 振込手数料 空欄
            //=== 空文字
            $row['koumoku_01'] = ""; // 空 項目 ,   // 振込手数料

            $teigaku_F = ""; // 定額振込

            //===  10 空欄 ===
            $row['koumoku_02'] = ""; // 空 項目 ,   //   振込先 1　金額,
            $row['koumoku_03'] = ""; // 空 項目 ,   // 振込先2　金融機関コード,
            $row['koumoku_04'] = ""; // 空 項目 ,   // 振込先2　金融機関名,
            $row['koumoku_05'] = ""; // 空 項目 ,   // 振込先2　金融機関フリガナ,
            $row['koumoku_06'] = ""; // 空 項目 ,   // 振込先2　支店コード,
            $row['koumoku_07'] = ""; // 空 項目 ,   // 振込先2  支店名,
            $row['koumoku_08'] = ""; // 空 項目 ,  振込先2   支店フリガナ
            $row['koumoku_09'] = ""; // 空 項目 ,   // 振込先2　預金種目,
            $row['koumoku_10'] = ""; // 空 項目 ,   // 振込先2　口座番号,
            $row['koumoku_11'] = ""; // 空 項目 ,   // 振込先2　振込手数料,
            $row['koumoku_12'] = ""; // 空 項目 ,   // 振込先2　定額振込,

            //====== ラストカラム 「金額」
            $last_kingaku = "";

            // ==============================================================
            // ====================================== 配列　挿入
            // ==============================================================
            $retunr_excel_bank[] = array(

              'employee_id' => $row['employee_id'],  //項目_1:::社員番号
              'user_name' => $row['user_name'],      //項目_2:::社員氏名
              'Siharai_M_Number' => $Siharai_M_Number, //項目_3:::支払元登録No
              'bank_code' => $row['bank_code'], //項目_4:::振込先①　金融機関コード
              'bank_name' => $row['bank_name'],  //項目_5:::振込先①　金融機関名
              'bank_name_kana' => $row['bank_name_kana'],  //項目_6:::振込先①　金融機関フリガナ
              'bank_siten_code' => $row['bank_siten_code'], //項目_7:::振込先①　支店コード
              'bank_siten_name' => $row['bank_siten_name'], //項目_8:::振込先①　支店名
              'bank_siten_name_kana' => $bank_siten_name_kana, //項目_9:::振込先①　支店フリガナ
              'bank_kamoku' => $bank_kamoku,  //項目_10:::振込先①　預金種目
              'kouzzz_number' => $row['kouzzz_number'], //項目_11:::振込先①　口座番号
              'koumoku_01' => $row['koumoku_01'],  //項目_12:::振込先①　振込手数料
              'teigaku_F' => $teigaku_F,            //項目_13:::振込先①　定額振込
              'koumoku_01' => $row['koumoku_01'],  //項目_14:::振込先①　金額
              'koumoku_02' => $row['koumoku_02'],  //項目_15:::振込先②　金融機関コード
              'koumoku_03' => $row['koumoku_03'],  //項目_16:::振込先②　金融機関名
              'koumoku_04' => $row['koumoku_04'],  //項目_17:::振込先②　金融機関フリガナ
              'koumoku_05' => $row['koumoku_05'],  //項目_18:::振込先②　支店コード
              'koumoku_06' => $row['koumoku_06'],   //項目_19:::振込先②　支店名
              'koumoku_07' => $row['koumoku_07'], //項目_20:::振込先②　支店フリガナ
              'koumoku_08' => $row['koumoku_08'],  //項目_21:::振込先②　預金種目
              'koumoku_09' => $row['koumoku_09'],  //項目_22:::振込先②　口座番号
              'koumoku_10' => $row['koumoku_10'], //項目_23:::振込先②　振込手数料
              'koumoku_11' => $row['koumoku_11'], //項目_24:::振込先②　定額振込
              'koumoku_12' => $row['koumoku_12'],  //項目_25:::振込先②　金額
              'last_kingaku' => $last_kingaku,  // 「金額」
            );
          }

          if (!empty($retunr_excel_bank)) {

            $excel_file_err = 0;

            // Spreadsheetオブジェクト生成
            $objSpreadsheet = new Spreadsheet();
            // シート設定
            $objSheet = $objSpreadsheet->getActiveSheet();

            // Spreadsheetオブジェクト生成
            $objSpreadsheet = new Spreadsheet();
            // シート設定
            $objSheet = $objSpreadsheet->getActiveSheet();

            // ヘッダー項目
            // 横方向の配列データ
            $arrX = array(
              '社員番号',
              '社員氏名',
              '支払元登録No',
              '振込先①　金融機関コード',
              '振込先①　金融機関名',
              '振込先①　金融機関フリガナ',
              '振込先①　支店コード',
              '振込先①　支店名',
              '振込先①　支店フリガナ',
              '振込先①　預金種目',
              '振込先①　口座番号',
              '振込先①　振込手数料',
              '振込先①　定額振込',
              '振込先①　金額',
              '振込先②　金融機関コード',
              '振込先②　金融機関名',
              '振込先②　金融機関フリガナ',
              '振込先②　支店コード',
              '振込先②　支店名',
              '振込先②　支店フリガナ',
              '振込先②　預金種目',
              '振込先②　口座番号',
              '振込先②　振込手数料',
              '振込先②　定額振込',
              '振込先②　金額'
            );

            $objSheet->fromArray(
              $arrX,      // 配列データ
              NULL,       // 配列データの中でセルに設定しないNULL値の指定
              'A1'        // 左上座標(デフォルト:"A1")
            );

            //　Excelファイルへ　データ挿入
            $objSheet->fromArray($retunr_excel_bank, null, 'A2');

            // XLSX形式オブジェクト生成
            $objWriter = new Xlsx($objSpreadsheet);

            // ファイル書込み
            $objWriter->save($create_excel_bank);

            //  exit();

            // ********* フラグ処理
            $csv_file_ok_FLG = "0"; // デフォルト

            $excel_file_FLG = "1"; // Excel　作成完了

          } else {

            // 指定した日付に該当の　Excelファイルがなかった場合
            //      $excel_file_err = 1;

            $excel_file_FLG = "2"; // Excel　エラー

          }
        } catch (PDOException $e) {

          // ************** エラー処理 ***************
          $csv_file_ok_FLG = "2";
        }

        // =========================================== ====================================================
        // =========================================== 銀行用　CSV try  END ================================
        // =========================================== ====================================================


        //==================================================================
        //================= アップデート 処理 データ処理フラグを データ作成済みにする
        //==================================================================

        try {

          // PDO オブジェクト
          $pdo = new PDO($dsn, $user, $password);

          $stmt = $pdo->prepare("UPDATE User_Info_Table SET data_Flg = '1' 
                  WHERE creation_time BETWEEN ? AND ?");

          // CSV エクスポート 開始日
          $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

          // CSV エクスポート 終了日
          $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);

          // SQL 実行 
          $res = $stmt->execute();
        } catch (PDOException $e) {

          // ************** エラー処理 ***************
          $csv_file_ok_FLG = "2";
        } finally {

          $pdo = null;

          // ページリロード
          header("Location: " . $_SERVER['PHP_SELF']);

          exit;
        }



        //==============================================
        //*************** ファイルのダウンロード処理 */
        //==============================================
        //  t_download($protocol);

        //===================================
        // =========== 現在のフルURL 取得
        //===================================

        /*
    if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {

      $protocol = 'https://';
    } else {

      $protocol = 'http://';
    }

    // ./files の . を削除
    $new_create_excel = mb_substr($create_excel, 1);

    $protocol .= $_SERVER["HTTP_HOST"] . '/job_recruit/csv' . $new_create_excel;
    //. $_SERVER["REQUEST_URI"];

    //      var_dump($new_create_excel);



    // ファイルタイプを指定
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    // ファイルタイプを指定

    // ファイルサイズを取得し、ダウンロードの進捗を表示
    //      header('Content-Length: '.filesize($protocol));
    // ファイルのダウンロード、リネームを指示
    header('Content-Disposition: attachment; filename="' . $file_name_excel . '"');

    ob_clean();  //追記
    flush();     //追記

    // ファイルを読み込みダウンロードを実行
    readfile($protocol);
    exit;
    */

        // ============= エラー処理 000   
        // ============= ※ 「選択してください」が選ばれていた時  



      }
    }
  } else if ($export_output === "0") {

    //  print "aaaaaa";

    // === 日付ボックスが　２つとも　空だった場合 *** エラー処理 ***
    $error = "日付指定が空です。どちらか日付を入力してください。";

    $export_output_err = "1";
  }
} //============== END download_btn  POST



?>


<html lang="ja">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">



  <head>

    <!-- jim style.css -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- jQuery デートピッカー -->
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!--CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />

    <!-- バリデーション用 -->
    <!--Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!--Font Awesome5-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <!-- font awsome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">

    <!--JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- 修正箇所1 titleセクションを呼び出す。デフォルト値は no titleとする -->
    <title>アルバイト募集サイト TOP　画面１</title>

    <style>
      /* Vue.js 用 */
      .v-enter-active,
      .v-leave-actibe {
        transition: opacity 1s;
      }

      .v-enter,
      .v-leave-to {
        opacity: 0;
        transition: opacity 1s;
      }

      /* jQuery デートピッカー */

      .ui-datepicker {
        width: 44em;
        padding: .2em .2em 0;
        display: none;
      }

      .ui-datepicker td span,
      .ui-datepicker td a {
        display: block;
        padding: .6em;
        text-align: right;
        text-decoration: none;
        font-size: .9em;
      }

      .dl_link>a {
        background: #007bff;
        color: #fff;
        padding: 1.4em 1.6em;
        font-size: 1.4em !important;
        display: block;
        transition: .33s;
      }

      .dl_link>a:hover {
        opacity: .55;
      }
    </style>


  </head>

<body>

  <div class="jumbotron">
    <div class="container">
      <h1 class="display-5 m_h1">株式会社　●●</h1>
      <p class="lead">
        <!-- アルバイト者情報登録 -->
        <?php site_name_hozyo(); ?>
      </p>
    </div>
  </div>

  <!-- ダウンロードページ　への　遷移 -->
  <div class="container" style="margin-bottom: 25px;">
    <div class="col-lg4 col-xs-10 col-md-4 dl_link">

      <a href="./download.php">
        ダウンロード ページへ移動
      </a>

    </div>
  </div> <!-- container END -->

  <div class="container maile_c" id="chk">
    <div class="row">

      <div class="col-lg-12 col-sm-12 col-12">

        <form action="csv_export.php" method="POST" id="csv_form" class="" novalidate enctype="multipart/form-data" autocomplete="off">


          <!-- 未出力フィル チェックボックス デフォルトチェック　なし あり => checked  -->
          <label class="ECM_CheckboxInput">

            <!-- 「未出力ファイル出力」 チェックボックス  -->
            <input class="ECM_CheckboxInput-Input" type="checkbox" name="uninput_file" value="1">


            <span class="ECM_CheckboxInput-DummyInput"></span>
            <span class="ECM_CheckboxInput-LabelText">未出力ファイル出力</span>
          </label>

          <div class="row">

            <div class="form-group was-validated col-lg-3 col-sm-3 col-md-3 col-xs-10" id="mail_div">
              <label for="date_target" class="font-weight-bold label_01">開始日を指定してください。</label>
              <input type="text" id="date_target" required class="form-control" name="date_target" placeholder="開始日">
            </div>

            <div class="form-group was-validated col-lg-3 col-sm-3 col-md-3 col-xs-10" id="mail_div">
              <label for="date_target_02" class="font-weight-bold label_01">終了日を指定してください。</label>
              <input type="text" id="date_target_02" required class="form-control" name="date_target_02" placeholder="終了日">
            </div>

            <!-- 表示　ボタン -->
            <div class="form-group col-lg-3 col-sm-3 col-md-3 col-xs-10 temp_pos" id="hyouzi_box">
              <button type="submit" name="view_btn" class="btn csv_btn e_btn font_a" id="h_btn">
                表示
              </button>
            </div>
            <!--/ 表示　ボタン END -->

            <!-- CSV エクスポート　ボタン -->
            <div class="form-group col-lg-3 col-sm-3 col-md-3 col-xs-10" id="file_box">


              <!-- 出力形式　選択 -->
              <select name="export_output" id="export" class="app_input_form date-birth-year jsc-select-bold">

                <option value="0" class="jsc-default-selected">選択</option>

                <option class="jsc-select-bold" value="csv">
                  CSV
                </option>

                <option class="jsc-select-bold" value="excel">
                  Excel
                </option>

              </select>


              <button type="submit" name="download_btn" class="btn csv_btn e_btn font_a">
                ファイル出力
              </button>



            </div>
            <!--/ CSV エクスポート END -->



          </div> <!-- row END -->

        </form>

      </div>

    </div>

    <!-- ============== 表示用 Table 開始 ===========  -->
    <div class="row">

      <table class="table table-hover" style="font-size: 14.5px !important;">
        <thead class="thead-dark">

          <tr>
            <th>名前</th>
            <th>ふりがな</th>
            <th>メールアドレス</th>
            <th>性別</th>
            <th>登録日</th>
            <th>ファイル出力</th>
          </tr>

        </thead>

        <!--  csv ファイル出力 表示 -->
        <?php if (!empty($return)) : ?>
          <?php foreach ($return as $r_item) : ?>
            <tr>
              <td> <?php print h($r_item['user_name']); ?> </td>
              <td> <?php print h($r_item['furi_name']); ?> </td>

              <td> <?php print h($r_item['email']); ?> </td>

              <td>
                <?php if ($r_item['sex'] == "0") : ?>
                  <p>男性</p>
                <?php else : ?>
                  <p>女性</p>
                <?php endif; ?>
              </td>

              <!-- 登録日 -->
              <td> <?php print h($r_item['creation_time']); ?></td>

              <!-- ======= ファイル出力　スタッツ　未出力：０  出力済み：１ ======= -->
              <?php if (strcmp($r_item['data_Flg'], "0") == 0) : ?>
                <td><i class="fas fa-window-close"></i><span class="mi_icon_ng">未出力</span></td>
              <?php else : ?>
                <td><i class="fas fa-check-square"></i><span class="mi_icon_ok">出力済み</span></td>
              <?php endif; ?>

            </tr>
          <?php endforeach; ?>

        <?php else : ?>

          <p></p>

        <?php endif; ?>

        <!-- Excel出力 -->
        <?php if (!empty($retunr_excel)) : ?>
          <?php foreach ($retunr_excel as $r_item) : ?>
            <tr>
              <td> <?php print h($r_item['user_name']); ?> </td>
              <td> <?php print h($r_item['furi_name']); ?> </td>

              <td> <?php print h($r_item['email']); ?> </td>

              <td>
                <?php if ($r_item['sex'] == "0") : ?>
                  <p>男性</p>
                <?php else : ?>
                  <p>女性</p>
                <?php endif; ?>
              </td>

              <!-- 登録日 -->
              <td> <?php print h($r_item['creation_time']); ?></td>

              <!-- ======= ファイル出力　スタッツ　未出力：０  出力済み：１ ======= -->
              <?php if (strcmp($r_item['data_Flg'], "0") === 0) : ?>
                <td><i class="fas fa-window-close"></i><span class="mi_icon_ng">未出力</span></td>
              <?php else : ?>
                <td><i class="fas fa-check-square"></i><span class="mi_icon_ok">出力済み</span></td>
              <?php endif; ?>

            </tr>
          <?php endforeach; ?>

        <?php else : ?>

          <p></p>

        <?php endif; ?>


        <!-- 未出力ファイル 出力 -->
        <?php if (!empty($retunr_output_excel)) : ?>
          <?php foreach ($retunr_output_excel as $r_item) : ?>
            <tr>
              <td> <?php print h($r_item['user_name']); ?> </td>
              <td> <?php print h($r_item['furi_name']); ?> </td>

              <td> <?php print h($r_item['email']); ?> </td>

              <td>
                <?php if ($r_item['sex'] == "0") : ?>
                  <p>男性</p>
                <?php else : ?>
                  <p>女性</p>
                <?php endif; ?>
              </td>

              <!-- 登録日 -->
              <td> <?php print h($r_item['creation_time']); ?></td>

              <!-- ======= ファイル出力　スタッツ　未出力：０  出力済み：１ ======= -->
              <?php if (strcmp($r_item['data_Flg'], "0") === 0) : ?>
                <td><i class="fas fa-window-close"></i><span class="mi_icon_ng">未出力</span></td>
              <?php else : ?>
                <td><i class="fas fa-check-square"></i><span class="mi_icon_ok">出力済み</span></td>
              <?php endif; ?>

            </tr>
          <?php endforeach; ?>

        <?php else : ?>

          <p></p>

        <?php endif; ?>



        <!-- 表示　ボタン処理　エラー -->
        <?php if (empty($display_err)) : ?>

        <?php else : ?>
          <div class="alert alert-danger" role="alert"><?php print(h($display_err)); ?></div>

        <?php endif; ?>

        <!-- CSV 出力　ボタンエラー処理 （日付ボックスが空だから） -->
        <?php if (empty($error)) : ?>

          <!-- ok 処理 -->

        <?php else : ?>

          <div class="alert alert-danger" role="alert">
            [CSV 出力エラー] 日付ボックスを入力してください。<br />
          </div>

        <?php endif; ?>

        <!-- CSV ファイル作成が完了した場合  1, ダメだったら 0 -->
        <?php if (strcmp($csv_file_ok_FLG, "1") == 0) : ?>

          <div class="alert alert-success font_1" role="alert">
            <i class="fas fa-check-circle"></i>
            <span class="ok_text">
              CSVファイルの作成完了しました。
            </span>
          </div>

        <?php elseif (strcmp($csv_file_ok_FLG, "2") == 0) : ?>
          <div class="alert alert-danger font_1" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="ng_text">CSVファイルの作成が失敗しました。</span>
            <br />
          </div>

        <?php else : ?>


        <?php endif; ?>

        <!-- Excelファイル　出力結果処理 -->
        <?php if (strcmp($excel_file_FLG, "1") == 0) : ?>

          <div class="alert alert-success font_1" role="alert">
            <i class="fas fa-check-circle"></i>
            <span class="ok_text">
              Excel ファイルの作成完了しました。
            </span>
          </div>

        <?php elseif (strcmp($excel_file_FLG, "2") == 0) : ?>

          <div class="alert alert-danger font_1" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="ng_text">CSVファイルの作成が失敗しました。</span>
            <br />
          </div>

        <?php else : ?>

        <?php endif; ?>

        <!-- エラー処理 Excelファイル　出力が　空の場合 -->
        <?php if (strcmp($excel_file_FLG, "2") == 0) : ?>

          <div class="alert alert-danger font_1" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="ng_text">選択した日付には、ファイルがありません。</span>
            <br />
          </div>


        <?php else : ?>


        <?php endif; ?>



        <!-- SQL 部分　CSV 出力　エラー（日付ボックスが空だから） -->
        <?php if (empty($export_err)) : ?>

        <?php else : ?>
          <p><?php print(h($export_err)); ?><br /></p>
        <?php endif; ?>

        <!-- 出力形式が選択されていない場合のエラー処理 -->
        <?php if (strcmp($export_output_err, "1") == 0) : ?>

          <div class="alert alert-danger" role="alert">
            出力形式を選択してください。
          </div>

        <?php else : ?>


        <?php endif; ?>

      </table>

    </div>


  </div> <!-- container END -->


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
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

  <!-- jQuery デートピッカー -->
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <!-- jQuery UI -->
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>


  <!-- jQueru デートピッカー　動作部分 -->
  <script>
    //====== 日付選択部分　前
    $(function() {
      $('#date_target').datepicker({
        defaultDate: new Date(), // デフォルト年月日  
        changeYear: true, // 年を表示
        changeMonth: true, // 月を選択
        maxDate: '+1y +6m', // 1年半後まで選択可
        minDate: '-1y -6m', // 1年半前まで選択可 

      });
    });

    //====== 日付選択部分　後ろ
    $(function() {
      $('#date_target_02').datepicker({
        defaultDate: new Date(), // デフォルト年月日  
        changeYear: true, // 年を表示
        changeMonth: true, // 月を選択
        maxDate: '+1y +6m', // 1年半後まで選択可
        minDate: '-1y -6m', // 1年半前まで選択可 

      });
    });
  </script>




</body>

</html>