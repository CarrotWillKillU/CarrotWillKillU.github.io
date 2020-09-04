<?php
if (!defined("TKM")) {
	die("<pre>Access denied!</pre>");
}

$page['title']   = "Remote Console (RCON)";
$page['pid']	 = "rcon-console";

if ($_SERVER['REQUEST_METHOD'] == "POST" &&
	!empty($_POST['command']) &&
	(int) $_POST['server'] >= 0
) {
	$rcon = new RCON((int) $_POST['server']);

	if (!@$rcon->connect()) {
		exit("RCON: Failed connection!");
	}

	$rcon->send_command($_POST['command']);
	echo $rcon->get_response();
	$rcon->disconnect();
	exit(0);
}

if (count($config['servers']) == 0) {
	$page['content'] = "No servers were found!";
} else {
	if (isset($_POST['server']) &&
		(int)$_POST['server'] <= (count($config['servers']) - 1) &&
		(int)$_POST['server'] >= 0
	) {
		$server = (int)$_POST['server'];
	} else {
		$server = 0;
	}

	ob_start();

	for ($i = 0; $i < count($config['servers']); $i++) {
		include STYLE_DIR . '/admin/rcon/servers_id.html';
	}

	$servers = ob_get_clean();
	$rcon = new RCON($server);

	if (@$rcon->connect()) {
		$first_out = '<div class="console-out">[15:10:00] Rcon connected!</div>';
		$enabled = true;
	} else {
		$first_out = '<div class="console-out">[15:10:00] Rcon error!</div>';
		$enabled = false;
	}

	ob_start();
	include STYLE_DIR . '/admin/rcon/main.html';
	$page['content'] = ob_get_clean();
}