<?php
if (!defined("TKM")) {
    die("<pre>Access denied!</pre>");
}

$page['title'] = "Settings";
$page['pid'] = "settings";

$bootstrapVars = [
    'default' => [
        'img' => 'style/img/bootstrap_default.png',
        'url' => '',
    ],
];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (@$_GET['id'] == "links") {
        $config['links'] = array();
        $limit = 10;

        for ($i = 0; $i <= $limit; $i++) {
            if (isset($_POST['link_name_' . $i]) &&
                isset($_POST['link_href_' . $i]) &&
                !empty($_POST['link_name_' . $i]) &&
                !empty($_POST['link_href_' . $i])
            ) {
                if ($i == $limit &&
                    isset($_POST['link_name_' . ($i + 1)]) &&
                    !empty($_POST['link_name_' . ($i + 1)])
                ) {
                    $limit++;
                }

                $config['links'][]['name'] = $_POST['link_name_' . $i];
                $config['links'][(count($config['links']) - 1)]['href'] = $_POST['link_href_' . $i];

                if (isset($_POST['link_footer_' . $i])) {
                    $config['links'][(count($config['links']) - 1)]['footer'] = true;
                } else {
                    $config['links'][(count($config['links']) - 1)]['footer'] = false;
                }

                if (isset($_POST['link_blank_' . $i])) {
                    $config['links'][(count($config['links']) - 1)]['blank'] = true;
                } else {
                    $config['links'][(count($config['links']) - 1)]['blank'] = false;
                }
            }
        }
    } elseif (@$_GET['id'] == "rcon") {
        $config['servers'] = array();
        $limit = 10;

        for ($i = 0; $i <= $limit; $i++) {
            if (isset($_POST['srv_name_' . $i]) &&
                isset($_POST['srv_host_' . $i]) &&
                isset($_POST['srv_port_' . $i]) &&
                isset($_POST['srv_password_' . $i]) &&
                isset($_POST['srv_host_websocket_' . $i]) &&
                isset($_POST['srv_port_websocket_' . $i]) &&
                isset($_POST['srv_password_websocket_' . $i]) &&
                !empty($_POST['srv_name_' . $i]) &&
                !empty($_POST['srv_host_' . $i]) &&
                !empty($_POST['srv_port_' . $i]) &&
                !empty($_POST['srv_password_' . $i]) &&
                !empty($_POST['srv_host_websocket_' . $i]) &&
                !empty($_POST['srv_port_websocket_' . $i]) &&
                !empty($_POST['srv_password_websocket_' . $i])

            ) {
                if ($i == $limit &&
                    isset($_POST['srv_name_' . ($i + 1)]) &&
                    !empty($_POST['srv_name_' . ($i + 1)])
                ) {
                    $limit++;
                }

                $config['servers'][]['name'] = $_POST['srv_name_' . $i];
                $config['servers'][(count($config['servers']) - 1)]['host'] = $_POST['srv_host_' . $i];
                $config['servers'][(count($config['servers']) - 1)]['port'] = $_POST['srv_port_' . $i];
                $config['servers'][(count($config['servers']) - 1)]['password'] = $_POST['srv_password_' . $i];
                $config['servers'][(count($config['servers']) - 1)]['host_websocket'] = $_POST['srv_host_websocket_' . $i];
                $config['servers'][(count($config['servers']) - 1)]['port_websocket'] = $_POST['srv_port_websocket_' . $i];
                $config['servers'][(count($config['servers']) - 1)]['password_websocket'] = $_POST['srv_password_websocket_' . $i];

            }
        }
    } elseif (@$_GET['id'] == "notifications") {
        if (!empty($_POST['webhook'])) {
            $config['discord']['webhook'] = $_POST['webhook'];
        }

        $limit = 10;

    } elseif (@$_GET['id'] == "design") {
        if (!empty($_POST['bootstrapVar'])) {
            $var = $_POST['bootstrapVar'];
            if (key_exists($var, $bootstrapVars)) {
                $config['main']['addon-css'] = $bootstrapVars[$var]['url'];
            }
        }
        if (!empty($_FILES['bg']) && $_FILES['bg']['error'] == 0) {
            if ($_FILES['bg']['type'] != 'image/png') {
                MessageSend(1, "yes");
            }
            if (!move_uploaded_file($_FILES['bg']['tmp_name'], STYLE_DIR . '/img/bg.png')) {
                MessageSend(1, "yes");
            }
        }
    } else {
        $config['main']['site_name'] = (isset($_POST['site_name'])) ? ($_POST['site_name'] != $config['main']['site_name']) ? TextSave($_POST['site_name']) : $config['main']['site_name'] : $config['main']['site_name'];
        $config['main']['db_host'] = (isset($_POST['db_host'])) ? ($_POST['db_host'] != $config['main']['db_host']) ? TextSave($_POST['db_host']) : $config['main']['db_host'] : $config['main']['db_host'];
        $config['main']['db_name'] = (isset($_POST['db_name'])) ? ($_POST['db_name'] != $config['main']['db_name']) ? TextSave($_POST['db_name']) : $config['main']['db_name'] : $config['main']['db_name'];
        $config['main']['db_user'] = (isset($_POST['db_user'])) ? ($_POST['db_user'] != $config['main']['db_user']) ? TextSave($_POST['db_user']) : $config['main']['db_user'] : $config['main']['db_user'];
        $config['main']['db_pass'] = (isset($_POST['db_pass']) && !empty($_POST['db_pass'])) ? ($_POST['db_pass'] != $config['main']['db_pass']) ? TextSave($_POST['db_pass']) : $config['main']['db_pass'] : $config['main']['db_pass'];
        $config['main']['serverIP'] = (isset($_POST['serverIP'])) ? ($_POST['serverIP'] != $config['main']['serverIP']) ? TextSave($_POST['serverIP']) : $config['main']['serverIP'] : $config['main']['serverIP'];
        $config['main']['serverPort'] = (isset($_POST['serverPort'])) ? ($_POST['serverPort'] != $config['main']['serverPort']) ? TextSave($_POST['serverPort']) : $config['main']['serverPort'] : $config['main']['serverPort'];
        $config['main']['currency_symbol'] = (isset($_POST['currency_symbol'])) ? ($_POST['currency_symbol'] != $config['main']['currency_symbol']) ? TextSave($_POST['currency_symbol']) : $config['main']['currency_symbol'] : $config['main']['currency_symbol'];
        $config['main']['social'] = (isset($_POST['social'])) ? ($_POST['social'] != $config['main']['social']) ? TextSave($_POST['social']) : $config['main']['social'] : $config['main']['social'];
        $config['main']['withdraw'] = (isset($_POST['withdraw'])) ? ($_POST['withdraw'] != $config['main']['withdraw']) ? TextSave($_POST['withdraw']) : $config['main']['withdraw'] : $config['main']['withdraw'];


        $config['main']['db_port'] = (isset($_POST['db_port']) && (int)$_POST['db_port'] > 0) ? ($_POST['db_port'] != $config['main']['db_port']) ? (int)$_POST['db_port'] : $config['main']['db_port'] : $config['main']['db_port'];
        //Merchant
        //Fix checkbox reset
        if (@$_GET['id'] == 'merchant') {
            $config['merchant']['ik_enable'] = (isset($_POST['ik_enable'])) ? true : false;
            $config['merchant']['up_enable'] = (isset($_POST['up_enable'])) ? true : false;
            $config['merchant']['fk_enable'] = (isset($_POST['fk_enable'])) ? true : false;
            $config['merchant']['mk_enable'] = (isset($_POST['mk_enable'])) ? true : false;
            $config['merchant']['rk_enable'] = (isset($_POST['rk_enable'])) ? true : false;
            $config['merchant']['wm_enable'] = (isset($_POST['wm_enable'])) ? true : false;
            $config['merchant']['wmi_enable'] = (isset($_POST['wmi_enable'])) ? true : false;
            $config['merchant']['fondy_enable'] = (isset($_POST['fondy_enable'])) ? true : false;
            $config['merchant']['2check_enable'] = (isset($_POST['2check_enable'])) ? true : false;
            $config['merchant']['g2a_enable'] = (isset($_POST['g2a_enable'])) ? true : false;
            $config['merchant']['paypal_enable'] = (isset($_POST['paypal_enable'])) ? true : false;
            $config['merchant']['paysera_enable'] = (isset($_POST['paysera_enable'])) ? true : false;
        }
        //InterKassa
        $config['merchant']['ik_shop_id'] = (isset($_POST['ik_shop_id'])) ? ($_POST['ik_shop_id'] != $config['merchant']['ik_shop_id']) ? TextSave($_POST['ik_shop_id']) : $config['merchant']['ik_shop_id'] : $config['merchant']['ik_shop_id'];
        $config['merchant']['ik_secret_key'] = (isset($_POST['ik_secret_key'])) ? ($_POST['ik_secret_key'] != $config['merchant']['ik_secret_key']) ? TextSave($_POST['ik_secret_key']) : $config['merchant']['ik_secret_key'] : $config['merchant']['ik_secret_key'];
        $config['merchant']['ik_testing'] = (isset($_POST['ik_testing'])) ? ((boolean)$_POST['ik_testing'] != $config['merchant']['ik_testing']) ? (boolean)$_POST['ik_testing'] : $config['merchant']['ik_testing'] : $config['merchant']['ik_testing'];
        $config['merchant']['ik_testing_key'] = (isset($_POST['ik_testing_key'])) ? ($_POST['ik_testing_key'] != $config['merchant']['ik_testing_key']) ? TextSave($_POST['ik_testing_key']) : $config['merchant']['ik_testing_key'] : $config['merchant']['ik_testing_key'];
        //Unitpay
        $config['merchant']['up_shop_id'] = (isset($_POST['up_shop_id'])) ? ($_POST['up_shop_id'] != $config['merchant']['up_shop_id']) ? TextSave($_POST['up_shop_id']) : $config['merchant']['up_shop_id'] : $config['merchant']['up_shop_id'];
        $config['merchant']['up_secret_key'] = (isset($_POST['up_secret_key'])) ? ($_POST['up_secret_key'] != $config['merchant']['up_secret_key']) ? TextSave($_POST['up_secret_key']) : $config['merchant']['up_secret_key'] : $config['merchant']['up_secret_key'];
        //Freekassa
        $config['merchant']['fk_shop_id'] = (isset($_POST['fk_shop_id'])) ? ($_POST['fk_shop_id'] != $config['merchant']['fk_shop_id']) ? TextSave($_POST['fk_shop_id']) : $config['merchant']['fk_shop_id'] : $config['merchant']['fk_shop_id'];
        $config['merchant']['fk_shop_key_1'] = (isset($_POST['fk_shop_key_1'])) ? ($_POST['fk_shop_key_1'] != $config['merchant']['fk_shop_key_1']) ? TextSave($_POST['fk_shop_key_1']) : $config['merchant']['fk_shop_key_1'] : $config['merchant']['fk_shop_key_1'];
        $config['merchant']['fk_shop_key_2'] = (isset($_POST['fk_shop_key_2'])) ? ($_POST['fk_shop_key_2'] != $config['merchant']['fk_shop_key_2']) ? TextSave($_POST['fk_shop_key_2']) : $config['merchant']['fk_shop_key_2'] : $config['merchant']['fk_shop_key_2'];
        //MyKassa
        $config['merchant']['mk_shop_id'] = (isset($_POST['mk_shop_id'])) ? ($_POST['mk_shop_id'] != $config['merchant']['mk_shop_id']) ? TextSave($_POST['mk_shop_id']) : $config['merchant']['mk_shop_id'] : $config['merchant']['mk_shop_id'];
        $config['merchant']['mk_shop_key_1'] = (isset($_POST['mk_shop_key_1'])) ? ($_POST['mk_shop_key_1'] != $config['merchant']['mk_shop_key_1']) ? TextSave($_POST['mk_shop_key_1']) : $config['merchant']['mk_shop_key_1'] : $config['merchant']['mk_shop_key_1'];
        $config['merchant']['mk_shop_key_2'] = (isset($_POST['mk_shop_key_2'])) ? ($_POST['mk_shop_key_2'] != $config['merchant']['mk_shop_key_2']) ? TextSave($_POST['mk_shop_key_2']) : $config['merchant']['mk_shop_key_2'] : $config['merchant']['mk_shop_key_2'];
        //RoboKassa
        $config['merchant']['rk_shop_id'] = (isset($_POST['rk_shop_id'])) ? ($_POST['rk_shop_id'] != $config['merchant']['rk_shop_id']) ? TextSave($_POST['rk_shop_id']) : $config['merchant']['rk_shop_id'] : $config['merchant']['rk_shop_id'];
        $config['merchant']['rk_shop_key_1'] = (isset($_POST['rk_shop_key_1'])) ? ($_POST['rk_shop_key_1'] != $config['merchant']['rk_shop_key_1']) ? TextSave($_POST['rk_shop_key_1']) : $config['merchant']['rk_shop_key_1'] : $config['merchant']['rk_shop_key_1'];
        $config['merchant']['rk_shop_key_2'] = (isset($_POST['rk_shop_key_2'])) ? ($_POST['rk_shop_key_2'] != $config['merchant']['rk_shop_key_2']) ? TextSave($_POST['rk_shop_key_2']) : $config['merchant']['rk_shop_key_2'] : $config['merchant']['rk_shop_key_2'];
        //WebMoney
        $config['merchant']['wm_purse'] = (isset($_POST['wm_purse'])) ? ($_POST['wm_purse'] != $config['merchant']['wm_purse']) ? TextSave($_POST['wm_purse']) : $config['merchant']['wm_purse'] : $config['merchant']['wm_purse'];
        $config['merchant']['wm_secret_key'] = (isset($_POST['wm_secret_key'])) ? ($_POST['wm_secret_key'] != $config['merchant']['wm_secret_key']) ? TextSave($_POST['wm_secret_key']) : $config['merchant']['wm_secret_key'] : $config['merchant']['wm_secret_key'];
        //WalletOne
        $config['merchant']['wmi_shop_id'] = (isset($_POST['wmi_shop_id'])) ? ($_POST['wmi_shop_id'] != $config['merchant']['wmi_shop_id']) ? TextSave($_POST['wmi_shop_id']) : $config['merchant']['wmi_shop_id'] : $config['merchant']['wmi_shop_id'];
        $config['merchant']['wmi_secret_key'] = (isset($_POST['wmi_secret_key'])) ? ($_POST['wmi_secret_key'] != $config['merchant']['wmi_secret_key']) ? TextSave($_POST['wmi_secret_key']) : $config['merchant']['wmi_secret_key'] : $config['merchant']['wmi_secret_key'];
        //Fondy
        $config['merchant']['fondy_id'] = (isset($_POST['fondy_id'])) ? ($_POST['fondy_id'] != $config['merchant']['fondy_id']) ? TextSave($_POST['fondy_id']) : $config['merchant']['fondy_id'] : $config['merchant']['fondy_id'];
        $config['merchant']['fondy_signature'] = (isset($_POST['fondy_signature'])) ? ($_POST['fondy_signature'] != $config['merchant']['fondy_signature']) ? TextSave($_POST['fondy_signature']) : $config['merchant']['fondy_signature'] : $config['merchant']['fondy_signature'];
        $config['merchant']['fondy_credit_key'] = (isset($_POST['fondy_credit_key'])) ? ((boolean)$_POST['fondy_credit_key'] != $config['merchant']['fondy_credit_key']) ? (boolean)$_POST['fondy_credit_key'] : $config['merchant']['fondy_credit_key'] : $config['merchant']['fondy_credit_key'];
        $config['merchant']['fondy_currency'] = (isset($_POST['fondy_currency'])) ? ($_POST['fondy_currency'] != $config['merchant']['fondy_currency']) ? TextSave($_POST['fondy_currency']) : $config['merchant']['fondy_currency'] : $config['merchant']['fondy_currency'];
        //PayPal
        $config['merchant']['paypal_user'] = (isset($_POST['paypal_user'])) ? ($_POST['paypal_user'] != $config['merchant']['paypal_user']) ? TextSave($_POST['paypal_user']) : $config['merchant']['paypal_user'] : $config['merchant']['paypal_user'];
        $config['merchant']['paypal_password'] = (isset($_POST['paypal_password'])) ? ($_POST['paypal_password'] != $config['merchant']['paypal_password']) ? TextSave($_POST['paypal_password']) : $config['merchant']['paypal_password'] : $config['merchant']['paypal_password'];
        $config['merchant']['paypal_testing'] = (isset($_POST['paypal_testing'])) ? ((boolean)$_POST['paypal_testing'] != $config['merchant']['paypal_testing']) ? (boolean)$_POST['paypal_testing'] : $config['merchant']['paypal_testing'] : $config['merchant']['paypal_testing'];
        $config['merchant']['paypal_signature'] = (isset($_POST['paypal_signature'])) ? ($_POST['paypal_signature'] != $config['merchant']['paypal_signature']) ? TextSave($_POST['paypal_signature']) : $config['merchant']['paypal_signature'] : $config['merchant']['paypal_signature'];
        $config['merchant']['paypal_currency_code'] = (isset($_POST['paypal_currency_code'])) ? ($_POST['paypal_currency_code'] != $config['merchant']['paypal_currency_code']) ? TextSave($_POST['paypal_currency_code']) : $config['merchant']['paypal_currency_code'] : $config['merchant']['paypal_currency_code'];
        //G2APay
        $config['merchant']['g2a_hash'] = (isset($_POST['g2a_hash'])) ? ($_POST['g2a_hash'] != $config['merchant']['g2a_hash']) ? TextSave($_POST['g2a_hash']) : $config['merchant']['g2a_hash'] : $config['merchant']['g2a_hash'];
        $config['merchant']['g2a_secret'] = (isset($_POST['g2a_secret'])) ? ($_POST['g2a_secret'] != $config['merchant']['g2a_secret']) ? TextSave($_POST['g2a_secret']) : $config['merchant']['g2a_secret'] : $config['merchant']['g2a_secret'];
        $config['merchant']['g2a_email'] = (isset($_POST['g2a_email'])) ? ($_POST['g2a_email'] != $config['merchant']['g2a_email']) ? TextSave($_POST['g2a_email']) : $config['merchant']['g2a_email'] : $config['merchant']['g2a_email'];
        //2Checkout
        $config['merchant']['2check_id'] = (isset($_POST['2check_id'])) ? ($_POST['2check_id'] != $config['merchant']['2check_id']) ? TextSave($_POST['2check_id']) : $config['merchant']['2check_id'] : $config['merchant']['2check_id'];
        $config['merchant']['2check_secret_word'] = (isset($_POST['2check_secret_word'])) ? ($_POST['2check_secret_word'] != $config['merchant']['2check_secret_word']) ? TextSave($_POST['2check_secret_word']) : $config['merchant']['2check_secret_word'] : $config['merchant']['2check_secret_word'];
        $config['merchant']['2check_currency'] = (isset($_POST['2check_currency'])) ? ($_POST['2check_currency'] != $config['merchant']['2check_currency']) ? TextSave($_POST['2check_currency']) : $config['merchant']['2check_currency'] : $config['merchant']['2check_currency'];

        $config['merchant']['paysera_currency'] = (isset($_POST['paysera_currency'])) ? ($_POST['paysera_currency'] != $config['merchant']['paysera_currency']) ? TextSave($_POST['paysera_currency']) : $config['merchant']['paysera_currency'] : $config['merchant']['paysera_currency'];
        $config['merchant']['paysera_country'] = (isset($_POST['paysera_country'])) ? ($_POST['paysera_country'] != $config['merchant']['paysera_country']) ? TextSave($_POST['paysera_country']) : $config['merchant']['paysera_country'] : $config['merchant']['paysera_country'];
        $config['merchant']['paysera_projectid'] = (isset($_POST['paysera_projectid'])) ? ($_POST['paysera_projectid'] != $config['merchant']['paysera_projectid']) ? TextSave($_POST['paysera_projectid']) : $config['merchant']['paysera_projectid'] : $config['merchant']['paysera_projectid'];
        $config['merchant']['paysera_sign_password'] = (isset($_POST['paysera_sign_password'])) ? ($_POST['paysera_sign_password'] != $config['merchant']['paysera_sign_password']) ? TextSave($_POST['paysera_sign_password']) : $config['merchant']['paysera_sign_password'] : $config['merchant']['paysera_sign_password'];
    }

    savecfg();
    MessageSend(3, "Settings have been saved successfully!");
}

switch (@$_GET['id']) {
    case 'rcon':
        ob_start();
        for ($i = 0; $i < count($config['servers']); $i++) {
            if (isset($_GET['test']) and (int)$_GET['test'] == $i) {
                $rcon = new RCON($i);

                if (@$rcon->connect()) {
                    $status = '<p class="col-xs-12"><code>Status: <i>ONLINE</i></code></p>';
                } else {
                    $status = '<p class="col-xs-12"><code>Status: <i>OFFLINE</i></code></p>';
                }
            } else {
                $status = '';
            }

            include STYLE_DIR . '/admin/settings/servers_id.html';
        }
        $servers = ob_get_clean();

        $page['pid'] = "rcon";

        ob_start();
        include STYLE_DIR . '/admin/settings/rcon.html';
        $page['content'] = ob_get_clean();
        break;

    case 'merchant':
        $page['pid'] = "merchant";

        ob_start();
        include STYLE_DIR . '/admin/settings/merchant.html';
        $page['content'] = ob_get_clean();
        break;

    case 'links':
        $page['pid'] = "links";

        $links = '';
        $ii = 0;

        ob_start();
        for ($i = 0; $i <= (count($config['links']) - 1); $i++) {
            include STYLE_DIR . '/admin/settings/links_id.html';
            $ii = $i;
        }
        $links = ob_get_clean();

        ob_start();
        include STYLE_DIR . '/admin/settings/links.html';
        $page['content'] = ob_get_clean();
        break;

    case 'design':
        $page['title'] = "Design editing";
        $page['pid'] = "design";

        $data = ['bootstrapVars' => $bootstrapVars];

        $page['content'] = loadTpl('/admin/settings/design.html', $data);
        break;

    case 'notifications':
        $page['title'] = "Discord notifications";
        $page['pid'] = "notifications";

        if (!isset($config['discord'])) {
            $config['discord'] = ['webhook' => ''];
            savecfg();
        }

        $data = ['accs' => ''];
        $i = 0;

        $data['i'] = $i - 1;

        $page['content'] = loadTpl('/admin/settings/notifications.html', $data);
        break;


    default:
        ob_start();
        include STYLE_DIR . '/admin/settings/main.html';
        $page['content'] = ob_get_clean();
        break;
}