<?php
define("TKM", true);

require(realpath("../system.php"));

if (isset($_GET['prepare_once'])) {
	$hash = md5($config['merchant']['mk_shop_id'].":".$_GET['oa'].":".$config['merchant']['mk_shop_key_2'].":".$_GET['l']);
	exit('<hash>'.$hash.'</hash>');
}

function getrIP() {
	if(isset($_SERVER['HTTP_X_REAL_IP'])) {
		return $_SERVER['HTTP_X_REAL_IP'];
	}
	return $_SERVER['REMOTE_ADDR'];
}

if (!in_array(getrIP(), array(
	'144.76.93.115',
	'144.76.93.119',
	'78.47.60.198')
)) {

	die("Hacking attempt!");
}

$sign = md5($config['merchant']['mk_shop_id'] . ':' .
	$_REQUEST['AMOUNT'] . ':' .
	$config['merchant']['mk_shop_key_2'] . ':' .
	$_REQUEST['MERCHANT_ORDER_ID']);

if ($sign != $_REQUEST['SIGN']) {
	die('wrong sign');
}

$payid = $_REQUEST['MERCHANT_ORDER_ID'];
$payment = new Payment;
$status = $payment->select($payid, $_REQUEST['AMOUNT']);

if (!$status) {
	exit($status);
}

$status = $payment->give();

if (!$status) {
	exit($status);
}

exit('YES');