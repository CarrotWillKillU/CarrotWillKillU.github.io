<?php
define("TKM", true);
require(realpath("../system.php"));

$hashSecretWord = $config['merchant']['2check_secret_word'];
$hashSid = $config['merchant']['2check_id'];
$hashTotal = $_REQUEST['total'];
$hashOrder = $_REQUEST['order_number']; // Вписать 1, если используется демка
$StringToHash = strtoupper(md5($hashSecretWord . $hashSid . $hashOrder . $hashTotal));

if ($StringToHash !== $_REQUEST['key']) {
    exit('Fail - Hash Mismatch');
}

$id = $_REQUEST['merchant_order_id'];

$payment = new Payment;
$status = $payment->select($id, $hashTotal);

if (!$status) {
    exit($status);
}

$status = $payment->give();

if (!$status) {
    exit($status);
}

header('Location: /');
exit();