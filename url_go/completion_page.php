<?php

// �G���[���o�͂���
ini_set('display_errors', "On");

// functions.php �ǂݍ���
require(dirname(__FILE__) . '/../functions.php');

// �Z�b�V�����X�^�[�g
session_start();

// �G���[�y�[�W�f�B���N�g��
$kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

// ============ �ϐ��錾
$jim_token_FLG = "0";


//=============== ���_�C���N�g�h�~�p �g�[�N���@==================== 
// ���_�C���N�g�h�~�p�@�g�[�N���擾
$jim_token_2 = isset($_POST['jim_token_2']) ? $_POST['jim_token_2'] : "";
// �Z�b�V�����ϐ�����l���擾
$session_jim_token_2 = isset($_SESSION['jim_token_2']) ? $_SESSION['jim_token_2'] : "";

// �g�[�N���폜
unset($_SESSION['jim_token_2']);

if ($jim_token_2 != "" && $jim_token_2 == $session_jim_token_2) {

    $jim_token_FLG = "1";


    //==========  Gmail�p�@�G���R�[�f�B���O�΍�
    $senderName = "������� ����";
    // ���M���̖��O��7bit�Ƃ��Ĉ�����悤�ɏC��
    $senderName = base64_encode($senderName);
    $senderName = "=?UTF-8?B?{$senderName}?=";
    // "=?[�����G���R�[�f�B���O]?[B(base64)���邢��Q(Quoted-Printable)]?

    // info@niigatadelica.jp =>  �{�ԗp���[��

    // �ȏ�̐ݒ肩�牺�L�̃��[���w�b�_�[���z�肳���B
    $mailHeaders = <<< EOF
From: {$senderName} <<info@niigatadelica.jp>>
Reply-To: info@niigatadelica.jp
Return-Path: info@niigatadelica.jp
X-Mailer: X-Mailer
MIME-Version: 1.0
Content-Type: text/plain;charset=UTF-8
Content-Transfer-Encoding: 8bit
EOF;
    // Content-Type: ���[���{����UTF-8�ŃG���R�[�h�����
    // Content-Transfer-Encoding: �������A���M����base64������

    //=========================== �o�^ ok ������̃��[�����M
    $to = "test@co.jp";
    $subject = "�A���o�C�g�̓o�^������܂����B";
    $message = "�A���o�C�g�o�^�t�H�[�����炲�o�^������܂����B" . "\n";
    $message .= "���m�F�����肢���܂��B" . "\n";

    // ���[�����M
    mail($to, $subject, $message, $mailHeaders);
} else {

    $jim_token_FLG = "2";

    //=== �G���[����
    header("Location: {$kari_uri}");
}



?>


<!doctype html>
<html lang="ja">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <head>
        <!-- jim style.css -->
        <link rel="stylesheet" href="../css/style.css">

        <!-- Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

        <!-- font awsome cdn -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">

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

    <!-- ���̓y�[�W�@�i�J�����g�\���j -->
    <div class="container">
        <div class="row form-flow my-5 text-center col-12">
            <div class="col-2 form-flow-list-wrap py-3">
                <span class="form-flow-list-no">1</span><span style="display:block;" id="cl_one">�����^�C���p�X���[�h����</span>
            </div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap py-3"><span class="form-flow-list-no">2</span>����</div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap py-3"><span class="form-flow-list-no">3</span>�m�F</div>
            <div class="col form-flow-list-allow py-3"></div>

            <div class="col-2 form-flow-list-wrap form-flow-active py-3"><span class="form-flow-list-no">4</span>����</div>
        </div>
    </div>


    <div class="container">

        <?php if (strcmp($jim_token_FLG, "1") == 0) : ?>
            <div class="col-lg-10 col-sm-10 col-10">
                <p style="text-align: center;"> �o�^�������������܂����B</p>

                <h2 class="my-4 sank_h2" style="text-align: center;" id="Thanks_text_h2">
                    ���o�^���肪�Ƃ��������܂����B
                </h2>


            </div>

        <?php elseif (strcmp($jim_token_FLG, "2") == 0) : ?>

            <div class="col-lg-10 col-sm-10 col-10">
                <h2 class="my-4 sank_h2" style="text-align: center;">
                    �o�^�G���[���������܂����B
                </h2>

                <p style="text-align: center;">�o�^�������������܂���ł����B</p>
            </div>

        <?php else : ?>

            <div class="col-lg-10 col-sm-10 col-10">
                <h2 class="my-4 sank_h2" style="text-align: center;">

                </h2>

                <p style="text-align: center;"></p>
            </div>

        <?php endif; ?>


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

    <!-- ============= �X�}�z�p�@�J�����g�y�[�W�\���΍� -->
    <script>
        function isSmartPhone() {

            if (window.matchMedia && window.matchMedia('(max-device-width: 599px)').matches) {

                let cl_one = document.getElementById("cl_one");

                cl_one.innerText = "�p�X";

                console.log(cl_one.innerText);

                return true;
            } else {

                // PC �́@�������Ȃ�

            }

        }

        window.addEventListener('load', (event) => {

            isSmartPhone();

        });
    </script>

</body>

</html>