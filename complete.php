<?php

// �G���[���o�͂���
ini_set('display_errors', "On");

// �G���[�y�[�W�f�B���N�g��
$kari_uri = "http://www.niigatadelica.jp/job_recruit/err/err.php";

// functions.php �ǂݍ���
require(dirname(__FILE__) . '/functions.php');

// �Z�b�V�����X�^�[�g
session_start();

//==============�@�ϐ��錾
$e_year = "";
$e_month = "";
$e_day = "";

$pass = "";

$e_t = "";
$e_b = "";

$mail_FLG = 0;

//====================== �g�[�N�����擾����
$token_jim_2 = isset($_POST['token_jim_2']) ? $_POST["token_jim_2"] : "";

$session_token_jim_2 = isset($_SESSION['token_jim_2']) ? $_SESSION["token_jim_2"] : "";

// === �Z�b�V�����ϐ��̍폜
unset($_SESSION['token_jim_2']);


$email = "";
if (isset($_POST['email'])) {
  $email = $_POST['email'];
}

// === ���[���A�h���X�� @ �Ő؂�

$email_arr = [];
$email_arr = explode("@", $email);

// ���[���A�h���X�@@ ��� �O
$e_t = $email_arr[0];

// ���[���A�h���X�@@ ��� ��
$e_b = $email_arr[1];


if (isset($_POST['email_hh'])) {
  $email_hh = $_POST['email_hh'];
}




//============================= �t�H�[��������t���擾 �i�����[���o�^���j
// �N �擾
if (isset($_POST['e_year'])) {

  $e_year = $_POST['e_year'];
}
// �� �擾
if (isset($_POST['e_month'])) {

  $e_month = $_POST['e_month'];
}
// �� �擾
if (isset($_POST['e_day'])) {

  $e_day = $_POST['e_day'];
}

//============================== �p�X���[�h�i�����擾�j

if (isset($_POST['pass'])) {

  $pass = $_POST['pass'];
}


//======= ��Ж� �����@�G���R�[�f�B���O =======
$kaisya = "������� ����";
$tmp_kaisya = mb_convert_encoding($kaisya, 'UTF-8', 'AUTO');
$e_kaisya = mb_encode_mimeheader($tmp_kaisya, "UTF-8", "B");

//======= ��Ж� �����@�G���R�[�f�B���O END =======

//======= �o�^��@URL�@=======
/*
    $send_url = "http://xs810378.xsrv.jp/php_password_01/url_go/index.php?email=" . $email_hh . 
      "&e_b=" . $e_b . "&e_t=" . $e_t;
    */

$send_url = "http://www.�h���C��/job_recruit/url_go/index.php?email=" . $email_hh .
  "&e_b=" . $e_b . "&e_t=" . $e_t;


//======= �o�^��@URL END�@=======

// ************** ���[�����M���� *****************
//  $MAILTO = $email . "?email=" . $email_hh;  //���惁�[���A�h���X

$MAILTO = $email;  //���惁�[���A�h���X

// ���[���^�C�g��
const SUBJECT = "���q�l��p��ʃ��O�C���p�����^�C���p�X���[�h�̂��ē�";

// ���[���{��
$content =  $e_year . "�N" . $e_month . "��" . $e_day . "��" . "\n";
$content .= "�����̃��[���͎����ԐM���[���ł��B" . "\n\n";

$content .= "���q���ܐ�p��ʃ��O�C���p�����^�C���p�X���[�h�̂��ē�" . "\n";
$content .= "���̓x�́A���Ћ��l�ɂ����傢�������܂��Đ��ɂ��肪�Ƃ��������܂��B" . "\n";
$content .= "���q���ܐ�p��ʃ��O�C���p�̃����^�C���p�X���[�h�𑗕t�������܂��B���L�t�q" . "\n" .
  "�k�ɃA�N�Z�X���������A�A�N�Z�X�L�[�ƃ����^�C���p�X���[�h�ɂă��O�C��������" . "\n" .
  "���������܂��B" . "\n\n" .
  "�ŏ��̑��삩��30���ȓ��ɓ��͂��������Ȃ��ꍇ�A�����^�C���p�X���[�h�͖����ɂȂ�܂��B" . "\n\n";;

//�@������ �����^�C���p�X���[�h�̗L���������L�ڂ���Ȃ炱�����牺�@������

$content .= "�������^�C���p�X���[�h" . "\n\n";

// �����^�C���p�@�p�X���[�h
$content .= "    �E�����^�C���p�X���[�h�F" . $pass . "\n";

// �o�^�p�@URL
$content .= "    �E���O�C���p�t�q�k�F" .  $send_url . "\n";


//==========  Gmail�p�@�G���R�[�f�B���O�΍�
$senderName = "������� �������ƕ�";
// ���M���̖��O��7bit�Ƃ��Ĉ�����悤�ɏC��
$senderName = base64_encode($senderName);
$senderName = "=?UTF-8?B?{$senderName}?=";
// "=?[�����G���R�[�f�B���O]?[B(base64)���邢��Q(Quoted-Printable)]?

// info@niigatadelica.jp =>  �{�ԗp���[��

// �ȏ�̐ݒ肩�牺�L�̃��[���w�b�_�[���z�肳���B
$mailHeaders = <<< EOF
From: {$senderName} <<info@�h���C��.jp>>
Reply-To: info@�h���C��.jp
Return-Path: info@�h���C��.jp
X-Mailer: X-Mailer
MIME-Version: 1.0
Content-Type: text/plain;charset=UTF-8
Content-Transfer-Encoding: 8bit
EOF;
// Content-Type: ���[���{����UTF-8�ŃG���R�[�h�����
// Content-Transfer-Encoding: �������A���M����base64������


// ���M�e�X�g�@OK
//    $headers = "{$e_kaisya} From:<tossy0130@xs810378.xsrv.jp>";


if ($token_jim_2 != "" && $token_jim_2 == $session_token_jim_2) {


  // ������������悤�Ȃ牺�L�̃R�����g�A�E�g�������Ď���
  // mb_language("ja");

  mb_internal_encoding("UTF-8");
  $is_success = mb_send_mail($MAILTO, SUBJECT, $content, $mailHeaders);

  if (!$is_success) {

    // ==== ���[�����s���̏���
    $mail_FLG = 0;
  } else {

    // === ���[���������̏���

    $mail_FLG = 1;
    // ===================== $_SESSION �폜 =====================

    // �Z�b�V�����폜
    unset($_SESSION['email']);
  }
} else {

  // �G���[�y�[�W��΂�
  header("Location: {$kari_uri}");
}


// === �������g�ց@��΂�
/*
    header('Location: ./');
    exit;
    */


?>

<!doctype html>
<html lang="ja">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


  <head>

    <!-- jim style.css -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!-- �C���ӏ�1 title�Z�N�V�������Ăяo���B�f�t�H���g�l�� no title�Ƃ��� -->
    <title>�A���o�C�g��W�T�C�g ������ʁ@��ʂR</title>

    <style>

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

  <div class="container">

    <?php if ($mail_FLG == 1) : ?>
      <h2 class="my-4 sank_h2">
        ���o�^���肪�Ƃ��������܂����B<br />
        ���L�̃��[���A�h���X�ցA���[�����M�������������܂����B<br /><br />
      </h2>

    <?php else : ?>

      <h2 class="my-4 sank_h2">
        ���[���̑��M�����Ɏ��s���܂����B<br />
        Wi-Fi�����m�F�̏������x���葱�����������B<br /><br />
      </h2>

    <?php endif; ?>

    <p class="p_01"> ���M��A�h���X�F<span class="mozi_01"><?php echo h($email); ?></span></p>

  </div> <!-- END container -->

  <!-- �t�b�^�[ start -->
  <footer class="bg-light text-center text-lg-start">
    <!-- Copyright -->
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
      copyrightc2014 HATASHO NIIGATADELICA all rights reserved.
    </div>
    <!-- Copyright -->
  </footer>

  <!-- �t�b�^�[ END -->

</body>

</html>