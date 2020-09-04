<?php
if (!defined("TKM")) die("<pre>Access denied!</pre>");
//MineStore Script. Made on Earth/Europe!
$config = array (
  'main' => 
  array (
    'installed' => false,
    'site_name' => '',
    'db_host' => '',
    'db_name' => '',
    'db_user' => '',
    'db_pass' => '',
    'db_port' => '3306',
    'serverIP' => '',
    'serverPort' => '',
    'currency_symbol' => '$',
    'mainurl' => '',
    'addon-css' => '',
    'withdraw' => 'plugin',
    'social' => 'https://twitter.com/test/',
  ),
  'links' => 
  array (
    0 => 
    array (
      'name' => 'RULES',
      'href' => 'rules',
      'footer' => false,
      'blank' => false,
    ),
    1 => 
    array (
      'name' => 'SOCIAL',
      'href' => 'twitter',
      'footer' => false,
      'blank' => true,
    ),
    2 => 
    array (
      'name' => 'Footer link #1',
      'href' => 'bottom1',
      'footer' => true,
      'blank' => false,
    ),
    3 => 
    array (
      'name' => 'Footer link #2',
      'href' => 'bottom2',
      'footer' => true,
      'blank' => true,
    ),
    4 => 
    array (
      'name' => 'FORUM',
      'href' => 'forum',
      'footer' => false,
      'blank' => false,
    ),
    5 => 
    array (
      'name' => 'CONTACTS',
      'href' => 'contacts',
      'footer' => false,
      'blank' => false,
    ),
  ),
  'advert' => 
  array (
    'title' => '',
    'text' => '',
    'button_name' => '',
    'button_href' => '',
  ),
  'merchant' => 
  array (
    'ik_enable' => false,
    'ik_shop_id' => '',
    'ik_secret_key' => '',
    'ik_testing' => false,
    'ik_testing_key' => '',
    'up_enable' => false,
    'up_shop_id' => '',
    'up_secret_key' => '',
    'fk_enable' => false,
    'fk_shop_id' => '',
    'fk_shop_key_1' => '',
    'fk_shop_key_2' => '',
    'rk_enable' => false,
    'rk_shop_id' => '',
    'rk_shop_key_1' => '',
    'rk_shop_key_2' => '',
    'mk_enable' => false,
    'mk_shop_id' => '',
    'mk_shop_key_1' => '',
    'mk_shop_key_2' => '',
    'wm_enable' => false,
    'wm_purse' => '',
    'wm_secret_key' => '',
    'wmi_enable' => false,
    'wmi_shop_id' => '',
    'wmi_secret_key' => '',
    'paypal_enable' => false,
    'paypal_testing' => false,
    'paypal_user' => '',
    'paypal_password' => '',
    'paypal_signature' => '',
    'paypal_currency_code' => 'USD',
    'g2a_enable' => false,
    'g2a_hash' => '',
    'g2a_secret' => '',
    'g2a_email' => '',
    'fondy_enable' => false,
    'fondy_id' => '',
    'fondy_signature' => '',
    'fondy_credit_key' => '',
    'fondy_currency' => 'USD',
    '2check_enable' => false,
    '2check_id' => '',
    '2check_secret_word' => '',
    '2check_currency' => 'USD',
    'paysera_enable' => false,
    'paysera_currency' => 'EUR',
    'paysera_country' => 'LT',
    'paysera_projectid' => '',
    'paysera_sign_password' => '',
  ),
  'servers' => 
  array (
    0 => 
    array (
      'name' => 'Your server',
      'host' => 'put 1 (if RCON disabled)',
      'port' => 'put 1 (if RCON disabled)',
      'password' => 'put 1 (if RCON disabled)',
      'host_websocket' => 'your_ip',
      'port_websocket' => '6666',
      'password_websocket' => 'socket_password',
    ),
  ),
  'discord' => 
  array (
    'webhook' => 'discord_webhook_url',
  ),
);
?>