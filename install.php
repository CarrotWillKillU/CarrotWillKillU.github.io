<?php
if (!file_exists(dirname(__FILE__) . '/system.php')) {
	echo '<pre>';
	echo 'Error! File system.php not founded!';
	echo '</pre>';
	die;
}

define("TKM", true);

require(dirname(__FILE__) . '/system.php');

if (@$config['main']['installed']) {
	echo '<pre>';
	echo 'Script already installed.' . PHP_EOL;
	echo 'Delete this file (install.php)';
	echo '</pre>';
	die;
}

if (!is_writable(ROOT_DIR . '/config.php') && is_readable(ROOT_DIR . '/config.php')) {
	echo '<pre>';
	echo 'Installation canceled!' . PHP_EOL;
	echo 'Give `777` rights (via FTP) for `config.php`.';
	echo '</pre>';
	die;
}

$sql_file = @file_get_contents(ROOT_DIR . '/install.sql');

if (empty($sql_file)) {
	echo '<pre>';
	echo 'Installation canceled!' . PHP_EOL;
	echo 'File `install.sql` not founded.';
	echo '</pre>';
	die;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$site_name = TextSave(@$_POST['site_name']);
	$mainurl   = TextSave(@$_POST['mainurl']);
	$db_host   = TextSave(@$_POST['db_host']);
	$db_port   = TextSave(@$_POST['db_port']);
	$serverIP   = TextSave(@$_POST['serverIP']);
	$serverPort   = TextSave(@$_POST['serverPort']);

	if (!intval($db_port)) {
		$db_port = 3306;
	}

	$db_name   = TextSave(@$_POST['db_name']);
	$db_user   = TextSave(@$_POST['db_user']);
	$db_pass   = TextSave(@$_POST['db_pass']);

	if (empty($site_name) || empty($db_host) || empty($db_name) || empty($db_user)) {
		MessageSend(1, "Fill in all the fields of the form!", 'install.php');
	}

	$obj = @new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

	if ($obj->connect_errno) {
		MessageSend(1, "The problem with connecting to the database. Check the settings!", 'install.php');
	}

	if (!$obj->set_charset("utf8")) {
		MessageSend(1, "The problem with connecting to the database. Failed to set encoding (UTF-8)!", 'install.php');
	}

	$obj->multi_query($sql_file);

	//Main
	$config['main']['installed'] = true;
	$config['main']['mainurl'] = $mainurl;
	$config['main']['db_host'] = $db_host;
	$config['main']['db_name'] = $db_name;
	$config['main']['db_user'] = $db_user;
	$config['main']['db_pass'] = $db_pass;
	$config['main']['db_port'] = $db_port;
	$config['main']['site_name'] = $site_name;
	$config['main']['serverIP'] = $serverIP;
	$config['main']['serverPort'] = $serverPort;
	//Merchant OFF
	$config['merchant']['ik_enable'] = false;
	$config['merchant']['ik_testing'] = false;
	$config['merchant']['up_enable'] = false;
	$config['merchant']['fk_enable'] = false;
	$config['merchant']['rk_enable'] = false;
	$config['merchant']['mk_enable'] = false;
	$config['merchant']['wm_enable'] = false;
	$config['merchant']['paypal_enable'] = false;
	$config['merchant']['g2a_enable'] = false;
	$config['merchant']['fondy_enable'] = false;
	$config['merchant']['2check_enable'] = false;
	$config['merchant']['paysera_enable'] = false;
	//saving
	savecfg();

	sleep(3);
	MessageSend(3, "<p>The script was successfully installed! <b>Delete <i>install.php</i>!</b></p><p>Visit a adminpanel <a style=\"color:orange\" href=\"cpanel.php\">Dashboard</a> (Login Information (login:pass): admin:admin)</p>", 'index.php');
}

$mainurl = str_replace('install.php', '', $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);

require STYLE_DIR . '/install/index.html';