<?php
if (!defined("TKM")) {
	die("<pre>Access denied!</pre>");
}

$page['title']   = "Users";
$page['pid']	 = "users";

if (isset($_GET['do']) && $_GET['do'] == "edit") {
	$login = $_GET['id'];
	$sth = $link->prepare("SELECT * FROM `AD_ADMINS` WHERE `login` = ?");
	$sth->execute(array($login));
	$row = $sth->fetch();

	if (empty($row['login'])) {
		MessageSend(1, "Not found!", '?mode=users');
	}

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$new_login = TextSave(@$_POST['login']);
		$password = (!empty($_POST['password'])) ? md5("KOBA".TextSave(@$_POST['password']).md5(TextSave(@$_POST['password']))) : $row['password'];

		if (empty($new_login)) {
			MessageSend(1, "Fill in all the fields!");
		}

		$link->prepare("UPDATE `AD_ADMINS` SET `login` = '{$new_login}',`password`='{$password}' WHERE `login` = '{$login}'")->execute();
		MessageSend(3, "User has been successfully changed!", '?mode=users');
	}

	$page['title'] = "Editing \"{$row['login']}\"";

	ob_start();
	include STYLE_DIR . '/admin/users/edit.html';
	$page['content'] = ob_get_clean();
} elseif (isset($_GET['do']) && $_GET['do'] == "add") {
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$login = TextSave(@$_POST['login']);
		$password = TextSave(@$_POST['password']);

		if (empty($login) OR empty($password)) {
			MessageSend(1, "Fill in all the fields!");
		}

		$password = md5("KOBA".TextSave(@$_POST['password']).md5(TextSave(@$_POST['password'])));

		$link->prepare("INSERT INTO `AD_ADMINS` (`login`,`password`,`ip`) VALUES ('{$login}','{$password}','".getIP()."')")->execute();
		MessageSend(3, "User has been successfully added!", '?mode=users');
	}

	$page['title'] = "Adding user";
	$row = array('login' => '', 'ip' => '', );

	ob_start();
	include STYLE_DIR . '/admin/users/edit.html';
	$page['content'] = ob_get_clean();
} elseif (isset($_GET['do']) && $_GET['do'] == "delete") {
	$login = $_GET['id'];

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$sth = $link->prepare("SELECT `login` FROM `AD_ADMINS` WHERE `login` = ?");
		$sth->execute(array($login));
		$check = $sth->fetch();
		
		if (empty($check['login'])) {
			MessageSend(1, "Not found!", '?mode=users');
		}

		$count = $link->query("SELECT COUNT(*) FROM `AD_ADMINS`")->fetchColumn();

		if ($count <= 1) {
			MessageSend(1, "The minimum ammount of users is 1.", '?mode=users');
		}

		if (!$link->prepare("DELETE FROM `AD_ADMINS` WHERE `login` = ?")->execute(array($login))) {
			MessageSend(1, "Error committing action!", '?mode=users');
		}

		MessageSend(3, "User has been successfully deleted!", '?mode=users');
	}

	$page['title'] = "Confirmation";

	ob_start();
	include STYLE_DIR . '/admin/check.html';
	$page['content'] = ob_get_clean();
} else {
	$qur = $link->query("SELECT * FROM `AD_ADMINS`");

	ob_start();

	while ($data = $qur->fetch()) {
		include STYLE_DIR . '/admin/users/list_id.html';
	}

	$list = ob_get_clean();

	if (empty($list)) {
		$list = '<tr><td class="text-center" colspan="3"><b>Here is empty <i class="ti-face-sad"></i></b></td></tr>';
	}

	ob_start();
	include STYLE_DIR . '/admin/users/main.html';
	$page['content'] = ob_get_clean();
}