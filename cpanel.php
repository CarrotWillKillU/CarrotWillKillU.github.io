<?php
if (!file_exists(dirname(__FILE__) . '/system.php')) {
	echo '<pre>';
	echo 'Error! File system.php not founded!';
	echo '</pre>';
	die;
}

define('TKM', true);
require_once dirname(__FILE__) . '/system.php';

if(!$config['main']['installed']) {
	exit(header("Location: install.php"));
}

if (isset($_SESSION['authsession'])) {
	$authsession = explode("|", $_SESSION['authsession']);
	if (!isset($authsession['0']) ||
		!isset($authsession['1']) ||
		!isset($authsession['2'])
	) {
		unset($_SESSION['authsession']);
		exit(header("Location: cpanel.php"));
	}

	$row = $link->prepare("SELECT * FROM `AD_ADMINS` WHERE `login` = ? AND `password` = ?");
	$row->execute(array($authsession['0'], $authsession['1']));

	if ($row) {
		$row = $row->fetch(PDO::FETCH_ASSOC);
	}

	if (empty($row['login']) || $authsession['2'] != $row['ip']) {
		unset($_SESSION['authsession']);
		exit(header("Location: cpanel.php"));
	}

	$user = array(
		"login" => $row['login'],
		"ip" => $row['ip'],
	);

	if (isset($_GET['do']) && $_GET['do'] == "logout") {
		unset($_SESSION['authsession']);
		MessageSend(3, "You have successfully logged out!", 'cpanel.php');
	}

	$page = array(
		"title"   => 'default',
		"pid"     => 'default',
		"content" => 'default',
	);

	if (isset($_GET['mode'])) {
		if (!file_exists(ROOT_DIR . "/amodes/{$_GET['mode']}.php")) {
			$mode = 'info';
		} else {
			$mode = $_GET['mode'];
		}
	} else {
		$mode = 'info';
	}

	require ROOT_DIR . '/amodes/' . $mode .'.php';

	include STYLE_DIR . '/admin/index.html';
} else {
	if (isset($_GET['do']) && $_GET['do'] == "login") {
		$login = TextSave(@$_POST['login']);
		$passw = TextSave(@$_POST['password']);

		if (empty($login) OR empty($passw))	{
			MessageSend(1, "Fill in all the fields!");
		} else {
			$passw = md5("KOBA".TextSave(@$_POST['password']).md5(TextSave(@$_POST['password'])));

			$row = $link->prepare("SELECT * FROM `AD_ADMINS` WHERE `login` = ? AND `password` = ?");
			$row->execute(array($login, $passw));

			if ($row) {
				$row = $row->fetch(PDO::FETCH_ASSOC);
			}

			if (empty($row['login'])) {
				MessageSend(1, "Wrong login/password!");
			}

			$link->prepare("UPDATE `AD_ADMINS` SET `ip` = '".getIP()."' WHERE `login` = '{$login}'")->execute();

			$_SESSION['authsession'] = $row['login'] . "|" . $row['password'] . "|" . getIP();
			MessageSend(3, "You have successfully logged in!");
		}
	}

	require STYLE_DIR . '/admin/auth.html';
}