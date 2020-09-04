<?php
define("TKM", true);

require(realpath("../system.php"));

$tm = getdate(time()+9*3600);
$date = "$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";

$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$crc = $_REQUEST["SignatureValue"];

$crc = strtoupper($crc);

$my_crc = strtoupper(md5($out_summ.':'.$inv_id.':'.$config['merchant']['rk_shop_key_2']));

if ($my_crc !=$crc) {
	exit("bad sign\n");
}

$payid = $inv_id;
$payment = new Payment;
$status = $payment->select($payid, $out_summ);

if (!$status) {
	exit($status);
}

$status = $payment->give();

if (!$status) {
	exit($status);
}

exit("OK{$inv_id}\n");