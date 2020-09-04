<?php
define("TKM", true);
require(realpath("../system.php"));

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$shop_id = $config['merchant']['ik_shop_id'];
	$secret_key = $config['merchant']['ik_secret_key'];
	$test_secret_key = $config['merchant']['ik_testing_key'];
	$testing = $config['merchant']['ik_testing'];

	function ikSign($params, $ikKey) {
		unset($params['ik_sign']);
		foreach ($params as $key => $value)
			if (!preg_match("/^ik_/is", $key))
				unset($params[$key]);

		ksort($params, SORT_STRING);
		array_push($params, $ikKey);
		$sign = implode(":", $params);
		$sign = base64_encode(md5($sign, true));
		return $sign;
	}

	$kassaId = trim($_REQUEST['ik_co_id']);
	$PayID = trim(strip_tags($_REQUEST['ik_pm_no']));
	$summ = intval($_REQUEST['ik_am']);
	$paySystem = trim($_REQUEST['ik_pw_via']);
	$payStatus = trim($_REQUEST['ik_inv_st']);
	$sign = trim($_REQUEST['ik_sign']);
	$ik_payment_timestamp = trim($_REQUEST['ik_inv_prc']);
	$secretKey = $secret_key;
	$PayID = explode("_", $PayID);
	$PayID = $PayID['1'];

	if ($testing and ($paySystem == "test_interkassa_test_xts")) {
		$secretKey = $test_secret_key;
	} elseif ($paySystem == "test_interkassa_test_xts") {
		exit("OK");
	}

	if ($kassaId != $shop_id) {
		exit("Not valid shop ID!");
	}

	if ($sign != ikSign($_REQUEST, $secretKey)) {
		exit("Bad sign");
	}

	$payment = new Payment;
	$status = $payment->select($PayID, $summ);

	if (!$status) {
		exit($status);
	}

	$status = $payment->give();

	if (!$status) {
		exit($status);
	}
}

exit("OK");