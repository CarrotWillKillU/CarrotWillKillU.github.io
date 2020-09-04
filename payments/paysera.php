<?php

require_once('WebToPay.php');

try {
    $response = WebToPay::checkResponse($_GET, array(
        'projectid'     => $config['merchant']['paysera_projectid'],
        'sign_password' => $config['merchant']['paysera_sign_password']
    ));

    if ($response['test'] !== '0') {
        throw new Exception('Testing, real payment was not made');
    }
    if ($response['type'] !== 'macro') {
        throw new Exception('Only macro payment callbacks are accepted');
    }

    $orderId = $response['orderid'];
    $amount = $response['amount'];

    $payment = new Payment;
    $status = $payment->select($orderId, $amount);

    if (!$status) {
        exit($status);
    }

    $status = $payment->give();

    if (!$status) {
        exit($status);
    }

    echo 'OK';
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage();
}