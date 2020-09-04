<?php
if (!defined("TKM")) {
	die("<pre>Access denied!</pre>");
}

$page['title']   = "Coupons";
$page['pid']     = "cupons";

if (isset($_GET['do']) && $_GET['do'] == "edit") {
	$id = @$_GET['id'];
	$sth = $link->prepare("SELECT * FROM `AD_CUPONS` WHERE `name` = ?");
	$sth->execute(array($id));
	$row = $sth->fetch();

	if (empty($row['name'])) {
		MessageSend(1, "Not found!", '?mode=cupons');
	}

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$name = TextSave(@$_POST['name']);
		$summ = (int) @$_POST['summ'];
		$count = (int) @$_POST['count'];

		if (empty($name) OR $summ == 0 OR $count == 0) {
			MessageSend(1, "Fill in all the fields!");
		}
		
		$link->prepare("UPDATE `AD_CUPONS` SET `name` = '{$name}',`summ`='{$summ}',`count`='{$count}' WHERE `name` = '{$id}'")->execute();
		MessageSend(3, "Coupon has been successfully changed!", '?mode=cupons');
	}

	$page['title'] = "Editing \"{$row['name']}\" coupon";

	ob_start();
	include STYLE_DIR . '/admin/cupons/edit.html';
	$page['content'] = ob_get_clean();
} elseif (isset($_GET['do']) && $_GET['do'] == "add") {
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$name = TextSave(@$_POST['name']);
		$summ = (int) @$_POST['summ'];
		$count = (int) @$_POST['count'];

		if (empty($name) OR $summ == 0 OR $count == 0) {
			MessageSend(1, "Fill in all the fields!");
		}

		$link->prepare("INSERT INTO `AD_CUPONS` (`name`,`summ`,`count`) VALUES ('{$name}','{$summ}','{$count}')")->execute();
		MessageSend(3, "Coupon has been successfully added!");
	}

	$page['title'] = "Create a coupon";
	$row = array('name' => '', 'summ' => '', 'count' => '', );

	ob_start();
	include STYLE_DIR . '/admin/cupons/edit.html';
	$page['content'] = ob_get_clean();
} elseif (isset($_GET['do']) && $_GET['do'] == "delete") {
	$id = $_GET['id'];

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$sth = @$link->prepare("SELECT `name` FROM `AD_CUPONS` WHERE `name` = ?");
		$sth->execute(array($id));
		$check = $sth->fetch();
		if (empty($check['name'])) {
			MessageSend(1, "Not found!", '?mode=cupons');
		}

		if (!$link->prepare("DELETE FROM `AD_CUPONS` WHERE `name` = ?")->execute(array($id))) {
			MessageSend(1, "Error committing action!", '?mode=cupons');
		}

		MessageSend(3, "Coupon has been successfully deleted!", '?mode=cupons');
	}

	$page['title'] = "Confirmation";

	ob_start();
	include STYLE_DIR . '/admin/check.html';
	$page['content'] = ob_get_clean();
} else {
	$query = $link->query("SELECT * FROM `AD_CUPONS` ORDER BY `summ` DESC");

	ob_start();

	while ($data = $query->fetch())	{
		include STYLE_DIR . '/admin/cupons/list_id.html';
	}

	$list = ob_get_clean();

	if (empty($list)) {
		$list = '<tr><td class="text-center" colspan="4"><b>Here is empty</b></td></tr>';
	}

	ob_start();
	include STYLE_DIR . '/admin/cupons/main.html';
	$page['content'] = ob_get_clean();
}