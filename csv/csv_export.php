<?php


// �G���[���o�͂���
ini_set('display_errors', "On");
ini_set("memory_limit", "3072M");

// functions.php �ǂݍ���
require(dirname(__FILE__) . '/../functions.php');

require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// �G���[�y�[�W�f�B���N�g��
$kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

//=============== �ϐ��@��`
$date_target = "";
$date_target_02 = "";

//  ���̓��t�{�b�N�X�����@���͂��� => 1
//  �E�̓��t�{�b�N�X�����@���͂��� => 2
//  �����̃{�b�N�X�@���͂��� => 3
//  �����̃{�b�N�X�@���͂Ȃ� => 0 
$csv_FLG = "0";
$csv_file_ok_FLG = "0";

$excel_file_FLG = "0"; //  0 => �f�t�H���g�����Ȃ� , 1 => �o�� OK , 2 �G���[

//====== ���t�@�I��p �i�[�@�z��
$arr_user_name = []; // ���O
$arr_furi_name = []; // �ӂ肪��
$arr_age = []; // �N��
$arr_email = []; // ���[���A�h���X
$arr_sex = []; // ����

$arr_get_test = [];

$file_name = "";

$csv_file_ok = "";

$export_output = "";

$export_output_err = "0";

//==== excel�@file �G���[�Ǘ��t���O
$excel_file_err = 0;

// �����̓t�@�C����checkbox�Ƀ`�F�b�N�������Ă��邩�A���Ȃ����B
$check_box_FLG = "";

$h_check_box_FLG = "";

//  $arr_user_name = "";

// =========================== ���t�擾�@�N���X
class get_now_date
{

  function get_to_date()
  {

    //====================== ���ݎ����̎擾
    $n_year = "";
    $n_month = "";
    $n_day = "";

    $n_hour = "";
    $n_minute = "";
    $n_second = "";

    $now = new DateTime();

    $now_tm = $now->format('Y-m-d H:i:s');

    //======== ���t�� - �󔒁@: �����
    $tt_0 = str_replace("-", "", $now_tm);
    $ttt_0 = str_replace(":", "", $tt_0);
    $now_tmp = str_replace(" ", "", $ttt_0);

    // ======== �؂�o�� �N�@���@��
    $n_year = mb_substr($now_tmp, 0, 4);
    $n_month = mb_substr($now_tmp, 4, 2);
    $n_day = mb_substr($now_tmp, 6, 2);

    // ======= ���Ԑ؂�o��
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
//====================== ���N�o�߂����@�f�[�^���폜 ::::: half_year_delete();
half_year_delete();
// ===============================================

// �I�u�W�F�N�g
$get_now_date = new get_now_date();
$get_now_arr = $get_now_date->get_to_date();


// === ���t�{�b�N�X�@��p�@
$go_now_sql = $get_now_arr['year'] . "-" . $get_now_arr['month'] . "-" . $get_now_arr['day'];



//=============================================================================
//====================================
//======================== view_btn �\������
//====================================
//=============================================================================

//================= �`�F�b�N�{�b�N�X�@check�e�X�g
if (isset($_POST['uninput_file'])) {

  $h_check_box_FLG = $_POST['uninput_file'];

  //  print("check�{�b�N�X�e�X�g:::" .  $test_check_box);

  $h_check_box_FLG = "1";
} else {

  //  print("check�����Ă��܂���B");

  $h_check_box_FLG = "0";
}


if (isset($_POST["view_btn"])) {

  //================================  
  //================== �ڑ����
  //================================  

  $dsn = '';
  $user = '';
  $password = '';


  // === �G���[�ϐ�
  $display_err = "";

  //========================================== $csv_FLG ����
  //================= �G���[���� ���t 01 �J�n��
  if (isset($_POST['date_target']) || isset($_POST['date_target_02'])) {

    if (empty($_POST['date_target']) && empty($_POST['date_target_02'])) {

      $display_err = "�\���ł��܂���B���t�{�b�N�X��I�����Ă�������";

      // === ���t�{�b�N�X���@�Q�Ƃ��@�󂾂����ꍇ
      $csv_FLG = "0";
    } else if (!empty($_POST['date_target']) && empty($_POST['date_target_02'])) {
      // === ���� �� ���͂���̏���

      $date_target = $_POST['date_target'];

      $date_target = str_replace("/", "-", $date_target);

      $display_err = "";

      $csv_FLG = "1";
    } else if (empty($_POST['date_target']) && !empty($_POST['date_target_02'])) {
      // === �E�������@���͂���̏���
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

    // === ���t�{�b�N�X���@�Q�Ƃ��@�󂾂����ꍇ
    $csv_FLG = "0";

    $display_err = "�\���ł��܂���B���t�{�b�N�X��I�����Ă�������";
  }

  //========================================== $csv_FLG ����@END




  //===================================
  // ========================== �ڑ�����
  //===================================

  try {

    // PDO �I�u�W�F�N�g
    $pdo = new PDO($dsn, $user, $password);

    //=======================================================================
    //=======================================================================
    //====================  �������\���������@���t BOX ���Q�����Ă����ꍇ
    //=======================================================================
    //=======================================================================

    // check_box_FLG = 0  ==> �����̓t�@�C���@�Ƀ`�F�b�N�Ȃ�

    if (strcmp($csv_FLG, "3") == 0 && strcmp($h_check_box_FLG, "0") == 0) {

      // �y�[�W�����[�h
      //    header("Location: " . $_SERVER['PHP_SELF']);

      // �G���[�ϐ��@������
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
                  WHERE creation_time BETWEEN ? AND ?  ORDER BY user_id DESC");

      // CSV �G�N�X�|�[�g �J�n��
      $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

      // CSV �G�N�X�|�[�g �I����
      $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);


      // SQL ���s 
      $res = $stmt->execute();

      // �g�����U�N�V�����@�R�~�b�g
      if ($res) {
      }


      $idx = 0;

      // �i�荞�݁@���ʁ@�i�[�z��
      $retunr = [];

      //======= ���t�I���@�f�[�^�@�擾
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'], 'creation_time' => $row['creation_time'],
          'data_Flg' => $row['data_Flg']
        );
      }


      // ******************************************************
      // �u�����̓t�@�C���o�́v�@�Ɂ@�`�F�b�N����
      // ******************************************************
    } else if (strcmp($csv_FLG, "3") == 0 && strcmp($h_check_box_FLG, "1") == 0) {

      // �y�[�W�����[�h
      //  header("Location: " . $_SERVER['PHP_SELF']);

      // PDO �I�u�W�F�N�g
      $pdo = new PDO($dsn, $user, $password);

      // �G���[�ϐ��@������
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
                  WHERE creation_time BETWEEN ? AND ? AND data_Flg = '0' ORDER BY user_id DESC");

      // CSV �G�N�X�|�[�g �J�n��
      $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

      // CSV �G�N�X�|�[�g �I����
      $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);


      // SQL ���s 
      $res = $stmt->execute();



      $idx = 0;

      // �i�荞�݁@���ʁ@�i�[�z��
      $retunr = [];

      //======= ���t�I���@�f�[�^�@�擾
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'], 'creation_time' => $row['creation_time'],
          'data_Flg' => $row['data_Flg']
        );
      }


      //=======================================================================
      //=======================================================================
      //====================  �������\���������@���t BOX �� �������������Ă����ꍇ
      //=======================================================================
      //=======================================================================
    } else if (strcmp($csv_FLG, "1") == 0 && strcmp($h_check_box_FLG, "0") == 0) {


      // PDO �I�u�W�F�N�g
      $pdo = new PDO($dsn, $user, $password);

      // �G���[�ϐ��@������
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table WHERE 
                creation_time >= ? ORDER BY creation_time DESC;");

      // CSV �G�N�X�|�[�g �J�n��
      $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

      // CSV �G�N�X�|�[�g �I����
      //  $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);


      // SQL ���s 
      $res = $stmt->execute();

      $idx = 0;

      // �i�荞�݁@���ʁ@�i�[�z��
      $retunr = [];

      //======= ���t�I���@�f�[�^�@�擾
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'], 'creation_time' => $row['creation_time'],
          'data_Flg' => $row['data_Flg']
        );
      }


      //=======================================================================
      //=======================================================================
      //====================  �������\���������@���t BOX �� �������������Ă����ꍇ
      // �u�����̓t�@�C���o�́v�@�`�F�b�N����
      //=======================================================================
      //=======================================================================
    } else if (strcmp($csv_FLG, "1") == 0 && strcmp($h_check_box_FLG, "1") == 0) {

      // �G���[�ϐ��@������
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table WHERE 
                creation_time >= ? AND data_Flg = '0' ORDER BY creation_time DESC;");

      $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

      // ���ݎ���  2000-00-00 => $go_now_sql
      /*
            $stmt->bindValue(2, $go_now_sql, PDO::PARAM_STR);
            */

      // SQL ���s 
      $res = $stmt->execute();

      $idx = 0;

      // �i�荞�݁@���ʁ@�i�[�z��
      $retunr = [];

      //======= ���t�I���@�f�[�^�@�擾
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'], 'creation_time' => $row['creation_time'],
          'data_Flg' => $row['data_Flg']
        );
      }

      //=======================================================================
      //=======================================================================
      //====================  �������\���������@���t BOX �� �E�������@�����Ă����ꍇ
      //=======================================================================
      //=======================================================================
    } else if (strcmp($csv_FLG, "2") == 0 && strcmp($h_check_box_FLG, "0") == 0) {

      // �G���[�ϐ��@������
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
              WHERE creation_time  <= ? ORDER BY user_id DESC");

      $stmt->bindValue(1, $date_target_02, PDO::PARAM_STR);

      // SQL ���s 
      $res = $stmt->execute();

      // �g�����U�N�V�����@�R�~�b�g
      if ($res) {
      }



      $idx = 0;

      // �i�荞�݁@���ʁ@�i�[�z��
      $retunr = [];

      //======= ���t�I���@�f�[�^�@�擾
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'],
          'creation_time' => $row['creation_time'], 'data_Flg' => $row['data_Flg']
        );
      }


      //=======================================================================
      //=======================================================================
      //====================  �������\���������@���t BOX �� �E�������@�����Ă����ꍇ 
      // �u�����̓t�@�C���o�́v�@�`�F�b�N����
      //=======================================================================
      //=======================================================================
    } else if (strcmp($csv_FLG, "2") == 0 && strcmp($h_check_box_FLG, "1") == 0) {


      // �G���[�ϐ��@������
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
              WHERE creation_time  <= ? AND data_Flg = '0' ORDER BY user_id DESC");

      $stmt->bindValue(1, $date_target_02, PDO::PARAM_STR);

      // SQL ���s 
      $res = $stmt->execute();

      // �g�����U�N�V�����@�R�~�b�g
      if ($res) {
      }



      $idx = 0;

      // �i�荞�݁@���ʁ@�i�[�z��
      $retunr = [];

      //======= ���t�I���@�f�[�^�@�擾
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'],
          'creation_time' => $row['creation_time'], 'data_Flg' => $row['data_Flg']
        );
      }


      //=======================================================================
      //=======================================================================
      //====================  �������\���������@���t BOX �� ���������Ă��Ȃ��ꍇ  �S���o��
      //=======================================================================
      //=======================================================================

    } else if (strcmp($csv_FLG, "0") == 0 && strcmp($h_check_box_FLG, "0") == 0) {


      // �G���[�ϐ��@������
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table order by user_id DESC");

      /*
               // CSV �G�N�X�|�[�g �J�n��
               $stmt->bindValue(1, $date_target, PDO::PARAM_STR);
               
               // CSV �G�N�X�|�[�g �I����
               $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);
               */


      // SQL ���s 
      $res = $stmt->execute();

      // �g�����U�N�V�����@�R�~�b�g
      if ($res) {
      }


      $idx = 0;

      // �i�荞�݁@���ʁ@�i�[�z��
      $retunr = [];

      //======= ���t�I���@�f�[�^�@�擾
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return[] = array(
          'user_name' => $row['user_name'], 'furi_name' => $row['furi_name'],
          'age' => $row['age'], 'email' => $row['email'], 'sex' => $row['sex'],
          'creation_time' => $row['creation_time'], 'data_Flg' => $row['data_Flg']
        );
      }
    } else if (strcmp($csv_FLG, "0") == 0 && strcmp($h_check_box_FLG, "1") == 0) {


      // �G���[�ϐ��@������
      $display_err = "";

      // SQL
      $stmt = $pdo->prepare("SELECT * FROM User_Info_Table WHERE 
                    data_Flg = '0' order by user_id DESC");

      /*
               // CSV �G�N�X�|�[�g �J�n��
               $stmt->bindValue(1, $date_target, PDO::PARAM_STR);
               
               // CSV �G�N�X�|�[�g �I����
               $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);
               */


      // SQL ���s 
      $res = $stmt->execute();

      // �g�����U�N�V�����@�R�~�b�g
      if ($res) {
      }


      $idx = 0;

      // �i�荞�݁@���ʁ@�i�[�z��
      $retunr = [];

      //======= ���t�I���@�f�[�^�@�擾
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

    // ************** �G���[���� ***************
    header("Location: {$kari_uri}");
  } finally {

    $pdo = null;
  }
} //============== END download_btn  POST


//==============================================================================
//==============================================================================
//======================== download_btn CSV �_�E�����[�h����
//===============================================================================
//==============================================================================

if (isset($_POST['download_btn'])) {

  //===============================================================================
  //===============================================================================
  //  =============================== CSV �o�͏��� �@���t�I���@�_�E�����[�h
  //===============================================================================
  //===============================================================================


  //======================================
  if (isset($_POST['export_output'])) {

    $export_output = $_POST['export_output'];

    // �e�X�g�@�o��
    // print ("�Z���N�g�{�b�N�X�̒l" . $export_output . "<br />");

  }

  //================= �`�F�b�N�{�b�N�X�@check�e�X�g
  if (isset($_POST['uninput_file'])) {

    $test_check_box = $_POST['uninput_file'];

    //  print("check�{�b�N�X�e�X�g:::" .  $test_check_box);

    $check_box_FLG = "1";
  } else {

    //  print("check�����Ă��܂���B");

    $check_box_FLG = "0";
  }


  if ($export_output === "csv" && empty($_POST['uninput_file'])) {

    //============================================
    //============== �C�� 2022_01_13  ���t����̏ꍇ
    //============================================
    if (isset($_POST['date_target']) || isset($_POST['date_target_02'])) {

      if (empty($_POST['date_target']) && empty($_POST['date_target_02'])) {

        $display_err = "�t�@�C���쐬���ł��܂���B���t�{�b�N�X����ł��B�u���t����͂��邩�v�A�u���o�̓t�@�C���v�Ƀ`�F�b�N�����Ă��������B";

        // === ���t�{�b�N�X���@�Q�Ƃ��@�󂾂����ꍇ
        $csv_FLG = "0";
      } else {


        //=============================================
        /*================ DB �ڑ� CSV�@�G�N�X�|�[�g ===================== */
        //=============================================


        //================================  
        //================== �ڑ����
        //================================  
        $dsn = '';
        $user = '';
        $password = '';


        // ====== �G���[�p�@�z��
        $error = "";

        //========================================== $csv_FLG ����
        //================= �G���[���� ���t 01 �J�n��


        if (isset($_POST['date_target']) || isset($_POST['date_target_02'])) {

          if (empty($_POST['date_target']) && empty($_POST['date_target_02'])) {

            // === ���t�{�b�N�X���@�Q�Ƃ��@�󂾂����ꍇ *** �G���[���� ***
            $error = "���t�w�肪��ł��B�ǂ��炩���t����͂��Ă��������B";
          } else if (!empty($_POST['date_target']) && empty($_POST['date_target_02'])) {
            // === ���� �� ���͂���̏���

            // === ���t�{�b�N�X���@�Q�Ƃ��@�󂾂����ꍇ
            $csv_FLG_exp = "0";

            // === ���t�{�b�N�X���@�Q�Ƃ��@�󂾂����ꍇ *** �G���[���� ***
            $error = "���t�w�肪��ł��B���͂��Ă��������B";
          } else if (empty($_POST['date_target']) && !empty($_POST['date_target_02'])) {
            // === �E�������@���͂���̏���

            // === ���t�{�b�N�X���@�Q�Ƃ��@�󂾂����ꍇ
            $csv_FLG_exp = "0";

            // === ���t�{�b�N�X���@�Q�Ƃ��@�󂾂����ꍇ *** �G���[���� ***
            $error = "���t�w�肪��ł��B���͂��Ă��������B";
          } else if (!empty($_POST['date_target']) && !empty($_POST['date_target_02'])) {

            // ========== �����Ƃ����͂���@ok ==========

            $date_target = $_POST['date_target'];

            $date_target = str_replace("/", "-", $date_target);

            $date_target_02 = $_POST['date_target_02'];

            $date_target_02 = str_replace("/", "-", $date_target_02);

            $error = "";

            $csv_FLG_exp = "3";
          }
        } else {

          // === ���t�{�b�N�X���@�Q�Ƃ��@�󂾂����ꍇ
          $csv_FLG_exp = "0";

          // === ���t�{�b�N�X���@�Q�Ƃ��@�󂾂����ꍇ *** �G���[���� ***
          $error = "���t�w�肪��ł��B�ǂ��炩���t����͂��Ă��������B";
        }


        // ==============================================================
        //====== ���t�������Ȃ�A���t�̉E���{�b�N�X�����Z������B
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

        //==================== ���t�v���X  1 END =========================>

        //======================================
        //============== CSV �t�@�C���� 
        //=======================================

        // CSV �t�@�C����  => �A���o�C�g�Ǘ�_2021-10-12_121212.csv
        $file_name = "Baito_Kanri_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
          "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
          $get_now_arr['minute'] . $get_now_arr['second'] . ".csv";

        // CSV �t�@�C���� 2 => file_name_02

        // �t�@�C�����̕����R�[�h�ϊ�  
        $file_name = mb_convert_encoding($file_name, "SJIS", "UTF-8");

        /*
    $export_csv_title = [
      "���O", "�ӂ肪��", "�N��","���[���A�h���X", "����", "�o�^��"
    ]; //DB�e�[�u���̃w�b�_�[����
    */

        // ======= CSV �t�@�C���@�w�b�_�[�^�C�g���@�쐬
        //============= ���� 237 
        $export_csv_title = [
          "�Ј��ԍ�",
          "�Ј�����",
          "�t���K�i",
          "����",
          "���N����",
          "���N�����i����j",
          "���Ћ敪",
          "���ДN����",
          "���ДN�����i����j",
          "�Α��N��",
          "�Α��N��_����",
          "�Ј��敪",
          "��E",
          "�w��",
          "��������",
          "�E��",
          "�X�֔ԍ�",
          "�Z���P",
          "�Z���Q",
          "�Z���i�t���K�i�j�P",
          "�Z���i�t���K�i�j�Q",
          "�d�b�ԍ�",
          "���[���A�h���X",
          "��Q�敪",
          "�Ǖw",
          "�Ǖv",
          "�ΘJ�w��",
          "�ЊQ��",
          "�O���l",
          "�ސE���R",
          "�ސE�N����",
          "�ސE�N�����i����j",
          "���^�敪",
          "�ŕ\�敪",
          "�N�������Ώ�",
          "�x���`��",
          "�x�����p�^�[��",
          "���^���׏��p�^�[��",
          "�ܗ^���׏��p�^�[��",
          "���N�ی��Ώ�",
          "���ی��Ώ�",
          "���ی��z�i��~�j",
          "���ۏؔԍ��i����j",
          "���ۏؔԍ��i�g���j",
          "���ہE���N�@���i�擾�N����",
          "�����N���Ώ�",
          "70�Έȏ��p��",
          "�ی��Ҏ��",
          "���N���z�i��~�j",
          "��b�N���ԍ��P",
          "��b�N���ԍ��Q",
          "�Z���ԘJ���ҁi3/4�����j",
          "�J�Еی��Ώ�",
          "�ٗp�ی��Ώ�",
          "�J���ی��p�敪",
          "�ٗp�ی���ی��Ҕԍ��P",
          "�ٗp�ی���ی��Ҕԍ��Q",
          "�ٗp�ی���ی��Ҕԍ��R",
          "�J���ی��@���i�擾�N����",
          "�z���",
          "�z��Ҏ���",
          "�z��҃t���K�i",
          "�z��Ґ���",
          "�z��Ґ��N����",
          "�z��Ґ��N�����i����j",
          "����T���Ώ۔z���",
          "�z��ҘV�l",
          "�z��ҏ�Q��",
          "�z��ғ���",
          "�z��Ҕ񋏏Z��",
          "�}�{�T���Ώېl��",
          "�}�{�e��_�}�{�e����_1",
          "�}�{�e��_�t���K�i_1",
          "�}�{�e��_����_1",
          "�}�{�e��_���N����_1",
          "�}�{�e��_���N�����i����j_1",
          "�}�{�e��_�}�{�敪_1",
          "�}�{�e��_��Q�ҋ敪_1",
          "�}�{�e��_�����敪_1",
          "�}�{�e��_�����V�e���敪_1",
          "�}�{�e��_�񋏏Z�ҋ敪_1",
          "�}�{�e��_�T���v�Z�敪_1",
          "�}�{�e��_�}�{�e����_2",
          "�}�{�e��_�t���K�i_2",
          "�}�{�e��_����_2",
          "�}�{�e��_���N����_2",
          "�}�{�e��_���N�����i����j_2",
          "�}�{�e��_�}�{�敪_2",
          "�}�{�e��_��Q�ҋ敪_2",
          "�}�{�e��_�����敪_2",
          "�}�{�e��_�����V�e���敪_2",
          "�}�{�e��_�񋏏Z�ҋ敪_2",
          "�}�{�e��_�T���v�Z�敪_2",
          "�}�{�e��_�}�{�e����_3",
          "�}�{�e��_�t���K�i_3",
          "�}�{�e��_����_3",
          "�}�{�e��_���N����_3",
          "�}�{�e��_���N�����i����j_3",
          "�}�{�e��_�}�{�敪_3",
          "�}�{�e��_��Q�ҋ敪_3",
          "�}�{�e��_�����敪_3",
          "�}�{�e��_�����V�e���敪_3",
          "�}�{�e��_�񋏏Z�ҋ敪_3",
          "�}�{�e��_�T���v�Z�敪_3",
          "�}�{�e��_�}�{�e����_4",
          "�}�{�e��_�t���K�i_4",
          "�}�{�e��_����_4",
          "�}�{�e��_���N����_4",
          "�}�{�e��_���N�����i����j_4",
          "�}�{�e��_�}�{�敪_4",
          "�}�{�e��_��Q�ҋ敪_4",
          "�}�{�e��_�����敪_4",
          "�}�{�e��_�����V�e���敪_4",
          "�}�{�e��_�񋏏Z�ҋ敪_4",
          "�}�{�e��_�T���v�Z�敪_4",
          "�}�{�e��_�}�{�e����_5",
          "�}�{�e��_�t���K�i_5",
          "�}�{�e��_����_5",
          "�}�{�e��_���N����_5",
          "�}�{�e��_���N�����i����j_5",
          "�}�{�e��_�}�{�敪_5",
          "�}�{�e��_��Q�ҋ敪_5",
          "�}�{�e��_�����敪_5",
          "�}�{�e��_�����V�e���敪_5",
          "�}�{�e��_�񋏏Z�ҋ敪_5",
          "�}�{�e��_�T���v�Z�敪_5",
          "�}�{�e��_�}�{�e����_6",
          "�}�{�e��_�t���K�i_6",
          "�}�{�e��_����_6",
          "�}�{�e��_���N����_6",
          "�}�{�e��_���N�����i����j_6",
          "�}�{�e��_�}�{�敪_6",
          "�}�{�e��_��Q�ҋ敪_6",
          "�}�{�e��_�����敪_6",
          "�}�{�e��_�����V�e���敪_6",
          "�}�{�e��_�񋏏Z�ҋ敪_6",
          "�}�{�e��_�T���v�Z�敪_6",
          "�}�{�e��_�}�{�e����_7",
          "�}�{�e��_�t���K�i_7",
          "�}�{�e��_����_7",
          "�}�{�e��_���N����_7",
          "�}�{�e��_���N�����i����j_7",
          "�}�{�e��_�}�{�敪_7",
          "�}�{�e��_��Q�ҋ敪_7",
          "�}�{�e��_�����敪_7",
          "�}�{�e��_�����V�e���敪_7",
          "�}�{�e��_�񋏏Z�ҋ敪_7",
          "�}�{�e��_�T���v�Z�敪_7",
          "�}�{�e��_�}�{�e����_8",
          "�}�{�e��_�t���K�i_8",
          "�}�{�e��_����_8",
          "�}�{�e��_���N����_8",
          "�}�{�e��_���N�����i����j_8",
          "�}�{�e��_�}�{�敪_8",
          "�}�{�e��_��Q�ҋ敪_8",
          "�}�{�e��_�����敪_8",
          "�}�{�e��_�����V�e���敪_8",
          "�}�{�e��_�񋏏Z�ҋ敪_8",
          "�}�{�e��_�T���v�Z�敪_8",
          "�}�{�e��_�}�{�e����_9",
          "�}�{�e��_�t���K�i_9",
          "�}�{�e��_����_9",
          "�}�{�e��_���N����_9",
          "�}�{�e��_���N�����i����j_9",
          "�}�{�e��_�}�{�敪_9",
          "�}�{�e��_��Q�ҋ敪_9",
          "�}�{�e��_�����敪_9",
          "�}�{�e��_�����V�e���敪_9",
          "�}�{�e��_�񋏏Z�ҋ敪_9",
          "�}�{�e��_�T���v�Z�敪_9",
          "�}�{�e��_�}�{�e����_10",
          "�}�{�e��_�t���K�i_10",
          "�}�{�e��_����_10",
          "�}�{�e��_���N����_10",
          "�}�{�e��_���N�����i����j_10",
          "�}�{�e��_�}�{�敪_10",
          "�}�{�e��_��Q�ҋ敪_10",
          "�}�{�e��_�����敪_10",
          "�}�{�e��_�����V�e���敪_10",
          "�}�{�e��_�񋏏Z�ҋ敪_10",
          "�}�{�e��_�T���v�Z�敪_10",
          "�}�{�e��_�}�{�e����_11",
          "�}�{�e��_�t���K�i_11",
          "�}�{�e��_����_11",
          "�}�{�e��_���N����_11",
          "�}�{�e��_���N�����i����j_11",
          "�}�{�e��_�}�{�敪_11",
          "�}�{�e��_��Q�ҋ敪_11",
          "�}�{�e��_�����敪_11",
          "�}�{�e��_�����V�e���敪_11",
          "�}�{�e��_�񋏏Z�ҋ敪_11",
          "�}�{�e��_�T���v�Z�敪_11",
          "�}�{�e��_�}�{�e����_12",
          "�}�{�e��_�t���K�i_12",
          "�}�{�e��_����_12",
          "�}�{�e��_���N����_12",
          "�}�{�e��_���N�����i����j_12",
          "�}�{�e��_�}�{�敪_12",
          "�}�{�e��_��Q�ҋ敪_12",
          "�}�{�e��_�����敪_12",
          "�}�{�e��_�����V�e���敪_12",
          "�}�{�e��_�񋏏Z�ҋ敪_12",
          "�}�{�e��_�T���v�Z�敪_12",
          "�}�{�e��_�}�{�e����_13",
          "�}�{�e��_�t���K�i_13",
          "�}�{�e��_����_13",
          "�}�{�e��_���N����_13",
          "�}�{�e��_���N�����i����j_13",
          "�}�{�e��_�}�{�敪_13",
          "�}�{�e��_��Q�ҋ敪_13",
          "�}�{�e��_�����敪_13",
          "�}�{�e��_�����V�e���敪_13",
          "�}�{�e��_�񋏏Z�ҋ敪_13",
          "�}�{�e��_�T���v�Z�敪_13",
          "�}�{�e��_�}�{�e����_14",
          "�}�{�e��_�t���K�i_14",
          "�}�{�e��_����_14",
          "�}�{�e��_���N����_14",
          "�}�{�e��_���N�����i����j_14",
          "�}�{�e��_�}�{�敪_14",
          "�}�{�e��_��Q�ҋ敪_14",
          "�}�{�e��_�����敪_14",
          "�}�{�e��_�����V�e���敪_14",
          "�}�{�e��_�񋏏Z�ҋ敪_14",
          "�}�{�e��_�T���v�Z�敪_14",
          "�}�{�e��_�}�{�e����_15",
          "�}�{�e��_�t���K�i_15",
          "�}�{�e��_����_15",
          "�}�{�e��_���N����_15",
          "�}�{�e��_���N�����i����j_15",
          "�}�{�e��_�}�{�敪_15",
          "�}�{�e��_��Q�ҋ敪_15",
          "�}�{�e��_�����敪_15",
          "�}�{�e��_�����V�e���敪_15",
          "�}�{�e��_�񋏏Z�ҋ敪_15",
          "�}�{�e��_�T���v�Z�敪_15",
        ];

        $export_header = [];

        foreach ($export_csv_title as $key => $val) {

          $export_header[] = mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
        }



        $export_header_02 = [];

        $export_csv_title_02 = [];

        $export_csv_title_02 = [
          '�Ј��ԍ�',
          '�Ј�����',
          '�x�����o�^No',
          '�U����@�@���Z�@�փR�[�h',
          '�U����@�@���Z�@�֖�',
          '�U����@�@���Z�@�փt���K�i',
          '�U����@�@�x�X�R�[�h',
          '�U����@�@�x�X��',
          '�U����@�@�x�X�t���K�i',
          '�U����@�@�a�����',
          '�U����@�@�����ԍ�',
          '�U����@�@�U���萔��',
          '�U����@�@��z�U��',
          '�U����@�@���z',
          '�U����A�@���Z�@�փR�[�h',
          '�U����A�@���Z�@�֖�',
          '�U����A�@���Z�@�փt���K�i',
          '�U����A�@�x�X�R�[�h',
          '�U����A�@�x�X��',
          '�U����A�@�x�X�t���K�i',
          '�U����A�@�a�����',
          '�U����A�@�����ԍ�',
          '�U����A�@�U���萔��',
          '�U����A�@��z�U��',
          '�U����A�@���z'
        ];

        foreach ($export_csv_title_02 as $key => $val) {

          $export_header_02[] = mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
        }



        try {

          // PDO �I�u�W�F�N�g
          $pdo = new PDO($dsn, $user, $password);

          //========= �g�����U�N�V�����J�n
          //    $pdo->beginTransaction();

          //====== SQL���@�����@���t�e�L�X�g�{�b�N�X�̏󋵂ɍ��킹�ā@����
          if (strcmp($csv_FLG_exp, "3") == 0) {
          } else if (strcmp($csv_FLG_exp, "0") == 0) {

            $export_err = "�\���ł��܂���";
          }

          // SQL
          $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
                  WHERE creation_time BETWEEN ? AND ? ORDER BY user_id DESC");

          // CSV �G�N�X�|�[�g �J�n��
          $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

          // CSV �G�N�X�|�[�g �I����
          $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);

          // SQL ���s 
          $res = $stmt->execute();

          $idx = 0;

          // �i�荞�݁@���ʁ@�i�[�z��
          $retunr_tmp = [];
          $return = [];

          // �t�@���_���Ȃ�������A�t�H���_���쐬  �`�� files_20211025
          $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];
          if (!file_exists($dirpath)) {
            mkdir($dirpath, 0777);

            //chmod�֐��Łuhoge�v�f�B���N�g���̃p�[�~�b�V�������u0777�v�ɂ���
            //   chmod($dirpath, 0777);
          }

          //====== �t�@�C���̕ۑ�
          // �t�@�C�������ꏊ
          $create_csv = $dirpath . "/" . $file_name;


          if (touch($create_csv)) {
            $file = new SplFileObject($create_csv, "w");

            // �o�͂��� CSV �Ɂ@�w�b�_�[����������
            $file->fputcsv($export_header);

            //====================================================== 
            //=========================== CSV �p�@�ϐ��錾
            //====================================================== 
            $sex = ""; // ����
            $wareki = ""; // �a��̔N
            $wareki_r = ""; // �a�� �N���� �o�͗p
            $month_r = "";
            $day_r = ""; // ���t�@���ɂ�

            $Seireki_r = ""; // ����o�͗p

            $hokensya_K = ""; // �ی��Ҏ��  �j�q�A���q


            //======= ���t�I���@�f�[�^�@�擾
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


              // ============ ���������΍� ============
              $row['user_name'] = mb_convert_encoding($row['user_name'], "SJIS-win", "auto"); // �Ј�����


              //=== �ӂ肪�ȁ@���@�J�i�@�֕ϊ�
              $row['furi_name'] = mb_convert_kana($row['furi_name'], "h", "UTF-8"); // �t���K�i
              $row['furi_name'] = mb_convert_encoding($row['furi_name'], "SJIS-win", "auto");

              // ���[���A�h���X
              $row['email'] = mb_convert_encoding($row['email'], "SJIS-win", "auto");

              // �z��� spouse
              if (strcmp($row['spouse'], "0") == 0) {
                $row['spouse'] = "�Ȃ�";
                $row['spouse'] = mb_convert_encoding($row['spouse'], "SJIS-win", "auto");
              } else {
                $row['spouse'] = "����";
                $row['spouse'] = mb_convert_encoding($row['spouse'], "SJIS-win", "auto");
              }

              // �}�{�l�� 	dependents_num
              $row['dependents_num'] = mb_convert_encoding($row['dependents_num'], "SJIS-win", "auto");

              $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto"); // 0 => �j�� , 1 => ����

              //=== ����
              if (strcmp($row['sex'], "0") == 0) {
                $row['sex'] = "�j"; // �ی��Ҏ��
                $hokensya_K = ""; // �ی��Ҏ��

                $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
                $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
              } else {
                $row['sex'] = "��"; // �ی��Ҏ��
                $hokensya_K = ""; // �ی��Ҏ��

                $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
                $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
              }

              //=== ���N�����@�i���� => �a��@�ϊ��j
              $row['birthday'] = mb_convert_encoding($row['birthday'], "SJIS-win", "auto");

              // �N
              $year = mb_substr($row['birthday'], 0, 4); // ����@���o�� 1900

              // �a��ϊ��p function  Wareki_Parse
              $wareki = Wareki_Parse($year);

              // ��
              $month = mb_substr($row['birthday'], 5, 2);

              if (strpos(
                $month,
                '-'
              ) !== false) {
                // - �n�C�t�� ���܂܂�Ă���ꍇ , 0�@�p�f�B���O
                $month = str_replace("-", "", $month);
                $month_r = "0" . $month;
              } else {
                $month_r = $month;
              }


              // ��
              $day = mb_substr($row['birthday'], 7, 2);
              // ������̒����擾  1,  10
              $day_num = mb_strlen($day);

              if ($day_num == 1) {
                $day_r = "0" . $day;
              } else if (strpos($day, '-') !== false) {
                $day = str_replace("-", "", $day);
                $day_r = $day;
              } else {
                $day_r = $day;
              }

              $wareki_r = $wareki . $month_r . "��" . $day_r . "��";
              $wareki_r = mb_convert_encoding($wareki_r, "SJIS-win", "auto");

              // ����
              $Seireki_r = $year . "�N" . $month_r . "��" . $day_r . "��";
              $Seireki_r = mb_convert_encoding($Seireki_r, "SJIS-win", "auto");

              //=== �X�֔ԍ� zip  �i-�j ������
              $row['zip'] = substr($row['zip'], 0, 3) . "-" . substr($row['zip'], 3);
              $row['zip'] = mb_convert_encoding($row['zip'], "SJIS-win", "auto");

              //=== �Z���P address_01
              $row['address_01'] = mb_convert_encoding($row['address_01'], "SJIS-win", "auto");


              //=== �d�b�ԍ� tel
              // �d�b�ԍ� �i-�j �n�C�t���t��
              $row['tel'] = substr($row['tel'], 0, 3) . "-" . substr($row['tel'], 3, 4) . "-" . substr($row['tel'], 7);
              $row['tel'] = mb_convert_encoding($row['tel'], "SJIS-win", "auto");


              $row['creation_time'] = mb_convert_encoding($row['creation_time'], "SJIS-win", "auto");
              $row['data_Flg'] = mb_convert_encoding($row['data_Flg'], "SJIS-win", "auto");

              // �Ј��ԍ�
              // ================ 0 �p�f�B���O
              $row['employee_id'] = sprintf('%06d', $row['employee_id']);
              $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");

              //=== �󕶎�
              $row['kara_01'] = ""; // �� ���� ,   // ����7: ���Ћ敪
              $row['kara_02'] = ""; // �� ���� ,   // ����8: ���ДN����
              $row['kara_03'] = ""; // �� ���� ,   // ����9: ���ДN�����i����j
              $row['kara_04'] = ""; // �� ���� ,   // ����10: �Α��N��
              $row['kara_05'] = ""; // �� ���� ,   // ����11: �Α��N��_����

              $syasin_K = "�A���o�C�g"; // �� ���� ,   // ����12: �Ј��敪 => �A���o�C�g
              $syasin_K = mb_convert_encoding($syasin_K, "SJIS-win", "auto");


              $row['kara_07'] = ""; // �� ���� ,   // ����13: ��E
              $row['kara_08'] = ""; // �� ���� ,   // ����14: �w��

              //==============================
              //=========== 2021_12_21 �l �ύX
              //============================== 
              // $syozoku_b = "003";                 // ����15: ��������
              $syozoku_b = "�A���o�C�g";
              $syozoku_b = mb_convert_encoding($syozoku_b, "SJIS-win", "auto");

              $row['kara_09'] = ""; // �� ���� ,   // 
              $row['kara_10'] = ""; // �� ���� ,   // �E��

              $row['kara_11'] = ""; // �� ���� ,  // �Z�� 2 
              $row['kara_12'] = ""; // �� ���� ,  // �Z�� �i�t���K�i�j�P �����ꂽ�� 
              $row['kara_13'] = ""; // �� ���� ,  // �Z�� �i�t���K�i�j�Q

              //=== ��Y�� Start
              $row['higaitou_01'] = "��Y��";
              $row['higaitou_02'] = "��Y��";
              $row['higaitou_03'] = "��Y��";
              $row['higaitou_04'] = "��Y��";
              $row['higaitou_05'] = "��Y��";
              $row['higaitou_06'] = "��Y��";
              // �G���R�[�h
              $row['higaitou_01'] = mb_convert_encoding($row['higaitou_01'], "SJIS-win", "auto");
              $row['higaitou_02'] = mb_convert_encoding($row['higaitou_02'], "SJIS-win", "auto");
              $row['higaitou_03'] = mb_convert_encoding($row['higaitou_03'], "SJIS-win", "auto");
              $row['higaitou_04'] = mb_convert_encoding($row['higaitou_04'], "SJIS-win", "auto");
              $row['higaitou_05'] = mb_convert_encoding($row['higaitou_05'], "SJIS-win", "auto");
              $row['higaitou_06'] = mb_convert_encoding($row['higaitou_06'], "SJIS-win", "auto");
              //=== ��Y�� End

              //=== �󕶎�
              $row['kara_14'] = "";  // �ސE���R
              $row['kara_15'] = "";  // �ސE�N����
              $row['kara_16'] = "";  // �ސE�N�����i����j

              //=== ��^�@�敪�@
              $row['kubun_01'] =  "����"; // ���^�敪
              $row['kubun_02'] =  "���z�b��"; // �ŕ\�敪
              $row['kubun_03'] =  "����"; // �N�������Ώ� 
              $row['kubun_04'] =  "�U��"; // �x���`��
              $row['kubun_05'] =  "�T���v��"; // �x�����p�^�[��

              //=============== 2021_12_21 �l�ύX
              //  $row['kubun_06'] =  "�Ј��p�^�[��"; // ���^���׏��p�^�[��
              $row['kubun_06'] =  ""; // ���^���׏��p�^�[��

              //=============== 2021_12_21 �l�ύX
              //   $row['kubun_07'] =  "�ܗ^�T���v��"; // �ܗ^���׏��p�^�[��
              $row['kubun_07'] =  ""; // �ܗ^���׏��p�^�[��

              $row['kubun_08'] =  "";   // ���N�ی��Ώ�
              $row['kubun_09'] =  "";   //���ی��Ώ�

              //=============== 2021_12_21 �l�ύX
              //     $row['kubun_10'] =  "";   //  ���ی��z�i��~�j
              $row['kubun_10'] =  "";   //  ���ی��z�i��~�j

              //=============== 2021_12_21 �l�ύX
              //     $row['kubun_10_02'] = "367"; // ���ۏؔԍ�
              $row['kubun_10_02'] = ""; // ���ۏؔԍ�


              // �G���R�[�h
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
              //=== ��^�@�敪 END

              //=== �󕶎�
              $row['kara_17'] = "";   // ��b�N���ԍ��P
              $row['kara_18'] = "";   // ��b�N���ԍ��Q

              //=== ��^�@�敪�@
              $row['kubun_11'] = ""; // �����N���Ώ� , �Ώ�  ��

              $row['kubun_12'] = "��Y��"; // 70�Έȏ��p��
              $row['kubun_13'] = $hokensya_K; // �ی��Ҏ�� , �j�q�A���q
              $row['kubun_14'] = ""; // ����49:���N���z�i��~�j ��
              // �G���R�[�h
              $row['kubun_11'] = mb_convert_encoding($row['kubun_11'], "SJIS-win", "auto");
              $row['kubun_12'] = mb_convert_encoding($row['kubun_12'], "SJIS-win", "auto");
              $row['kubun_13'] = mb_convert_encoding($row['kubun_13'], "SJIS-win", "auto");
              $row['kubun_14'] = mb_convert_encoding($row['kubun_14'], "SJIS-win", "auto");
              //=== ��^�@�敪�@END

              $row['kara_19'] = ""; // ��b�N���ԍ��P
              $row['kara_20'] = ""; // ��b�N���ԍ��Q

              //=== ��^�敪
              $row['kubun_15'] =  "�ΏۊO";  // �Z���ԘJ���ҁi3/4�����j
              $row['kubun_16'] =  "�Ώ�";  // �J�Еی��Ώ�

              //=============== 2021_12_21 �l�ύX
              //  $row['kubun_17'] =  "�Ώ�";  // �ٗp�ی��Ώ�
              $row['kubun_17'] =  "";  // �ٗp�ی��Ώ�

              //=============== 2021_12_21 �l�ύX
              $row['kubun_18'] =  "";  // �J���ی��p�敪

              //=============== 2021_12_21 �l�ύX
              //     $row['kubun_19'] =  "5029";  // �ٗp�ی���ی��Ҕԍ��P
              $row['kubun_19'] =  "";  // �ٗp�ی���ی��Ҕԍ��P

              //=============== 2021_12_21 �l�ύX
              //     $row['kubun_20'] =  "530842";  // �ٗp�ی���ی��Ҕԍ� 2
              $row['kubun_20'] =  "";  // �ٗp�ی���ی��Ҕԍ� 2

              //=============== 2021_12_21 �l�ύX
              //     $row['kubun_21'] =  "4";  // �ٗp�ی���ی��Ҕԍ� 3
              $row['kubun_21'] =  "";  // �ٗp�ی���ی��Ҕԍ� 3

              // �G���R�[�h
              $row['kubun_15'] = mb_convert_encoding($row['kubun_15'], "SJIS-win", "auto");
              $row['kubun_16'] = mb_convert_encoding($row['kubun_16'], "SJIS-win", "auto");
              $row['kubun_17'] = mb_convert_encoding($row['kubun_17'], "SJIS-win", "auto");
              $row['kubun_18'] = mb_convert_encoding($row['kubun_18'], "SJIS-win", "auto");
              $row['kubun_19'] = mb_convert_encoding($row['kubun_19'], "SJIS-win", "auto");
              $row['kubun_20'] = mb_convert_encoding($row['kubun_20'], "SJIS-win", "auto");
              $row['kubun_21'] = mb_convert_encoding($row['kubun_21'], "SJIS-win", "auto");

              //=== �󕶎�
              $row['kara_22'] = ""; // �J���ی��@���i�擾�N����

              $row['kara_23'] = "";   // 
              $row['kara_24'] = "";   //  �z��Ґ��� �� 2021_12_07 �C��

              $row['kubun_22'] =  "";  // �z��Ґ��N����
              $row['kubun_22'] = mb_convert_encoding($row['kubun_22'], "SJIS-win", "auto");

              $row['kara_25'] = "";  //  �z��Ґ��N�����i����j
              //       $row['kara_26'] = "";  // 

              $row['higaitou_07'] = "��Y��";   // ����T���Ώ۔z���
              $row['higaitou_08'] = "��Y��";   // �z��ҘV�l
              $row['higaitou_09'] = "��Y��";   // �z��ҏ�Q��
              $row['higaitou_10'] = "�񓯋�";   // �z��ғ���  : �񓯋�
              $row['higaitou_11'] = "��Y��";   // �z��Ҕ񋏏Z��

              $row['higaitou_07'] = mb_convert_encoding($row['higaitou_07'], "SJIS-win", "auto");
              $row['higaitou_08'] = mb_convert_encoding($row['higaitou_08'], "SJIS-win", "auto");
              $row['higaitou_09'] = mb_convert_encoding($row['higaitou_09'], "SJIS-win", "auto");
              $row['higaitou_10'] = mb_convert_encoding($row['higaitou_10'], "SJIS-win", "auto");
              $row['higaitou_11'] = mb_convert_encoding($row['higaitou_11'], "SJIS-win", "auto");

              // =========�@���K�{ �}�{�T���Ώېl��
              $row['dependents_num'] = mb_convert_encoding($row['dependents_num'], "SJIS-win", "auto");

              //======== 165 �́@�󔒍s
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
              //*********************  �Q�����z��֊i�[ ******************************
              /********************************************************************** */
              $return[] = array(
                'employee_id' => $row['employee_id'], // ���ڂP�F �Ǘ��ԍ� �Z
                'user_name' => $row['user_name'], // ���ڂQ�F �Ј������@�Z
                'furi_name' => $row['furi_name'], // ���ڂR�F �t���K�i�@�Z
                'sex' => $row['sex'], // ���ڂS�F ���ʁ@�Z
                'wareki' => $wareki_r, //�@���ڂT�F �a��@�Z

                'birthday' => $Seireki_r, //�@���ڂU�F ����@�Z

                /*  
                   'creation_time' => $row['creation_time'],
                   'data_Flg' => $row['data_Flg'],
                   */

                'kara_01' => $row['kara_01'], //  ���ڂV�F�@���Ћ敪�@
                'kara_02' => $row['kara_02'], //�@���ڂW�F�@���ДN����
                'kara_03' => $row['kara_03'], //  ���ڂX�F�@���ДN�����i����j
                'kara_04' => $row['kara_04'], //  ���ڂP�O�F�Α��N���@
                'kara_05' => $row['kara_05'], //  ���ڂP�P�F�Α��N��_����

                'syasin_K' => $syasin_K, // ���ڂP�Q�F ,�Ј��敪�@�Z

                'kara_07' => $row['kara_07'], // ����13�F ��E
                'kara_08' => $row['kara_08'], // ����14�F �w��

                'syozoku_b' => $syozoku_b, // ����15�F ��������@�Z

                'kara_10' => $row['kara_10'], // ����16�F�E��

                'zip' => $row['zip'], // ���� 17 �F�@�X�֔ԍ��@�Z
                'address_01' => $row['address_01'], // ����18 �F �Z���P�@�Z

                'kara_11' => $row['kara_11'], // ��@�J���� 19  �Z���Q
                'kara_12' => $row['kara_12'], // ��@�J���� 20  �Z���i�t���K�i�j�P
                'kara_13' => $row['kara_13'], // ��@�J���� 21  �Z���i�t���K�i�j�Q

                'tel' => $row['tel'],   // ����22 �d�b�ԍ��@�Z
                'email' => $row['email'],   // ����23: email�@�Z

                'higaitou_01' => $row['higaitou_01'],   // ����24:��Q�敪  ��Y��
                'higaitou_02' => $row['higaitou_02'],   // ����25:�Ǖw  ��Y��
                'higaitou_03' => $row['higaitou_03'],   // ����26:�Ǖv ��Y��
                'higaitou_04' => $row['higaitou_04'],   // ����27: ��Y��
                'higaitou_05' => $row['higaitou_05'],   // ����28: ��Y��
                'higaitou_06' => $row['higaitou_06'],   // ����29: ��Y��

                'kara_14' => $row['kara_14'], // ����30:�ސE���R ��@�J���� , 14
                'kara_15' => $row['kara_15'], // ����31:�ސE�N���� ��@�J���� , 15
                'kara_16' => $row['kara_16'], // ����32:�ސE�N���� ��@�J���� , 16

                'kubun_01' => $row['kubun_01'], // ��^�敪 01, ����33:���^�敪:����
                'kubun_02' => $row['kubun_02'], // ��^�敪 02, ����34:�ŕ\�敪:���z�b��
                'kubun_03' => $row['kubun_03'], // ��^�敪 03, ����35:�N�������Ώ�:����
                'kubun_04' => $row['kubun_04'], // ��^�敪 04, ����36:�x���`��:�U��
                'kubun_05' => $row['kubun_05'], // ��^�敪 05, ����37:�x�����p�^�[��:�T���v��
                'kubun_06' => $row['kubun_06'], // ��^�敪 06, ����38:���^���׏��p�^�[��:�Ј��p�^�[��
                'kubun_07' => $row['kubun_07'], // ��^�敪 07, ����39:�ܗ^���׏��p�^�[��:�ܗ^�T���v��
                'kubun_08' => $row['kubun_08'], // ��^�敪 08, ����40:���N�ی��Ώ�
                'kubun_09' => $row['kubun_09'], // ��^�敪 09, ����41:���ی��Ώ�
                'kubun_10' => $row['kubun_10'], // ��^�敪 10, ����42:���ی��z�i��~�j:220
                'kubun_10_02' => $row['kubun_10_02'], // ����43:���ۏؔԍ��i����j 367

                'kara_17' => $row['kara_17'], // ��@�J���� , ����44:���ۏؔԍ��i�g���j
                'kara_18' => $row['kara_18'], // ��@�J���� , ����45:���ہE���N�@���i�擾�N����

                'kubun_11' => $row['kubun_11'], // ��^�敪 11 ����46:�����N���Ώ�, ��

                'kubun_12' => $row['kubun_12'], // ��^�敪 12 ����47:70�Έȏ��p��:��Y��
                'kubun_13' => $row['kubun_13'], // ��^�敪 13 ����48:�ی��Ҏ��:���q
                'kubun_14' => $row['kubun_14'], // ��^�敪 14 ����49:���N���z�i��~�j

                'kara_19' => $row['kara_19'], // ��@�J���� , 19  ����50:��b�N���ԍ��P
                'kara_20' => $row['kara_20'], // ��@�J���� , 20  ����51:��b�N���ԍ��Q

                'kubun_15' => $row['kubun_15'], // ��^�敪 15 ����52:�Z���ԘJ���ҁi3/4�����j:�ΏۊO
                'kubun_16' => $row['kubun_16'], // ��^�敪 16 ����53:�J�Еی��Ώ�:�Ώ�
                'kubun_17' => $row['kubun_17'], // ��^�敪 17 ����54:�ٗp�ی��Ώ�:�Ώ�
                'kubun_18' => $row['kubun_18'], // ��^�敪 18 ����55:�J���ی��p�敪:��p
                'kubun_19' => $row['kubun_19'], // ��^�敪 19 ����56:�ٗp�ی���ی��Ҕԍ��P:5029
                'kubun_20' => $row['kubun_20'], // ��^�敪 20 ����57:�ٗp�ی���ی��Ҕԍ��Q:530842
                'kubun_21' => $row['kubun_21'], // ��^�敪 21 ����58:�ٗp�ی���ی��Ҕԍ��R:4

                'kara_21' => $row['kara_21'], // ��@�J���� , 21 ����59:�J���ی��@���i�擾�N����:


                // �z��҂���@�A�Ȃ�
                'spouse' => $row['spouse'], // ����60:�z���:�Ȃ�

                'kara_22' => $row['kara_22'], // ��@�J���� , 22 ����61:�z��Ҏ���:
                'kara_23' => $row['kara_23'], // ��@�J���� , 23 ����62:�z��҃t���K�i:
                'kara_24' => $row['kara_24'], // ��@�J���� , 24 ����63:�z��Ґ���:

                'kubun_22' => $row['kubun_22'], // ��^�敪 22 ����64:�z��Ґ��N����:

                'kara_25' => $row['kara_25'], // ��@�J���� , 25 ����65:�z��Ґ��N�����i����j
                //  'kara_26' => $row['kara_26'], // ��@�J���� , 26

                'higaitou_07' => $row['higaitou_07'], // ����66:����T���Ώ۔z���:��Y��
                'higaitou_08' => $row['higaitou_08'], // ����67:�z��ҘV�l:��Y��
                'higaitou_09' => $row['higaitou_09'], // ����68:�z��ҏ�Q��:��Y��
                'higaitou_10' => $row['higaitou_10'], // ����69:�z��ғ���:�񓯋�
                'higaitou_11' => $row['higaitou_11'], // ����70:�z��Ҕ񋏏Z��:��Y��

                //�@���K�{ �}�{�l�� dependents_num
                'dependents_num' => $row['dependents_num'], // ����71:�}�{�T���Ώېl��:0

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

              // bank�p�@2�����z��@$return_bank []

            }

            // �t�@�C���֑}�� �i.csv�j
            foreach ($return as $item) {
              $file->fputcsv($item);
            }



            //================= CSV �t�@�C���쐬���� �t���O
            $csv_file_ok_FLG = "1";

            // csv �Z���N�g�I���{�b�N�X �L�� OK 
            $export_output_err = "0";
          } //=== END if


          //==============================================
          //*************** �t�@�C���̃_�E�����[�h���� */
          //==============================================
          //  t_download($protocol);

          //===================================
          // =========== ���݂̃t��URL �擾
          //===================================
          /*
      if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {

        $protocol = 'https://';
      } else {

        $protocol = 'http://';
      }

      // ./files �� . ���폜
      $new_create_csv = mb_substr($create_csv, 1);

      $protocol .= $_SERVER["HTTP_HOST"] . '/job_recruit/csv' . $new_create_csv;
    
     


      // �t�@�C���^�C�v���w��
      header('Content-Type: application/octet-stream');

      // �t�@�C���T�C�Y���擾���A�_�E�����[�h�̐i����\��
      // header('Content-Length: '.filesize($protocol));

      // �t�@�C���̃_�E�����[�h�A���l�[�����w��
      header('Content-Disposition: attachment; filename="' . $file_name . '"');

      ob_clean();  //�ǋL
      flush();     //�ǋL

      // �t�@�C����ǂݍ��݃_�E�����[�h�����s
      readfile($protocol);

      //    exit;
      */
        } catch (PDOException $e) {

          //   print('Error:'.$e->getMessage());

          // (�g�����U�N�V����) ���[���o�b�N
          //       $pdo->rollBack();

          // ************** �G���[���� ***************
          $csv_file_ok_FLG = "2";
        } finally {

          $pdo = null;
        }


        try {

          //=====================================================
          //=====================================================
          //================== ��s�p�@CSV �쐬
          //=====================================================
          //=====================================================

          //=== CSV �t�@�C����  => �A���o�C�g�Ǘ�_2021-10-12_121212.csv
          $file_name_02 = "GinkouJ_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
            "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
            $get_now_arr['minute'] . $get_now_arr['second'] . ".csv";

          // CSV �t�@�C���� 2 => file_name_02

          //=== �t�@�C�����̕����R�[�h�ϊ�  
          $file_name_02 = mb_convert_encoding($file_name_02, "SJIS", "UTF-8");

          //=== �t�@�C���p�X
          $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];

          // �V�t�@�C��
          $create_csv_02 = $dirpath . "/" . $file_name_02;

          // PDO �I�u�W�F�N�g
          $pdo = new PDO($dsn, $user, $password);

          // SQL
          $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
                  WHERE creation_time BETWEEN ? AND ? ORDER BY user_id DESC");

          // CSV �G�N�X�|�[�g �J�n��
          $stmt->bindValue(
            1,
            $date_target,
            PDO::PARAM_STR
          );

          // CSV �G�N�X�|�[�g �I����
          $stmt->bindValue(
            2,
            $date_target_02,
            PDO::PARAM_STR
          );

          // SQL ���s 
          $res = $stmt->execute();

          if (touch($create_csv_02)) {
            $file_02 = new SplFileObject($create_csv_02, "w");

            // �o�͂��� CSV �Ɂ@�w�b�_�[����������
            $file_02->fputcsv($export_header_02);
            // ============= END if

            //====================== �ϐ��錾

            $Siharai_M_Number; // �x�����o�^No
            $bank_siten_name_kana = "����ض��";   // �x�X���@�J�i
            $bank_kamoku = ""; //   �a�����

            $teigaku_F = "0"; // ��z�U��

            $koumoku_Last; // �f�t�H���g 0

            //======= ���t�I���@�f�[�^�@�擾
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


              // �Ј�����
              $row['user_name'] = mb_convert_encoding($row['user_name'], "SJIS-win", "auto");

              // �Ј��ԍ� 
              //  ========= 0 �p�f�B���O �i�U���j
              $row['employee_id'] = sprintf('%06d', $row['employee_id']);
              $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");

              // ���Z�@�փR�[�h 	bank_code �i�S���j
              $row['bank_code'] = sprintf('%04d', $row['bank_code']);
              $row['bank_code'] = mb_convert_encoding($row['bank_code'], "SJIS-win", "auto");

              // ���Z�@�֖� bank_name
              $row['bank_name'] = mb_convert_encoding($row['bank_name'], "SJIS-win", "auto");
              // ���Z�@�փt���K�i bank_name_kana


              $row['bank_name_kana'] = mb_convert_encoding($row['bank_name_kana'], "SJIS-win", "auto");


              // �x�X�R�[�h bank_siten_code �i�R���j
              $row['bank_code'] = sprintf('%03d', $row['bank_code']);
              $row['bank_siten_code'] = mb_convert_encoding($row['bank_siten_code'], "SJIS-win", "auto");

              // �x�X�� bank_siten_name 
              $row['bank_siten_name'] = mb_convert_encoding($row['bank_siten_name'], "SJIS-win", "auto");


              // �a�����  0:���� , 1:���� bank_kamoku
              $row['bank_kamoku'] = mb_convert_encoding($row['bank_kamoku'], "SJIS-win", "auto");
              if (strcmp($row['bank_kamoku'], "0") == 0) {
                $bank_kamoku = "����";
                $bank_kamoku = mb_convert_encoding($bank_kamoku, "SJIS-win", "auto");
              } else {
                $bank_kamoku = "����";
                $bank_kamoku = mb_convert_encoding($bank_kamoku, "SJIS-win", "auto");
              }

              // �����ԍ� kouzzz_number
              $row['kouzzz_number'] = mb_convert_encoding($row['kouzzz_number'], "SJIS-win", "auto");

              // �x�����o�^No
              $Siharai_M_Number = "1";

              // �x�X�t���K�i ���J�����Ȃ� $bank_siten_name_kana
              $bank_siten_name_kana = "����ض��";
              $bank_siten_name_kana = mb_convert_encoding($bank_siten_name_kana, "SJIS-win", "auto");

              // �U���萔�� ��
              //=== �󕶎�
              $row['kara_01'] = ""; // �� ���� ,   // �U���萔��

              $teigaku_F = "";
              $teigaku_F = mb_convert_encoding($teigaku_F, "SJIS-win", "auto"); // ��z�U��

              //===  10 �� ===
              $row['kara_02'] = ""; // �� ���� ,   //   �U���� 1�@���z,
              $row['kara_03'] = ""; // �� ���� ,   // �U����2�@���Z�@�փR�[�h,
              $row['kara_04'] = ""; // �� ���� ,   // �U����2�@���Z�@�֖�,
              $row['kara_05'] = ""; // �� ���� ,   // �U����2�@���Z�@�փt���K�i,
              $row['kara_06'] = ""; // �� ���� ,   // �U����2�@�x�X�R�[�h,
              $row['kara_07'] = ""; // �� ���� ,   // �U����2  �x�X��,
              $row['kara_08'] = ""; // �� ���� ,  �U����2   �x�X�t���K�i
              $row['kara_09'] = ""; // �� ���� ,   // �U����2�@�a�����,
              $row['kara_10'] = ""; // �� ���� ,   // �U����2�@�����ԍ�,
              $row['kara_11'] = ""; // �� ���� ,   // �U����2�@�U���萔��,
              $row['kara_12'] = ""; // �� ���� ,   // �U����2�@��z�U��,

              // �U����2, �������K�{������ �f�t�H���g 0  ���ڃ��X�g
              $koumoku_Last = "";

              $return_bank[] = array(
                'employee_id' => $row['employee_id'], // ���� �F �Ј��ԍ�
                'user_name' => $row['user_name'], // ���� �F �Ј�����

                // �x�����o�^No  �f�t�H���g: 1
                'Siharai_M_Number' => $Siharai_M_Number,

                'bank_code' => $row['bank_code'], // ���Z�@�փR�[�h
                'bank_name' => $row['bank_name'], // ���Z�@�֖�
                'bank_name_kana' => $row['bank_name_kana'], // ���Z�@�փt���K�i

                'bank_siten_code' => $row['bank_siten_code'], // �x�X�R�[�h
                'bank_siten_name' => $row['bank_siten_name'], // �x�X��
                'bank_siten_name_kana' => $bank_siten_name_kana, // �x�X�t���K�i

                'bank_kamoku' =>  $bank_kamoku, // �a�����
                'kouzzz_number' => $row['kouzzz_number'], // �����ԍ�

                'kara_01' => $row['kara_01'], // �U���萔��

                'teigaku_F' => $teigaku_F, // ��z�U��

                'kara_02' => $row['kara_02'], // ��@�J���� , 2
                'kara_03' => $row['kara_03'], // ��@�J���� , 3
                'kara_04' => $row['kara_04'], // ��@�J���� , 4
                'kara_05' => $row['kara_05'], // ��@�J���� , 5
                'kara_06' => $row['kara_06'], // ��@�J���� , 6
                'kara_07' => $row['kara_07'], // ��@�J���� , 7
                'kara_08' => $row['kara_08'], // ��@�J���� , 8
                'kara_09' => $row['kara_09'], // ��@�J���� , 9
                'kara_10' => $row['kara_10'], // ��@�J���� , 10
                'kara_11' => $row['kara_11'], // ��@�J���� , 10
                'kara_12' => $row['kara_12'], // ��@�J���� , 10

                'koumoku_Last' => $koumoku_Last, // ���ڃ��X�g 

              );
            }


            // �t�@�C���֑}�� �i.csv�j
            foreach ($return_bank as $item) {
              $file_02->fputcsv($item);
            }
          } //======= END if 




          //===================================
          // =========== ���݂̃t��URL �擾   �t�@�C���_�E�����[�h����
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
          //================= �A�b�v�f�[�g ���� �f�[�^�����t���O�� �f�[�^�쐬�ς݂ɂ���
          //==================================================================

          $stmt = $pdo->prepare("UPDATE User_Info_Table SET data_Flg = '1' 
            WHERE creation_time BETWEEN ? AND ?");

          // CSV �G�N�X�|�[�g �J�n��
          $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

          // CSV �G�N�X�|�[�g �I����
          $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);

          // SQL ���s 
          $res = $stmt->execute();
        } catch (PDOException $e) {

          // ************** �G���[���� ***************
          $csv_file_ok_FLG = "2";
        } finally {

          $pdo = null;

          // �y�[�W�����[�h
          header("Location: " . $_SERVER['PHP_SELF']);

          exit;
        }
      }
    }



    //==============================================================================
    //==============================================================================
    //=======  ���o�̓t�@�C���@�`�F�b�N (�f�t�H���g)   download_btn CSV �_�E�����[�h���� 
    //===============================================================================
    //==============================================================================

  } else if ($export_output === "csv" && isset($_POST['uninput_file'])) {


    //============ ���o�� �t�@�C���� CSV , Mcsv_baito_kanri_

    // CSV �t�@�C����  => ���o��_�A���o�C�g�Ǘ�_2021-10-12_121212.csv
    $file_name = "Mcsv_baito_kanri_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
      "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
      $get_now_arr['minute'] . $get_now_arr['second'] . ".csv";

    // �t�@�C�����̕����R�[�h�ϊ�  
    //  $file_name = mb_convert_encoding($file_name, "SJIS", "auto");
    $file_name = mb_convert_encoding($file_name, "SJIS", "UTF-8");

    /*
    $export_csv_title = [
      "���O", "�ӂ肪��", "�N��","���[���A�h���X", "����", "�o�^��"
    ]; //DB�e�[�u���̃w�b�_�[����
    */

    // ======= CSV �t�@�C���@�w�b�_�[�^�C�g���@�쐬
    //============= ���� 237 
    $export_csv_title = [
      "�Ј��ԍ�",
      "�Ј�����",
      "�t���K�i",
      "����",
      "���N����",
      "���N�����i����j",
      "���Ћ敪",
      "���ДN����",
      "���ДN�����i����j",
      "�Α��N��",
      "�Α��N��_����",
      "�Ј��敪",
      "��E",
      "�w��",
      "��������",
      "�E��",
      "�X�֔ԍ�",
      "�Z���P",
      "�Z���Q",
      "�Z���i�t���K�i�j�P",
      "�Z���i�t���K�i�j�Q",
      "�d�b�ԍ�",
      "���[���A�h���X",
      "��Q�敪",
      "�Ǖw",
      "�Ǖv",
      "�ΘJ�w��",
      "�ЊQ��",
      "�O���l",
      "�ސE���R",
      "�ސE�N����",
      "�ސE�N�����i����j",
      "���^�敪",
      "�ŕ\�敪",
      "�N�������Ώ�",
      "�x���`��",
      "�x�����p�^�[��",
      "���^���׏��p�^�[��",
      "�ܗ^���׏��p�^�[��",
      "���N�ی��Ώ�",
      "���ی��Ώ�",
      "���ی��z�i��~�j",
      "���ۏؔԍ��i����j",
      "���ۏؔԍ��i�g���j",
      "���ہE���N�@���i�擾�N����",
      "�����N���Ώ�",
      "70�Έȏ��p��",
      "�ی��Ҏ��",
      "���N���z�i��~�j",
      "��b�N���ԍ��P",
      "��b�N���ԍ��Q",
      "�Z���ԘJ���ҁi3/4�����j",
      "�J�Еی��Ώ�",
      "�ٗp�ی��Ώ�",
      "�J���ی��p�敪",
      "�ٗp�ی���ی��Ҕԍ��P",
      "�ٗp�ی���ی��Ҕԍ��Q",
      "�ٗp�ی���ی��Ҕԍ��R",
      "�J���ی��@���i�擾�N����",
      "�z���",
      "�z��Ҏ���",
      "�z��҃t���K�i",
      "�z��Ґ���",
      "�z��Ґ��N����",
      "�z��Ґ��N�����i����j",
      "����T���Ώ۔z���",
      "�z��ҘV�l",
      "�z��ҏ�Q��",
      "�z��ғ���",
      "�z��Ҕ񋏏Z��",
      "�}�{�T���Ώېl��",
      "�}�{�e��_�}�{�e����_1",
      "�}�{�e��_�t���K�i_1",
      "�}�{�e��_����_1",
      "�}�{�e��_���N����_1",
      "�}�{�e��_���N�����i����j_1",
      "�}�{�e��_�}�{�敪_1",
      "�}�{�e��_��Q�ҋ敪_1",
      "�}�{�e��_�����敪_1",
      "�}�{�e��_�����V�e���敪_1",
      "�}�{�e��_�񋏏Z�ҋ敪_1",
      "�}�{�e��_�T���v�Z�敪_1",
      "�}�{�e��_�}�{�e����_2",
      "�}�{�e��_�t���K�i_2",
      "�}�{�e��_����_2",
      "�}�{�e��_���N����_2",
      "�}�{�e��_���N�����i����j_2",
      "�}�{�e��_�}�{�敪_2",
      "�}�{�e��_��Q�ҋ敪_2",
      "�}�{�e��_�����敪_2",
      "�}�{�e��_�����V�e���敪_2",
      "�}�{�e��_�񋏏Z�ҋ敪_2",
      "�}�{�e��_�T���v�Z�敪_2",
      "�}�{�e��_�}�{�e����_3",
      "�}�{�e��_�t���K�i_3",
      "�}�{�e��_����_3",
      "�}�{�e��_���N����_3",
      "�}�{�e��_���N�����i����j_3",
      "�}�{�e��_�}�{�敪_3",
      "�}�{�e��_��Q�ҋ敪_3",
      "�}�{�e��_�����敪_3",
      "�}�{�e��_�����V�e���敪_3",
      "�}�{�e��_�񋏏Z�ҋ敪_3",
      "�}�{�e��_�T���v�Z�敪_3",
      "�}�{�e��_�}�{�e����_4",
      "�}�{�e��_�t���K�i_4",
      "�}�{�e��_����_4",
      "�}�{�e��_���N����_4",
      "�}�{�e��_���N�����i����j_4",
      "�}�{�e��_�}�{�敪_4",
      "�}�{�e��_��Q�ҋ敪_4",
      "�}�{�e��_�����敪_4",
      "�}�{�e��_�����V�e���敪_4",
      "�}�{�e��_�񋏏Z�ҋ敪_4",
      "�}�{�e��_�T���v�Z�敪_4",
      "�}�{�e��_�}�{�e����_5",
      "�}�{�e��_�t���K�i_5",
      "�}�{�e��_����_5",
      "�}�{�e��_���N����_5",
      "�}�{�e��_���N�����i����j_5",
      "�}�{�e��_�}�{�敪_5",
      "�}�{�e��_��Q�ҋ敪_5",
      "�}�{�e��_�����敪_5",
      "�}�{�e��_�����V�e���敪_5",
      "�}�{�e��_�񋏏Z�ҋ敪_5",
      "�}�{�e��_�T���v�Z�敪_5",
      "�}�{�e��_�}�{�e����_6",
      "�}�{�e��_�t���K�i_6",
      "�}�{�e��_����_6",
      "�}�{�e��_���N����_6",
      "�}�{�e��_���N�����i����j_6",
      "�}�{�e��_�}�{�敪_6",
      "�}�{�e��_��Q�ҋ敪_6",
      "�}�{�e��_�����敪_6",
      "�}�{�e��_�����V�e���敪_6",
      "�}�{�e��_�񋏏Z�ҋ敪_6",
      "�}�{�e��_�T���v�Z�敪_6",
      "�}�{�e��_�}�{�e����_7",
      "�}�{�e��_�t���K�i_7",
      "�}�{�e��_����_7",
      "�}�{�e��_���N����_7",
      "�}�{�e��_���N�����i����j_7",
      "�}�{�e��_�}�{�敪_7",
      "�}�{�e��_��Q�ҋ敪_7",
      "�}�{�e��_�����敪_7",
      "�}�{�e��_�����V�e���敪_7",
      "�}�{�e��_�񋏏Z�ҋ敪_7",
      "�}�{�e��_�T���v�Z�敪_7",
      "�}�{�e��_�}�{�e����_8",
      "�}�{�e��_�t���K�i_8",
      "�}�{�e��_����_8",
      "�}�{�e��_���N����_8",
      "�}�{�e��_���N�����i����j_8",
      "�}�{�e��_�}�{�敪_8",
      "�}�{�e��_��Q�ҋ敪_8",
      "�}�{�e��_�����敪_8",
      "�}�{�e��_�����V�e���敪_8",
      "�}�{�e��_�񋏏Z�ҋ敪_8",
      "�}�{�e��_�T���v�Z�敪_8",
      "�}�{�e��_�}�{�e����_9",
      "�}�{�e��_�t���K�i_9",
      "�}�{�e��_����_9",
      "�}�{�e��_���N����_9",
      "�}�{�e��_���N�����i����j_9",
      "�}�{�e��_�}�{�敪_9",
      "�}�{�e��_��Q�ҋ敪_9",
      "�}�{�e��_�����敪_9",
      "�}�{�e��_�����V�e���敪_9",
      "�}�{�e��_�񋏏Z�ҋ敪_9",
      "�}�{�e��_�T���v�Z�敪_9",
      "�}�{�e��_�}�{�e����_10",
      "�}�{�e��_�t���K�i_10",
      "�}�{�e��_����_10",
      "�}�{�e��_���N����_10",
      "�}�{�e��_���N�����i����j_10",
      "�}�{�e��_�}�{�敪_10",
      "�}�{�e��_��Q�ҋ敪_10",
      "�}�{�e��_�����敪_10",
      "�}�{�e��_�����V�e���敪_10",
      "�}�{�e��_�񋏏Z�ҋ敪_10",
      "�}�{�e��_�T���v�Z�敪_10",
      "�}�{�e��_�}�{�e����_11",
      "�}�{�e��_�t���K�i_11",
      "�}�{�e��_����_11",
      "�}�{�e��_���N����_11",
      "�}�{�e��_���N�����i����j_11",
      "�}�{�e��_�}�{�敪_11",
      "�}�{�e��_��Q�ҋ敪_11",
      "�}�{�e��_�����敪_11",
      "�}�{�e��_�����V�e���敪_11",
      "�}�{�e��_�񋏏Z�ҋ敪_11",
      "�}�{�e��_�T���v�Z�敪_11",
      "�}�{�e��_�}�{�e����_12",
      "�}�{�e��_�t���K�i_12",
      "�}�{�e��_����_12",
      "�}�{�e��_���N����_12",
      "�}�{�e��_���N�����i����j_12",
      "�}�{�e��_�}�{�敪_12",
      "�}�{�e��_��Q�ҋ敪_12",
      "�}�{�e��_�����敪_12",
      "�}�{�e��_�����V�e���敪_12",
      "�}�{�e��_�񋏏Z�ҋ敪_12",
      "�}�{�e��_�T���v�Z�敪_12",
      "�}�{�e��_�}�{�e����_13",
      "�}�{�e��_�t���K�i_13",
      "�}�{�e��_����_13",
      "�}�{�e��_���N����_13",
      "�}�{�e��_���N�����i����j_13",
      "�}�{�e��_�}�{�敪_13",
      "�}�{�e��_��Q�ҋ敪_13",
      "�}�{�e��_�����敪_13",
      "�}�{�e��_�����V�e���敪_13",
      "�}�{�e��_�񋏏Z�ҋ敪_13",
      "�}�{�e��_�T���v�Z�敪_13",
      "�}�{�e��_�}�{�e����_14",
      "�}�{�e��_�t���K�i_14",
      "�}�{�e��_����_14",
      "�}�{�e��_���N����_14",
      "�}�{�e��_���N�����i����j_14",
      "�}�{�e��_�}�{�敪_14",
      "�}�{�e��_��Q�ҋ敪_14",
      "�}�{�e��_�����敪_14",
      "�}�{�e��_�����V�e���敪_14",
      "�}�{�e��_�񋏏Z�ҋ敪_14",
      "�}�{�e��_�T���v�Z�敪_14",
      "�}�{�e��_�}�{�e����_15",
      "�}�{�e��_�t���K�i_15",
      "�}�{�e��_����_15",
      "�}�{�e��_���N����_15",
      "�}�{�e��_���N�����i����j_15",
      "�}�{�e��_�}�{�敪_15",
      "�}�{�e��_��Q�ҋ敪_15",
      "�}�{�e��_�����敪_15",
      "�}�{�e��_�����V�e���敪_15",
      "�}�{�e��_�񋏏Z�ҋ敪_15",
      "�}�{�e��_�T���v�Z�敪_15",
    ];

    $export_header = [];

    foreach ($export_csv_title as $key => $val) {

      $export_header[] = mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
    }



    $export_header_02 = [];

    $export_csv_title_02 = [];

    $export_csv_title_02 = [
      '�Ј��ԍ�',
      '�Ј�����',
      '�x�����o�^No',
      '�U����@�@���Z�@�փR�[�h',
      '�U����@�@���Z�@�֖�',
      '�U����@�@���Z�@�փt���K�i',
      '�U����@�@�x�X�R�[�h',
      '�U����@�@�x�X��',
      '�U����@�@�x�X�t���K�i',
      '�U����@�@�a�����',
      '�U����@�@�����ԍ�',
      '�U����@�@�U���萔��',
      '�U����@�@��z�U��',
      '�U����@�@���z',
      '�U����A�@���Z�@�փR�[�h',
      '�U����A�@���Z�@�֖�',
      '�U����A�@���Z�@�փt���K�i',
      '�U����A�@�x�X�R�[�h',
      '�U����A�@�x�X��',
      '�U����A�@�x�X�t���K�i',
      '�U����A�@�a�����',
      '�U����A�@�����ԍ�',
      '�U����A�@�U���萔��',
      '�U����A�@��z�U��',
      '�U����A�@���z'
    ];

    foreach ($export_csv_title_02 as $key => $val) {

      $export_header_02[] = mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
    }


    //================================  
    //================== �ڑ����
    //================================  
    $dsn = '';
    $user = '';
    $password = '';

    // �i�荞�݁@���ʁ@�i�[�z��
    $retunr_output_csv = []; // CSV

    try {

      // PDO �I�u�W�F�N�g�쐬
      $pdo = new PDO($dsn, $user, $password);

      $stmt = $pdo->prepare("SELECT * from User_Info_Table WHERE data_Flg = '0'");

      // SQL ���s
      $res = $stmt->execute();


      // �i�荞�݁@���ʁ@�i�[�z��
      $retunr_tmp = [];
      $return = [];

      // �t�@���_���Ȃ�������A�t�H���_���쐬  �`�� files_20211025
      $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];
      if (!file_exists($dirpath)) {
        mkdir($dirpath, 0777);

        //chmod�֐��Łuhoge�v�f�B���N�g���̃p�[�~�b�V�������u0777�v�ɂ���
        //   chmod($dirpath, 0777);
      }

      //====== �t�@�C���̕ۑ�
      // �t�@�C�������ꏊ
      $create_csv = $dirpath . "/" . $file_name;


      if (touch($create_csv)) {
        $file = new SplFileObject($create_csv, "w");

        // �o�͂��� CSV �Ɂ@�w�b�_�[����������
        $file->fputcsv($export_header);

        //====================================================== 
        //=========================== CSV �p�@�ϐ��錾
        //====================================================== 
        $sex = ""; // ����
        $wareki = ""; // �a��̔N
        $wareki_r = ""; // �a�� �N���� �o�͗p
        $month_r = "";
        $day_r = ""; // ���t�@���ɂ�

        $Seireki_r = ""; // ����o�͗p

        $hokensya_K = ""; // �ی��Ҏ��  �j�q�A���q
        $syasin_K = ""; // �Ј��敪
        $syozoku_b = ""; // ��������


        //======= ���t�I���@�f�[�^�@�擾
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


          // ============ ���������΍� ============
          $row['user_name'] = mb_convert_encoding($row['user_name'], "SJIS-win", "auto"); // �Ј�����


          //=== �ӂ肪�ȁ@���@�J�i�@�֕ϊ�
          $row['furi_name'] = mb_convert_kana($row['furi_name'], "h", "UTF-8"); // �t���K�i
          $row['furi_name'] = mb_convert_encoding($row['furi_name'], "SJIS-win", "auto");

          // ���[���A�h���X
          $row['email'] = mb_convert_encoding($row['email'], "SJIS-win", "auto");

          // �z��� spouse
          if (strcmp($row['spouse'], "0") == 0) {
            $row['spouse'] = "�Ȃ�";
            $row['spouse'] = mb_convert_encoding($row['spouse'], "SJIS-win", "auto");
          } else {
            $row['spouse'] = "����";
            $row['spouse'] = mb_convert_encoding($row['spouse'], "SJIS-win", "auto");
          }


          // �}�{�l�� 	dependents_num
          $row['dependents_num'] = mb_convert_encoding($row['dependents_num'], "SJIS-win", "auto");

          $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto"); // 0 => �j�� , 1 => ����

          //=== ����
          if (strcmp($row['sex'], "0") == 0) {
            $row['sex'] = "�j"; // �ی��Ҏ��
            $hokensya_K = ""; // �ی��Ҏ��

            $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
            $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
          } else {
            $row['sex'] = "��"; // �ی��Ҏ��
            $hokensya_K = ""; // �ی��Ҏ��

            $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
            $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
          }

          //=== ���N�����@�i���� => �a��@�ϊ��j
          $row['birthday'] = mb_convert_encoding($row['birthday'], "SJIS-win", "auto");

          // �N
          $year = mb_substr($row['birthday'], 0, 4); // ����@���o�� 1900

          // �a��ϊ��p function  Wareki_Parse
          $wareki = Wareki_Parse($year);

          // ��
          $month = mb_substr($row['birthday'], 5, 2);

          if (strpos(
            $month,
            '-'
          ) !== false) {
            // - �n�C�t�� ���܂܂�Ă���ꍇ , 0�@�p�f�B���O
            $month = str_replace("-", "", $month);
            $month_r = "0" . $month;
          } else {
            $month_r = $month;
          }


          // ��
          $day = mb_substr($row['birthday'], 7, 2);
          // ������̒����擾  1,  10
          $day_num = mb_strlen($day);

          if ($day_num == 1) {
            $day_r = "0" . $day;
          } else if (strpos($day, '-') !== false) {
            $day = str_replace("-", "", $day);
            $day_r = $day;
          } else {
            $day_r = $day;
          }

          $wareki_r = $wareki . $month_r . "��" . $day_r . "��";
          $wareki_r = mb_convert_encoding($wareki_r, "SJIS-win", "auto");

          // ����
          $Seireki_r = $year . "�N" . $month_r . "��" . $day_r . "��";
          $Seireki_r = mb_convert_encoding($Seireki_r, "SJIS-win", "auto");

          //=== �X�֔ԍ� zip  �i-�j ������
          $row['zip'] = substr($row['zip'], 0, 3) . "-" . substr($row['zip'], 3);
          $row['zip'] = mb_convert_encoding($row['zip'], "SJIS-win", "auto");

          //=== �Z���P address_01
          $row['address_01'] = mb_convert_encoding($row['address_01'], "SJIS-win", "auto");
          //=== �d�b�ԍ� tel

          // �d�b�ԍ� �i-�j �n�C�t���t��
          $row['tel'] = substr($row['tel'], 0, 3) . "-" . substr($row['tel'], 3, 4) . "-" . substr($row['tel'], 7);
          $row['tel'] = mb_convert_encoding($row['tel'], "SJIS-win", "auto");


          $row['creation_time'] = mb_convert_encoding($row['creation_time'], "SJIS-win", "auto");
          $row['data_Flg'] = mb_convert_encoding($row['data_Flg'], "SJIS-win", "auto");

          // �Ј��ԍ� employee_id �i�U���j
          $row['employee_id'] = sprintf('%06d', $row['employee_id']);
          $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");

          //=== �󕶎�
          $row['kara_01'] = ""; // �� ���� ,   // ����7: ���Ћ敪
          $row['kara_02'] = ""; // �� ���� ,   // ����8: ���ДN����
          $row['kara_03'] = ""; // �� ���� ,   // ����9: ���ДN�����i����j
          $row['kara_04'] = ""; // �� ���� ,   // ����10: �Α��N��
          $row['kara_05'] = ""; // �� ���� ,   // ����11: �Α��N��_����

          $syasin_K = "�A���o�C�g"; // �� ���� ,   // ����12: �Ј��敪 => �A���o�C�g
          $syasin_K = mb_convert_encoding($syasin_K, "SJIS-win", "auto");


          $row['kara_07'] = ""; // �� ���� ,   // ����13: ��E
          $row['kara_08'] = ""; // �� ���� ,   // ����14: �w��

          //=============== 2021_12_21 �l�ύX
          //     $syozoku_b = "003";                 // ����15: ��������
          $syozoku_b = "�A���o�C�g";                 // ����15: ��������
          $syozoku_b = mb_convert_encoding($syozoku_b, "SJIS-win", "auto");

          $row['kara_09'] = ""; // �� ���� ,   // 
          $row['kara_10'] = ""; // �� ���� ,   // �E��

          $row['kara_11'] = ""; // �� ���� ,  // �Z�� 2 
          $row['kara_12'] = ""; // �� ���� ,  // �Z�� �i�t���K�i�j�P �����ꂽ�� 
          $row['kara_13'] = ""; // �� ���� ,  // �Z�� �i�t���K�i�j�Q

          //=== ��Y�� Start
          $row['higaitou_01'] = "��Y��";
          $row['higaitou_02'] = "��Y��";
          $row['higaitou_03'] = "��Y��";
          $row['higaitou_04'] = "��Y��";
          $row['higaitou_05'] = "��Y��";
          $row['higaitou_06'] = "��Y��";
          // �G���R�[�h
          $row['higaitou_01'] = mb_convert_encoding($row['higaitou_01'], "SJIS-win", "auto");
          $row['higaitou_02'] = mb_convert_encoding($row['higaitou_02'], "SJIS-win", "auto");
          $row['higaitou_03'] = mb_convert_encoding($row['higaitou_03'], "SJIS-win", "auto");
          $row['higaitou_04'] = mb_convert_encoding($row['higaitou_04'], "SJIS-win", "auto");
          $row['higaitou_05'] = mb_convert_encoding($row['higaitou_05'], "SJIS-win", "auto");
          $row['higaitou_06'] = mb_convert_encoding($row['higaitou_06'], "SJIS-win", "auto");
          //=== ��Y�� End

          //=== �󕶎�
          $row['kara_14'] = "";  // �ސE���R
          $row['kara_15'] = "";  // �ސE�N����
          $row['kara_16'] = "";  // �ސE�N�����i����j

          //=== ��^�@�敪�@
          $row['kubun_01'] =  "����"; // ���^�敪
          $row['kubun_02'] =  "���z�b��"; // �ŕ\�敪
          $row['kubun_03'] =  "����"; // �N�������Ώ� 
          $row['kubun_04'] =  "�U��"; // �x���`��
          $row['kubun_05'] =  "�T���v��"; // �x�����p�^�[��

          //=============== 2021_12_21 �l�ύX
          //   $row['kubun_06'] =  "�Ј��p�^�[��"; // ���^���׏��p�^�[��
          $row['kubun_06'] =  ""; // ���^���׏��p�^�[��

          //=============== 2021_12_21 �l�ύX
          //  $row['kubun_07'] =  "�ܗ^�T���v��"; // �ܗ^���׏��p�^�[��
          $row['kubun_07'] =  ""; // �ܗ^���׏��p�^�[��


          $row['kubun_08'] =  "";   // ���N�ی��Ώ�
          $row['kubun_09'] =  "";   //���ی��Ώ�

          //=============== 2021_12_21 �l�ύX
          //  $row['kubun_10'] =  "220";   //  ���ی��z�i��~�j
          $row['kubun_10'] =  "";   //  ���ی��z�i��~�j

          //=============== 2021_12_21 �l�ύX
          //  $row['kubun_10_02'] = "367"; // ���ۏؔԍ�
          $row['kubun_10_02'] = ""; // ���ۏؔԍ�

          // �G���R�[�h
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
          //=== ��^�@�敪 END

          //=== �󕶎�
          $row['kara_17'] = "";   // ��b�N���ԍ��P
          $row['kara_18'] = "";   // ��b�N���ԍ��Q

          //=== ��^�@�敪�@
          $row['kubun_11'] = ""; // �����N���Ώ� , �Ώ�  ��

          $row['kubun_12'] = "��Y��"; // 70�Έȏ��p��
          $row['kubun_13'] = $hokensya_K; // �ی��Ҏ�� , �j�q�A���q
          $row['kubun_14'] = ""; // ����49:���N���z�i��~�j ��
          // �G���R�[�h
          $row['kubun_11'] = mb_convert_encoding($row['kubun_11'], "SJIS-win", "auto");
          $row['kubun_12'] = mb_convert_encoding($row['kubun_12'], "SJIS-win", "auto");
          $row['kubun_13'] = mb_convert_encoding($row['kubun_13'], "SJIS-win", "auto");
          $row['kubun_14'] = mb_convert_encoding($row['kubun_14'], "SJIS-win", "auto");
          //=== ��^�@�敪�@END

          $row['kara_19'] = ""; // ��b�N���ԍ��P
          $row['kara_20'] = ""; // ��b�N���ԍ��Q

          //=== ��^�敪
          $row['kubun_15'] =  "�ΏۊO";  // �Z���ԘJ���ҁi3/4�����j
          $row['kubun_16'] =  "�Ώ�";  // �J�Еی��Ώ�
          $row['kubun_17'] =  "�Ώ�";  // �ٗp�ی��Ώ�
          $row['kubun_18'] =  "��p";  // �J���ی��p�敪

          //=============== 2021_12_21 �l�ύX
          //  $row['kubun_19'] =  "5029";  // �ٗp�ی���ی��Ҕԍ��P
          $row['kubun_19'] =  "";  // �ٗp�ی���ی��Ҕԍ��P

          //=============== 2021_12_21 �l�ύX
          //  $row['kubun_20'] =  "530842";  // �ٗp�ی���ی��Ҕԍ� 2
          $row['kubun_20'] =  "";  // �ٗp�ی���ی��Ҕԍ� 2

          //=============== 2021_12_21 �l�ύX
          //  $row['kubun_21'] =  "4";  // �ٗp�ی���ی��Ҕԍ� 3
          $row['kubun_21'] =  "";  // �ٗp�ی���ی��Ҕԍ� 3

          // �G���R�[�h
          $row['kubun_15'] = mb_convert_encoding($row['kubun_15'], "SJIS-win", "auto");
          $row['kubun_16'] = mb_convert_encoding($row['kubun_16'], "SJIS-win", "auto");
          $row['kubun_17'] = mb_convert_encoding($row['kubun_17'], "SJIS-win", "auto");
          $row['kubun_18'] = mb_convert_encoding($row['kubun_18'], "SJIS-win", "auto");
          $row['kubun_19'] = mb_convert_encoding($row['kubun_19'], "SJIS-win", "auto");
          $row['kubun_20'] = mb_convert_encoding($row['kubun_20'], "SJIS-win", "auto");
          $row['kubun_21'] = mb_convert_encoding($row['kubun_21'], "SJIS-win", "auto");

          //=== �󕶎�
          $row['kara_22'] = ""; // �J���ی��@���i�擾�N����

          $row['kara_23'] = "";   // 
          $row['kara_24'] = "";   //  �z��Ґ��� �� 2021_12_07 �C��

          $row['kubun_22'] =  "";  // �z��Ґ��N����
          $row['kubun_22'] = mb_convert_encoding($row['kubun_22'], "SJIS-win", "auto");

          $row['kara_25'] = "";  //  �z��Ґ��N�����i����j
          //       $row['kara_26'] = "";  // 

          $row['higaitou_07'] = "��Y��";   // ����T���Ώ۔z���
          $row['higaitou_08'] = "��Y��";   // �z��ҘV�l
          $row['higaitou_09'] = "��Y��";   // �z��ҏ�Q��
          $row['higaitou_10'] = "�񓯋�";   // �z��ғ���  : �񓯋�
          $row['higaitou_11'] = "��Y��";   // �z��Ҕ񋏏Z��

          $row['higaitou_07'] = mb_convert_encoding($row['higaitou_07'], "SJIS-win", "auto");
          $row['higaitou_08'] = mb_convert_encoding($row['higaitou_08'], "SJIS-win", "auto");
          $row['higaitou_09'] = mb_convert_encoding($row['higaitou_09'], "SJIS-win", "auto");
          $row['higaitou_10'] = mb_convert_encoding($row['higaitou_10'], "SJIS-win", "auto");
          $row['higaitou_11'] = mb_convert_encoding($row['higaitou_11'], "SJIS-win", "auto");

          // =========�@���K�{ �}�{�T���Ώېl��
          $row['dependents_num'] = mb_convert_encoding($row['dependents_num'], "SJIS-win", "auto");

          //======== 165 �́@�󔒍s
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
          //*********************  �Q�����z��֊i�[ ******************************
          /********************************************************************** */
          $retunr_output_csv[] = array(
            'employee_id' => $row['employee_id'], // ���ڂP�F �Ǘ��ԍ� �Z
            'user_name' => $row['user_name'], // ���ڂQ�F �Ј������@�Z
            'furi_name' => $row['furi_name'], // ���ڂR�F �t���K�i�@�Z
            'sex' => $row['sex'], // ���ڂS�F ���ʁ@�Z
            'wareki' => $wareki_r, //�@���ڂT�F �a��@�Z

            'birthday' => $Seireki_r, //�@���ڂU�F ����@�Z

            /*  
              'creation_time' => $row['creation_time'],
              'data_Flg' => $row['data_Flg'],
              */

            'kara_01' => $row['kara_01'], //  ���ڂV�F�@���Ћ敪�@
            'kara_02' => $row['kara_02'], //�@���ڂW�F�@���ДN����
            'kara_03' => $row['kara_03'], //  ���ڂX�F�@���ДN�����i����j
            'kara_04' => $row['kara_04'], //  ���ڂP�O�F�Α��N���@
            'kara_05' => $row['kara_05'], //  ���ڂP�P�F�Α��N��_����

            'syasin_K' => $syasin_K, // ���ڂP�Q�F ,�Ј��敪�@�Z

            'kara_07' => $row['kara_07'], // ����13�F ��E
            'kara_08' => $row['kara_08'], // ����14�F �w��

            'syozoku_b' => $syozoku_b, // ����15�F ��������@�Z

            'kara_10' => $row['kara_10'], // ����16�F�E��

            'zip' => $row['zip'], // ���� 17 �F�@�X�֔ԍ��@�Z
            'address_01' => $row['address_01'], // ����18 �F �Z���P�@�Z

            'kara_11' => $row['kara_11'], // ��@�J���� 19  �Z���Q
            'kara_12' => $row['kara_12'], // ��@�J���� 20  �Z���i�t���K�i�j�P
            'kara_13' => $row['kara_13'], // ��@�J���� 21  �Z���i�t���K�i�j�Q

            'tel' => $row['tel'],   // ����22 �d�b�ԍ��@�Z
            'email' => $row['email'],   // ����23: email�@�Z

            'higaitou_01' => $row['higaitou_01'],   // ����24:��Q�敪  ��Y��
            'higaitou_02' => $row['higaitou_02'],   // ����25:�Ǖw  ��Y��
            'higaitou_03' => $row['higaitou_03'],   // ����26:�Ǖv ��Y��
            'higaitou_04' => $row['higaitou_04'],   // ����27: ��Y��
            'higaitou_05' => $row['higaitou_05'],   // ����28: ��Y��
            'higaitou_06' => $row['higaitou_06'],   // ����29: ��Y��

            'kara_14' => $row['kara_14'], // ����30:�ސE���R ��@�J���� , 14
            'kara_15' => $row['kara_15'], // ����31:�ސE�N���� ��@�J���� , 15
            'kara_16' => $row['kara_16'], // ����32:�ސE�N���� ��@�J���� , 16

            'kubun_01' => $row['kubun_01'], // ��^�敪 01, ����33:���^�敪:����
            'kubun_02' => $row['kubun_02'], // ��^�敪 02, ����34:�ŕ\�敪:���z�b��
            'kubun_03' => $row['kubun_03'], // ��^�敪 03, ����35:�N�������Ώ�:����
            'kubun_04' => $row['kubun_04'], // ��^�敪 04, ����36:�x���`��:�U��
            'kubun_05' => $row['kubun_05'], // ��^�敪 05, ����37:�x�����p�^�[��:�T���v��
            'kubun_06' => $row['kubun_06'], // ��^�敪 06, ����38:���^���׏��p�^�[��:�Ј��p�^�[��
            'kubun_07' => $row['kubun_07'], // ��^�敪 07, ����39:�ܗ^���׏��p�^�[��:�ܗ^�T���v��
            'kubun_08' => $row['kubun_08'], // ��^�敪 08, ����40:���N�ی��Ώ�
            'kubun_09' => $row['kubun_09'], // ��^�敪 09, ����41:���ی��Ώ�
            'kubun_10' => $row['kubun_10'], // ��^�敪 10, ����42:���ی��z�i��~�j:220
            'kubun_10_02' => $row['kubun_10_02'], // ����43:���ۏؔԍ��i����j 367

            'kara_17' => $row['kara_17'], // ��@�J���� , ����44:���ۏؔԍ��i�g���j
            'kara_18' => $row['kara_18'], // ��@�J���� , ����45:���ہE���N�@���i�擾�N����

            'kubun_11' => $row['kubun_11'], // ��^�敪 11 ����46:�����N���Ώ�, ��

            'kubun_12' => $row['kubun_12'], // ��^�敪 12 ����47:70�Έȏ��p��:��Y��
            'kubun_13' => $row['kubun_13'], // ��^�敪 13 ����48:�ی��Ҏ��:���q
            'kubun_14' => $row['kubun_14'], // ��^�敪 14 ����49:���N���z�i��~�j

            'kara_19' => $row['kara_19'], // ��@�J���� , 19  ����50:��b�N���ԍ��P
            'kara_20' => $row['kara_20'], // ��@�J���� , 20  ����51:��b�N���ԍ��Q

            'kubun_15' => $row['kubun_15'], // ��^�敪 15 ����52:�Z���ԘJ���ҁi3/4�����j:�ΏۊO
            'kubun_16' => $row['kubun_16'], // ��^�敪 16 ����53:�J�Еی��Ώ�:�Ώ�
            'kubun_17' => $row['kubun_17'], // ��^�敪 17 ����54:�ٗp�ی��Ώ�:�Ώ�
            'kubun_18' => $row['kubun_18'], // ��^�敪 18 ����55:�J���ی��p�敪:��p
            'kubun_19' => $row['kubun_19'], // ��^�敪 19 ����56:�ٗp�ی���ی��Ҕԍ��P:5029
            'kubun_20' => $row['kubun_20'], // ��^�敪 20 ����57:�ٗp�ی���ی��Ҕԍ��Q:530842
            'kubun_21' => $row['kubun_21'], // ��^�敪 21 ����58:�ٗp�ی���ی��Ҕԍ��R:4

            'kara_21' => $row['kara_21'], // ��@�J���� , 21 ����59:�J���ی��@���i�擾�N����:


            // �z��҂���@�A�Ȃ�
            'spouse' => $row['spouse'], // ����60:�z���:�Ȃ�

            'kara_22' => $row['kara_22'], // ��@�J���� , 22 ����61:�z��Ҏ���:
            'kara_23' => $row['kara_23'], // ��@�J���� , 23 ����62:�z��҃t���K�i:
            'kara_24' => $row['kara_24'], // ��@�J���� , 24 ����63:�z��Ґ���:

            'kubun_22' => $row['kubun_22'], // ��^�敪 22 ����64:�z��Ґ��N����:

            'kara_25' => $row['kara_25'], // ��@�J���� , 25 ����65:�z��Ґ��N�����i����j
            //  'kara_26' => $row['kara_26'], // ��@�J���� , 26

            'higaitou_07' => $row['higaitou_07'], // ����66:����T���Ώ۔z���:��Y��
            'higaitou_08' => $row['higaitou_08'], // ����67:�z��ҘV�l:��Y��
            'higaitou_09' => $row['higaitou_09'], // ����68:�z��ҏ�Q��:��Y��
            'higaitou_10' => $row['higaitou_10'], // ����69:�z��ғ���:�񓯋�
            'higaitou_11' => $row['higaitou_11'], // ����70:�z��Ҕ񋏏Z��:��Y��

            //�@���K�{ �}�{�l�� dependents_num
            'dependents_num' => $row['dependents_num'], // ����71:�}�{�T���Ώېl��:0

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

          // bank�p�@2�����z��@$return_bank []

        }

        // �t�@�C���֑}�� �i.csv�j
        foreach ($retunr_output_csv as $item) {
          $file->fputcsv($item);
        }


        //================= CSV �t�@�C���쐬���� �t���O
        $csv_file_ok_FLG = "1";

        // csv �Z���N�g�I���{�b�N�X �L�� OK 
        $export_output_err = "0";
      } //=== END if


      //. $_SERVER["REQUEST_URI"];

      //==============================================
      //*************** �t�@�C���̃_�E�����[�h���� */
      //==============================================
      //  t_download($protocol);


      //===================================
      // =========== ���݂̃t��URL �擾
      //===================================

      /*
      if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {

        $protocol = 'https://';
      } else {

        $protocol = 'http://';
      }

      // ./files �� . ���폜
      $new_create_csv = mb_substr($create_csv, 1);

      $protocol .= $_SERVER["HTTP_HOST"] . '/job_recruit/csv' . $new_create_csv;
   

      // �t�@�C���^�C�v���w��
      header('Content-Type: application/octet-stream');
      // �t�@�C���T�C�Y���擾���A�_�E�����[�h�̐i����\��
      //       header('Content-Length: '.filesize($protocol));
      // �t�@�C���̃_�E�����[�h�A���l�[�����w��


      header('Content-Disposition: attachment; filename="' . $file_name . '"');

      ob_clean();  //�ǋL
      flush();     //�ǋL

      // �t�@�C����ǂݍ��݃_�E�����[�h�����s
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
      //================== ��s�p�@CSV �쐬
      //=====================================================
      //=====================================================

      //=== CSV �t�@�C����  => �A���o�C�g�Ǘ�_2021-10-12_121212.csv
      $file_name_02 = "Mcsv_GinkouJ_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
        "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
        $get_now_arr['minute'] . $get_now_arr['second'] . ".csv";

      // CSV �t�@�C���� 2 => file_name_02

      //=== �t�@�C�����̕����R�[�h�ϊ�  
      $file_name_02 = mb_convert_encoding($file_name_02, "SJIS", "UTF-8");

      //=== �t�@�C���p�X
      $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];

      // �V�t�@�C��
      $create_csv_02 = $dirpath . "/" . $file_name_02;

      // PDO �I�u�W�F�N�g�쐬
      $pdo = new PDO($dsn, $user, $password);

      // SQL
      $stmt = $pdo->prepare("SELECT * from User_Info_Table WHERE data_Flg = '0'");

      // SQL ���s 
      $res = $stmt->execute();

      if (touch($create_csv_02)) {
        $file_02 = new SplFileObject($create_csv_02, "w");

        // �o�͂��� CSV �Ɂ@�w�b�_�[����������
        $file_02->fputcsv($export_header_02);
        // ============= END if

        //====================== �ϐ��錾

        $Siharai_M_Number; // �x�����o�^No
        $bank_siten_name_kana = "��݃t���K�i";   // �x�X���@�J�i
        $bank_kamoku = ""; //   �a�����

        $teigaku_F = "0"; // ��z�U��

        $koumoku_Last; // �f�t�H���g 0

        //======= ���t�I���@�f�[�^�@�擾
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


          // �Ј�����
          $row['user_name'] = mb_convert_encoding($row['user_name'], "SJIS-win", "auto");

          // �Ј��ԍ��@�i�U���j
          $row['employee_id'] = sprintf('%06d', $row['employee_id']);
          $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");


          // ���Z�@�փR�[�h 	bank_code�@�i�S���j
          $row['bank_code'] = sprintf('%04d', $row['bank_code']);
          $row['bank_code'] = mb_convert_encoding($row['bank_code'], "SJIS-win", "auto");

          // ���Z�@�֖� bank_name
          $row['bank_name'] = mb_convert_encoding($row['bank_name'], "SJIS-win", "auto");

          // ���Z�@�փt���K�i bank_name_kana
          $row['bank_name_kana'] = mb_convert_encoding($row['bank_name_kana'], "SJIS-win", "auto");

          // �x�X�R�[�h bank_siten_code
          $row['bank_siten_code'] = sprintf('%03d', $row['bank_siten_code']);
          $row['bank_siten_code'] = mb_convert_encoding($row['bank_siten_code'], "SJIS-win", "auto");

          // �x�X�� bank_siten_name 
          $row['bank_siten_name'] = mb_convert_encoding($row['bank_siten_name'], "SJIS-win", "auto");


          // �a�����  0:���� , 1:���� bank_kamoku
          $row['bank_kamoku'] = mb_convert_encoding($row['bank_kamoku'], "SJIS-win", "auto");
          if (strcmp($row['bank_kamoku'], "0") == 0) {
            $bank_kamoku = "����";
            $bank_kamoku = mb_convert_encoding($bank_kamoku, "SJIS-win", "auto");
          } else {
            $bank_kamoku = "����";
            $bank_kamoku = mb_convert_encoding($bank_kamoku, "SJIS-win", "auto");
          }

          // �����ԍ� kouzzz_number
          $row['kouzzz_number'] = mb_convert_encoding($row['kouzzz_number'], "SJIS-win", "auto");

          // �x�����o�^No
          $Siharai_M_Number = "1";

          // �x�X�t���K�i ���J�����Ȃ� $bank_siten_name_kana
          $bank_siten_name_kana = "����ض��";
          $bank_siten_name_kana = mb_convert_encoding($bank_siten_name_kana, "SJIS-win", "auto");

          // �U���萔�� ��
          //=== �󕶎�
          $row['kara_01'] = ""; // �� ���� ,   // �U���萔��

          $teigaku_F = "";
          $teigaku_F = mb_convert_encoding($teigaku_F, "SJIS-win", "auto"); // ��z�U��

          //===  10 �� ===
          $row['kara_02'] = ""; // �� ���� ,   //   �U���� 1�@���z,
          $row['kara_03'] = ""; // �� ���� ,   // �U����2�@���Z�@�փR�[�h,
          $row['kara_04'] = ""; // �� ���� ,   // �U����2�@���Z�@�֖�,
          $row['kara_05'] = ""; // �� ���� ,   // �U����2�@���Z�@�փt���K�i,
          $row['kara_06'] = ""; // �� ���� ,   // �U����2�@�x�X�R�[�h,
          $row['kara_07'] = ""; // �� ���� ,   // �U����2  �x�X��,
          $row['kara_08'] = ""; // �� ���� ,  �U����2   �x�X�t���K�i
          $row['kara_09'] = ""; // �� ���� ,   // �U����2�@�a�����,
          $row['kara_10'] = ""; // �� ���� ,   // �U����2�@�����ԍ�,
          $row['kara_11'] = ""; // �� ���� ,   // �U����2�@�U���萔��,
          $row['kara_12'] = ""; // �� ���� ,   // �U����2�@��z�U��,

          // �U����2, �������K�{������ �f�t�H���g 0  ���ڃ��X�g
          $koumoku_Last = "";

          $return_bank_all[] = array(
            'employee_id' => $row['employee_id'], // ���� �F �Ј��ԍ�
            'user_name' => $row['user_name'], // ���� �F �Ј�����

            // �x�����o�^No  �f�t�H���g: 1
            'Siharai_M_Number' => $Siharai_M_Number,

            'bank_code' => $row['bank_code'], // ���Z�@�փR�[�h
            'bank_name' => $row['bank_name'], // ���Z�@�֖�
            'bank_name_kana' => $row['bank_name_kana'], // ���Z�@�փt���K�i

            'bank_siten_code' => $row['bank_siten_code'], // �x�X�R�[�h
            'bank_siten_name' => $row['bank_siten_name'], // �x�X��
            'bank_siten_name_kana' => $bank_siten_name_kana, // �x�X�t���K�i

            'bank_kamoku' =>  $bank_kamoku, // �a�����
            'kouzzz_number' => $row['kouzzz_number'], // �����ԍ�

            'kara_01' => $row['kara_01'], // �U���萔��

            'teigaku_F' => $teigaku_F, // ��z�U��

            'kara_02' => $row['kara_02'], // ��@�J���� , 2
            'kara_03' => $row['kara_03'], // ��@�J���� , 3
            'kara_04' => $row['kara_04'], // ��@�J���� , 4
            'kara_05' => $row['kara_05'], // ��@�J���� , 5
            'kara_06' => $row['kara_06'], // ��@�J���� , 6
            'kara_07' => $row['kara_07'], // ��@�J���� , 7
            'kara_08' => $row['kara_08'], // ��@�J���� , 8
            'kara_09' => $row['kara_09'], // ��@�J���� , 9
            'kara_10' => $row['kara_10'], // ��@�J���� , 10
            'kara_11' => $row['kara_11'], // ��@�J���� , 10
            'kara_12' => $row['kara_12'], // ��@�J���� , 10

            'koumoku_Last' => $koumoku_Last, // ���ڃ��X�g 

          );
        }


        // �t�@�C���֑}�� �i.csv�j
        foreach ($return_bank_all as $item) {
          $file_02->fputcsv($item);
        }
      } //======= END if 





      // ********* �t���O����
      $csv_file_ok_FLG = "0"; // �f�t�H���g

      $excel_file_FLG = "1"; // Excel�@�쐬����

      //==================================================================
      //================= �A�b�v�f�[�g ���� �f�[�^�����t���O�� �f�[�^�쐬�ς݂ɂ���
      //==================================================================
      $stmt = $pdo->prepare("UPDATE User_Info_Table SET data_Flg = '1' 
                        WHERE data_Flg = ? ");

      $stmt->bindValue(
        1,
        '0',
        PDO::PARAM_STR
      );

      // SQL ���s 
      $res = $stmt->execute();

      exit;
    } catch (PDOException $e) {

      $e->getMessage();
    } finally {

      $pdo = null;

      // �y�[�W�����[�h
      header("Location: " . $_SERVER['PHP_SELF']);

      exit;
    } //========================= END try 



    //==============================================================================
    //==============================================================================
    //=======  ���o�̓t�@�C���@�`�F�b�N (�f�t�H���g)    Excel �_�E�����[�h���� 
    //===============================================================================
    //==============================================================================

  } else if ($export_output === "excel" && strcmp($check_box_FLG, "1") == 0) {


    // Excel�o�� �t�@�C����  => ���o��_�A���o�C�g�Ǘ�_2021-10-12_121212.xlsx
    $file_name_excel = "M_baito_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
      "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
      $get_now_arr['minute'] . $get_now_arr['second'] . ".xlsx";

    // �t�@�C�����̕����R�[�h�ϊ�  
    //  $file_name_excel = mb_convert_encoding($file_name_excel, "SJIS", "UTF-8");


    // Excel�o�� �t�@�C����  => �A���o�C�g�Ǘ�_2021-10-12_121212.xlsx
    $file_name_excel_bank = "M_ginkou_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
      "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
      $get_now_arr['minute'] . $get_now_arr['second'] . ".xlsx";

    // �t�@�C�����̕����R�[�h�ϊ�  
    //  $file_name_excel_bank = mb_convert_encoding($file_name_excel, "SJIS", "UTF-8");


    //================================  
    //================== �ڑ����
    //================================  
    $dsn = '';
    $user = '';
    $password = '';


    $retunr_output_excel = []; // Excel

    try {

      // PDO �I�u�W�F�N�g�쐬
      $pdo = new PDO($dsn, $user, $password);

      $stmt = $pdo->prepare("SELECT * from User_Info_Table WHERE data_Flg = '0'");

      // SQL ���s
      $res = $stmt->execute();

      // �t�@���_���Ȃ�������A�t�H���_���쐬  �`�� files_20211025
      $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];
      if (!file_exists($dirpath)) {
        mkdir($dirpath, 0777);

        //chmod�֐��Łuhoge�v�f�B���N�g���̃p�[�~�b�V�������u0777�v�ɂ���
        //   chmod($dirpath, 0777);
      }

      //====== �t�@�C���̕ۑ�
      // �t�@�C�������ꏊ
      $create_excel = $dirpath . "/" . $file_name_excel;


      //====================================================== 
      //=========================== Excel �p�@�ϐ��錾
      //====================================================== 
      $sex = ""; // ����
      $wareki = ""; // �a��̔N
      $wareki_r = ""; // �a�� �N���� �o�͗p
      $month_r = "";
      $day_r = ""; // ���t�@���ɂ�

      $Seireki_r = ""; // ����o�͗p

      $hokensya_K = ""; // �ی��Ҏ��  �j�q�A���q
      $syasin_K = ""; // �Ј��敪
      $syozoku_b = ""; // ��������


      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        //=== ���� 1 employee_id �Ј��ԍ�  000101 �@�U���Ɂ@?�p�f�B���O
        $row['employee_id'] = sprintf('%06d', $row['employee_id']);
        //       $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");

        //=== ���� 2
        $row['user_name'] = mb_convert_kana($row['user_name'], "h", "UTF-8"); // �Ј�����

        //=== ���� 3 �ӂ肪�ȁ@���@�J�i�@�֕ϊ�
        $row['furi_name'] = mb_convert_kana($row['furi_name'], "h", "UTF-8"); // �t���K�i
        //      $row['furi_name'] = mb_convert_encoding($row['furi_name'], "SJIS-win", "auto");

        // ���[���A�h���X
        //      $row['email'] = mb_convert_encoding($row['email'], "SJIS-win", "auto");

        // �z��� spouse
        //      $row['spouse'] = mb_convert_encoding($row['spouse'], "SJIS-win", "auto");

        if (strcmp($row['spouse'], "0") == 0) {
          $row['spouse'] = "�Ȃ�";
        } else {
          $row['spouse'] = "����";
        }

        // �}�{�l�� 	dependents_num
        //      $row['dependents_num'] = mb_convert_encoding($row['dependents_num'], "SJIS-win", "auto");

        //=== ���� 4

        //=== ����
        if (strcmp($row['sex'], "0")) {
          $row['sex'] = "�j"; // �ی��Ҏ��
          $hokensya_K = ""; // �ی��Ҏ��

          //        $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
          //     $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
        } else {
          $row['sex'] = "��"; // �ی��Ҏ��
          $hokensya_K = ""; // �ی��Ҏ��

          //       $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
          //      $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
        }

        // === ���� 5

        //=== ���N�����@�i���� => �a��@�ϊ��j
        //        $row['birthday'] = mb_convert_encoding($row['birthday'], "SJIS-win", "auto");

        // �N
        $year = mb_substr($row['birthday'], 0, 4); // ����@���o�� 1900

        // �a��ϊ��p function  Wareki_Parse
        $wareki = Wareki_Parse($year);

        // ��
        $month = mb_substr($row['birthday'], 5, 2);

        if (strpos($month, '-') !== false) {
          // - �n�C�t�� ���܂܂�Ă���ꍇ , 0�@�p�f�B���O
          $month = str_replace("-", "", $month);
          $month_r = "0" . $month;
        } else {
          $month_r = $month;
        }


        // ��
        $day = mb_substr($row['birthday'], 7, 2);
        // ������̒����擾  1,  10
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

        // �a��
        $wareki_r = $wareki . $month_r . "��" . $day_r . "��";
        //      $wareki_r = mb_convert_encoding($wareki_r, "SJIS-win", "auto");

        //=== ����6  ����  
        $Seireki_r = $year . "�N" . $month_r . "��" . $day_r . "��";
        //        $Seireki_r = mb_convert_encoding($Seireki_r, "SJIS-win", "auto");


        //===  �X�֔ԍ� zip
        //=== �X�֔ԍ� zip  �i-�j ������
        $row['zip'] = substr($row['zip'], 0, 3) . "-" . substr($row['zip'], 3);
        //      $row['zip'] = mb_convert_encoding($row['zip'], "SJIS-win", "auto");

        //=== �Z���P address_01
        //      $row['address_01'] = mb_convert_encoding($row['address_01'], "SJIS-win", "auto");

        //=== �d�b�ԍ� tel
        $row['tel'] = substr($row['tel'], 0, 3) . "-" . substr($row['tel'], 3, 4) . "-" . substr($row['tel'], 7);

        //       $row['creation_time'] = mb_convert_encoding($row['creation_time'], "SJIS-win", "auto");
        //      $row['data_Flg'] = mb_convert_encoding($row['data_Flg'], "SJIS-win", "auto");

        //=== �Ј��ԍ�
        //       $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");


        //=== �Ј��敪
        $syasin_K = "�A���o�C�g";
        //       $syasin_K = mb_convert_encoding($syasin_K, "SJIS-win", "auto");

        //=== ��������

        //============================= 20221_12_21 �l�ύX
        //  $syozoku_b = "003";
        $syozoku_b = "�A���o�C�g";

        //       $syozoku_b = mb_convert_encoding($syozoku_b, "SJIS-win", "auto");

        // ======================== �t�H�[������擾�ȊO�̍���
        $row['koumoku_01'] = ""; // ����7:���Ћ敪  �i��j
        $row['koumoku_02'] = ""; // ����8:���ДN�����@�i��j
        $row['koumoku_03'] = ""; // ����9:���ДN�����i����j�@�i��j
        $row['koumoku_04'] = ""; // ����10:�Α��N���@�i��j
        $row['koumoku_05'] = ""; // ����11 �Α��N��_�����@�i��j

        $row['koumoku_06'] = ""; // ����13:��E �@�i��j
        $row['koumoku_07'] = ""; // ����14:�w��: �i��j
        $row['koumoku_08'] = ""; // ����16:�E��: �i��j
        $row['koumoku_09'] = ""; // ����19:�Z���Q
        $row['koumoku_10'] = ""; // ����20:�Z���i�t���K�i�j�P

        $row['koumoku_11'] = ""; // ����21:�Z���i�t���K�i�j�Q
        $row['koumoku_12'] = "��Y��"; // ����24:��Q�敪:��Y��
        $row['koumoku_13'] = "��Y��"; // ����25:�Ǖw:��Y��
        $row['koumoku_14'] = "��Y��"; // ����26:�Ǖv:��Y��
        $row['koumoku_15'] = "��Y��"; // ����27:�ΘJ�w��:��Y��

        $row['koumoku_16'] = "��Y��"; // ����28:�ЊQ��:��Y��
        $row['koumoku_17'] = "��Y��"; // ����29:�O���l:��Y��
        $row['koumoku_18'] = ""; // ����30:�ސE���R: �i��j
        $row['koumoku_19'] = ""; // ����31:�ސE�N����: �i��j
        $row['koumoku_20'] = ""; // ����32:�ސE�N�����i����j: �i��j

        $row['koumoku_21'] = "����"; // ����33:���^�敪:����
        $row['koumoku_22'] = "���z�b��"; // ����34:�ŕ\�敪:���z�b��
        $row['koumoku_23'] = "����"; // ����35:�N�������Ώ�:����
        $row['koumoku_24'] = "�U��"; // ����36:�x���`��:�U��
        $row['koumoku_25'] = "�T���v��"; // ����37:�x�����p�^�[��:�T���v��

        //============================= 20221_12_21 �l�ύX
        // $row['koumoku_26'] = "�Ј��p�^�[��"; // ����38:���^���׏��p�^�[��:�Ј��p�^�[��
        $row['koumoku_26'] = ""; // ����38:���^���׏��p�^�[��:�Ј��p�^�[��

        //============================= 20221_12_21 �l�ύX
        //  $row['koumoku_27'] = "�ܗ^�T���v��"; // ����39:�ܗ^���׏��p�^�[��:�ܗ^�T���v��
        $row['koumoku_27'] = ""; // ����39:�ܗ^���׏��p�^�[��:�ܗ^�T���v��

        $row['koumoku_28'] = ""; // ����40:���N�ی��Ώ�: �i��j
        $row['koumoku_29'] = ""; // ����41:���ی��Ώ�: �i��j

        //============================= 20221_12_21 �l�ύX
        //  $row['koumoku_30'] = "220"; // ����42:���ی��z�i��~�j:220
        $row['koumoku_30'] = ""; // ����42:���ی��z�i��~�j:220

        //============================= 20221_12_21 �l�ύX
        //  $row['koumoku_31'] = "367"; // ����43:���ۏؔԍ��i����j367
        $row['koumoku_31'] = ""; // ����43:���ۏؔԍ��i����j367

        $row['koumoku_32'] = ""; // ����44:���ۏؔԍ��i�g���j: �i��j
        $row['koumoku_33'] = ""; // ����45:���ہE���N�@���i�擾�N����: �i��j
        $row['koumoku_34'] = ""; // ����46:�����N���Ώ�: �i��j
        $row['koumoku_35'] = "��Y��"; // ����47:70�Έȏ��p��:��Y��

        // $hokensya_K  ����48:�ی��Ҏ��:���q�@������ �ϐ����Ă͂� ������
        $row['koumoku_37'] = ""; // ����49:���N���z�i��~�j: �i��j
        $row['koumoku_38'] = ""; // ����50:��b�N���ԍ��P: �i��j
        $row['koumoku_39'] = ""; // ����51:��b�N���ԍ��Q:�Ώ�
        $row['koumoku_40'] = "�ΏۊO"; // ����52:�Z���ԘJ���ҁi3/4�����j:�ΏۊO

        $row['koumoku_41'] = "�Ώ�"; // ����53:�J�Еی��Ώ�:�Ώ�

        //============================= 20221_12_21 �l�ύX
        //  $row['koumoku_42'] = "�Ώ�"; // ����54:�ٗp�ی��Ώ�:�Ώ�
        $row['koumoku_42'] = ""; // ����54:�ٗp�ی��Ώ�:�Ώ�

        //============================= 20221_12_21 �l�ύX
        $row['koumoku_43'] = "��p"; // ����55:�J���ی��p�敪:��p
        //  $row['koumoku_43'] = "��p"; // ����55:�J���ی��p�敪:��p

        //============================= 20221_12_21 �l�ύX
        //  $row['koumoku_44'] = ""; // ����56:�ٗp�ی���ی��Ҕԍ��P:5029
        $row['koumoku_44'] = "5029"; // ����56:�ٗp�ی���ی��Ҕԍ��P:5029

        //============================= 20221_12_21 �l�ύX
        //  $row['koumoku_45'] = ""; // ����57:�ٗp�ی���ی��Ҕԍ��Q:530842
        $row['koumoku_45'] = "530842"; // ����57:�ٗp�ی���ی��Ҕԍ��Q:530842

        //============================= 20221_12_21 �l�ύX
        //  $row['koumoku_46'] = ""; // ����58:�ٗp�ی���ی��Ҕԍ��R:4
        $row['koumoku_46'] = "4"; // ����58:�ٗp�ی���ی��Ҕԍ��R:4


        $row['koumoku_47'] = ""; // ����59:�J���ی��@���i�擾�N����: �i��j
        // ����60:�z���:�Ȃ��@�@�����@$row['spouse']
        $row['koumoku_49'] = ""; // ����61:�z��Ҏ���: �i��j
        $row['koumoku_50'] = ""; // ����62:�z��҃t���K�i: �i��j

        $row['koumoku_51'] = ""; // ����63:�z��Ґ���: �i��j
        $row['koumoku_52'] = ""; // ����64:�z��Ґ��N����: �i��j
        $row['koumoku_53'] = ""; // ����65:�z��Ґ��N�����i����j: �i��j
        $row['koumoku_54'] = "��Y��"; // ����66:����T���Ώ۔z���:��Y��
        $row['koumoku_55'] = "��Y��"; // ����67:�z��ҘV�l:��Y��

        $row['koumoku_56'] = "��Y��"; // ����68:�z��ҘV�l:��Y��
        $row['koumoku_57'] = "�񓯋�"; // ����69:�z��ҘV�l:�񓯋�
        $row['koumoku_58'] = "��Y��"; // ����70:�z��ҘV�l:��Y��
        //     $row['koumoku_59'] = ""; // ����71:�}�{�T���Ώېl��:0



        //======== �����΂��΍�
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

        //======== 165 �́@�󔒍s
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
        // ====================================== �z��@�}��
        // ==============================================================
        $retunr_excel[] = array(

          'employee_id' => $row['employee_id'], // ���� 1 employee_id �Ј��ԍ�
          'user_name' => $row['user_name'],   // ���� 2 �Ј�����
          'furi_name' => $row['furi_name'],  // ���� 3  �t���K�i
          'sex' => $row['sex'], // ���� 4  ����
          'wareki_r' => $wareki_r,  // ���� 5 ���N�����i�a��j

          'Seireki_r' => $Seireki_r, // ���� 6 ���N�����i����j
          'koumoku_01' => $row['koumoku_01'], // ����7:���Ћ敪
          'koumoku_02' => $row['koumoku_02'], // ����8:���ДN����
          'koumoku_03' => $row['koumoku_03'], // ����9:���ДN�����i����j
          'koumoku_04' => $row['koumoku_04'], // ����10:�Α��N��

          'koumoku_05' => $row['koumoku_05'], // ����11 �Α��N��_����
          'syasin_K' => $syasin_K, // ���� 12 �Ј��敪
          'koumoku_06' => $row['koumoku_06'], // ����13:��E �@�i��j
          'koumoku_07' => $row['koumoku_07'], // ����14:�w��: �i��j

          'syozoku_b' => $syozoku_b, //  ����15:��������  

          'koumoku_08' => $row['koumoku_08'], // ����16 : �E��
          'zip' => $row['zip'],   // ����17�@�X�֔ԍ�
          'address_01' => $row['address_01'],  // ����18 �Z���@�P
          'koumoku_09' => $row['koumoku_09'],  // ����19:�Z���Q: �i��j
          'koumoku_10' => $row['koumoku_10'],  // ����20:�Z���i�t���K�i�j�P: �i��j

          'koumoku_11' => $row['koumoku_11'],  // ����21:�Z���i�t���K�i�j�Q: �i��j
          'tel' => $row['tel'],  // ����22:�d�b�ԍ�:090-1111-2222
          'email' => $row['email'], // ����23:���[���A�h���X
          'koumoku_12' => $row['koumoku_12'], // ����24:��Q�敪:��Y��
          'koumoku_13' => $row['koumoku_13'], // ����25:�Ǖw:��Y��

          'koumoku_14' => $row['koumoku_14'], // ����26:�Ǖv:��Y��
          'koumoku_15' => $row['koumoku_15'], // ����27:�ΘJ�w��:��Y��
          'koumoku_16' => $row['koumoku_16'], // ����28:�ЊQ��:��Y��
          'koumoku_17' => $row['koumoku_17'], // ����29:�O���l:��Y��
          'koumoku_18' => $row['koumoku_18'], // ����30:�ސE���R: �i��j
          // 30 ?
          'koumoku_19' => $row['koumoku_19'], // ����31:�ސE�N����: �i��j
          'koumoku_20' => $row['koumoku_20'], // ����32:�ސE�N�����i����j: �i��j
          'koumoku_21' => $row['koumoku_21'], // ����33:���^�敪:����
          'koumoku_22' => $row['koumoku_22'], // ����34:�ŕ\�敪:���z�b��
          'koumoku_23' => $row['koumoku_23'], // ����35:�N�������Ώ�:����
          // 35
          'koumoku_24' => $row['koumoku_24'], // ����36:�x���`��:�U��
          'koumoku_25' => $row['koumoku_25'], // ����37:�x�����p�^�[��:�T���v��
          'koumoku_26' => $row['koumoku_26'], // ����38:���^���׏��p�^�[��:�Ј��p�^�[��
          'koumoku_27' => $row['koumoku_27'], // ����39:�ܗ^���׏��p�^�[��:�ܗ^�T���v��
          'koumoku_28' => $row['koumoku_28'], // ����40:���N�ی��Ώ�: �i��j
          // 40
          'koumoku_29' => $row['koumoku_29'], // ����41:���ی��Ώ�: �i��j
          'koumoku_30' => $row['koumoku_30'], // ����42:���ی��z�i��~�j:220
          'koumoku_31' => $row['koumoku_31'], // ����43:���ۏؔԍ��i����j:367
          'koumoku_32' => $row['koumoku_32'], // ����44:���ۏؔԍ��i�g���j: �i��j
          'koumoku_33' => $row['koumoku_33'], // ����45:���ہE���N�@���i�擾�N����: �i��j
          // 45
          'koumoku_34' => $row['koumoku_34'], // ����46:�����N���Ώ�: �i��j
          'koumoku_35' => $row['koumoku_35'], // ����47:70�Έȏ��p��:��Y��

          // $hokensya_K�@���Ă͂�
          'hokensya_K' => $hokensya_K, // ����48:�ی��Ҏ��:���q  $hokensya_K

          'koumoku_37' => $row['koumoku_36'], // ����49:���N���z�i��~�j: �i��j
          'koumoku_38' => $row['koumoku_37'], // ����50:��b�N���ԍ��P: �i��j
          // 50
          'koumoku_39' => $row['koumoku_39'], // ����51:��b�N���ԍ��Q:�Ώ�
          'koumoku_40' => $row['koumoku_40'], // ����52:�Z���ԘJ���ҁi3/4�����j:�ΏۊO
          'koumoku_41' => $row['koumoku_41'], // ����53:�J�Еی��Ώ�:�Ώ�
          'koumoku_42' => $row['koumoku_42'], // ����54:�ٗp�ی��Ώ�:�Ώ�
          'koumoku_43' => $row['koumoku_43'], // ����55:�J���ی��p�敪:��p
          // 55
          'koumoku_44' => $row['koumoku_44'], // ����56:�ٗp�ی���ی��Ҕԍ��P:5029
          'koumoku_45' => $row['koumoku_45'], // ����57:�ٗp�ی���ی��Ҕԍ��Q:530842
          'koumoku_46' => $row['koumoku_46'], // ����58:�ٗp�ی���ی��Ҕԍ��R:4
          'koumoku_47' => $row['koumoku_47'], // ����59:�J���ی��@���i�擾�N����: �i��j
          'spouse' => $row['spouse'], // ����60:�z���:�@�t�H�[������l�擾
          // 60
          'koumoku_49' => $row['koumoku_49'], // ����61:�z��Ҏ���: �i��j
          'koumoku_50' => $row['koumoku_50'], // ����62:�z��҃t���K�i: �i��j
          'koumoku_51' => $row['koumoku_51'], // ����63:�z��Ґ���: �i��j
          'koumoku_52' => $row['koumoku_52'], // ����64:�z��Ґ��N����: �i��j
          'koumoku_53' => $row['koumoku_53'], // ����65:�z��Ґ��N�����i����j: �i��j
          // 65
          'koumoku_54' => $row['koumoku_54'], // ����66:����T���Ώ۔z���:��Y��
          'koumoku_55' => $row['koumoku_55'], // ����67:�z��ҘV�l:��Y��
          'koumoku_56' => $row['koumoku_56'], // ����68:�z��ҏ�Q��:��Y��
          'koumoku_57' => $row['koumoku_57'], // ����69:�z��ғ���:�񓯋�
          'koumoku_58' => $row['koumoku_58'], // ����70:�z��Ҕ񋏏Z��:��Y��

          // ����71:�}�{�T���Ώېl��:  �t�H�[������擾
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

        // Spreadsheet�I�u�W�F�N�g����
        $objSpreadsheet = new Spreadsheet();
        // �V�[�g�ݒ�
        $objSheet = $objSpreadsheet->getActiveSheet();

        // Spreadsheet�I�u�W�F�N�g����
        $objSpreadsheet = new Spreadsheet();
        // �V�[�g�ݒ�
        $objSheet = $objSpreadsheet->getActiveSheet();

        // �w�b�_�[����
        // �������̔z��f�[�^
        $arrX = array(
          '�Ј��ԍ�', // ����0
          '�Ј�����', // ����1
          '�t���K�i', // ����2
          '����', // ����3
          '���N����', // ����4
          '���N�����i����j', // ����5
          '���Ћ敪', // ����6
          '���ДN����', // ����7
          '���ДN�����i����j', // ����8
          '�Α��N��', // ����9
          '�Α��N��_����', // ����10
          '�Ј��敪', // ����11
          '��E', // ����12
          '�w��', // ����13
          '��������', // ����14
          '�E��', // ����15
          '�X�֔ԍ�', // ����16
          '�Z���P', // ����17
          '�Z���Q', // ����18
          '�Z���i�t���K�i�j�P', // ����19
          '�Z���i�t���K�i�j�Q', // ����20
          '�d�b�ԍ�', // ����21
          '���[���A�h���X', // ����22
          '��Q�敪', // ����23
          '�Ǖw', // ����24
          '�Ǖv', // ����25
          '�ΘJ�w��', // ����26
          '�ЊQ��', // ����27
          '�O���l', // ����28
          '�ސE���R', // ����29
          '�ސE�N����', // ����30
          '�ސE�N�����i����j', // ����31
          '���^�敪', // ����32
          '�ŕ\�敪', // ����33
          '�N�������Ώ�', // ����34
          '�x���`��', // ����35
          '�x�����p�^�[��', // ����36
          '���^���׏��p�^�[��', // ����37
          '�ܗ^���׏��p�^�[��', // ����38
          '���N�ی��Ώ�', // ����39
          '���ی��Ώ�', // ����40
          '���ی��z�i��~�j', // ����41
          '���ۏؔԍ��i����j', // ����42
          '���ۏؔԍ��i�g���j', // ����43
          '���ہE���N�@���i�擾�N����', // ����44
          '�����N���Ώ�', // ����45
          '70�Έȏ��p��', // ����46
          '�ی��Ҏ��', // ����47
          '���N���z�i��~�j', // ����48
          '��b�N���ԍ��P', // ����49
          '��b�N���ԍ��Q', // ����50
          '�Z���ԘJ���ҁi3/4�����j', // ����51
          '�J�Еی��Ώ�', // ����52
          '�ٗp�ی��Ώ�', // ����53
          '�J���ی��p�敪', // ����54
          '�ٗp�ی���ی��Ҕԍ��P', // ����55
          '�ٗp�ی���ی��Ҕԍ��Q', // ����56
          '�ٗp�ی���ی��Ҕԍ��R', // ����57
          '�J���ی��@���i�擾�N����', // ����58
          '�z���', // ����59
          '�z��Ҏ���', // ����60
          '�z��҃t���K�i', // ����61
          '�z��Ґ���', // ����62
          '�z��Ґ��N����', // ����63
          '�z��Ґ��N�����i����j', // ����64
          '����T���Ώ۔z���', // ����65
          '�z��ҘV�l', // ����66
          '�z��ҏ�Q��', // ����67
          '�z��ғ���', // ����68
          '�z��Ҕ񋏏Z��', // ����69
          '�}�{�T���Ώېl��', // ����70
          '�}�{�e��_�}�{�e����_1', // ����71
          '�}�{�e��_�t���K�i_1', // ����72
          '�}�{�e��_����_1', // ����73
          '�}�{�e��_���N����_1', // ����74
          '�}�{�e��_���N�����i����j_1', // ����75
          '�}�{�e��_�}�{�敪_1', // ����76
          '�}�{�e��_��Q�ҋ敪_1', // ����77
          '�}�{�e��_�����敪_1', // ����78
          '�}�{�e��_�����V�e���敪_1', // ����79
          '�}�{�e��_�񋏏Z�ҋ敪_1', // ����80
          '�}�{�e��_�T���v�Z�敪_1', // ����81
          '�}�{�e��_�}�{�e����_2', // ����82
          '�}�{�e��_�t���K�i_2', // ����83
          '�}�{�e��_����_2', // ����84
          '�}�{�e��_���N����_2', // ����85
          '�}�{�e��_���N�����i����j_2', // ����86
          '�}�{�e��_�}�{�敪_2', // ����87
          '�}�{�e��_��Q�ҋ敪_2', // ����88
          '�}�{�e��_�����敪_2', // ����89
          '�}�{�e��_�����V�e���敪_2', // ����90
          '�}�{�e��_�񋏏Z�ҋ敪_2', // ����91
          '�}�{�e��_�T���v�Z�敪_2', // ����92
          '�}�{�e��_�}�{�e����_3', // ����93
          '�}�{�e��_�t���K�i_3', // ����94
          '�}�{�e��_����_3', // ����95
          '�}�{�e��_���N����_3', // ����96
          '�}�{�e��_���N�����i����j_3', // ����97
          '�}�{�e��_�}�{�敪_3', // ����98
          '�}�{�e��_��Q�ҋ敪_3', // ����99
          '�}�{�e��_�����敪_3', // ����100
          '�}�{�e��_�����V�e���敪_3', // ����101
          '�}�{�e��_�񋏏Z�ҋ敪_3', // ����102
          '�}�{�e��_�T���v�Z�敪_3', // ����103
          '�}�{�e��_�}�{�e����_4', // ����104
          '�}�{�e��_�t���K�i_4', // ����105
          '�}�{�e��_����_4', // ����106
          '�}�{�e��_���N����_4', // ����107
          '�}�{�e��_���N�����i����j_4', // ����108
          '�}�{�e��_�}�{�敪_4', // ����109
          '�}�{�e��_��Q�ҋ敪_4', // ����110
          '�}�{�e��_�����敪_4', // ����111
          '�}�{�e��_�����V�e���敪_4', // ����112
          '�}�{�e��_�񋏏Z�ҋ敪_4', // ����113
          '�}�{�e��_�T���v�Z�敪_4', // ����114
          '�}�{�e��_�}�{�e����_5', // ����115
          '�}�{�e��_�t���K�i_5', // ����116
          '�}�{�e��_����_5', // ����117
          '�}�{�e��_���N����_5', // ����118
          '�}�{�e��_���N�����i����j_5', // ����119
          '�}�{�e��_�}�{�敪_5', // ����120
          '�}�{�e��_��Q�ҋ敪_5', // ����121
          '�}�{�e��_�����敪_5', // ����122
          '�}�{�e��_�����V�e���敪_5', // ����123
          '�}�{�e��_�񋏏Z�ҋ敪_5', // ����124
          '�}�{�e��_�T���v�Z�敪_5', // ����125
          '�}�{�e��_�}�{�e����_6', // ����126
          '�}�{�e��_�t���K�i_6', // ����127
          '�}�{�e��_����_6', // ����128
          '�}�{�e��_���N����_6', // ����129
          '�}�{�e��_���N�����i����j_6', // ����130
          '�}�{�e��_�}�{�敪_6', // ����131
          '�}�{�e��_��Q�ҋ敪_6', // ����132
          '�}�{�e��_�����敪_6', // ����133
          '�}�{�e��_�����V�e���敪_6', // ����134
          '�}�{�e��_�񋏏Z�ҋ敪_6', // ����135
          '�}�{�e��_�T���v�Z�敪_6', // ����136
          '�}�{�e��_�}�{�e����_7', // ����137
          '�}�{�e��_�t���K�i_7', // ����138
          '�}�{�e��_����_7', // ����139
          '�}�{�e��_���N����_7', // ����140
          '�}�{�e��_���N�����i����j_7', // ����141
          '�}�{�e��_�}�{�敪_7', // ����142
          '�}�{�e��_��Q�ҋ敪_7', // ����143
          '�}�{�e��_�����敪_7', // ����144
          '�}�{�e��_�����V�e���敪_7', // ����145
          '�}�{�e��_�񋏏Z�ҋ敪_7', // ����146
          '�}�{�e��_�T���v�Z�敪_7', // ����147
          '�}�{�e��_�}�{�e����_8', // ����148
          '�}�{�e��_�t���K�i_8', // ����149
          '�}�{�e��_����_8', // ����150
          '�}�{�e��_���N����_8', // ����151
          '�}�{�e��_���N�����i����j_8', // ����152
          '�}�{�e��_�}�{�敪_8', // ����153
          '�}�{�e��_��Q�ҋ敪_8', // ����154
          '�}�{�e��_�����敪_8', // ����155
          '�}�{�e��_�����V�e���敪_8', // ����156
          '�}�{�e��_�񋏏Z�ҋ敪_8', // ����157
          '�}�{�e��_�T���v�Z�敪_8', // ����158
          '�}�{�e��_�}�{�e����_9', // ����159
          '�}�{�e��_�t���K�i_9', // ����160
          '�}�{�e��_����_9', // ����161
          '�}�{�e��_���N����_9', // ����162
          '�}�{�e��_���N�����i����j_9', // ����163
          '�}�{�e��_�}�{�敪_9', // ����164
          '�}�{�e��_��Q�ҋ敪_9', // ����165
          '�}�{�e��_�����敪_9', // ����166
          '�}�{�e��_�����V�e���敪_9', // ����167
          '�}�{�e��_�񋏏Z�ҋ敪_9', // ����168
          '�}�{�e��_�T���v�Z�敪_9', // ����169
          '�}�{�e��_�}�{�e����_10', // ����170
          '�}�{�e��_�t���K�i_10', // ����171
          '�}�{�e��_����_10', // ����172
          '�}�{�e��_���N����_10', // ����173
          '�}�{�e��_���N�����i����j_10', // ����174
          '�}�{�e��_�}�{�敪_10', // ����175
          '�}�{�e��_��Q�ҋ敪_10', // ����176
          '�}�{�e��_�����敪_10', // ����177
          '�}�{�e��_�����V�e���敪_10', // ����178
          '�}�{�e��_�񋏏Z�ҋ敪_10', // ����179
          '�}�{�e��_�T���v�Z�敪_10', // ����180
          '�}�{�e��_�}�{�e����_11', // ����181
          '�}�{�e��_�t���K�i_11', // ����182
          '�}�{�e��_����_11', // ����183
          '�}�{�e��_���N����_11', // ����184
          '�}�{�e��_���N�����i����j_11', // ����185
          '�}�{�e��_�}�{�敪_11', // ����186
          '�}�{�e��_��Q�ҋ敪_11', // ����187
          '�}�{�e��_�����敪_11', // ����188
          '�}�{�e��_�����V�e���敪_11', // ����189
          '�}�{�e��_�񋏏Z�ҋ敪_11', // ����190
          '�}�{�e��_�T���v�Z�敪_11', // ����191
          '�}�{�e��_�}�{�e����_12', // ����192
          '�}�{�e��_�t���K�i_12', // ����193
          '�}�{�e��_����_12', // ����194
          '�}�{�e��_���N����_12', // ����195
          '�}�{�e��_���N�����i����j_12', // ����196
          '�}�{�e��_�}�{�敪_12', // ����197
          '�}�{�e��_��Q�ҋ敪_12', // ����198
          '�}�{�e��_�����敪_12', // ����199
          '�}�{�e��_�����V�e���敪_12', // ����200
          '�}�{�e��_�񋏏Z�ҋ敪_12', // ����201
          '�}�{�e��_�T���v�Z�敪_12', // ����202
          '�}�{�e��_�}�{�e����_13', // ����203
          '�}�{�e��_�t���K�i_13', // ����204
          '�}�{�e��_����_13', // ����205
          '�}�{�e��_���N����_13', // ����206
          '�}�{�e��_���N�����i����j_13', // ����207
          '�}�{�e��_�}�{�敪_13', // ����208
          '�}�{�e��_��Q�ҋ敪_13', // ����209
          '�}�{�e��_�����敪_13', // ����210
          '�}�{�e��_�����V�e���敪_13', // ����211
          '�}�{�e��_�񋏏Z�ҋ敪_13', // ����212
          '�}�{�e��_�T���v�Z�敪_13', // ����213
          '�}�{�e��_�}�{�e����_14', // ����214
          '�}�{�e��_�t���K�i_14', // ����215
          '�}�{�e��_����_14', // ����216
          '�}�{�e��_���N����_14', // ����217
          '�}�{�e��_���N�����i����j_14', // ����218
          '�}�{�e��_�}�{�敪_14', // ����219
          '�}�{�e��_��Q�ҋ敪_14', // ����220
          '�}�{�e��_�����敪_14', // ����221
          '�}�{�e��_�����V�e���敪_14', // ����222
          '�}�{�e��_�񋏏Z�ҋ敪_14', // ����223
          '�}�{�e��_�T���v�Z�敪_14', // ����224
          '�}�{�e��_�}�{�e����_15', // ����225
          '�}�{�e��_�t���K�i_15', // ����226
          '�}�{�e��_����_15', // ����227
          '�}�{�e��_���N����_15', // ����228
          '�}�{�e��_���N�����i����j_15', // ����229
          '�}�{�e��_�}�{�敪_15', // ����230
          '�}�{�e��_��Q�ҋ敪_15', // ����231
          '�}�{�e��_�����敪_15', // ����232
          '�}�{�e��_�����V�e���敪_15', // ����233
          '�}�{�e��_�񋏏Z�ҋ敪_15', // ����234
          '�}�{�e��_�T���v�Z�敪_15', // ����235
        );

        $objSheet->fromArray(
          $arrX,      // �z��f�[�^
          NULL,       // �z��f�[�^�̒��ŃZ���ɐݒ肵�Ȃ�NULL�l�̎w��
          'A1'        // ������W(�f�t�H���g:"A1")
        );


        //�@Excel�t�@�C���ց@�f�[�^�}��
        $objSheet->fromArray($retunr_excel, null, 'A2');

        // XLSX�`���I�u�W�F�N�g����
        $objWriter = new Xlsx($objSpreadsheet);

        // �t�@�C��������
        $objWriter->save($create_excel);

        //   exit();

        // ********* �t���O����
        $csv_file_ok_FLG = "0"; // �f�t�H���g

        $excel_file_FLG = "1"; // Excel�@�쐬����

      } else {

        // �w�肵�����t�ɊY���́@Excel�t�@�C�����Ȃ������ꍇ
        //      $excel_file_err = 1;

        $excel_file_FLG = "2"; // Excel�@�G���[

      }
    } catch (PDOException $e) {

      //    print('Error:'.$e->getMessage());

      // (�g�����U�N�V����) ���[���o�b�N
      //       $pdo->rollBack();

      // ************** �G���[���� ***************
      $csv_file_ok_FLG = "2";
    } finally {

      $pdo = null;
    } //======================================== END try ==================================


    // =========================================================================================== 
    // =========================================== ��s�p�@Excel try ================================
    // ===========================================================================================
    // �i�荞�݁@���ʁ@�i�[�z��
    // $retunr_excel_bank = [];
    //========================================
    try {

      // PDO �I�u�W�F�N�g
      $pdo = new PDO($dsn, $user, $password);

      // SQL
      $stmt = $pdo->prepare("SELECT * from User_Info_Table WHERE data_Flg = '0'");

      // SQL ���s 
      $res = $stmt->execute();

      $idx = 0;

      // �t�@���_���Ȃ�������A�t�H���_���쐬  �`�� files_20211025
      $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];

      //====== �t�@�C���̕ۑ�
      // �t�@�C�������ꏊ
      $create_excel_bank = $dirpath . "/" . $file_name_excel_bank;


      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // ============= 0 �p�f�B���O ===============
        $row['employee_id'] = sprintf('%06d', $row['employee_id']);
        $row['bank_code'] = sprintf('%04d', $row['bank_code']);
        $row['bank_siten_code'] = sprintf('%03d', $row['bank_siten_code']);

        // �a�����  0:���� , 1:���� bank_kamoku
        if (strcmp($row['bank_kamoku'], "0") == 0) {
          $bank_kamoku = "����";
        } else {
          $bank_kamoku = "����";
        }

        // �x�����o�^No
        $Siharai_M_Number = "1";

        // �x�X�t���K�i ���J�����Ȃ� $bank_siten_name_kana
        $bank_siten_name_kana = "����ض��";

        // �U���萔�� ��
        //=== �󕶎�
        $row['koumoku_01'] = ""; // �� ���� ,   // �U���萔��

        $teigaku_F = ""; // ��z�U��

        //===  10 �� ===
        $row['koumoku_02'] = ""; // �� ���� ,   //   �U���� 1�@���z,
        $row['koumoku_03'] = ""; // �� ���� ,   // �U����2�@���Z�@�փR�[�h,
        $row['koumoku_04'] = ""; // �� ���� ,   // �U����2�@���Z�@�֖�,
        $row['koumoku_05'] = ""; // �� ���� ,   // �U����2�@���Z�@�փt���K�i,
        $row['koumoku_06'] = ""; // �� ���� ,   // �U����2�@�x�X�R�[�h,
        $row['koumoku_07'] = ""; // �� ���� ,   // �U����2  �x�X��,
        $row['koumoku_08'] = ""; // �� ���� ,  �U����2   �x�X�t���K�i
        $row['koumoku_09'] = ""; // �� ���� ,   // �U����2�@�a�����,
        $row['koumoku_10'] = ""; // �� ���� ,   // �U����2�@�����ԍ�,
        $row['koumoku_11'] = ""; // �� ���� ,   // �U����2�@�U���萔��,
        $row['koumoku_12'] = ""; // �� ���� ,   // �U����2�@��z�U��,

        //====== ���X�g�J���� �u���z�v
        $last_kingaku = "";

        // ==============================================================
        // ====================================== �z��@�}��
        // ==============================================================
        $retunr_excel_bank[] = array(

          'employee_id' => $row['employee_id'],  //����_1:::�Ј��ԍ�
          'user_name' => $row['user_name'],      //����_2:::�Ј�����
          'Siharai_M_Number' => $Siharai_M_Number, //����_3:::�x�����o�^No
          'bank_code' => $row['bank_code'], //����_4:::�U����@�@���Z�@�փR�[�h
          'bank_name' => $row['bank_name'],  //����_5:::�U����@�@���Z�@�֖�
          'bank_name_kana' => $row['bank_name_kana'],  //����_6:::�U����@�@���Z�@�փt���K�i
          'bank_siten_code' => $row['bank_siten_code'], //����_7:::�U����@�@�x�X�R�[�h
          'bank_siten_name' => $row['bank_siten_name'], //����_8:::�U����@�@�x�X��
          'bank_siten_name_kana' => $bank_siten_name_kana, //����_9:::�U����@�@�x�X�t���K�i
          'bank_kamoku' => $bank_kamoku,  //����_10:::�U����@�@�a�����
          'kouzzz_number' => $row['kouzzz_number'], //����_11:::�U����@�@�����ԍ�
          'koumoku_01' => $row['koumoku_01'],  //����_12:::�U����@�@�U���萔��
          'teigaku_F' => $teigaku_F,            //����_13:::�U����@�@��z�U��
          'koumoku_01' => $row['koumoku_01'],  //����_14:::�U����@�@���z
          'koumoku_02' => $row['koumoku_02'],  //����_15:::�U����A�@���Z�@�փR�[�h
          'koumoku_03' => $row['koumoku_03'],  //����_16:::�U����A�@���Z�@�֖�
          'koumoku_04' => $row['koumoku_04'],  //����_17:::�U����A�@���Z�@�փt���K�i
          'koumoku_05' => $row['koumoku_05'],  //����_18:::�U����A�@�x�X�R�[�h
          'koumoku_06' => $row['koumoku_06'],   //����_19:::�U����A�@�x�X��
          'koumoku_07' => $row['koumoku_07'], //����_20:::�U����A�@�x�X�t���K�i
          'koumoku_08' => $row['koumoku_08'],  //����_21:::�U����A�@�a�����
          'koumoku_09' => $row['koumoku_09'],  //����_22:::�U����A�@�����ԍ�
          'koumoku_10' => $row['koumoku_10'], //����_23:::�U����A�@�U���萔��
          'koumoku_11' => $row['koumoku_11'], //����_24:::�U����A�@��z�U��
          'koumoku_12' => $row['koumoku_12'],  //����_25:::�U����A�@���z
          'last_kingaku' => $last_kingaku,  // �u���z�v
        );
      }

      if (!empty($retunr_excel_bank)) {

        $excel_file_err = 0;

        // Spreadsheet�I�u�W�F�N�g����
        $objSpreadsheet = new Spreadsheet();
        // �V�[�g�ݒ�
        $objSheet = $objSpreadsheet->getActiveSheet();

        // Spreadsheet�I�u�W�F�N�g����
        $objSpreadsheet = new Spreadsheet();
        // �V�[�g�ݒ�
        $objSheet = $objSpreadsheet->getActiveSheet();

        // �w�b�_�[����
        // �������̔z��f�[�^
        $arrX = array(
          '�Ј��ԍ�',
          '�Ј�����',
          '�x�����o�^No',
          '�U����@�@���Z�@�փR�[�h',
          '�U����@�@���Z�@�֖�',
          '�U����@�@���Z�@�փt���K�i',
          '�U����@�@�x�X�R�[�h',
          '�U����@�@�x�X��',
          '�U����@�@�x�X�t���K�i',
          '�U����@�@�a�����',
          '�U����@�@�����ԍ�',
          '�U����@�@�U���萔��',
          '�U����@�@��z�U��',
          '�U����@�@���z',
          '�U����A�@���Z�@�փR�[�h',
          '�U����A�@���Z�@�֖�',
          '�U����A�@���Z�@�փt���K�i',
          '�U����A�@�x�X�R�[�h',
          '�U����A�@�x�X��',
          '�U����A�@�x�X�t���K�i',
          '�U����A�@�a�����',
          '�U����A�@�����ԍ�',
          '�U����A�@�U���萔��',
          '�U����A�@��z�U��',
          '�U����A�@���z'
        );

        $objSheet->fromArray(
          $arrX,      // �z��f�[�^
          NULL,       // �z��f�[�^�̒��ŃZ���ɐݒ肵�Ȃ�NULL�l�̎w��
          'A1'        // ������W(�f�t�H���g:"A1")
        );

        //�@Excel�t�@�C���ց@�f�[�^�}��
        $objSheet->fromArray($retunr_excel_bank, null, 'A2');

        // XLSX�`���I�u�W�F�N�g����
        $objWriter = new Xlsx($objSpreadsheet);

        // �t�@�C��������
        $objWriter->save($create_excel_bank);

        //   exit();

        // ********* �t���O����
        $csv_file_ok_FLG = "0"; // �f�t�H���g

        $excel_file_FLG = "1"; // Excel�@�쐬����

      } else {

        // �w�肵�����t�ɊY���́@Excel�t�@�C�����Ȃ������ꍇ
        //      $excel_file_err = 1;

        $excel_file_FLG = "2"; // Excel�@�G���[

      }
    } catch (PDOException $e) {

      // ************** �G���[���� ***************
      $csv_file_ok_FLG = "2";
    }

    // =========================================== ====================================================
    // =========================================== ��s�p�@Excel try  END ================================
    // =========================================== ====================================================




    //==================================================================
    //================= �A�b�v�f�[�g ���� �f�[�^�����t���O�� �f�[�^�쐬�ς݂ɂ���
    //==================================================================
    try {

      // PDO �I�u�W�F�N�g
      $pdo = new PDO($dsn, $user, $password);

      $stmt = $pdo->prepare("UPDATE User_Info_Table SET data_Flg = '1' 
                  WHERE data_Flg = '0' ");

      // CSV �G�N�X�|�[�g �J�n��
      /*
      $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

    
      $stmt->bindValue(
        2,
        $date_target_02,
        PDO::PARAM_STR
      );
      */

      // SQL ���s 
      $res = $stmt->execute();
    } catch (PDOException $e) {

      // ************** �G���[���� ***************
      $csv_file_ok_FLG = "2";
    } finally {

      $pdo = null;

      // �y�[�W�����[�h
      header("Location: " . $_SERVER['PHP_SELF']);

      exit;
    }

    //==============================================
    //*************** �t�@�C���̃_�E�����[�h���� */
    //==============================================
    //  t_download($protocol);


    //===================================
    // =========== ���݂̃t��URL �擾
    //===================================

    /*
    if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {

      $protocol = 'https://';
    } else {

      $protocol = 'http://';
    }

    // ./files �� . ���폜
    $new_create_excel = mb_substr($create_excel, 1);

    $protocol .= $_SERVER["HTTP_HOST"] . '/job_recruit/csv' . $new_create_excel;
    //. $_SERVER["REQUEST_URI"];

 


    // �t�@�C���^�C�v���w��
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    // �t�@�C���T�C�Y���擾���A�_�E�����[�h�̐i����\��
    //        header('Content-Length: '.filesize($protocol));
    // �t�@�C���̃_�E�����[�h�A���l�[�����w��
    header('Content-Disposition: attachment; filename="' . $file_name_excel . '"');

    ob_clean();  //�ǋL
    flush();     //�ǋL

    // �t�@�C����ǂݍ��݃_�E�����[�h�����s
    readfile($protocol);
    exit;
    */


    // ============================================================================
    // ============================================================================
    // =============== ************ Excel�@�o�� *************  ���t�I��
    // ============================================================================
    // ============================================================================

  } else if ($export_output === "excel" && strcmp($check_box_FLG, "0") == 0) {

    //  print("ok");


    //============================================
    //============== �C�� 2022_01_13  ���t����̏ꍇ
    //============================================
    if (isset($_POST['date_target']) || isset($_POST['date_target_02'])) {

      if (empty($_POST['date_target']) && empty($_POST['date_target_02'])) {

        $display_err = "�t�@�C���쐬���ł��܂���B���t�{�b�N�X����ł��B�u���t����͂��邩�v�A�u���o�̓t�@�C���v�Ƀ`�F�b�N�����Ă��������B";

        // === ���t�{�b�N�X���@�Q�Ƃ��@�󂾂����ꍇ
        $csv_FLG = "0";
      } else {


        //========== ���t��POST �f�[�^�擾
        $date_target = $_POST['date_target'];
        $date_target = str_replace("/", "-", $date_target);

        $date_target_02 = $_POST['date_target_02'];
        $date_target_02 = str_replace("/", "-", $date_target_02);







        // ==============================================================
        //====== ���t�������Ȃ�A���t�̉E���{�b�N�X�����Z������B
        // ==============================================================

        if (!empty($date_target) && !empty($date_target_02)) {

          if ($date_target === $date_target_02) {

            $tmp_date = strtotime($date_target_02);

            $date_target_02 = date("Y-m-d", strtotime("+1 day", $tmp_date));

            //        print ("���t���Z" . $date_target_02);

          } else {

            $tmp_date = strtotime($date_target_02);

            $date_target_02 = date("Y-m-d", strtotime("+1 day", $tmp_date));

            //         print ("���t���Z" . $date_target_02);

          }
        } // ========= END if 

        //==================== ���t�v���X  1 END =========================>


        // Excel�o�� �t�@�C����  => �A���o�C�g�Ǘ�_2021-10-12_121212.xlsx
        $file_name_excel = "R_Baito_Kanri_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
          "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
          $get_now_arr['minute'] . $get_now_arr['second'] . ".xlsx";


        // Excel�o�� �t�@�C����  => �A���o�C�g�Ǘ�_2021-10-12_121212.xlsx
        $file_name_excel_bank = "Ginkou_Baito_Kanri_" . $get_now_arr['year'] . "-" . $get_now_arr['month'] .
          "-" . $get_now_arr['day'] . "_" . $get_now_arr['hour'] .
          $get_now_arr['minute'] . $get_now_arr['second'] . ".xlsx";

        //================================  
        //================== �ڑ����
        //================================  
        $dsn = '';
        $user = '';
        $password = '';

        // �i�荞�݁@���ʁ@�i�[�z��
        $retunr_excel = [];

        try {

          // PDO �I�u�W�F�N�g
          $pdo = new PDO($dsn, $user, $password);

          // SQL
          $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
            WHERE creation_time BETWEEN ? AND ? ORDER BY user_id DESC");

          // CSV �G�N�X�|�[�g �J�n��
          $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

          // CSV �G�N�X�|�[�g �I����
          $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);

          // SQL ���s 
          $res = $stmt->execute();

          $idx = 0;

          // �t�@���_���Ȃ�������A�t�H���_���쐬  �`�� files_20211025
          $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];
          if (!file_exists($dirpath)) {
            mkdir($dirpath, 0777);

            //chmod�֐��Łuhoge�v�f�B���N�g���̃p�[�~�b�V�������u0777�v�ɂ���
            // chmod($dirpath, 0777);
          }



          //====== �t�@�C���̕ۑ�
          // �t�@�C�������ꏊ
          $create_excel = $dirpath . "/" . $file_name_excel;

          //====================================================== 
          //=========================== Excel �p�@�ϐ��錾
          //====================================================== 
          $sex = ""; // ����
          $wareki = ""; // �a��̔N
          $wareki_r = ""; // �a�� �N���� �o�͗p
          $month_r = "";
          $day_r = ""; // ���t�@���ɂ�

          $Seireki_r = ""; // ����o�͗p

          $hokensya_K = ""; // �ی��Ҏ��  �j�q�A���q
          $syasin_K = ""; // �Ј��敪
          $syozoku_b = ""; // ��������


          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            //=== ���� 1 employee_id �Ј��ԍ�  000101 �@�U���Ɂ@?�p�f�B���O
            $row['employee_id'] = sprintf('%06d', $row['employee_id']);
            //       $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");

            //=== ���� 2
            $row['user_name'] = mb_convert_kana($row['user_name'], "h", "UTF-8"); // �Ј�����

            //=== ���� 3 �ӂ肪�ȁ@���@�J�i�@�֕ϊ�
            $row['furi_name'] = mb_convert_kana($row['furi_name'], "h", "UTF-8"); // �t���K�i
            //      $row['furi_name'] = mb_convert_encoding($row['furi_name'], "SJIS-win", "auto");

            // ���[���A�h���X
            //      $row['email'] = mb_convert_encoding($row['email'], "SJIS-win", "auto");

            // �z��� spouse
            //      $row['spouse'] = mb_convert_encoding($row['spouse'], "SJIS-win", "auto");
            if (strcmp($row['spouse'], "0") == 0) {
              $row['spouse'] = "�Ȃ�";
            } else {
              $row['spouse'] = "����";
            }

            // �}�{�l�� 	dependents_num
            //      $row['dependents_num'] = mb_convert_encoding($row['dependents_num'], "SJIS-win", "auto");

            //=== ���� 4

            //=== ����
            if (strcmp($row['sex'], "0") == 0) {
              $row['sex'] = "�j"; // �ی��Ҏ��
              $hokensya_K = ""; // �ی��Ҏ��

              //        $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
              //     $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
            } else {
              $row['sex'] = "��"; // �ی��Ҏ��
              $hokensya_K = ""; // �ی��Ҏ��

              //       $row['sex'] = mb_convert_encoding($row['sex'], "SJIS-win", "auto");
              //      $hokensya_K = mb_convert_encoding($hokensya_K, "SJIS-win", "auto");
            }

            // === ���� 5

            //=== ���N�����@�i���� => �a��@�ϊ��j
            //        $row['birthday'] = mb_convert_encoding($row['birthday'], "SJIS-win", "auto");

            // �N
            $year = mb_substr($row['birthday'], 0, 4); // ����@���o�� 1900

            // �a��ϊ��p function  Wareki_Parse
            $wareki = Wareki_Parse($year);

            // ��
            $month = mb_substr($row['birthday'], 5, 2);

            if (strpos($month, '-') !== false) {
              // - �n�C�t�� ���܂܂�Ă���ꍇ , 0�@�p�f�B���O
              $month = str_replace("-", "", $month);
              $month_r = "0" . $month;
            } else {
              $month_r = $month;
            }


            // ��
            $day = mb_substr(
              $row['birthday'],
              7,
              2
            );
            // ������̒����擾  1,  10
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

            // �a��
            $wareki_r = $wareki . $month_r . "��" . $day_r . "��";
            //      $wareki_r = mb_convert_encoding($wareki_r, "SJIS-win", "auto");

            //=== ����6  ����  
            $Seireki_r = $year . "�N" . $month_r . "��" . $day_r . "��";
            //        $Seireki_r = mb_convert_encoding($Seireki_r, "SJIS-win", "auto");


            //===  �X�֔ԍ� zip
            //=== �X�֔ԍ� zip  �i-�j ������
            $row['zip'] = substr($row['zip'], 0, 3) . "-" . substr($row['zip'], 3);
            //      $row['zip'] = mb_convert_encoding($row['zip'], "SJIS-win", "auto");

            //=== �Z���P address_01
            //      $row['address_01'] = mb_convert_encoding($row['address_01'], "SJIS-win", "auto");

            //=== �d�b�ԍ� tel
            $row['tel'] = substr($row['tel'], 0, 3) . "-" . substr($row['tel'], 3, 4) . "-" . substr($row['tel'], 7);

            //       $row['creation_time'] = mb_convert_encoding($row['creation_time'], "SJIS-win", "auto");
            //      $row['data_Flg'] = mb_convert_encoding($row['data_Flg'], "SJIS-win", "auto");

            //=== �Ј��ԍ�
            //       $row['employee_id'] = mb_convert_encoding($row['employee_id'], "SJIS-win", "auto");


            //=== �Ј��敪
            $syasin_K = "�A���o�C�g";
            //       $syasin_K = mb_convert_encoding($syasin_K, "SJIS-win", "auto");

            //=== ��������
            //============================= 20221_12_21 �l�ύX
            // $syozoku_b = "003";
            $syozoku_b = "�A���o�C�g";

            //       $syozoku_b = mb_convert_encoding($syozoku_b, "SJIS-win", "auto");

            // ======================== �t�H�[������擾�ȊO�̍���
            $row['koumoku_01'] = ""; // ����7:���Ћ敪  �i��j
            $row['koumoku_02'] = ""; // ����8:���ДN�����@�i��j
            $row['koumoku_03'] = ""; // ����9:���ДN�����i����j�@�i��j
            $row['koumoku_04'] = ""; // ����10:�Α��N���@�i��j
            $row['koumoku_05'] = ""; // ����11 �Α��N��_�����@�i��j

            $row['koumoku_06'] = ""; // ����13:��E �@�i��j
            $row['koumoku_07'] = ""; // ����14:�w��: �i��j
            $row['koumoku_08'] = ""; // ����16:�E��: �i��j
            $row['koumoku_09'] = ""; // ����19:�Z���Q
            $row['koumoku_10'] = ""; // ����20:�Z���i�t���K�i�j�P

            $row['koumoku_11'] = ""; // ����21:�Z���i�t���K�i�j�Q
            $row['koumoku_12'] = "��Y��"; // ����24:��Q�敪:��Y��
            $row['koumoku_13'] = "��Y��"; // ����25:�Ǖw:��Y��
            $row['koumoku_14'] = "��Y��"; // ����26:�Ǖv:��Y��
            $row['koumoku_15'] = "��Y��"; // ����27:�ΘJ�w��:��Y��

            $row['koumoku_16'] = "��Y��"; // ����28:�ЊQ��:��Y��
            $row['koumoku_17'] = "��Y��"; // ����29:�O���l:��Y��
            $row['koumoku_18'] = ""; // ����30:�ސE���R: �i��j
            $row['koumoku_19'] = ""; // ����31:�ސE�N����: �i��j
            $row['koumoku_20'] = ""; // ����32:�ސE�N�����i����j: �i��j

            $row['koumoku_21'] = "��Y��"; // ����33:���^�敪:����
            $row['koumoku_22'] = "��Y��"; // ����34:�ŕ\�敪:���z�b��
            $row['koumoku_23'] = ""; // ����35:�N�������Ώ�:����
            $row['koumoku_24'] = "�U��"; // ����36:�x���`��:�U��
            $row['koumoku_25'] = "�T���v��"; // ����37:�x�����p�^�[��:�T���v��

            //============================= 20221_12_21 �l�ύX
            //  $row['koumoku_26'] = "�Ј��p�^�[��"; // ����38:���^���׏��p�^�[��:�Ј��p�^�[��
            $row['koumoku_26'] = ""; // ����38:���^���׏��p�^�[��:�Ј��p�^�[��

            //============================= 20221_12_21 �l�ύX
            //  $row['koumoku_27'] = "�ܗ^�T���v��"; // ����39:�ܗ^���׏��p�^�[��:�ܗ^�T���v��
            $row['koumoku_27'] = ""; // ����39:�ܗ^���׏��p�^�[��:�ܗ^�T���v��

            $row['koumoku_28'] = ""; // ����40:���N�ی��Ώ�: �i��j
            $row['koumoku_29'] = ""; // ����41:���ی��Ώ�: �i��j

            //============================= 20221_12_21 �l�ύX
            //  $row['koumoku_30'] = "220"; // ����42:���ی��z�i��~�j:220
            $row['koumoku_30'] = ""; // ����42:���ی��z�i��~�j:220

            //============================= 20221_12_21 �l�ύX
            //  $row['koumoku_31'] = "367"; // ����43:���ۏؔԍ��i����j367
            $row['koumoku_31'] = ""; // ����43:���ۏؔԍ��i����j367


            $row['koumoku_32'] = ""; // ����44:���ۏؔԍ��i�g���j: �i��j
            $row['koumoku_33'] = ""; // ����45:���ہE���N�@���i�擾�N����: �i��j
            $row['koumoku_34'] = ""; // ����46:�����N���Ώ�: �i��j
            $row['koumoku_35'] = "��Y��"; // ����47:70�Έȏ��p��:��Y��

            // $hokensya_K  ����48:�ی��Ҏ��:���q�@������ �ϐ����Ă͂� ������
            $row['koumoku_37'] = ""; // ����49:���N���z�i��~�j: �i��j
            $row['koumoku_38'] = ""; // ����50:��b�N���ԍ��P: �i��j
            $row['koumoku_39'] = ""; // ����51:��b�N���ԍ��Q:�Ώ�
            $row['koumoku_40'] = "�ΏۊO"; // ����52:�Z���ԘJ���ҁi3/4�����j:�ΏۊO

            $row['koumoku_41'] = "�Ώ�"; // ����53:�J�Еی��Ώ�:�Ώ�

            //============================= 20221_12_21 �l�ύX
            //  $row['koumoku_42'] = "�Ώ�"; // ����54:�ٗp�ی��Ώ�:�Ώ�
            $row['koumoku_42'] = ""; // ����54:�ٗp�ی��Ώ�:�Ώ�

            $row['koumoku_43'] = "��p"; // ����55:�J���ی��p�敪:��p

            //============================= 20221_12_21 �l�ύX
            //  $row['koumoku_44'] = "5029"; // ����56:�ٗp�ی���ی��Ҕԍ��P:5029
            $row['koumoku_44'] = ""; // ����56:�ٗp�ی���ی��Ҕԍ��P:5029

            //============================= 20221_12_21 �l�ύX
            //  $row['koumoku_45'] = "530842"; // ����57:�ٗp�ی���ی��Ҕԍ��Q:530842
            $row['koumoku_45'] = ""; // ����57:�ٗp�ی���ی��Ҕԍ��Q:530842

            //============================= 20221_12_21 �l�ύX
            //  $row['koumoku_46'] = "4"; // ����58:�ٗp�ی���ی��Ҕԍ��R:4
            $row['koumoku_46'] = ""; // ����58:�ٗp�ی���ی��Ҕԍ��R:4


            $row['koumoku_47'] = ""; // ����59:�J���ی��@���i�擾�N����: �i��j
            // ����60:�z���:�Ȃ��@�@�����@$row['spouse']
            $row['koumoku_49'] = ""; // ����61:�z��Ҏ���: �i��j
            $row['koumoku_50'] = ""; // ����62:�z��҃t���K�i: �i��j

            $row['koumoku_51'] = ""; // ����63:�z��Ґ���: �i��j
            $row['koumoku_52'] = ""; // ����64:�z��Ґ��N����: �i��j
            $row['koumoku_53'] = ""; // ����65:�z��Ґ��N�����i����j: �i��j
            $row['koumoku_54'] = "��Y��"; // ����66:����T���Ώ۔z���:��Y��
            $row['koumoku_55'] = "��Y��"; // ����67:�z��ҘV�l:��Y��

            $row['koumoku_56'] = "��Y��"; // ����68:�z��ҘV�l:��Y��
            $row['koumoku_57'] = "�񓯋�"; // ����69:�z��ҘV�l:�񓯋�
            $row['koumoku_58'] = "��Y��"; // ����70:�z��ҘV�l:��Y��
            //     $row['koumoku_59'] = ""; // ����71:�}�{�T���Ώېl��:0

            // ����71:�}�{�T���Ώېl��:  �t�H�[������擾


            //======== 165 �́@�󔒍s
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
            // ====================================== �z��@�}��
            // ==============================================================
            $retunr_excel[] = array(

              'employee_id' => $row['employee_id'], // ���� 1 employee_id �Ј��ԍ�
              'user_name' => $row['user_name'],   // ���� 2 �Ј�����
              'furi_name' => $row['furi_name'],  // ���� 3  �t���K�i
              'sex' => $row['sex'], // ���� 4  ����
              'wareki_r' => $wareki_r,  // ���� 5 ���N�����i�a��j

              'Seireki_r' => $Seireki_r, // ���� 6 ���N�����i����j
              'koumoku_01' => $row['koumoku_01'], // ����7:���Ћ敪
              'koumoku_02' => $row['koumoku_02'], // ����8:���ДN����
              'koumoku_03' => $row['koumoku_03'], // ����9:���ДN�����i����j
              'koumoku_04' => $row['koumoku_04'], // ����10:�Α��N��

              'koumoku_05' => $row['koumoku_05'], // ����11 �Α��N��_����
              'syasin_K' => $syasin_K, // ���� 12 �Ј��敪
              'koumoku_06' => $row['koumoku_06'], // ����13:��E �@�i��j
              'koumoku_07' => $row['koumoku_07'], // ����14:�w��: �i��j
              'syozoku_b' => $row['syozoku_b'], //  ����15:��������  

              'koumoku_08' => $row['koumoku_08'], // ����16 : �E��
              'zip' => $row['zip'],   // ����17�@�X�֔ԍ�
              'address_01' => $row['address_01'],  // ����18 �Z���@�P
              'koumoku_09' => $row['koumoku_09'],  // ����19:�Z���Q: �i��j
              'koumoku_10' => $row['koumoku_10'],  // ����20:�Z���i�t���K�i�j�P: �i��j

              'koumoku_11' => $row['koumoku_11'],  // ����21:�Z���i�t���K�i�j�Q: �i��j
              'tel' => $row['tel'],  // ����22:�d�b�ԍ�:090-1111-2222
              'email' => $row['email'], // ����23:���[���A�h���X
              'koumoku_12' => $row['koumoku_12'], // ����24:��Q�敪:��Y��
              'koumoku_13' => $row['koumoku_13'], // ����25:�Ǖw:��Y��

              'koumoku_14' => $row['koumoku_14'], // ����26:�Ǖv:��Y��
              'koumoku_15' => $row['koumoku_15'], // ����27:�ΘJ�w��:��Y��
              'koumoku_16' => $row['koumoku_16'], // ����28:�ЊQ��:��Y��
              'koumoku_17' => $row['koumoku_17'], // ����29:�O���l:��Y��
              'koumoku_18' => $row['koumoku_18'], // ����30:�ސE���R: �i��j
              // 30 ?
              'koumoku_19' => $row['koumoku_19'], // ����31:�ސE�N����: �i��j
              'koumoku_20' => $row['koumoku_20'], // ����32:�ސE�N�����i����j: �i��j
              'koumoku_21' => $row['koumoku_21'], // ����33:���^�敪:����
              'koumoku_22' => $row['koumoku_22'], // ����34:�ŕ\�敪:���z�b��
              'koumoku_23' => $row['koumoku_23'], // ����35:�N�������Ώ�:����
              // 35
              'koumoku_24' => $row['koumoku_24'], // ����36:�x���`��:�U��
              'koumoku_25' => $row['koumoku_25'], // ����37:�x�����p�^�[��:�T���v��
              'koumoku_26' => $row['koumoku_26'], // ����38:���^���׏��p�^�[��:�Ј��p�^�[��
              'koumoku_27' => $row['koumoku_27'], // ����39:�ܗ^���׏��p�^�[��:�ܗ^�T���v��
              'koumoku_28' => $row['koumoku_28'], // ����40:���N�ی��Ώ�: �i��j
              // 40
              'koumoku_29' => $row['koumoku_29'], // ����41:���ی��Ώ�: �i��j
              'koumoku_30' => $row['koumoku_30'], // ����42:���ی��z�i��~�j:220
              'koumoku_31' => $row['koumoku_31'], // ����43:���ۏؔԍ��i����j:367
              'koumoku_32' => $row['koumoku_32'], // ����44:���ۏؔԍ��i�g���j: �i��j
              'koumoku_33' => $row['koumoku_33'], // ����45:���ہE���N�@���i�擾�N����: �i��j
              // 45
              'koumoku_34' => $row['koumoku_34'], // ����46:�����N���Ώ�: �i��j
              'koumoku_35' => $row['koumoku_35'], // ����47:70�Έȏ��p��:��Y��

              // $hokensya_K �ϐ��i�[
              'hokensya_K' => $hokensya_K, // ����48:�ی��Ҏ��:���q

              'koumoku_37' => $row['koumoku_36'], // ����49:���N���z�i��~�j: �i��j
              'koumoku_38' => $row['koumoku_37'], // ����50:��b�N���ԍ��P: �i��j
              // 50
              'koumoku_39' => $row['koumoku_39'], // ����51:��b�N���ԍ��Q:�Ώ�
              'koumoku_40' => $row['koumoku_40'], // ����52:�Z���ԘJ���ҁi3/4�����j:�ΏۊO
              'koumoku_41' => $row['koumoku_41'], // ����53:�J�Еی��Ώ�:�Ώ�
              'koumoku_42' => $row['koumoku_42'], // ����54:�ٗp�ی��Ώ�:�Ώ�
              'koumoku_43' => $row['koumoku_43'], // ����55:�J���ی��p�敪:��p
              // 55
              'koumoku_44' => $row['koumoku_44'], // ����56:�ٗp�ی���ی��Ҕԍ��P:5029
              'koumoku_45' => $row['koumoku_45'], // ����57:�ٗp�ی���ی��Ҕԍ��Q:530842
              'koumoku_46' => $row['koumoku_46'], // ����58:�ٗp�ی���ی��Ҕԍ��R:4
              'koumoku_47' => $row['koumoku_47'], // ����59:�J���ی��@���i�擾�N����: �i��j
              'spouse' => $row['spouse'], // ����60:�z���:�@�t�H�[������l�擾
              // 60
              'koumoku_49' => $row['koumoku_49'], // ����61:�z��Ҏ���: �i��j
              'koumoku_50' => $row['koumoku_50'], // ����62:�z��҃t���K�i: �i��j
              'koumoku_51' => $row['koumoku_51'], // ����63:�z��Ґ���: �i��j
              'koumoku_52' => $row['koumoku_52'], // ����64:�z��Ґ��N����: �i��j
              'koumoku_53' => $row['koumoku_53'], // ����65:�z��Ґ��N�����i����j: �i��j
              // 65
              'koumoku_54' => $row['koumoku_54'], // ����66:����T���Ώ۔z���:��Y��
              'koumoku_55' => $row['koumoku_55'], // ����67:�z��ҘV�l:��Y��
              'koumoku_56' => $row['koumoku_56'], // ����68:�z��ҏ�Q��:��Y��
              'koumoku_57' => $row['koumoku_57'], // ����69:�z��ғ���:�񓯋�
              'koumoku_58' => $row['koumoku_58'], // ����70:�z��Ҕ񋏏Z��:��Y��

              // ����71:�}�{�T���Ώېl��:  �t�H�[������擾
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

            // Spreadsheet�I�u�W�F�N�g����
            $objSpreadsheet = new Spreadsheet();
            // �V�[�g�ݒ�
            $objSheet = $objSpreadsheet->getActiveSheet();

            // Spreadsheet�I�u�W�F�N�g����
            $objSpreadsheet = new Spreadsheet();
            // �V�[�g�ݒ�
            $objSheet = $objSpreadsheet->getActiveSheet();

            // �w�b�_�[����
            // �������̔z��f�[�^
            $arrX = array(
              '�Ј��ԍ�', // ����0
              '�Ј�����', // ����1
              '�t���K�i', // ����2
              '����', // ����3
              '���N����', // ����4
              '���N�����i����j', // ����5
              '���Ћ敪', // ����6
              '���ДN����', // ����7
              '���ДN�����i����j', // ����8
              '�Α��N��', // ����9
              '�Α��N��_����', // ����10
              '�Ј��敪', // ����11
              '��E', // ����12
              '�w��', // ����13
              '��������', // ����14
              '�E��', // ����15
              '�X�֔ԍ�', // ����16
              '�Z���P', // ����17
              '�Z���Q', // ����18
              '�Z���i�t���K�i�j�P', // ����19
              '�Z���i�t���K�i�j�Q', // ����20
              '�d�b�ԍ�', // ����21
              '���[���A�h���X', // ����22
              '��Q�敪', // ����23
              '�Ǖw', // ����24
              '�Ǖv', // ����25
              '�ΘJ�w��', // ����26
              '�ЊQ��', // ����27
              '�O���l', // ����28
              '�ސE���R', // ����29
              '�ސE�N����', // ����30
              '�ސE�N�����i����j', // ����31
              '���^�敪', // ����32
              '�ŕ\�敪', // ����33
              '�N�������Ώ�', // ����34
              '�x���`��', // ����35
              '�x�����p�^�[��', // ����36
              '���^���׏��p�^�[��', // ����37
              '�ܗ^���׏��p�^�[��', // ����38
              '���N�ی��Ώ�', // ����39
              '���ی��Ώ�', // ����40
              '���ی��z�i��~�j', // ����41
              '���ۏؔԍ��i����j', // ����42
              '���ۏؔԍ��i�g���j', // ����43
              '���ہE���N�@���i�擾�N����', // ����44
              '�����N���Ώ�', // ����45
              '70�Έȏ��p��', // ����46
              '�ی��Ҏ��', // ����47
              '���N���z�i��~�j', // ����48
              '��b�N���ԍ��P', // ����49
              '��b�N���ԍ��Q', // ����50
              '�Z���ԘJ���ҁi3/4�����j', // ����51
              '�J�Еی��Ώ�', // ����52
              '�ٗp�ی��Ώ�', // ����53
              '�J���ی��p�敪', // ����54
              '�ٗp�ی���ی��Ҕԍ��P', // ����55
              '�ٗp�ی���ی��Ҕԍ��Q', // ����56
              '�ٗp�ی���ی��Ҕԍ��R', // ����57
              '�J���ی��@���i�擾�N����', // ����58
              '�z���', // ����59
              '�z��Ҏ���', // ����60
              '�z��҃t���K�i', // ����61
              '�z��Ґ���', // ����62
              '�z��Ґ��N����', // ����63
              '�z��Ґ��N�����i����j', // ����64
              '����T���Ώ۔z���', // ����65
              '�z��ҘV�l', // ����66
              '�z��ҏ�Q��', // ����67
              '�z��ғ���', // ����68
              '�z��Ҕ񋏏Z��', // ����69
              '�}�{�T���Ώېl��', // ����70
              '�}�{�e��_�}�{�e����_1', // ����71
              '�}�{�e��_�t���K�i_1', // ����72
              '�}�{�e��_����_1', // ����73
              '�}�{�e��_���N����_1', // ����74
              '�}�{�e��_���N�����i����j_1', // ����75
              '�}�{�e��_�}�{�敪_1', // ����76
              '�}�{�e��_��Q�ҋ敪_1', // ����77
              '�}�{�e��_�����敪_1', // ����78
              '�}�{�e��_�����V�e���敪_1', // ����79
              '�}�{�e��_�񋏏Z�ҋ敪_1', // ����80
              '�}�{�e��_�T���v�Z�敪_1', // ����81
              '�}�{�e��_�}�{�e����_2', // ����82
              '�}�{�e��_�t���K�i_2', // ����83
              '�}�{�e��_����_2', // ����84
              '�}�{�e��_���N����_2', // ����85
              '�}�{�e��_���N�����i����j_2', // ����86
              '�}�{�e��_�}�{�敪_2', // ����87
              '�}�{�e��_��Q�ҋ敪_2', // ����88
              '�}�{�e��_�����敪_2', // ����89
              '�}�{�e��_�����V�e���敪_2', // ����90
              '�}�{�e��_�񋏏Z�ҋ敪_2', // ����91
              '�}�{�e��_�T���v�Z�敪_2', // ����92
              '�}�{�e��_�}�{�e����_3', // ����93
              '�}�{�e��_�t���K�i_3', // ����94
              '�}�{�e��_����_3', // ����95
              '�}�{�e��_���N����_3', // ����96
              '�}�{�e��_���N�����i����j_3', // ����97
              '�}�{�e��_�}�{�敪_3', // ����98
              '�}�{�e��_��Q�ҋ敪_3', // ����99
              '�}�{�e��_�����敪_3', // ����100
              '�}�{�e��_�����V�e���敪_3', // ����101
              '�}�{�e��_�񋏏Z�ҋ敪_3', // ����102
              '�}�{�e��_�T���v�Z�敪_3', // ����103
              '�}�{�e��_�}�{�e����_4', // ����104
              '�}�{�e��_�t���K�i_4', // ����105
              '�}�{�e��_����_4', // ����106
              '�}�{�e��_���N����_4', // ����107
              '�}�{�e��_���N�����i����j_4', // ����108
              '�}�{�e��_�}�{�敪_4', // ����109
              '�}�{�e��_��Q�ҋ敪_4', // ����110
              '�}�{�e��_�����敪_4', // ����111
              '�}�{�e��_�����V�e���敪_4', // ����112
              '�}�{�e��_�񋏏Z�ҋ敪_4', // ����113
              '�}�{�e��_�T���v�Z�敪_4', // ����114
              '�}�{�e��_�}�{�e����_5', // ����115
              '�}�{�e��_�t���K�i_5', // ����116
              '�}�{�e��_����_5', // ����117
              '�}�{�e��_���N����_5', // ����118
              '�}�{�e��_���N�����i����j_5', // ����119
              '�}�{�e��_�}�{�敪_5', // ����120
              '�}�{�e��_��Q�ҋ敪_5', // ����121
              '�}�{�e��_�����敪_5', // ����122
              '�}�{�e��_�����V�e���敪_5', // ����123
              '�}�{�e��_�񋏏Z�ҋ敪_5', // ����124
              '�}�{�e��_�T���v�Z�敪_5', // ����125
              '�}�{�e��_�}�{�e����_6', // ����126
              '�}�{�e��_�t���K�i_6', // ����127
              '�}�{�e��_����_6', // ����128
              '�}�{�e��_���N����_6', // ����129
              '�}�{�e��_���N�����i����j_6', // ����130
              '�}�{�e��_�}�{�敪_6', // ����131
              '�}�{�e��_��Q�ҋ敪_6', // ����132
              '�}�{�e��_�����敪_6', // ����133
              '�}�{�e��_�����V�e���敪_6', // ����134
              '�}�{�e��_�񋏏Z�ҋ敪_6', // ����135
              '�}�{�e��_�T���v�Z�敪_6', // ����136
              '�}�{�e��_�}�{�e����_7', // ����137
              '�}�{�e��_�t���K�i_7', // ����138
              '�}�{�e��_����_7', // ����139
              '�}�{�e��_���N����_7', // ����140
              '�}�{�e��_���N�����i����j_7', // ����141
              '�}�{�e��_�}�{�敪_7', // ����142
              '�}�{�e��_��Q�ҋ敪_7', // ����143
              '�}�{�e��_�����敪_7', // ����144
              '�}�{�e��_�����V�e���敪_7', // ����145
              '�}�{�e��_�񋏏Z�ҋ敪_7', // ����146
              '�}�{�e��_�T���v�Z�敪_7', // ����147
              '�}�{�e��_�}�{�e����_8', // ����148
              '�}�{�e��_�t���K�i_8', // ����149
              '�}�{�e��_����_8', // ����150
              '�}�{�e��_���N����_8', // ����151
              '�}�{�e��_���N�����i����j_8', // ����152
              '�}�{�e��_�}�{�敪_8', // ����153
              '�}�{�e��_��Q�ҋ敪_8', // ����154
              '�}�{�e��_�����敪_8', // ����155
              '�}�{�e��_�����V�e���敪_8', // ����156
              '�}�{�e��_�񋏏Z�ҋ敪_8', // ����157
              '�}�{�e��_�T���v�Z�敪_8', // ����158
              '�}�{�e��_�}�{�e����_9', // ����159
              '�}�{�e��_�t���K�i_9', // ����160
              '�}�{�e��_����_9', // ����161
              '�}�{�e��_���N����_9', // ����162
              '�}�{�e��_���N�����i����j_9', // ����163
              '�}�{�e��_�}�{�敪_9', // ����164
              '�}�{�e��_��Q�ҋ敪_9', // ����165
              '�}�{�e��_�����敪_9', // ����166
              '�}�{�e��_�����V�e���敪_9', // ����167
              '�}�{�e��_�񋏏Z�ҋ敪_9', // ����168
              '�}�{�e��_�T���v�Z�敪_9', // ����169
              '�}�{�e��_�}�{�e����_10', // ����170
              '�}�{�e��_�t���K�i_10', // ����171
              '�}�{�e��_����_10', // ����172
              '�}�{�e��_���N����_10', // ����173
              '�}�{�e��_���N�����i����j_10', // ����174
              '�}�{�e��_�}�{�敪_10', // ����175
              '�}�{�e��_��Q�ҋ敪_10', // ����176
              '�}�{�e��_�����敪_10', // ����177
              '�}�{�e��_�����V�e���敪_10', // ����178
              '�}�{�e��_�񋏏Z�ҋ敪_10', // ����179
              '�}�{�e��_�T���v�Z�敪_10', // ����180
              '�}�{�e��_�}�{�e����_11', // ����181
              '�}�{�e��_�t���K�i_11', // ����182
              '�}�{�e��_����_11', // ����183
              '�}�{�e��_���N����_11', // ����184
              '�}�{�e��_���N�����i����j_11', // ����185
              '�}�{�e��_�}�{�敪_11', // ����186
              '�}�{�e��_��Q�ҋ敪_11', // ����187
              '�}�{�e��_�����敪_11', // ����188
              '�}�{�e��_�����V�e���敪_11', // ����189
              '�}�{�e��_�񋏏Z�ҋ敪_11', // ����190
              '�}�{�e��_�T���v�Z�敪_11', // ����191
              '�}�{�e��_�}�{�e����_12', // ����192
              '�}�{�e��_�t���K�i_12', // ����193
              '�}�{�e��_����_12', // ����194
              '�}�{�e��_���N����_12', // ����195
              '�}�{�e��_���N�����i����j_12', // ����196
              '�}�{�e��_�}�{�敪_12', // ����197
              '�}�{�e��_��Q�ҋ敪_12', // ����198
              '�}�{�e��_�����敪_12', // ����199
              '�}�{�e��_�����V�e���敪_12', // ����200
              '�}�{�e��_�񋏏Z�ҋ敪_12', // ����201
              '�}�{�e��_�T���v�Z�敪_12', // ����202
              '�}�{�e��_�}�{�e����_13', // ����203
              '�}�{�e��_�t���K�i_13', // ����204
              '�}�{�e��_����_13', // ����205
              '�}�{�e��_���N����_13', // ����206
              '�}�{�e��_���N�����i����j_13', // ����207
              '�}�{�e��_�}�{�敪_13', // ����208
              '�}�{�e��_��Q�ҋ敪_13', // ����209
              '�}�{�e��_�����敪_13', // ����210
              '�}�{�e��_�����V�e���敪_13', // ����211
              '�}�{�e��_�񋏏Z�ҋ敪_13', // ����212
              '�}�{�e��_�T���v�Z�敪_13', // ����213
              '�}�{�e��_�}�{�e����_14', // ����214
              '�}�{�e��_�t���K�i_14', // ����215
              '�}�{�e��_����_14', // ����216
              '�}�{�e��_���N����_14', // ����217
              '�}�{�e��_���N�����i����j_14', // ����218
              '�}�{�e��_�}�{�敪_14', // ����219
              '�}�{�e��_��Q�ҋ敪_14', // ����220
              '�}�{�e��_�����敪_14', // ����221
              '�}�{�e��_�����V�e���敪_14', // ����222
              '�}�{�e��_�񋏏Z�ҋ敪_14', // ����223
              '�}�{�e��_�T���v�Z�敪_14', // ����224
              '�}�{�e��_�}�{�e����_15', // ����225
              '�}�{�e��_�t���K�i_15', // ����226
              '�}�{�e��_����_15', // ����227
              '�}�{�e��_���N����_15', // ����228
              '�}�{�e��_���N�����i����j_15', // ����229
              '�}�{�e��_�}�{�敪_15', // ����230
              '�}�{�e��_��Q�ҋ敪_15', // ����231
              '�}�{�e��_�����敪_15', // ����232
              '�}�{�e��_�����V�e���敪_15', // ����233
              '�}�{�e��_�񋏏Z�ҋ敪_15', // ����234
              '�}�{�e��_�T���v�Z�敪_15', // ����235
            );

            $objSheet->fromArray(
              $arrX,      // �z��f�[�^
              NULL,       // �z��f�[�^�̒��ŃZ���ɐݒ肵�Ȃ�NULL�l�̎w��
              'A1'        // ������W(�f�t�H���g:"A1")
            );


            //�@Excel�t�@�C���ց@�f�[�^�}��
            $objSheet->fromArray($retunr_excel, null, 'A2');

            // XLSX�`���I�u�W�F�N�g����
            $objWriter = new Xlsx($objSpreadsheet);

            // �t�@�C��������
            $objWriter->save($create_excel);

            //  exit();

            // ********* �t���O����
            $csv_file_ok_FLG = "0"; // �f�t�H���g

            $excel_file_FLG = "1"; // Excel�@�쐬����

          } else {

            // �w�肵�����t�ɊY���́@Excel�t�@�C�����Ȃ������ꍇ
            //      $excel_file_err = 1;

            $excel_file_FLG = "2"; // Excel�@�G���[

          }
        } catch (PDOException $e) {

          //    print('Error:'.$e->getMessage());

          // (�g�����U�N�V����) ���[���o�b�N
          //       $pdo->rollBack();

          // ************** �G���[���� ***************
          $csv_file_ok_FLG = "2";
        } finally {

          $pdo = null;
        } //======================================== END try ==================================

        // =========================================================================================== 
        // =========================================== ��s�p�@CSV try ================================
        // ===========================================================================================
        // �i�荞�݁@���ʁ@�i�[�z��
        $retunr_excel_bank = [];
        //========================================
        try {

          // PDO �I�u�W�F�N�g
          $pdo = new PDO($dsn, $user, $password);

          // SQL
          $stmt = $pdo->prepare("SELECT * FROM User_Info_Table 
            WHERE creation_time BETWEEN ? AND ? ORDER BY user_id DESC");

          // CSV �G�N�X�|�[�g �J�n��
          $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

          // CSV �G�N�X�|�[�g �I����
          $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);

          // SQL ���s 
          $res = $stmt->execute();

          $idx = 0;

          // �t�@���_���Ȃ�������A�t�H���_���쐬  �`�� files_20211025
          $dirpath = './files_' . $get_now_arr['year'] . $get_now_arr['month'] . $get_now_arr['day'];

          //====== �t�@�C���̕ۑ�
          // �t�@�C�������ꏊ
          $create_excel_bank = $dirpath . "/" . $file_name_excel_bank;


          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            // ============= 0 �p�f�B���O ===============
            $row['employee_id'] = sprintf('%06d', $row['employee_id']);
            $row['bank_code'] = sprintf('%04d', $row['bank_code']);
            $row['bank_siten_code'] = sprintf('%03d', $row['bank_siten_code']);

            // �a�����  0:���� , 1:���� bank_kamoku
            if (strcmp($row['bank_kamoku'], "0") == 0) {
              $bank_kamoku = "����";
            } else {
              $bank_kamoku = "����";
            }

            // �x�����o�^No
            $Siharai_M_Number = "1";

            // �x�X�t���K�i ���J�����Ȃ� $bank_siten_name_kana
            $bank_siten_name_kana = "����ض��";

            // �U���萔�� ��
            //=== �󕶎�
            $row['koumoku_01'] = ""; // �� ���� ,   // �U���萔��

            $teigaku_F = ""; // ��z�U��

            //===  10 �� ===
            $row['koumoku_02'] = ""; // �� ���� ,   //   �U���� 1�@���z,
            $row['koumoku_03'] = ""; // �� ���� ,   // �U����2�@���Z�@�փR�[�h,
            $row['koumoku_04'] = ""; // �� ���� ,   // �U����2�@���Z�@�֖�,
            $row['koumoku_05'] = ""; // �� ���� ,   // �U����2�@���Z�@�փt���K�i,
            $row['koumoku_06'] = ""; // �� ���� ,   // �U����2�@�x�X�R�[�h,
            $row['koumoku_07'] = ""; // �� ���� ,   // �U����2  �x�X��,
            $row['koumoku_08'] = ""; // �� ���� ,  �U����2   �x�X�t���K�i
            $row['koumoku_09'] = ""; // �� ���� ,   // �U����2�@�a�����,
            $row['koumoku_10'] = ""; // �� ���� ,   // �U����2�@�����ԍ�,
            $row['koumoku_11'] = ""; // �� ���� ,   // �U����2�@�U���萔��,
            $row['koumoku_12'] = ""; // �� ���� ,   // �U����2�@��z�U��,

            //====== ���X�g�J���� �u���z�v
            $last_kingaku = "";

            // ==============================================================
            // ====================================== �z��@�}��
            // ==============================================================
            $retunr_excel_bank[] = array(

              'employee_id' => $row['employee_id'],  //����_1:::�Ј��ԍ�
              'user_name' => $row['user_name'],      //����_2:::�Ј�����
              'Siharai_M_Number' => $Siharai_M_Number, //����_3:::�x�����o�^No
              'bank_code' => $row['bank_code'], //����_4:::�U����@�@���Z�@�փR�[�h
              'bank_name' => $row['bank_name'],  //����_5:::�U����@�@���Z�@�֖�
              'bank_name_kana' => $row['bank_name_kana'],  //����_6:::�U����@�@���Z�@�փt���K�i
              'bank_siten_code' => $row['bank_siten_code'], //����_7:::�U����@�@�x�X�R�[�h
              'bank_siten_name' => $row['bank_siten_name'], //����_8:::�U����@�@�x�X��
              'bank_siten_name_kana' => $bank_siten_name_kana, //����_9:::�U����@�@�x�X�t���K�i
              'bank_kamoku' => $bank_kamoku,  //����_10:::�U����@�@�a�����
              'kouzzz_number' => $row['kouzzz_number'], //����_11:::�U����@�@�����ԍ�
              'koumoku_01' => $row['koumoku_01'],  //����_12:::�U����@�@�U���萔��
              'teigaku_F' => $teigaku_F,            //����_13:::�U����@�@��z�U��
              'koumoku_01' => $row['koumoku_01'],  //����_14:::�U����@�@���z
              'koumoku_02' => $row['koumoku_02'],  //����_15:::�U����A�@���Z�@�փR�[�h
              'koumoku_03' => $row['koumoku_03'],  //����_16:::�U����A�@���Z�@�֖�
              'koumoku_04' => $row['koumoku_04'],  //����_17:::�U����A�@���Z�@�փt���K�i
              'koumoku_05' => $row['koumoku_05'],  //����_18:::�U����A�@�x�X�R�[�h
              'koumoku_06' => $row['koumoku_06'],   //����_19:::�U����A�@�x�X��
              'koumoku_07' => $row['koumoku_07'], //����_20:::�U����A�@�x�X�t���K�i
              'koumoku_08' => $row['koumoku_08'],  //����_21:::�U����A�@�a�����
              'koumoku_09' => $row['koumoku_09'],  //����_22:::�U����A�@�����ԍ�
              'koumoku_10' => $row['koumoku_10'], //����_23:::�U����A�@�U���萔��
              'koumoku_11' => $row['koumoku_11'], //����_24:::�U����A�@��z�U��
              'koumoku_12' => $row['koumoku_12'],  //����_25:::�U����A�@���z
              'last_kingaku' => $last_kingaku,  // �u���z�v
            );
          }

          if (!empty($retunr_excel_bank)) {

            $excel_file_err = 0;

            // Spreadsheet�I�u�W�F�N�g����
            $objSpreadsheet = new Spreadsheet();
            // �V�[�g�ݒ�
            $objSheet = $objSpreadsheet->getActiveSheet();

            // Spreadsheet�I�u�W�F�N�g����
            $objSpreadsheet = new Spreadsheet();
            // �V�[�g�ݒ�
            $objSheet = $objSpreadsheet->getActiveSheet();

            // �w�b�_�[����
            // �������̔z��f�[�^
            $arrX = array(
              '�Ј��ԍ�',
              '�Ј�����',
              '�x�����o�^No',
              '�U����@�@���Z�@�փR�[�h',
              '�U����@�@���Z�@�֖�',
              '�U����@�@���Z�@�փt���K�i',
              '�U����@�@�x�X�R�[�h',
              '�U����@�@�x�X��',
              '�U����@�@�x�X�t���K�i',
              '�U����@�@�a�����',
              '�U����@�@�����ԍ�',
              '�U����@�@�U���萔��',
              '�U����@�@��z�U��',
              '�U����@�@���z',
              '�U����A�@���Z�@�փR�[�h',
              '�U����A�@���Z�@�֖�',
              '�U����A�@���Z�@�փt���K�i',
              '�U����A�@�x�X�R�[�h',
              '�U����A�@�x�X��',
              '�U����A�@�x�X�t���K�i',
              '�U����A�@�a�����',
              '�U����A�@�����ԍ�',
              '�U����A�@�U���萔��',
              '�U����A�@��z�U��',
              '�U����A�@���z'
            );

            $objSheet->fromArray(
              $arrX,      // �z��f�[�^
              NULL,       // �z��f�[�^�̒��ŃZ���ɐݒ肵�Ȃ�NULL�l�̎w��
              'A1'        // ������W(�f�t�H���g:"A1")
            );

            //�@Excel�t�@�C���ց@�f�[�^�}��
            $objSheet->fromArray($retunr_excel_bank, null, 'A2');

            // XLSX�`���I�u�W�F�N�g����
            $objWriter = new Xlsx($objSpreadsheet);

            // �t�@�C��������
            $objWriter->save($create_excel_bank);

            //  exit();

            // ********* �t���O����
            $csv_file_ok_FLG = "0"; // �f�t�H���g

            $excel_file_FLG = "1"; // Excel�@�쐬����

          } else {

            // �w�肵�����t�ɊY���́@Excel�t�@�C�����Ȃ������ꍇ
            //      $excel_file_err = 1;

            $excel_file_FLG = "2"; // Excel�@�G���[

          }
        } catch (PDOException $e) {

          // ************** �G���[���� ***************
          $csv_file_ok_FLG = "2";
        }

        // =========================================== ====================================================
        // =========================================== ��s�p�@CSV try  END ================================
        // =========================================== ====================================================


        //==================================================================
        //================= �A�b�v�f�[�g ���� �f�[�^�����t���O�� �f�[�^�쐬�ς݂ɂ���
        //==================================================================

        try {

          // PDO �I�u�W�F�N�g
          $pdo = new PDO($dsn, $user, $password);

          $stmt = $pdo->prepare("UPDATE User_Info_Table SET data_Flg = '1' 
                  WHERE creation_time BETWEEN ? AND ?");

          // CSV �G�N�X�|�[�g �J�n��
          $stmt->bindValue(1, $date_target, PDO::PARAM_STR);

          // CSV �G�N�X�|�[�g �I����
          $stmt->bindValue(2, $date_target_02, PDO::PARAM_STR);

          // SQL ���s 
          $res = $stmt->execute();
        } catch (PDOException $e) {

          // ************** �G���[���� ***************
          $csv_file_ok_FLG = "2";
        } finally {

          $pdo = null;

          // �y�[�W�����[�h
          header("Location: " . $_SERVER['PHP_SELF']);

          exit;
        }



        //==============================================
        //*************** �t�@�C���̃_�E�����[�h���� */
        //==============================================
        //  t_download($protocol);

        //===================================
        // =========== ���݂̃t��URL �擾
        //===================================

        /*
    if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {

      $protocol = 'https://';
    } else {

      $protocol = 'http://';
    }

    // ./files �� . ���폜
    $new_create_excel = mb_substr($create_excel, 1);

    $protocol .= $_SERVER["HTTP_HOST"] . '/job_recruit/csv' . $new_create_excel;
    //. $_SERVER["REQUEST_URI"];

    //      var_dump($new_create_excel);



    // �t�@�C���^�C�v���w��
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    // �t�@�C���^�C�v���w��

    // �t�@�C���T�C�Y���擾���A�_�E�����[�h�̐i����\��
    //      header('Content-Length: '.filesize($protocol));
    // �t�@�C���̃_�E�����[�h�A���l�[�����w��
    header('Content-Disposition: attachment; filename="' . $file_name_excel . '"');

    ob_clean();  //�ǋL
    flush();     //�ǋL

    // �t�@�C����ǂݍ��݃_�E�����[�h�����s
    readfile($protocol);
    exit;
    */

        // ============= �G���[���� 000   
        // ============= �� �u�I�����Ă��������v���I�΂�Ă�����  



      }
    }
  } else if ($export_output === "0") {

    //  print "aaaaaa";

    // === ���t�{�b�N�X���@�Q�Ƃ��@�󂾂����ꍇ *** �G���[���� ***
    $error = "���t�w�肪��ł��B�ǂ��炩���t����͂��Ă��������B";

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

    <!-- jQuery �f�[�g�s�b�J�[ -->
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!--CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />

    <!-- �o���f�[�V�����p -->
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

    <!-- �C���ӏ�1 title�Z�N�V�������Ăяo���B�f�t�H���g�l�� no title�Ƃ��� -->
    <title>�A���o�C�g��W�T�C�g TOP�@��ʂP</title>

    <style>
      /* Vue.js �p */
      .v-enter-active,
      .v-leave-actibe {
        transition: opacity 1s;
      }

      .v-enter,
      .v-leave-to {
        opacity: 0;
        transition: opacity 1s;
      }

      /* jQuery �f�[�g�s�b�J�[ */

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
      <h1 class="display-5 m_h1">������Ё@����</h1>
      <p class="lead">
        <!-- �A���o�C�g�ҏ��o�^ -->
        <?php site_name_hozyo(); ?>
      </p>
    </div>
  </div>

  <!-- �_�E�����[�h�y�[�W�@�ւ́@�J�� -->
  <div class="container" style="margin-bottom: 25px;">
    <div class="col-lg4 col-xs-10 col-md-4 dl_link">

      <a href="./download.php">
        �_�E�����[�h �y�[�W�ֈړ�
      </a>

    </div>
  </div> <!-- container END -->

  <div class="container maile_c" id="chk">
    <div class="row">

      <div class="col-lg-12 col-sm-12 col-12">

        <form action="csv_export.php" method="POST" id="csv_form" class="" novalidate enctype="multipart/form-data" autocomplete="off">


          <!-- ���o�̓t�B�� �`�F�b�N�{�b�N�X �f�t�H���g�`�F�b�N�@�Ȃ� ���� => checked  -->
          <label class="ECM_CheckboxInput">

            <!-- �u���o�̓t�@�C���o�́v �`�F�b�N�{�b�N�X  -->
            <input class="ECM_CheckboxInput-Input" type="checkbox" name="uninput_file" value="1">


            <span class="ECM_CheckboxInput-DummyInput"></span>
            <span class="ECM_CheckboxInput-LabelText">���o�̓t�@�C���o��</span>
          </label>

          <div class="row">

            <div class="form-group was-validated col-lg-3 col-sm-3 col-md-3 col-xs-10" id="mail_div">
              <label for="date_target" class="font-weight-bold label_01">�J�n�����w�肵�Ă��������B</label>
              <input type="text" id="date_target" required class="form-control" name="date_target" placeholder="�J�n��">
            </div>

            <div class="form-group was-validated col-lg-3 col-sm-3 col-md-3 col-xs-10" id="mail_div">
              <label for="date_target_02" class="font-weight-bold label_01">�I�������w�肵�Ă��������B</label>
              <input type="text" id="date_target_02" required class="form-control" name="date_target_02" placeholder="�I����">
            </div>

            <!-- �\���@�{�^�� -->
            <div class="form-group col-lg-3 col-sm-3 col-md-3 col-xs-10 temp_pos" id="hyouzi_box">
              <button type="submit" name="view_btn" class="btn csv_btn e_btn font_a" id="h_btn">
                �\��
              </button>
            </div>
            <!--/ �\���@�{�^�� END -->

            <!-- CSV �G�N�X�|�[�g�@�{�^�� -->
            <div class="form-group col-lg-3 col-sm-3 col-md-3 col-xs-10" id="file_box">


              <!-- �o�͌`���@�I�� -->
              <select name="export_output" id="export" class="app_input_form date-birth-year jsc-select-bold">

                <option value="0" class="jsc-default-selected">�I��</option>

                <option class="jsc-select-bold" value="csv">
                  CSV
                </option>

                <option class="jsc-select-bold" value="excel">
                  Excel
                </option>

              </select>


              <button type="submit" name="download_btn" class="btn csv_btn e_btn font_a">
                �t�@�C���o��
              </button>



            </div>
            <!--/ CSV �G�N�X�|�[�g END -->



          </div> <!-- row END -->

        </form>

      </div>

    </div>

    <!-- ============== �\���p Table �J�n ===========  -->
    <div class="row">

      <table class="table table-hover" style="font-size: 14.5px !important;">
        <thead class="thead-dark">

          <tr>
            <th>���O</th>
            <th>�ӂ肪��</th>
            <th>���[���A�h���X</th>
            <th>����</th>
            <th>�o�^��</th>
            <th>�t�@�C���o��</th>
          </tr>

        </thead>

        <!--  csv �t�@�C���o�� �\�� -->
        <?php if (!empty($return)) : ?>
          <?php foreach ($return as $r_item) : ?>
            <tr>
              <td> <?php print h($r_item['user_name']); ?> </td>
              <td> <?php print h($r_item['furi_name']); ?> </td>

              <td> <?php print h($r_item['email']); ?> </td>

              <td>
                <?php if ($r_item['sex'] == "0") : ?>
                  <p>�j��</p>
                <?php else : ?>
                  <p>����</p>
                <?php endif; ?>
              </td>

              <!-- �o�^�� -->
              <td> <?php print h($r_item['creation_time']); ?></td>

              <!-- ======= �t�@�C���o�́@�X�^�b�c�@���o�́F�O  �o�͍ς݁F�P ======= -->
              <?php if (strcmp($r_item['data_Flg'], "0") == 0) : ?>
                <td><i class="fas fa-window-close"></i><span class="mi_icon_ng">���o��</span></td>
              <?php else : ?>
                <td><i class="fas fa-check-square"></i><span class="mi_icon_ok">�o�͍ς�</span></td>
              <?php endif; ?>

            </tr>
          <?php endforeach; ?>

        <?php else : ?>

          <p></p>

        <?php endif; ?>

        <!-- Excel�o�� -->
        <?php if (!empty($retunr_excel)) : ?>
          <?php foreach ($retunr_excel as $r_item) : ?>
            <tr>
              <td> <?php print h($r_item['user_name']); ?> </td>
              <td> <?php print h($r_item['furi_name']); ?> </td>

              <td> <?php print h($r_item['email']); ?> </td>

              <td>
                <?php if ($r_item['sex'] == "0") : ?>
                  <p>�j��</p>
                <?php else : ?>
                  <p>����</p>
                <?php endif; ?>
              </td>

              <!-- �o�^�� -->
              <td> <?php print h($r_item['creation_time']); ?></td>

              <!-- ======= �t�@�C���o�́@�X�^�b�c�@���o�́F�O  �o�͍ς݁F�P ======= -->
              <?php if (strcmp($r_item['data_Flg'], "0") === 0) : ?>
                <td><i class="fas fa-window-close"></i><span class="mi_icon_ng">���o��</span></td>
              <?php else : ?>
                <td><i class="fas fa-check-square"></i><span class="mi_icon_ok">�o�͍ς�</span></td>
              <?php endif; ?>

            </tr>
          <?php endforeach; ?>

        <?php else : ?>

          <p></p>

        <?php endif; ?>


        <!-- ���o�̓t�@�C�� �o�� -->
        <?php if (!empty($retunr_output_excel)) : ?>
          <?php foreach ($retunr_output_excel as $r_item) : ?>
            <tr>
              <td> <?php print h($r_item['user_name']); ?> </td>
              <td> <?php print h($r_item['furi_name']); ?> </td>

              <td> <?php print h($r_item['email']); ?> </td>

              <td>
                <?php if ($r_item['sex'] == "0") : ?>
                  <p>�j��</p>
                <?php else : ?>
                  <p>����</p>
                <?php endif; ?>
              </td>

              <!-- �o�^�� -->
              <td> <?php print h($r_item['creation_time']); ?></td>

              <!-- ======= �t�@�C���o�́@�X�^�b�c�@���o�́F�O  �o�͍ς݁F�P ======= -->
              <?php if (strcmp($r_item['data_Flg'], "0") === 0) : ?>
                <td><i class="fas fa-window-close"></i><span class="mi_icon_ng">���o��</span></td>
              <?php else : ?>
                <td><i class="fas fa-check-square"></i><span class="mi_icon_ok">�o�͍ς�</span></td>
              <?php endif; ?>

            </tr>
          <?php endforeach; ?>

        <?php else : ?>

          <p></p>

        <?php endif; ?>



        <!-- �\���@�{�^�������@�G���[ -->
        <?php if (empty($display_err)) : ?>

        <?php else : ?>
          <div class="alert alert-danger" role="alert"><?php print(h($display_err)); ?></div>

        <?php endif; ?>

        <!-- CSV �o�́@�{�^���G���[���� �i���t�{�b�N�X���󂾂���j -->
        <?php if (empty($error)) : ?>

          <!-- ok ���� -->

        <?php else : ?>

          <div class="alert alert-danger" role="alert">
            [CSV �o�̓G���[] ���t�{�b�N�X����͂��Ă��������B<br />
          </div>

        <?php endif; ?>

        <!-- CSV �t�@�C���쐬�����������ꍇ  1, �_���������� 0 -->
        <?php if (strcmp($csv_file_ok_FLG, "1") == 0) : ?>

          <div class="alert alert-success font_1" role="alert">
            <i class="fas fa-check-circle"></i>
            <span class="ok_text">
              CSV�t�@�C���̍쐬�������܂����B
            </span>
          </div>

        <?php elseif (strcmp($csv_file_ok_FLG, "2") == 0) : ?>
          <div class="alert alert-danger font_1" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="ng_text">CSV�t�@�C���̍쐬�����s���܂����B</span>
            <br />
          </div>

        <?php else : ?>


        <?php endif; ?>

        <!-- Excel�t�@�C���@�o�͌��ʏ��� -->
        <?php if (strcmp($excel_file_FLG, "1") == 0) : ?>

          <div class="alert alert-success font_1" role="alert">
            <i class="fas fa-check-circle"></i>
            <span class="ok_text">
              Excel �t�@�C���̍쐬�������܂����B
            </span>
          </div>

        <?php elseif (strcmp($excel_file_FLG, "2") == 0) : ?>

          <div class="alert alert-danger font_1" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="ng_text">CSV�t�@�C���̍쐬�����s���܂����B</span>
            <br />
          </div>

        <?php else : ?>

        <?php endif; ?>

        <!-- �G���[���� Excel�t�@�C���@�o�͂��@��̏ꍇ -->
        <?php if (strcmp($excel_file_FLG, "2") == 0) : ?>

          <div class="alert alert-danger font_1" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="ng_text">�I���������t�ɂ́A�t�@�C��������܂���B</span>
            <br />
          </div>


        <?php else : ?>


        <?php endif; ?>



        <!-- SQL �����@CSV �o�́@�G���[�i���t�{�b�N�X���󂾂���j -->
        <?php if (empty($export_err)) : ?>

        <?php else : ?>
          <p><?php print(h($export_err)); ?><br /></p>
        <?php endif; ?>

        <!-- �o�͌`�����I������Ă��Ȃ��ꍇ�̃G���[���� -->
        <?php if (strcmp($export_output_err, "1") == 0) : ?>

          <div class="alert alert-danger" role="alert">
            �o�͌`����I�����Ă��������B
          </div>

        <?php else : ?>


        <?php endif; ?>

      </table>

    </div>


  </div> <!-- container END -->


  <!-- �t�b�^�[ start -->

  <footer class="bg-light text-center text-lg-start">
    <!-- Copyright -->
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
      copyrightc2014 HATASHO NIIGATADELICA all rights reserved.
    </div>
    <!-- Copyright -->
  </footer>

  <!-- �t�b�^�[ END -->


  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

  <!-- jQuery �f�[�g�s�b�J�[ -->
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <!-- jQuery UI -->
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>


  <!-- jQueru �f�[�g�s�b�J�[�@���암�� -->
  <script>
    //====== ���t�I�𕔕��@�O
    $(function() {
      $('#date_target').datepicker({
        defaultDate: new Date(), // �f�t�H���g�N����  
        changeYear: true, // �N��\��
        changeMonth: true, // ����I��
        maxDate: '+1y +6m', // 1�N����܂őI����
        minDate: '-1y -6m', // 1�N���O�܂őI���� 

      });
    });

    //====== ���t�I�𕔕��@���
    $(function() {
      $('#date_target_02').datepicker({
        defaultDate: new Date(), // �f�t�H���g�N����  
        changeYear: true, // �N��\��
        changeMonth: true, // ����I��
        maxDate: '+1y +6m', // 1�N����܂őI����
        minDate: '-1y -6m', // 1�N���O�܂őI���� 

      });
    });
  </script>




</body>

</html>