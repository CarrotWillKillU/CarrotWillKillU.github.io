<?php
define("TKM", true);

require(realpath("../system.php"));

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$key = $config['merchant']['wmi_secret_key'];

	if (!isset($_POST["WMI_SIGNATURE"])) {
		exit('WMI_RESULT=RETRY&WMI_DESCRIPTION=No parametr WMI_SIGNATURE');
	}

	if (!isset($_POST["WMI_PAYMENT_NO"])) {
		exit('WMI_RESULT=RETRY&WMI_DESCRIPTION=No parametr WMI_PAYMENT_NO');
	}

	if (!isset($_POST["WMI_ORDER_STATE"])) {
		exit('WMI_RESULT=RETRY&WMI_DESCRIPTION=No parametr WMI_ORDER_STATE');
	}

	foreach($_POST as $name => $value) {
		if ($name !== "WMI_SIGNATURE") $params[$name] = urldecode($value);
	}

	uksort($params, "strcasecmp"); $values = "";

	foreach($params as $name => $value) {
		$values .= $value;
	}

	$signature = base64_encode(pack("H*", md5($values . $key)));

	if ($signature == $_POST["WMI_SIGNATURE"]) {
		if (strtoupper($_POST["WMI_ORDER_STATE"]) == "ACCEPTED") {
			$payid = $_POST['WMI_PAYMENT_NO'];
			$payment = new Payment;
			$status = $payment->select($payid, $_POST['WMI_PAYMENT_AMOUNT']);

			if (!$status) {
				exit('WMI_RESULT=RETRY&WMI_DESCRIPTION=' . $status);
			}

			$status = $payment->give();

			if (!$status) {
				exit('WMI_RESULT=RETRY&WMI_DESCRIPTION=' . $status);
			}

			exit("WMI_RESULT=OK");
		} else {
			exit('WMI_RESULT=RETRY&WMI_DESCRIPTION=Wrong status ' . $_POST["WMI_ORDER_STATE"]);
		}
	} else {
		exit('WMI_RESULT=RETRY&WMI_DESCRIPTION=Wrong signature ' . $_POST["WMI_SIGNATURE"]);
	}
}

exit("WMI_RESULT=OK");