<?php
if (!defined("TKM")) {
	die("<pre>Access denied!</pre>");
}

$page['title']   = "Payments";
$page['pid']	 = "payments";

if (isset($_GET['do']) && $_GET['do'] == "view") {
	$id = (int) @$_GET['id'];
	$sth = $link->prepare("SELECT * FROM `AD_PAYMENTS` WHERE `id` = ?");
	$sth->execute(array($id));
	$row = $sth->fetch();
	if (empty($row['username'])) {
		MessageSend(1, "Not found!", '?mode=payments');
	}

	$data = json_decode($row['data'], true);
	$stime = ($row['status'] == 1) ? getTime($row['stime']) : '-';
	$cost = $data['cost'];

	if ($row['status'] == 1) {
		$log = '';
		$logs = json_decode($row['log'], true);

		for ($i = 0; $i < count($logs); $i++) {
			if ($logs[$i] == "u0000u0000") {
				$log .= "#{$i}: Empty response" . PHP_EOL;
			} else {
				$log .= "#{$i}: " . str_replace("u0000u0000", '', $logs[$i]) . PHP_EOL;
			}
		}
	}
	
	$page['title'] = "View payment #{$row['id']}";

	ob_start();
	include STYLE_DIR . '/admin/payments/view.html';
	$page['content'] = ob_get_clean();
} elseif(isset($_GET['do']) && $_GET['do'] == "apay") {
	$id = (int) @$_GET['id'];
	$sth = $link->prepare("SELECT * FROM `AD_PAYMENTS` WHERE `id` = ?");
	$sth->execute(array($id));
	$row = $sth->fetch();
	if (empty($row['username'])) {
		MessageSend(1, "Не найдено!", '?mode=payments');
	}

	$payment = new Payment;
	$status = $payment->select($id);

	if ($status !== true) {
		MessageSend(1, "Payment error was made! Error: ". $status, '?mode=payments');
	}

	$status = $payment->give(true);

	if ($status === true) {
		MessageSend(3, "The payment was successfully paid!", '?mode=payments');
	} else {
		MessageSend(1, "Payment error was made! Error: ". $status, '?mode=payments');
	}
} else {
	$qub = $link->query("SELECT `username`, `data`, `time` FROM `AD_PAYMENTS` WHERE `status` = '1' ORDER BY `time` DESC LIMIT 6");

	ob_start();
	while ($buyer = $qub->fetch()) {
		$goods_name = json_decode($buyer['data'])->{'goods_name'};
		include STYLE_DIR . "/admin/payments/last_buyers.html";
	}
	$lastBuyers = ob_get_clean();

	if (isset($_GET['page']) and intval($_GET['page'])) {
		if ($_GET['page'] < 0) {
			$cpage = 0;
		} else {
			$cpage = $_GET['page'];
		}
	} else {
		$cpage = 1;
	}

	$pCount = $link->query("SELECT COUNT(*) FROM `AD_PAYMENTS`")->fetchColumn();
	$qur = $link->query("SELECT * FROM `AD_PAYMENTS` ORDER BY `status` DESC, `time` DESC LIMIT " . (($cpage - 1) * 10) . ",10");

	ob_start();

	while ($data = $qur->fetch()) {
		include STYLE_DIR . "/admin/payments/list_id.html";
	}

	$list = ob_get_clean();

	if (empty($list)) {
		$list = '<tr><td class="text-center" colspan="5"><b>Here is empty...</b></td></tr>';
	}

	ob_start();
	include STYLE_DIR . '/admin/payments/main.html';
	$page['content'] = ob_get_clean();
}