<?php


// �G���[���o�͂���
ini_set('display_errors', "On");

// functions.php �ǂݍ���
require(dirname(__FILE__) . '/../functions.php');

// �G���[�y�[�W�f�B���N�g��
$kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

// �Z�b�V�����X�^�[�g
session_start();

// ************ ��d���M�h�~�p�g�[�N���̔��s ************
$jim_token_2 = uniqid('', true);
// �g�[�N�����Z�b�V�����ϐ��ɃZ�b�g
$_SESSION['jim_token_2'] = $jim_token_2;


//=============== ���_�C���N�g�h�~�p �g�[�N���@==================== 
// ���_�C���N�g�h�~�p�@�g�[�N���擾
$jim_token = isset($_POST['jim_token']) ? $_POST['jim_token'] : "";
// �Z�b�V�����ϐ�����l���擾
$session_jim_token = isset($_SESSION['jim_token']) ? $_SESSION['jim_token'] : "";

// �Z�b�V�����폜
// unset($_SESSION['jim_token']);



//================================== �ϐ�������
$get_form_eh = "";

// �t�H�[���֌W�ϐ�
$user_name = "";   // ����
$furi_name = "";   // �����@����


$email = ""; // ���[���A�h���X

$spouse = 0; // �z��җL��  0: �Ȃ�  1: ����

$sex = 0; // ���� 0: �j��  1: ����

$dependents_num = "";   //  �}�{�T���ΏێҐl��

$birthday = "";   // ���N����

$brth_year = "";  // POST �p�@�@�N
$brth_mnth = "";  // POST �p�@�@��
$brth_dy = "";  // POST �p�@�@�@��

$tel = ""; //   �d�b�ԍ�


$zip = "";   // �X�֔ԍ�
$address_01 = "";   // �Z��

/*
 $furikomi_saki = "";
 $branch_name = "";
 $account_type = 0;
 $account_number = "";
 $account_holder = "";
 */


$My_number_information = "";
$Inquiry_form = "";

/*====================== ��s�n�@��� ======================== */

$employee_id = 0;  // �Ј��R�[�h

$bank_code = 0; // ��s�R�[�h
$bank_name = ""; // ��s��
$bank_siten_code = 0; // �x�X�R�[�h
$bank_siten_name = ""; // �x�X��
$bank_kamoku = "";  //  0 => ���ʁ@=> ����
$bank_holder = ""; // �������`�l

$kouzzz_number = ""; // �����ԍ�


// ============= form �� POST ����
if (isset($_POST['get_form_eh'])) {

    // get_form_eh ���@�󂾂����ꍇ�́@�G���[�Ƃ��ā@���_�C���N�g����B
    if (empty($_POST['get_form_eh'])) {

        //=== GET �p�����[�^�[���Ȃ��ꍇ isset ����Ă��Ȃ��@�G���[����
        header("Location: {$kari_uri}");
    } else {

        // OK ����
        $get_form_eh = $_POST['get_form_eh'];
    }
} else {

    //=== �G���[����
    header("Location: {$kari_uri}");
}



//========================================
//======================�@�t�H�[�� POST����
//========================================
if (!empty($get_form_eh)) {

    //========== POST �擾����
    $user_name = $_POST['user_name'];
    $furi_name = $_POST['furi_name'];

    $email = $_POST['email'];

    $sex = $_POST['sex'];

    $spouse = $_POST['spouse'];  // �z��җL��  0: �Ȃ�  1: ����
    $dependents_num = $_POST['dependents_num'];

    $brth_year = $_POST['brth_year'];  // POST �p�@�@�N
    $brth_mnth = $_POST['brth_mnth'];  // POST �p�@�@��
    $brth_dy = $_POST['brth_dy'];  // POST �p�@�@�@��

    //========== ���N�����@ 2021-00-00
    $birthday = $brth_year . "-" . $brth_mnth . "-" . $brth_dy;


    $tel = $_POST['tel']; //   �d�b�ԍ�

    $zip = $_POST['zip'];   // �X�֔ԍ�
    $address_01 = $_POST['address_01'];   // �Z��

    /*
    $furikomi_saki = $_POST['furikomi_saki'];
    $branch_name = $_POST['branch_name'];
    $account_type = $_POST['account_type'];
    $account_number = $_POST['account_number'];
    $account_holder = $_POST['account_holder'];
    */

    $My_number_information = $_POST['My_number_information'];
    $Inquiry_form = $_POST['Inquiry_form'];

    /* =============================== ��s�n�f�[�^�擾 ============================ */


    $employee_id = $_POST['employee_id'];  // �Ј��R�[�h

    $bank_code = "0"; // ��s�R�[�h
    $bank_siten_code = "0"; // �x�X�R�[�h

    $bank_name = $_POST['bank_name']; // ��s��
    $bank_siten_name = $_POST['bank_siten_name']; // �x�X��
    $bank_kamoku = $_POST['bank_kamoku'];  //  0 => ���ʁ@=> ����
    $bank_holder = $_POST['bank_holder']; // �������`�l

    $kouzzz_number = $_POST['kouzzz_number']; // �����ԍ�



} else {

    //=== �G���[����
    header("Location: {$kari_uri}");
}


// ====================== POST �g�[�N���Ɓ@�Z�b�V�����̃g�[�N������v�����ꍇ
if ($jim_token != "" && $jim_token == $session_jim_token) {

    //if(isset($_POST['send'])) {

    // ==========================================
    // ======= �{�^���� submit ������ �C���T�[�g����
    // ==========================================

    //================================  
    //================== �ڑ����
    //================================  
    $dsn = 'mysql:dbname=d05618yadb1;host=localhost;port=3306';
    $user = 'd05618ya';
    $password = 'deli7332+C';


    try {

        // PDO �I�u�W�F�N�g�쐬
        $pdo = new PDO($dsn, $user, $password);

        // ======  �i�g�����U�N�V�����j �g�����U�N�V�����J�n ======
        $pdo->beginTransaction();

        // ====== SQL ��
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

        // ====== �o�C���h
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

        //==========  ��s�n�@�ǉ��@�J����  :in_13  �`  :in_19 ============
        $stmt->bindParam(':in_14', $employee_id, PDO::PARAM_STR);
        $stmt->bindParam(':in_15', $bank_code, PDO::PARAM_STR);
        $stmt->bindParam(':in_16', $bank_name, PDO::PARAM_STR);
        $stmt->bindParam(':in_17', $bank_siten_code, PDO::PARAM_STR);
        $stmt->bindParam(':in_18', $bank_siten_name, PDO::PARAM_STR);
        $stmt->bindParam(':in_19', $bank_kamoku, PDO::PARAM_STR);

        $stmt->bindParam(':in_20', $kouzzz_number, PDO::PARAM_STR);

        $stmt->bindParam(':in_21', $bank_holder, PDO::PARAM_STR);


        // SQL ���s
        $res = $stmt->execute();

        // �g�����U�N�V�����@�R�~�b�g
        if ($res) {
            $pdo->commit();
        }

        // ****** SQL �̌��ʂ����o�� ****** 

        /*
    if($res) {
            $data = $stmt->fetch();
           var_dump("insert �f�[�^:::" . $data);
    }
    */

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {

        print('Error:' . $e->getMessage());

        // (�g�����U�N�V����) ���[���o�b�N
        $pdo->rollBack();

        // �������@�G���[�����@������ 
        header("Location: {$kari_uri}");
    } finally {

        $pdo = null;
    }

    //} //============= send �{�^������ END



} else {
    //================================== �g�[�N������v���Ȃ��ꍇ

    // �������@�G���[�����@������ 
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

    <title>�t�H�[���C���T�[�g���</title>
</head>

<body>


    <div class="jumbotron">
        <div class="container">
            <h1 class="display-5">������Ё@����</h1>
            <p class="lead">
                <!-- �A���o�C�g�ҏ��o�^ -->
                <?php site_name_hozyo(); ?><br />
                ���͏��̂��m�F
            </p>
        </div>
    </div>

    <!-- ���̓y�[�W�@�i�J�����g�\���j -->
    <div class="container">
        <div class="row form-flow my-5 text-center col-12">
            <div class="col-2 form-flow-list-wrap py-3">
                <span class="form-flow-list-no">1</span><span style="display:block;" id="cl_one">�����^�C���p�X���[�h����</span>
            </div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap py-3" style="line-height: 2.5;"><span class="form-flow-list-no">2</span>����</div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap form-flow-active py-3" style="line-height: 2.5;"><span class="form-flow-list-no">3</span>�m�F</div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap py-3" style="line-height: 2.5;"><span class="form-flow-list-no">4</span>����</div>
        </div>
    </div>


    <!-- �f�[�^�@�m�F��� -->
    <div class="container">
        <table class="table table-hover col-10 ml-8">
            <thead>
                <tr>
                    <th>����</th>
                    <th><?php print h($user_name); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>��������</td>
                    <td><?php print h($furi_name); ?></td>
                </tr>

                <tr>
                    <td>����</td>
                    <td>

                        <?php if ($sex == 0) : ?>
                            <span>�j��</span>
                        <?php else : ?>
                            <span>����</span>
                        <?php endif; ?>

                    </td>
                </tr>

                <tr>
                    <td>���N����</td>
                    <td><?php print h($birthday); ?></td>
                </tr>

                <tr>
                    <td>�Z��</td>
                    <td><?php print h($address_01); ?></td>
                </tr>

                <tr>
                    <td>�d�b�ԍ�</td>
                    <td><?php print h($tel); ?></td>
                </tr>

                <tr>
                    <td>���[���A�h���X</td>
                    <td><?php print h($email); ?></td>
                </tr>

                <tr>
                    <td>�z��җL��</td>
                    <td>
                        <?php if ($spouse == 0) : ?>
                            <span>����</span>
                        <?php else : ?>
                            <span>�L��</span>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <td>�}�{�T�� �ΏێҐl��</td>
                    <td><?php print h($dependents_num); ?></td>
                </tr>


                <tr>
                    <td>�}�C�i���o�[</td>
                    <td><?php print h($My_number_information); ?></td>
                </tr>

                <!--�@=============== ��s��� ============== -->
                <tr>
                    <td>�]�ƈ��R�[�h</td>
                    <td><?php print h($employee_id); ?></td>
                </tr>

                <!-- ��s�R�[�h
                <tr>
                    <td>��s�R�[�h</td>
                    <td></td>
                </tr>
                 -->

                <tr>
                    <td>��s��</td>
                    <td><?php print h($bank_name); ?></td>
                </tr>

                <!-- �x�X�R�[�h
                <tr>
                    <td>�x�X�R�[�h</td>
                    <td></td>
                </tr>
                        -->

                <tr>
                    <td>�x�X��</td>
                    <td><?php print h($bank_siten_name); ?></td>
                </tr>

                <!-- �����Ȗ� bank_kamoku -->
                <tr>
                    <td>�����ԍ�</td>
                    <td>
                        <?php if (strcmp($bank_kamoku, "0") == 0) : ?>
                            <?php print h("���ʌ���"); ?>
                        <?php else : ?>
                            <?php print h("�����a��"); ?>
                        <?php endif; ?>
                    </td>
                </tr>


                <tr>
                    <td>�����ԍ�</td>
                    <td><?php print h($kouzzz_number); ?></td>
                </tr>
                �R�[�h�R�[�h
                <tr>
                    <td>�������`</td>
                    <?php if ($bank_kamoku == 0) : ?>
                        <td><?php print h("����"); ?></td>
                    <?php elseif ($bank_kamoku == 1) : ?>
                        <td><?php print h("����"); ?></td>
                    <?php else : ?>
                        <td><?php print h("���I��"); ?></td>
                    <?php endif; ?>
                </tr>



                <tr>

                    <td>���₢���킹���e</td>
                    <td><?php print h($Inquiry_form); ?></td>
                </tr>

            </tbody>
        </table>
    </div>


    <!-- ���� -->
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

            <!-- ================== ��s���@================== -->
            <input type="hidden" name="employee_id" value="<?php print h($employee_id); ?>">
            <input type="hidden" name="bank_code" value="<?php print h($bank_code); ?>">
            <input type="hidden" name="bank_name" value="<?php print h($bank_name); ?>">
            <input type="hidden" name="bank_siten_code" value="<?php print h($bank_siten_code); ?>">
            <input type="hidden" name="bank_siten_name" value="<?php print h($bank_siten_name); ?>">
            <input type="hidden" name="bank_kamoku" value="<?php print h($bank_kamoku); ?>">

            <input type="hidden" name="bank_kamoku" value="<?php print h($kouzzz_number); ?>">

            <input type="hidden" name="bank_holder" value="<?php print h($bank_holder); ?>">


            <!--�@�{�^�� -->
            <div class="form-row">
                <div class="col">

                    <!-- Button trigger modal -->
                    <button type="button" id="url_go_back_btn" class="btn csv_btn_3" data-toggle="modal" data-target="#exampleModal3" style="color:#000;">
                        �߂�
                    </button>

                    <!-- Modal �߂�{�^�� -->
                    <div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModal3Label" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModal3Label">�m�F���</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    �O�̉�ʂ֖߂�܂����H
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-info" data-dismiss="modal" onClick="history.back()">�͂�</button>
                                    <button type="button" class="btn btn-info" data-dismiss="modal" aria-label="Close">������</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="col">
                    <button type="submit" id="url_go_send_btn" name="send" class="form-control btn csv_btn_3" style="color:#000;background: lightblue;">�o�^�m��</button>
                </div>

            </div>


            <!-- ���_�C���N�g�h�~�p�@�g�[�N�� -->
            <input type="hidden" name="jim_token_2" value="<?php print h($jim_token_2); ?>">


        </form>


    </div>


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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    <!-- ============= �X�}�z�p�@�J�����g�y�[�W�\���΍� -->
    <script>
        function isSmartPhone() {

            console.log("�e�X�g function");

            if (window.matchMedia && window.matchMedia('(max-device-width: 599px)').matches) {

                console.log("�e�X�g function �X�}�z");

                var cl_one = document.getElementById("cl_one");

                console.log(cl_one.innerText);

                cl_one.innerText = "�p�X";

                return true;
            } else {

                console.log("�e�X�g function PC");

                // PC �́@�������Ȃ�

            }

        }

        window.addEventListener('load', (event) => {

            console.log("�e�X�g�C�x���g");

            isSmartPhone();

        });
    </script>



</body>

</html>