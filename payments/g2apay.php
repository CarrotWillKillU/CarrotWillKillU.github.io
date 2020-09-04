<?php
define("TKM", true);
require(realpath("../system.php"));

require_once("g2apay_lib.php");

$g2apay = new G2APay($g2apayConfig);
$payid = $_REQUEST['username'];

  $row = $link->query("SELECT * FROM `AD_PAYMENTS` WHERE `id` = '{$payid}'")->fetch();
  $data = json_decode($row['data'], true);
  $price = $data['cost'];

  $result = $g2apay->test()->createOrder(array());

$payment = new Payment;
$status = $payment->select($payid);

  if (!$status) {
    exit($status);
  }
  $status = $payment->give();
  
  
  if (!$status) {
      exit($status);
  }
  
  header('Location: /success.html');
  exit();