<?php
if (!defined("TKM")) {
	die("<pre>Access denied!</pre>");
}

$page['title'] = "Announcement";
$page['pid']   = "advert";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$config['advert']['title'] = (isset($_POST['title'])) ? ($_POST['title'] != $config['advert']['title']) ? TextSave($_POST['title']) : $config['advert']['title'] : $config['advert']['title'];
	$config['advert']['text'] = (isset($_POST['text'])) ? ($_POST['text'] != $config['advert']['text']) ? TextSave($_POST['text']) : $config['advert']['text'] : $config['advert']['text'];
	$config['advert']['button_href'] = (isset($_POST['button_href'])) ? ($_POST['button_href'] != $config['advert']['button_href']) ? TextSave($_POST['button_href']) : $config['advert']['button_href'] : $config['advert']['button_href'];
	$config['advert']['button_name'] = (isset($_POST['button_name'])) ? ($_POST['button_name'] != $config['advert']['button_name']) ? TextSave($_POST['button_name']) : $config['advert']['button_name'] : $config['advert']['button_name'];

	savecfg();
	MessageSend(3, "Saved!");
}

ob_start();
include STYLE_DIR . '/admin/advert/main.html';
$page['content'] = ob_get_clean();