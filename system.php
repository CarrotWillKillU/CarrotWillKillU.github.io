<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!defined("TKM")) die("<pre>Access denied!</pre>");

define('ROOT_DIR'    , dirname(__FILE__)            );
define('CLASSES_DIR' , ROOT_DIR . '/classes'        );
define('STYLE_DIR'   , ROOT_DIR . '/style'          );
define('STYLE_URL'   , '/style'                     );
define('VERSION'     , '1.6'                      );

session_start();

require(ROOT_DIR . "/config.php");
require(CLASSES_DIR . "/rcon.class.php");
require(CLASSES_DIR . "/payment.class.php");

if ($config['main']['installed']) {
	try {
		$opt = array(
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		);
		$link = @new PDO("mysql:host={$config['main']['db_host']};port={$config['main']['db_port']};dbname={$config['main']['db_name']};charset=utf8", $config['main']['db_user'], $config['main']['db_pass'], $opt);
	} catch (PDOException $ex) {
		exit("{$ex->getMessage()}<p>[Database] Failed to connect to the database.</p>");
	}
}

function loadTpl($file, $data = []) {
	global $config;
	$file = STYLE_DIR . $file;
	ob_start();

	if (file_exists($file)) {
		include $file;
	} else {
		echo 'TPL File not found!';
	}

	return ob_get_clean();
}

function TextSave($string) {
	return htmlspecialchars($string);
}

function MessageSend($t, $s, $u = '') {
	switch ($t) {
		case 2:
			$class_name = 'warning';
			$title = 'Warning';
			break;

		case 3:
			$class_name = 'success';
			$title = 'Success';
			break;

		case 1:
		default:
			$class_name = 'danger';
			$title = 'Error';
			break;
	}
	$_SESSION['message'] = '<div class="' . $class_name . '" id="alert">' . $s . '</div>';

	$_SESSION['message'] = <<<END
	<div class="alert alert-$class_name">
		<b>$title!</b> $s
	</div>
END;

	if ($u)	{
		exit(header("Location: ".$u));
	} else {
		if(isset($_SERVER['HTTP_REFERER'])) {
			exit(header("Location: ".$_SERVER['HTTP_REFERER']));
		} else {
			exit(header("Location: /"));
		}
	}
}

function MessageShow() {
	if (isset($_SESSION['message'])) {
		echo $_SESSION['message'];
		unset($_SESSION['message']);
	}
}

function savecfg() {
	global $config;

	$txt  = '<?php' . PHP_EOL;
	$txt .= 'if (!defined("TKM")) die("<pre>Access denied!</pre>");' . PHP_EOL;
	$txt .= "//MineStore Script. Made on Earth/Europe!" . PHP_EOL;
	$txt .= '$config = '.var_export($config, true).';' . PHP_EOL;
	$txt .= '?>';

	$result = file_put_contents(ROOT_DIR . "/config.php", $txt);

	if($result === false) {
		return false;
	}

	return true;
}

function getIP() {
	if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
		//CloudFlare proxy Support
		$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	} else {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip=$_SERVER['REMOTE_ADDR'];
		}
	}

	return $ip;
}

function getTime($time) {
	return date("d.m.Y H:i:s", $time);
}