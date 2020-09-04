<?php
if (!defined("TKM")) {
	die("<pre>Access denied!</pre>");
}

$page['title']   = "Pages";
$page['pid']	 = "pages";

$do = (isset($_GET['do'])) ? $_GET['do'] : 'list';

switch ($do) {
	case 'add':
		$page['title']   = "Create a page";

		if ($_SERVER['REQUEST_METHOD'] === "POST") {
			if (empty($_POST['name']) || empty($_POST['title']) || empty($_POST['text'])) {
				MessageSend(1, "Fill in all the fields!");
			}

			$name   = trim($_POST['name']);
			$title  = trim($_POST['title']);
			$text   = $_POST['text'];
			$adv	= (isset($_POST['adv']) && $_POST['adv'] == 'on') ? 1 : 0;

			$checkName = $link->prepare("SELECT `id` FROM `AD_PAGES` WHERE `name` = ? LIMIT 1");

			if (!$checkName->execute([$name])) {
				MessageSend(1, "MySQL error!");
			}

			if ($checkName->rowCount() >= 1) {
				MessageSend(1, "This ID is already in use!");
			}

			$link->prepare("INSERT INTO `AD_PAGES` (`name`, `title`, `text`, `adv`) VALUES (?, ?, ?, ?)")->execute([
				$name,
				$title,
				$text,
				$adv
			]);
			MessageSend(3, "The page has been successfully created!", '?mode=pages');
		}

		$page['content'] = loadTpl('/admin/pages/edit.html');
		break;

	case 'edit':
		$page['title']   = "Edit page";

		if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] < 0) {
			MessageSend(1, "The page was not found!");
		}

		$id = (int) $_GET['id'];
		$pageRow = $link->prepare("SELECT * FROM `AD_PAGES` WHERE `id` = ? LIMIT 1")->execute(array($id));

		if ($pageRow->rowCount() <= 0) {
			MessageSend(1, "The page was not found!");
		}

		$pageRow = $pageRow->fetch();

		if ($_SERVER['REQUEST_METHOD'] === "POST") {
			if (empty($_POST['name']) || empty($_POST['title']) || empty($_POST['text'])) {
				MessageSend(1, "Fill in all the fields!");
			}

			$name   = trim($_POST['name']);
			$title  = trim($_POST['title']);
			$text   = $_POST['text'];
			$adv	= (isset($_POST['adv']) && $_POST['adv'] == 'on') ? 1 : 0;

			$link->prepare("UPDATE `AD_PAGES` SET `name` = ?, `title` = ?, `text` = ?, `adv` = ? WHERE `id` = ? LIMIT 1")->execute([
				$name,
				$title,
				$text,
				$adv,
				$id,
			]);
			MessageSend(3, "The page has been successfully edited!", '?mode=pages');
		}

		$page['content'] = loadTpl('/admin/pages/edit.html', $pageRow);
		break;

	case 'remove':
		$page['title']   = "Delete page";

		if ($_SERVER['REQUEST_METHOD'] === "POST") {
			if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] < 0) {
				MessageSend(1, "The page was not found!", '?mode=pages');
			}

			$id = (int)$_GET['id'];
			$pageRow = $link->prepare("SELECT * FROM `AD_PAGES` WHERE `id` = ? LIMIT 1")->execute(array($id));

			if ($pageRow->rowCount() <= 0) {
				MessageSend(1, "The page was not found!", '?mode=pages');
			}

			$link->prepare("DELETE FROM `AD_PAGES` WHERE `id` = ? LIMIT 1")->execute(array($id));
			MessageSend(3, "The page has been successfully deleted!", '?mode=pages');
		}

		$page['content'] = loadTpl('/admin/check.html');
		break;

	default:
	case 'list':
		$pagesList = $link->query("SELECT `id`, `name`, `title` FROM `AD_PAGES` ORDER BY `id` DESC");
		$pages = '';

		foreach ($pagesList as $pageItem) {
			$pages .= loadTpl('/admin/pages/list_item.html', $pageItem);
		}

		if (empty($pages)) {
			$pages = '<tr><td class="text-center text-danger" colspan="5">Create content pages to provide additional information on your store.</td></tr>';
		}

		$page['content'] = loadTpl('/admin/pages/list.html', $pages);
		break;
}