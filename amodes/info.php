<?php
if (!defined("TKM")) {
	die("<pre>Access denied!</pre>");
}

$page['title']   = "Dashboard";
$page['pid']     = "info";
ini_set('default_socket_timeout', '10');

$lastVersion = @file_get_contents('https://minestorecms.com/text.txt');

if ($lastVersion) {
    $lastVersion = @json_decode($lastVersion, true);

    if (is_array($lastVersion)) {
        if ($lastVersion['version'] != VERSION) {
            $new_version = '<b class="text-info">Update available (' . $lastVersion['version'] . ')!</b>.';
        } else {
            $new_version = $lastVersion['version'];
        }

        if (!empty($lastVersion['adv'])) {
            echo $lastVersion['adv'];
        }
    } else {
        $new_version = '<span class="text-danger"><b>Server is not available!</b></span>';
    }
} else {
    $new_version = '<span class="text-danger"><b>Server is not available!</b></span>';
}

// Недельный график
$Monday            = strtotime((date("D") == "Mon") ? "Today" : "last Mon");
$Tuesday           = strtotime((date("D") == "Tue") ? "Today" : "last Tue");
$Wednesday         = strtotime((date("D") == "Wed") ? "Today" : "last Wed");
$Thursday          = strtotime((date("D") == "Thu") ? "Today" : "last Thu");
$Friday            = strtotime((date("D") == "Fri") ? "Today" : "last Fri");
$Saturday          = strtotime((date("D") == "Sat") ? "Today" : "last Sat");
$Sunday            = strtotime((date("D") == "Sun") ? "Today" : "last Sun");
$endMonday         = $Monday+86399;
$endTuesday        = $Tuesday+86399;
$endWednesday      = $Wednesday+86399;
$endThursday       = $Thursday+86399;
$endFriday         = $Friday+86399;
$endSaturday       = $Saturday+86399;
$endSunday         = $Sunday+86399;
$pays['Monday']    = (int) $link->query("SELECT COUNT(*) FROM `AD_PAYMENTS` WHERE `status` = 1 AND `time` BETWEEN '{$Monday}' AND '{$endMonday}'")->fetchColumn();
$pays['Tuesday']   = (int) $link->query("SELECT COUNT(*) FROM `AD_PAYMENTS` WHERE `status` = 1 AND `time` BETWEEN '{$Tuesday}' AND '{$endTuesday}'")->fetchColumn();
$pays['Wednesday'] = (int) $link->query("SELECT COUNT(*) FROM `AD_PAYMENTS` WHERE `status` = 1 AND `time` BETWEEN '{$Wednesday}' AND '{$endWednesday}'")->fetchColumn();
$pays['Thursday']  = (int) $link->query("SELECT COUNT(*) FROM `AD_PAYMENTS` WHERE `status` = 1 AND `time` BETWEEN '{$Thursday}' AND '{$endThursday}'")->fetchColumn();
$pays['Friday']    = (int) $link->query("SELECT COUNT(*) FROM `AD_PAYMENTS` WHERE `status` = 1 AND `time` BETWEEN '{$Friday}' AND '{$endFriday}'")->fetchColumn();
$pays['Saturday']  = (int) $link->query("SELECT COUNT(*) FROM `AD_PAYMENTS` WHERE `status` = 1 AND `time` BETWEEN '{$Saturday}' AND '{$endSaturday}'")->fetchColumn();
$pays['Sunday']    = (int) $link->query("SELECT COUNT(*) FROM `AD_PAYMENTS` WHERE `status` = 1 AND `time` BETWEEN '{$Sunday}' AND '{$endSunday}'")->fetchColumn();

switch (date("D")) {
	case 'Mon':
		$pays['Tuesday'] = 0;
		$pays['Wednesday'] = 0;
		$pays['Thursday'] = 0;
		$pays['Friday'] = 0;
		$pays['Saturday'] = 0;
		$pays['Sunday'] = 0;
		break;

	case 'Tue':
		$pays['Wednesday'] = 0;
		$pays['Thursday'] = 0;
		$pays['Friday'] = 0;
		$pays['Saturday'] = 0;
		$pays['Sunday'] = 0;
		break;

	case 'Wed':
		$pays['Thursday'] = 0;
		$pays['Friday'] = 0;
		$pays['Saturday'] = 0;
		$pays['Sunday'] = 0;
		break;

	case 'Thu':
		$pays['Friday'] = 0;
		$pays['Saturday'] = 0;
		$pays['Sunday'] = 0;
		break;

	case 'Fri':
		$pays['Saturday'] = 0;
		$pays['Sunday'] = 0;
		break;

	case 'Sat':
		$pays['Sunday'] = 0;
		break;
}

$users_count     = $link->query("SELECT COUNT(*) FROM `AD_ADMINS`")->fetchColumn();
$goods_count     = $link->query("SELECT COUNT(*) FROM `AD_GOODS`")->fetchColumn();
$time            = time()-86400;
$pays['all']     = $link->query("SELECT COUNT(*) FROM `AD_PAYMENTS` WHERE `time` > '{$time}'")->fetchColumn();
$pays['success'] = $link->query("SELECT COUNT(*) FROM `AD_PAYMENTS` WHERE (`status` = 1 OR `status` = 2) AND `time` > '{$time}'")->fetchColumn();
$pays['fail']    = $link->query("SELECT COUNT(*) FROM `AD_PAYMENTS` WHERE `status` = 0 AND `time` > '{$time}'")->fetchColumn();

ob_start();
include STYLE_DIR . '/admin/info/main.html';
$page['content'] = ob_get_clean();