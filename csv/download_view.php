<?php

// �G���[���o�͂���
ini_set('display_errors', "On");

// functions.php �ǂݍ���
require(dirname(__FILE__) . '/../functions.php');

// header('Location: ./'); 

// �J�����g�f�B���N�g���@���@�擾
$dir = __DIR__;


// ====================== �ϐ��錾 ====================

//$test_uri = "http://xs810378.xsrv.jp/php_password_01/csv/";
$test_uri = "url";


$dowonload_uri = "http://�h���C��/job_recruit/csv/download_go.php";

$get_dir_code = "";

$get_files = [];

//============ GET �p�����[�^�[�@�擾

if (isset($_GET['view_code'])) {

    if (!empty($_GET['view_code'])) {

        $get_dir_code = $_GET['view_code'];

        //*********  Excel�t�@�C���@�A�@CSV �t�@�C���擾 */
        $get_excel_files = glob($get_dir_code . "/*.xlsx");
        $get_csv_files = glob($get_dir_code . "/*.csv");
    }
} else {
}

/*
// �f�B���N�g���ꗗ�擾
$res = glob('file*', GLOB_ONLYDIR);
var_dump("�o�̓e�X�g�F�F�Fres" . $res);
*/


?>

<!doctype html>
<html lang="ja">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <head>

        <!-- Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <!--Font Awesome5-->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <!-- font awsome cdn -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">

        <!-- jim style.css -->
        <link rel="stylesheet" href="../css/style.css">

        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

        <!-- bootstrap �}�e���A���@�ǂݍ��� -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.dark.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.dark.rtl.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.rtl.min.css">



        <!-- �C���ӏ�1 title�Z�N�V�������Ăяo���B�f�t�H���g�l�� no title�Ƃ��� -->
        <title>�o�^�p�t�H�[�� �O�`�F�b�N</title>

        <style>
            .view_csv_item {
                list-style: none;
            }
        </style>

    </head>

<body>


    <div class="jumbotron">
        <div class="container">
            <h1 class="display-5 m_h1">������� �V���f���J</h1>
            <p class="lead">
                <!-- �A���o�C�g�ҏ��o�^ -->
                <?php site_name_hozyo(); ?>
            </p>
        </div>
    </div>


    <!-- �_�E�����[�h�y�[�W�@�ւ́@�J�� -->
    <div class="container" style="margin-bottom:30px;">

        <div class="col-lg4 col-xs-10 col-md-4 dl_link">

            <a href="./csv_export.php" style="color:#fff !important;">
                �O�֖߂�
            </a>

        </div>
    </div>


    <div class="container" style="margin-bottom:30px;">
        <div>
            <a href="./csv_export.php" class="view_back_btn">
                Excel,CSV�o�͉�ʂ֖߂�
            </a>
        </div>
    </div>


    <!-- ============== CSV  and  Excel�@�f�[�^�@�擾�@=================== -->
    <!-- CSV �t�@�C���ꗗ�@�擾 -->
    <?php $csv_files = []; ?>
    <?php $i = 0; ?>

    <?php foreach ($get_csv_files as $c_file) : ?>

        <?php $csv_files[$i] = $c_file; ?>
        <?php $i++; ?>

    <?php endforeach; ?>

    <!-- �t�@�C���ꗗ�@�擾 END -->

    <!-- CSV �t�@�C���ꗗ�@�擾 -->
    <?php $excel_files = []; ?>
    <?php $j = 0; ?>

    <?php if (!empty($get_excel_files)) : ?>


        <?php foreach ($get_excel_files as $e_file) : ?>

            <?php $excel_files[$j] = $e_file; ?>
            <?php $j++; ?>

        <?php endforeach; ?>

    <?php endif; ?>

    <!-- �t�@�C���ꗗ�@�擾 END -->

    <!-- ============== CSV  and  Excel�@�f�[�^�@�擾 END�@=================== -->

    <!-- CSV�f�[�^�@�Ɓ@Excel�@���@�}�[�W -->
    <?php $get_files = array_merge($csv_files, $excel_files) ?>
    <!-- CSV �f�[�^�@�Ɓ@Excel�f�[�^�@���������ďo��  -->

    <!-- ���������΍� -->
    <?php mb_convert_variables('UTF-8', 'auto', $get_files);  ?>

    <!-- �~���Ƀ\�[�g -->
    <?php rsort($get_files); ?>

    <?php $idx = 0; ?>

    <div class="container" id="app">

        <div class="container">

            <form method="POST" enctype="multipart/form-data" action="">

                <!--
    <ul class="view_csv_item">
-->

                <!-- ============== ���[�v �J�n ==============  -->

                <?php foreach ($get_files as $g_file) : ?>



                    <?php if ($idx == 0) : ?>

                        <ul class="t">

                        <?php elseif ($idx != 0 && $idx % 3 == 0) : ?>

                        </ul>

                        <ul class="tt">

                        <?php endif; ?>

                        <?php $file_start = strpos($g_file, '/'); ?>

                        <?php $file_result = substr($g_file, $file_start + 1); ?>


                        <!-- =========================== Excel �t�@�C���̏o�� ================================== -->
                        <!-- .xlsx ���@�t�@�C�����Ɋ܂܂�Ă����� -->
                        <?php if (strpos($file_result, ".xlsx")) : ?>

                            <!-- ���t�@�I�����@�iExcel �t�@�C�����ϊ��j -->
                            <?php $file_kanri_excel = ""; ?>
                            <?php $file_bank_excel = ""; ?>
                            <?php $file_kanri_excel_all = ""; ?>
                            <?php $file_bank_excel_all = ""; ?>

                            <?php if (strpos($file_result, "R_Baito_Kanri") !== false) : ?>
                                <?php $file_kanri_excel = str_replace("R_Baito_Kanri_", "�A���o�C�g�Ǘ�_", $file_result); ?>

                            <?php elseif (strpos($file_result, "Ginkou_Baito_Kanri") !== false) : ?>
                                <?php $file_bank_excel = str_replace("Ginkou_Baito_Kanri_", "��s�Ǘ�_", $file_result); ?>

                            <?php elseif (strpos($file_result, "M_baito_") !== false) : ?>
                                <?php $file_bank_excel = str_replace("M_baito_", "�i������ �S���j�A���o�C�g�Ǘ�_", $file_result); ?>

                            <?php elseif (strpos($file_result, "M_ginkou_") !== false) : ?>
                                <?php $file_bank_excel = str_replace("M_ginkou_", "�i������ �S���j��s�Ǘ�_", $file_result); ?>

                            <?php endif; ?>


                            <li class="item_xlsx">

                                <div class="item_xlsx_box">

                                    <!--
        <a href=" php file �p�X " class="li_csv_item" download>
            <i class="fas fa-file-excel"></i>
        </a>
        -->

                                    <i class="fas fa-file-excel"></i>
                                    <span class="li_csv_item">

                                        <!-- ==========  Excel ���t�I���t�@�C�� �o�� ============ -->
                                        <?php if ($file_kanri_excel != "") : ?>
                                            <?php print h($file_kanri_excel); ?>
                                        <?php endif; ?>

                                        <?php if ($file_bank_excel != "") : ?>
                                            <?php print h($file_bank_excel); ?>
                                        <?php endif; ?>

                                        <!-- ==========  Excel �i�����́j�t�@�C���o�� �o�� END ============ -->
                                        <?php if ($file_kanri_excel_all != "") : ?>
                                            <?php print h($file_kanri_excel_all); ?>
                                        <?php endif; ?>

                                        <?php if ($file_bank_excel_all != "") : ?>
                                            <?php print h($file_bank_excel_all); ?>
                                        <?php endif; ?>


                                    </span>

                                </div>

                                <!-- ============ �_�E�����[�h�{�^�� ============ -->
                                <a class="btn-gradient-3d-dl d_btn" href="<?php print h($dowonload_uri . "?d=" . $g_file); ?>">
                                    �_�E�����[�h
                                </a>



                                <!-- ============== �폜���� Start ============== -->
                                <button class="btn-gradient-3d-orange" type="submit" name="deletex<?php print $idx; ?>" id="delete" value="<?php print h($test_uri) . h($g_file); ?>">
                                    �t�@�C�����폜
                                </button>

                                <?php if (isset($_POST['deletex' . $idx])) : ?>

                                    <?php $name = $_POST['deletex' . $idx]; ?>

                                    <?php $new_name = str_replace("http://www.niigatadelica.jp/job_recruit/csv", "", $name); ?>

                                    <?php unlink("." . $new_name); ?>

                                    <?php echo ("<meta http-equiv='refresh' content='1'>"); ?>

                                    <?php break; ?>


                                <?php endif; ?>
                                <!-- ============== �폜�����@End ============== -->

                            </li>

                            <!-- =========================== CSV �t�@�C���̏o�� ================================== -->
                        <?php elseif (strpos($file_result, ".csv")) : ?>

                            <!-- ���t�@�I�����@�iCSV �t�@�C�����ϊ��j -->
                            <?php $file_kanri_csv = ""; ?>
                            <?php $file_bank_csv = ""; ?>

                            <!-- �����̓t�@�C���@�iCSV �t�@�C���j -->
                            <?php $file_kanri_csv_all = ""; ?>
                            <?php $file_bank_csv_all = ""; ?>

                            <?php if (strpos($file_result, "Mcsv") !== false && strpos($file_result, "GinkouJ_") !== false) : ?>
                                <?php $file_bank_csv_all = str_replace("Mcsv_GinkouJ_", "�i������ �S���j��s�Ǘ�_", $file_result); ?>

                            <?php elseif (strpos($file_result, "Mcsv") !== false && strpos($file_result, "baito_kanri_") !== false) : ?>
                                <?php $file_kanri_csv_all = str_replace("Mcsv_baito_kanri_", "�i������ �S���j�A���o�C�g�Ǘ�_", $file_result); ?>

                            <?php elseif (strpos($file_result, "Baito_Kanri_") !== false && strpos($file_result, "Mcsv") == false) : ?>
                                <?php $file_kanri_csv = str_replace("Baito_Kanri_", "�A���o�C�g�Ǘ�_", $file_result); ?>

                            <?php elseif (strpos($file_result, "GinkouJ_") !== false && strpos($file_result, "Mcsv") == false) : ?>
                                <?php $file_bank_csv = str_replace("GinkouJ_", "��s�Ǘ�_", $file_result); ?>

                            <?php endif; ?>


                            <li class="item_csv">

                                <div class="item_csv_box">

                                    <!--
        <a href=" php file �p�X " class="li_csv_item" download>
        </a>                        
        -->

                                    <i class="fas fa-file-csv"></i>
                                    <span class="li_csv_item">

                                        <!-- ==========  CSV ���t�I���t�@�C�� �o�� ============ -->
                                        <?php if ($file_bank_csv != "") : ?>
                                            <?php print h($file_bank_csv); ?>
                                        <?php endif; ?>

                                        <?php if ($file_kanri_csv != "") : ?>
                                            <?php print h($file_kanri_csv); ?>
                                        <?php endif; ?>

                                        <!-- ==========  CSV �����̓t�@�C�����t�@�C�� �o�� END ============ -->
                                        <?php if ($file_bank_csv_all != "") : ?>
                                            <?php print h($file_bank_csv_all); ?>
                                        <?php endif; ?>

                                        <?php if ($file_kanri_csv_all != "") : ?>
                                            <?php print h($file_kanri_csv_all); ?>
                                        <?php endif; ?>

                                    </span>

                                </div>

                                <!-- ============ �_�E�����[�h�{�^�� ============ -->
                                <a class="btn-gradient-3d-dl d_btn" href="<?php print h($dowonload_uri . "?d=" . $g_file); ?>">
                                    �_�E�����[�h
                                </a>

                                <!-- ============== �폜���� Start ============== -->
                                <button class="btn-gradient-3d-orange" type="submit" name="deletec<?php print $idx; ?>" id="delete" value="<?php print h($test_uri) . h($g_file); ?>">
                                    �t�@�C�����폜
                                </button>

                                <?php if (isset($_POST['deletec' . $idx])) : ?>

                                    <?php $name = $_POST['deletec' . $idx]; ?>


                                    <?php $new_name_csv = str_replace("http://www.niigatadelica.jp/job_recruit/csv", "", $name); ?>


                                    <?php unlink("." . $new_name_csv); ?>


                                    <?php echo ("<meta http-equiv='refresh' content='1'>"); ?>

                                    <?php break; ?>


                                <?php endif; ?>
                                <!-- ============== �폜�����@End ============== -->

                            </li>

                        <?php endif; ?>

                        <?php $idx += 1; ?>

                    <?php endforeach; ?>
                    <!-- ============== ���[�v �I�� ==============  -->

                    <!--
</ul>
-->

            </form>
        </div>


    </div> <!-- END -->

    <!-- bootstrap �}�e���A���@�ǂݍ��� -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.min.js"></script>



</body>

</html>