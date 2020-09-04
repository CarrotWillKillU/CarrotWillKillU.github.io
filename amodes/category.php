<?php
if (!defined("TKM")) {
	die("<pre>Access denied!</pre>");
}

$page['title'] = "Category";
$page['pid']   = "category";

if (isset($_GET['do']) && $_GET['do'] == "add") {
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$name = TextSave(@$_POST['title']);
		$priority = (int) @$_POST['priority'];

		if (empty($name)) {
			MessageSend(1, "Enter a category name!");
		}

		$query = $link->prepare("INSERT INTO `AD_CATEGORIES` VALUES (NULL, :name, :prio)");

		$arr = array("name" => $name, "prio" => $priority);

		if ($query->execute($arr)) {
			MessageSend(3, "Category was successfully added.!", '?mode=category');
		} else {
			MessageSend(1, "There was an error adding the category.", '?mode=category');
		}
	}

	$row = array("title" => '',);
	ob_start();
	include STYLE_DIR . '/admin/category/edit.html';
	$page['content'] = ob_get_clean();
} elseif (isset($_GET['do']) && $_GET['do'] == "edit") {
	$id = (int) $_GET['id'];
	$sth = $link->prepare("SELECT * FROM `AD_CATEGORIES` WHERE `id` = ?");
	$sth->execute(array($id));
	$row = $sth->fetch();

	if (empty($row['id'])) {
		MessageSend(1, "Category was not found.", '?mode=category');
	}

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$name = TextSave(@$_POST['title']);
		$priority = (int) @$_POST['priority'];

		if (empty($name)) MessageSend(1, "Enter a category name!");

		$query = $link->prepare("UPDATE `AD_CATEGORIES` SET `title` = :name, `priority` = :priority WHERE `id` = :id");
		$arr = array("name" => $name, "id" => $id, "priority" => $priority);

		if ($query->execute($arr)) {
			MessageSend(3, "The category has been successfully edited.", '?mode=category');
		} else {
			MessageSend(1, "An error occurred while editing the category.", '?mode=category');
		}
	}

	ob_start();
	include STYLE_DIR . '/admin/category/edit.html';
	$page['content'] = ob_get_clean();
} elseif (isset($_GET['do']) && $_GET['do'] == "delete") {
	$id = (int) $_GET['id'];

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$sth = @$link->prepare("SELECT * FROM `AD_CATEGORIES` WHERE `id` = ?");
		$sth->execute(array($id));
		$check = $sth->fetch();

		if (empty($check['id'])) MessageSend(1, "Category was not found.", '?mode=category');

		$query = $link->prepare("DELETE FROM `AD_CATEGORIES` WHERE `id` = :id")->execute(array("id" => $id));

		if (!$query) {
			MessageSend(1, "An error occurred while deleting the category!", '?mode=category');
		}

		MessageSend(3, "The category has been successfully deleted!", '?mode=category');
	}

	$page['title'] = "Confirmation";
	ob_start();
	include STYLE_DIR . '/admin/check.html';
	$page['content'] = ob_get_clean();
} else {
	$query = $link->query("SELECT * FROM `AD_CATEGORIES` ORDER BY `priority` DESC");
	$id = 0;

	ob_start();

	while ($data = $query->fetch())	{
		$id++;
		include STYLE_DIR . '/admin/category/list_id.html';
	}

	$list = ob_get_clean();
	$list = (empty($list)) ? '<tr><td colspan="4" class="text-center"><b>Here is empty.</b></td></tr>' : $list;

	ob_start();
	include STYLE_DIR . '/admin/category/main.html';
	$page['content'] = ob_get_clean();
}