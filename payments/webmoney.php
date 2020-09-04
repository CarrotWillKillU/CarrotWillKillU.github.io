<?php
define("TKM", true);

require(realpath("../system.php"));

if ($_POST['LMI_PREREQUEST'] == 1) {
	if ($_POST['LMI_PAYEE_PURSE'] == $config['merchant']['wm_purse']) {
		echo 'YES';
	}
} else {
	$key = 
		$_POST['LMI_PAYEE_PURSE'] .
		$_POST['LMI_PAYMENT_AMOUNT'] .
		$_POST['LMI_PAYMENT_NO'] .
		$_POST['LMI_MODE'] .
		$_POST['LMI_SYS_INVS_NO'] .
		$_POST['LMI_SYS_TRANS_NO'] .
		$_POST['LMI_SYS_TRANS_DATE'] .
		$config['merchant']['wm_secret_key'] .
		$_POST['LMI_PAYER_PURSE'] .
		$_POST['LMI_PAYER_WM'];

	if (strtoupper(hash('sha256', $key)) != $_POST['LMI_HASH']) exit;
	
	$payid = $_POST['LMI_SYS_INVS_NO'];
	$payment = new Payment;
	$status = $payment->select($payid, $out_summ);

	if (!$status) {
		exit($status);
	}

	$status = $payment->give();

	if (!$status) {
		exit($status);
	}

	exit("YES");
}