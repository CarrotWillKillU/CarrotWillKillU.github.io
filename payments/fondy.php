<?php
define("TKM", true);
require(realpath("../system.php"));

function getSignature( $merchant_id , $password , $params = array() ){
    $params['merchant_id'] = $merchant_id;
    $params = array_filter($params,'strlen');
    ksort($params);
    $params = array_values($params);
    array_unshift( $params , $password );
    $params = join('|',$params);
    return(sha1($params));
}

$params = $_POST;
unset($params['response_signature_string']);
unset($params['signature']);

$signature = getSignature($config['merchant']['fondy_id'], $config['merchant']['fondy_signature'], $params);

if ($signature !== $_POST['signature']) {
    exit('BAD SIGN');
}

if ($_POST['order_status'] !== 'approved') {
    exit('NOT APPROVED');
}

$payment = new Payment;
$status = $payment->select($_POST['order_id'], $_POST['amount']);

if (!$status) {
    exit($status);
}

$status = $payment->give();

if (!$status) {
    exit($status);
}

die('YES');