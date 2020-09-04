<?php
if (!defined("TKM")) {
	die("<pre>Access denied!</pre>");
}

$page['title']   = "Packages";
$page['pid']	 = "goods";

if (isset($_GET['do']) && $_GET['do'] == "edit") {
	$id = (int) @$_GET['id'];
	$row = $link->query("SELECT * FROM `AD_GOODS` WHERE `id` = '{$id}'")->fetch();

	if (empty($row['name'])) {
		MessageSend(1, "Not found!", '?mode=goods');
	}

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$name = TextSave(@$_POST['name']);
		$img = TextSave(@$_POST['img']);
		$cost = (int) @$_POST['cost'];
		$img = @$_POST['img'];
        $description = TextSave(@$_POST['description']);
		$cupon = (int) @$_POST['cupon'];
		$count = @$_POST['count'];
		$cat = (int) @$_POST['cat'];
		$dobuy = (@$_POST['dobuy'] == 0) ? 0 : 1;
		$priority = (int) @$_POST['priority'];
		$command = @$_POST['command'];

		if (empty($name)) {
			MessageSend(1, "Fill in all the fields!");
		}

		if (empty($command) or !is_array($command)) {
			MessageSend(1, "Add at least one command!");
		}

		if (empty(@$link->query("SELECT `id` FROM `AD_CATEGORIES` WHERE `id` = '{$cat}'")->fetch()['id'])) {
			MessageSend(1, "Select a category!");
		}

		$commands = array();

		for ($i = 0; $i <= count($command) ; $i++) {
			if (!empty($command[$i])) {
				$commands[] = $command[$i];
			}
		}

		if (count($commands) <= 0) {
			MessageSend(1, "Fill in all the fields!");
		}

		$commands = json_encode($commands, JSON_UNESCAPED_UNICODE);

		if ($count != "-") {
			$count = (int) $count;
		}

		if (@$_POST['server'] != -1 || @$_POST['server'] != '*') {
			if (isset($_POST['server']) &&
				(int)$_POST['server'] <= (count($config['servers']) - 1) &&
				(int)$_POST['server'] >= 0
			) {
				$server = (int)$_POST['server'];
			} else {
				$server = '*';
			}
		} else {
			$server = '*';
		}

		$link->prepare("UPDATE `AD_GOODS` SET `name` = '{$name}',`cost`='{$cost}',`img`='{$img}',`description`='${description}',`cupon`='{$cupon}',`count`='{$count}',`commands`='{$commands}',`server` = '{$server}',`cat` = '{$cat}',`priority` = '{$priority}', `dobuy` = '{$dobuy}' WHERE `id` = '{$id}'")->execute();
		MessageSend(3, "Package has been successfully changed.", '?mode=goods');
	}

	if ($row['server'] != '*') {
		if ($row['server'] <= (count($config['servers']) - 1) && $row['server'] >= 0) {
			$server = $row['server'];
		} else {
			$server = '-1';
		}
	} else {
		$server = '-1';
	}

	ob_start();

	for ($i = 0; $i < count($config['servers']); $i++)	{
		include STYLE_DIR . '/admin/goods/servers_id.html';
	}

	$servers = ob_get_clean();
	$query = $link->query("SELECT * FROM `AD_CATEGORIES`");

	ob_start();

	while ($crow = $query->fetch()) {
		include STYLE_DIR . '/admin/goods/cat_id.html';
	}

	$cats = ob_get_clean();
	$commands = json_decode($row['commands']);

	ob_start();

	for ($a = 0; $a < count($commands); $a++) {
		include STYLE_DIR . '/admin/goods/command.html';
	}

	$commands = ob_get_clean();
	$ii = $a;

	$page['title'] = "Editing \"{$row['name']}\" package";

	ob_start();
	include STYLE_DIR . '/admin/goods/edit.html';
	$page['content'] = ob_get_clean();
} elseif (isset($_GET['do']) && $_GET['do'] == "add") {
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$name = TextSave(@$_POST['name']);
		$cost = (int) @$_POST['cost'];
		$img = @$_POST['img'];
		$cupon = (int) @$_POST['cupon'];
		$img = TextSave(@$_POST['img']);
        $description = TextSave(@$_POST['description']);
		$count = @$_POST['count'];
		$cat = (int) @$_POST['cat'];
		$dobuy = (@$_POST['dobuy'] == 0) ? 0 : 1;
		$priority = (int) @$_POST['priority'];
		$command = @$_POST['command'];

		if (empty($name)) {
			MessageSend(1, "Fill in all the fields!");
		}

		if (empty($command) or !is_array($command)) {
			MessageSend(1, "Add at least one command!");
		}

		if (empty($link->query("SELECT `id` FROM `AD_CATEGORIES` WHERE `id` = '{$cat}'")->fetch()['id'])) {
			MessageSend(1, "Select a category!");
		}

		$commands = array();

		for ($i = 0; $i <= count($command) ; $i++) {
			if (!empty($command[$i])) {
				$commands[] = $command[$i];
			}
		}

		if (count($commands) <= 0) {
			MessageSend(1, "Fill in all the fields!");
		}

		$commands = json_encode($commands, JSON_UNESCAPED_UNICODE);

		if ($count != "-") {
			$count = (int) $count;
		}

		if (@$_POST['server'] != -1 || @$_POST['server'] != '*') {
			if (isset($_POST['server']) &&
				(int)$_POST['server'] <= (count($config['servers']) - 1) &&
				(int)$_POST['server'] >= 0
			) {
				$server = (int)$_POST['server'];
			} else {
				$server = '*';
			}
		} else {
			$server = '*';
		}

		$link->prepare("INSERT INTO `AD_GOODS` (`name`,`cost`,`img`,`description`,`cupon`,`count`,`commands`,`server`, `cat`, `priority`) VALUES ('{$name}','{$cost}','{$img}','{$description}','{$cupon}','{$count}','{$commands}','{$server}','{$cat}','{$priority}')")->execute();
		MessageSend(3, "Package has been successfully added.");
	}

	$page['title'] = "Adding package";

	$row = array('name' => '', 'cost' => '', 'img' => '', 'description' => '', 'cupon' => '', 'count' => '', 'commands' => '', );
	$server = '-1';

	ob_start();

	for ($i = 0; $i < count($config['servers']); $i++) {
		include STYLE_DIR . '/admin/goods/servers_id.html';
	}

	$servers = ob_get_clean();
	$query = $link->query("SELECT * FROM `AD_CATEGORIES`");

	ob_start();

	while ($crow = $query->fetch()) {
		include STYLE_DIR . '/admin/goods/cat_id.html';
	}

	$cats = ob_get_clean();
	$ii = 1;
	$a = 1;
	$commands[$a] = '';

	ob_start();
	include STYLE_DIR . '/admin/goods/command.html';
	$commands = ob_get_clean();

	ob_start();
	include STYLE_DIR . '/admin/goods/edit.html';
	$page['content'] = ob_get_clean();
} elseif (isset($_GET['do']) && $_GET['do'] == "delete") {
	$id = (int) $_GET['id'];

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$check = $link->query("SELECT `id` FROM `AD_GOODS` WHERE `id` = '{$id}'")->fetch();

		if (empty($check['id'])) {
			MessageSend(1, "Not found!", '?mode=goods');
		}

		if (!$link->prepare("DELETE FROM `AD_GOODS` WHERE `id` = '{$id}'")->execute()) {
			MessageSend(1, "Error committing action!", '?mode=goods');
		}

		MessageSend(3, "Package has been successfully deleted!", '?mode=goods');
	}

	$page['title'] = "Confirmation";

	ob_start();
	include STYLE_DIR . '/admin/check.html';
	$page['content'] = ob_get_clean();
} else {
	$qur = $link->query("SELECT * FROM `AD_GOODS` ORDER BY `id`");

	ob_start();

	while ($data = $qur->fetch()) {
		include STYLE_DIR . '/admin/goods/list_id.html';
	}

	$list = ob_get_clean();

	if (empty($list)) {
		$list = '<tr><td class="text-center" colspan="7"><b>Here is empty <i class="ti-face-sad"></i></b></td></tr>';
	}

	ob_start();
	include STYLE_DIR . '/admin/goods/main.html';
	$page['content'] = ob_get_clean();
}