<?php
define("TKM", true);
require(realpath("../system.php"));

// ONLY POST
if ($_SERVER['REQUEST_METHOD'] != "POST") {
	//die("No access!");
}

require_once("paypal_lib.php");
$paypal = new PayPal($paypalConfig);
$payid = $_REQUEST['username'];


  $row = $link->query("SELECT * FROM `AD_PAYMENTS` WHERE `id` = '{$payid}'")->fetch();
  $data = json_decode($row['data'], true);
  $cost = $data['cost'];

  $result = $paypal->call(array(
  'method'  => 'DoExpressCheckoutPayment',
  'paymentrequest_0_paymentaction' => 'sale',
  'paymentrequest_0_amt' => $cost,
  'paymentrequest_0_currencycode'  => $config['merchant']['paypal_currency_code'],
  'token'  => $_REQUEST['token'],
  'payerid'  => $_REQUEST['PayerID'],
));

if ($result['PAYMENTINFO_0_PAYMENTSTATUS'] != 'Completed') {
	exit("FAIL");
}

$payment = new Payment;
$status = $payment->select($payid);

if (!$status) {
  exit($status);
}
$status = $payment->give();


if (!$status) {
	exit($status);
}

header('Location: /#');
exit();