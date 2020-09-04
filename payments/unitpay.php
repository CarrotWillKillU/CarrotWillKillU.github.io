<?php
define("TKM", true);

require(realpath("../system.php"));

function upSign($method, $params, $secretKey) {
	ksort($params);
	unset($params['sign']);
	unset($params['signature']);
	array_push($params, $secretKey);
	array_unshift($params, $method);

	return hash('sha256', join('{up}', $params));
}

$method = $_GET['method'];
$params = $_GET['params'];

if ($params['signature'] != upSign($method, $params, $config['merchant']['up_secret_key'])) {
	exit('{"error": {"message": "Wrong signature!"}}');
}

if ($method != 'pay') {
	exit('{"result": {"message":"Request was successfully received [actionCheck]"}}');
}

$payment = new Payment;
$status = $payment->select($_GET['params']['account'], $params['orderSum']);

if ($status !== true) {
	exit('{"error": {"message": "{' . $status . '}"}}');
}

$status = $payment->give();

if ($status !== true) {
	exit('{"error": {"message": "{' . $status . '}"}}');
}

exit('{"result": {"message":"Request was successfully received [actionPay]"}}');