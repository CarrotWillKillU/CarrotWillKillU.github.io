<?php
if (!file_exists(dirname(__FILE__) . '/system.php')) {
	echo '<pre>';
	echo 'Error! File system.php not founded!';
	echo '</pre>';
	die;
}

define('TKM', true);
require_once dirname(__FILE__) . '/system.php';

if (!$config['main']['installed']) {
	echo '<pre>';
	echo 'Script not installed! <a href="install.php">Proceed with installation!</a>';
	echo '</pre>';
	die; 
}

if (isset($_GET['pstatus'])) {
	switch ($_GET['pstatus']) {
		case 'success':
			MessageSend(3, 'Payment was completed successfully. Thanks for the purchase!', 'index.php');
			break;

		case 'waiting':
			MessageSend(2, 'Payment was incomplete, waiting for payment ...', 'index.php');
			break;

		case 'fail':
			MessageSend(1, 'Payment canceled.', 'index.php');
			break;
	}
}


$admin_button = (isset($_SESSION['authsession'])) ? '<a href="cpanel.php">Dashboard</a>' : '';
$footer_links = '';
$head_links = '';

for ($i = 0; $i < count($config['links']); $i++) {
	$mlink['href'] = $config['links'][$i]['href'];
	$mlink['name'] = $config['links'][$i]['name'];

	if ($config['links'][$i]['footer'] === true) {
		ob_start();
		include STYLE_DIR . '/footer_link.html';
		$footer_links .= ob_get_clean();
	} else {
		ob_start();
		include STYLE_DIR . '/menu_link.html';
		$head_links .= ob_get_clean();
	}
}

if (empty($config['advert']['title']) && empty($config['advert']['text'])) {
	$advert = '';
} else {
	$config['advert']['title'] = htmlspecialchars_decode($config['advert']['title']);
	$config['advert']['text'] = htmlspecialchars_decode($config['advert']['text']);

	if (!empty($config['advert']['button_name'])) {
		ob_start();
		include STYLE_DIR . '/advert_button.html';
		$button = ob_get_clean();
	} else {
		$button = '';
	}

	ob_start();
	include STYLE_DIR . '/advert.html';
	$advert = ob_get_clean();
}

$qur = $link->query("SELECT `username`, `data`, `time` FROM `AD_PAYMENTS` WHERE `status` = '1' ORDER BY `time` DESC LIMIT 4");

ob_start();

while ($buyer = $qur->fetch()) {
	$goods_name = json_decode($buyer['data'])->{'goods_name'};
	include STYLE_DIR . "/last_buyers.html";
}

$lastBuyerList = ob_get_clean();
$lastBuyers = '<div class="row">' . $lastBuyerList . '</div>';

if (empty($lastBuyerList)) {
	$lastBuyers = '<h4 class="card-title">Empty :(</h4><p class="card-text">Here is empty :(</p>';
}

if (!empty($_GET['page'])) {
	$page = $link->prepare("SELECT * FROM `AD_PAGES` WHERE `name` = ? LIMIT 1");

	if (!$page->execute([ $_GET['page'] ])) {
		MessageSend(1, 'MySQL Error!', '');
	}

	if ($page->rowCount() <= 0) {
		MessageSend(1, 'Page not founded!', '');
	}

	$page = $page->fetch();

	if ($page['adv'] === 0) {
		$advert = '';
	}

	$goods = loadTpl('/page.html', $page);
} else {
	ob_start();

    $items = [];

	for ($i = 0; $i < count($config['servers']); $i++) {
		$first_query = $link->query("SELECT * FROM `AD_CATEGORIES` ORDER BY  `priority` DESC");
		$goodss = NULL;

		$d = 0;

		while ($catRow = $first_query->fetch()) {
			$second_query = $link->query("SELECT * FROM `AD_GOODS` WHERE (`count` > 0 OR `count` = '-') AND (`server` = '{$i}' OR `server` = '*') AND (`cat` = '{$catRow['id']}') ORDER BY `priority` DESC, `cost` ASC");
			ob_start();

			while ($data = $second_query->fetch()) {
			    $items[$i][$d]['items'][] = $data;
				if ($data['cupon'] != NULL and $data['cupon'] > 0) {
					$t = ($data['cost'] * $data['cupon']) / 100;
					$data['cost'] = $data['cost'] - $t;
					$skidka = " [Discount {$data['cupon']}%]";
				} else $skidka = "";
				//var_dump($data);

				include(STYLE_DIR . '/item.html');
			}

			if (isset($items[$i][$d]['items'])) {
                $items[$i][$d]['category'] = $catRow;
                $d++;
            }

			$goods_list = ob_get_clean();

			if (!empty($goods_list)) {
				ob_start();
				include STYLE_DIR . '/categoryItems.html';
				$goodss .= ob_get_clean();
			} else {
                $goodss .= '';
            }
		}

		include STYLE_DIR . '/server.html';
	}

	$goods = ob_get_clean();
}

require STYLE_DIR . '/index.html';