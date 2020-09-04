<?php
define("TKM", true);
require(dirname(__FILE__) . '/system.php');

$yesterday = time() - 86400;

$sql = $link->prepare("DELETE FROM `AD_PAYMENTS` WHERE `status` = 0 AND `time` < :yesterday");
$sql->execute(array(':yesterday' => $yesterday));
$query = $link->query("SELECT `id` FROM `AD_PAYMENTS` WHERE `status` = 2");

$payments = 0;

while($data = $query->fetch()) {
	$payment = new Payment;
	$payment->select($data['id']);
	$payment->give();
	$payments++;
}

echo "<pre>OK" . PHP_EOL;
echo "Processed: {$payments} payments.</pre>";