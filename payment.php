<?php
define("TKM", true);
require(dirname(__FILE__) . "/system.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$username = TextSave(@$_POST['username']);
	$selid = (int) TextSave(@$_POST['selid']);
	$server = (int) TextSave(@$_POST['server']);

	if (empty($username) OR $selid == 0 or $server < 0)	{
		MessageSend(1, "Fill in all the fields of the form!", 'index.php');
	}
	switch (@$_POST['merchant']) {
		case 'paypal':
			if (!$config['merchant']['paypal_enable']) {
				MessageSend(1, "Payment system not included!", 'index.php');
			}
			break;

		case 'g2a':
			if (!$config['merchant']['g2a_enable']) {
				MessageSend(1, "Payment system not included!", 'index.php');
			}
			break;

		case 'interkassa':
			if (!$config['merchant']['ik_enable']) {
				MessageSend(1, "Payment system not included!", 'index.php');
			}
			break;

		case 'unitpay':
			if (!$config['merchant']['up_enable']) {
				MessageSend(1, "Payment system not included!", 'index.php');
			}
			break;

		case 'freekassa':
			if (!$config['merchant']['fk_enable']) {
				MessageSend(1, "Payment system not included!", 'index.php');
			}
			break;

		case 'robokassa':
			if (!$config['merchant']['rk_enable']) {
				MessageSend(1, "Payment system not included!", 'index.php');
			}
			break;

		case 'mykassa':
			if (!$config['merchant']['mk_enable']) {
				MessageSend(1, "Payment system not included!", 'index.php');
			}
			break;

		case 'webmoney':
			if (!$config['merchant']['wm_enable']) {
				MessageSend(1, "Payment system not included!", 'index.php');
			}
			break;

		case 'walletone':
			if (!$config['merchant']['wmi_enable']) {
				MessageSend(1, "Payment system not included!", 'index.php');
			}
			break;

        case 'fondy':
            if (!$config['merchant']['fondy_enable']) {
                MessageSend(1, "Payment system not included!", 'index.php');
            }
            break;

        case 'check':
            if (!$config['merchant']['2check_enable']) {
                MessageSend(1, "Payment system not included!", 'index.php');
            }
            break;

        case 'paysera':
            if (!$config['merchant']['paysera_enable']) {
                MessageSend(1, "Payment system not included!", 'index.php');
            }
            break;

        default:
			exit;
			break;
	}

	if (isset($_POST['server']) &&
		(int)$_POST['server'] <= (count($config['servers']) - 1) &&
		(int)$_POST['server'] >= 0
		) {
		$server = (int)$_POST['server'];
	} else {
		MessageSend(1, "Choose a server!");
	}

	$row = $link->prepare("SELECT * FROM `AD_GOODS` WHERE `id` = ?");
	$row->execute(array($selid));

	if ($row) {
		$row = $row->fetch(PDO::FETCH_ASSOC);
	}

	if (empty($row['name'])) {
		MessageSend(1, "Unknown package!", 'index.php');
	}

	if ($server != $row['server'] && $row['server'] != '*') {
		MessageSend(1, "Unknown package!", 'index.php');
	}

	if (isset($_POST['dosubm']) && (empty($row['dobuy']) || $row['dobuy'] == 0)) {
		MessageSend(1, "This product is not possible to pay extra!", 'index.php');
	} elseif (isset($_POST['dosubm']) && $row['dobuy'] == 1) {
		$dobuy = true;
	} else {
		$dobuy = false;
	}

	if ((int)$row['count'] <= 0 && $row['count'] != '-') {
		MessageSend(1, "Package is over!", 'index.php');
	}

	if ($row['cupon'] != NULL and $row['cupon'] > 0) {
		$t = ($row['cost'] * $row['cupon']) / 100;
		$cost = $row['cost'] - $t;
	} else $cost = $row['cost'];

	if (isset($_POST['cupon']) AND !empty($_POST['cupon'])) {
		$cuponname = TextSave(@$_POST['cupon']);

		$crow = $link->prepare("SELECT * FROM `AD_CUPONS` WHERE `name` = ?");
		$crow->execute(array($cuponname));

		if ($crow) {
			$crow = $crow->fetch(PDO::FETCH_ASSOC);
		}

		if (empty($crow['name'])) {
			MessageSend(1, "Coupon is not valid!", 'index.php');
		}

		if ($crow['count'] <= 0) {
			$delCupon = $link->prepare("DELETE FROM `AD_CUPONS` WHERE `name` = ?");
			$delCupon->execute(array($cuponname));
			MessageSend(1, "Coupon is not valid!", 'index.php');
		}

		$t = ($cost * $crow['summ']) / 100;
		$cost = $cost - $t;

		if ($cost < 0) {
			MessageSend(1, "Price must be higher than 0!", 'index.php');
		}

		if ($crow['count'] > 1)	{
			$updCupon = $link->prepare("UPDATE `AD_CUPONS` SET `count` = `count` - 1 WHERE `name` = ?");
			$updCupon->execute(array($cuponname));
		} else {
			$delCupon = $link->prepare("DELETE FROM `AD_CUPONS` WHERE `name` = ?");
			$delCupon->execute(array($cuponname));
		}
	}


	if ($dobuy) {
		$drow = $link->prepare("SELECT * FROM `AD_BUYERS` WHERE `username` = ? AND `server` = ? ORDER BY `cost` DESC");
		$drow->execute(array($username, $server));

		if ($drow) {
			$drow = $drow->fetch(PDO::FETCH_ASSOC);
		}

		if ($drow['good'] == $row['id']) {
			MessageSend(1, "You already have this package!", 'index.php');
		}

		$dobuyCheckGoods = $link->prepare("SELECT `dobuy` FROM `AD_GOODS` WHERE `id` = ?");
		$dobuyCheckGoods->execute(array($drow['good']));

		if ($dobuyCheckGoods) {
			$dobuyCheckGoods = $dobuyCheckGoods->fetch(PDO::FETCH_ASSOC)['dobuy'];
		}

		if (!empty($drow['id']) &&
			(@$dobuyCheckGoods == 1)
		) {
			$cost -= (int)$drow['cost'];
		}
	}

	if ($cost < 0) {
		MessageSend(1, "Price must be higher than 0!", 'index.php');
	}

	$data = array(
		"goods_id" => $row['id'],
		"goods_name" => $row['name'],
		"goods_cost" => $row['cost'],
		"goods_cupon" => $row['cupon'],
		"cost" => $cost,
	);

	if ($dobuy) {
		$data['dobuy'] = true;
	}

	if (isset($cuponname)) {
		$data['cupon'] = $cuponname;
	}

	$data = @json_encode($data, JSON_UNESCAPED_UNICODE);

	$payment = new Payment;
	$payment->create($username, $data, $server);
	$payid = $payment->payment_id;

	if ($cost == 0) {
		$payment->select($payment->payment_id);
		$payment->give();
		MessageSend(3, "You have successfully purchased the package: " . $row['name'], 'index.php');
	}

	switch (@$_POST['merchant']) {
		case 'paypal':
			require_once("payments/paypal_lib.php");
			$paypal = new PayPal($paypalConfig);
			$result = $paypal->call(array(
			'method'  => 'SetExpressCheckout',
			'paymentrequest_0_paymentaction' => 'sale',
			'paymentrequest_0_amt' => $cost,
			'paymentrequest_0_currencycode' => $config['merchant']['paypal_currency_code'],
			'returnurl'  => 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/payments/paypal.php?username='.$payid.'&item='.$row['name'].'&ip='.$_SERVER['REMOTE_ADDR'],
			'cancelurl'  => 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']),
			));

			if ($result['ACK'] == 'Success') {
				$paypal->redirect($result);
			} else {
				var_dump($paypalConfig);
				var_dump($result);
				//var_dump($result);
				echo 'Handle the payment creation failure <br>';
			}
			break;

        case 'paysera':
            require_once("payments/WebToPay.php");

            function get_self_url() {
                $s = substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0,
                    strpos($_SERVER['SERVER_PROTOCOL'], '/'));

                if (!empty($_SERVER["HTTPS"])) {
                    $s .= ($_SERVER["HTTPS"] == "on") ? "s" : "";
                }

                $s .= '://'.$_SERVER['HTTP_HOST'];

                if (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
                    $s .= ':'.$_SERVER['SERVER_PORT'];
                }

                $s .= dirname($_SERVER['SCRIPT_NAME']);

                return $s;
            }

            try {
                $self_url = get_self_url();

                $request = WebToPay::redirectToPayment(array(
                    'projectid'     => $config['merchant']['paysera_projectid'],
                    'sign_password' => $config['merchant']['paysera_sign_password'],
                    'orderid'       => $payid,
                    'amount'        => $cost,
                    'currency'      => $config['merchant']['paysera_currency'],
                    'country'       => $config['merchant']['paysera_country'],
                    'accepturl'     => $self_url.'/',
                    'cancelurl'     => $self_url.'/',
                    'callbackurl'   => $self_url.'/payments/paysera.php',
                    'test'          => 0,
                ));
            } catch (WebToPayException $e) {
                // handle exception
            }
            break;

        case 'check':
            exit("
                <form id=pay name=pay action='https://2checkout.com/checkout/purchase' method='post'>
                  <input type='hidden' name='sid' value='" . $config['merchant']['2check_id'] . "' />
                  <input type='hidden' name='mode' value='2CO' />
                  <input type='hidden' name='li_0_name' value='" . 'Product purchase ' . $row['name'] . "' />
                  <input type='hidden' name='li_0_price' value='" . $cost . "' />
                  <input type='hidden' name='li_0_tangible' value='N' />
                  <input type='hidden' name='currency_code' value='" . $config['merchant']['2check_currency'] . "' />
                  <input type='hidden' name='merchant_order_id' value='" . $payid . "'>
                  <input type='hidden' name='demo' value='Y' />
                  <input type='submit' />
                </form>
			    <script type='text/javascript'>
					document.getElementById(\"pay\").submit();
				</script>
            ");
            break;

		case 'g2a':
			require_once("payments/g2apay_lib.php");
			$g2apay = new G2APay($g2aConfig["hash"],$g2aConfig["secret"],$g2aConfig["email"]);
			$orderId = 1; // Generate or save in your database
			$extras = [];
			$result = $g2apay->createOrder(array($orderId, $extras));
			if (isset($result['success']) && $result['success'] !== false){
				header("Location: ".$result['url']);
				exit();
			} else {
				var_dump($result);
			}
			break;

		case 'fondy':
            function getSignature( $merchant_id , $password , $params = array() ){
                $params['merchant_id'] = $merchant_id;
                $params = array_filter($params,'strlen');
                ksort($params);
                $params = array_values($params);
                array_unshift( $params , $password );
                $params = join('|',$params);
                return(sha1($params));
            }

            $params = [
                'server_callback_url' => 'http://local.minecraft.ru/payments/fondy.php',
                'response_url' => 'http://local.minecraft.ru/',
                'order_id' => $payid,
                'merchant_id' => $config['merchant']['fondy_id'],
                'order_desc' => 'Product purchase ' . $row['name'],
                'amount' => $cost,
                'currency' => $config['merchant']['fondy_currency']
            ];

			exit('<form id=pay name=pay method="POST" action="https://api.fondy.eu/api/checkout/redirect/">
                <input type="hidden" name="server_callback_url" value="' . $params['server_callback_url'] . '">
                <input type="hidden" name="response_url" value="' . $params['response_url'] . '">
				<input type="hidden" name="order_id" value="' . $params['order_id'] . '">
				<input type="hidden" name="merchant_id" value="' . $config['merchant']['fondy_id'] . '">
				<input type="hidden" name="order_desc" value="Product purchase ' . $row['name'] . '">
				<input type="hidden" name="signature" 
				    value="' . getSignature($config['merchant']['fondy_id'], $config['merchant']['fondy_signature'], $params) . '"
				>
				<input type="hidden" name="amount" value="' . $params['amount'] . '">
				<input type="hidden" name="currency" value="' . $config['merchant']['fondy_currency'] . '">
				<input type="submit">
			  </form>
			  <script type="text/javascript">
					document.getElementById(\'pay\').submit();
				</script>
			');
		break;

		case 'freekassa':
			$sign = md5($config['merchant']['fk_shop_id'].':'.$cost.':'.$config['merchant']['fk_shop_key_1'].':'.$payid);
			exit(header("Location: http://www.free-kassa.ru/merchant/cash.php?m={$config['merchant']['fk_shop_id']}&oa={$cost}&o={$payid}&s={$sign}"));
			break;

		case 'robokassa':
			$sign = md5($config['merchant']['rk_shop_id'].':'.$cost.':'.$payid.':'.$config['merchant']['rk_shop_key_1']);
			exit(header("Location: https://merchant.roboxchange.com/Index.aspx?MrchLogin={$config['merchant']['rk_shop_id']}&OutSum={$cost}&InvId={$payid}&Desc=Product purchase {$row['name']}&SignatureValue={$sign}"));
			break;

		case 'mykassa':
			$sign = md5($config['merchant']['mk_shop_id'].':'.$cost.':'.$config['merchant']['mk_shop_key_1'].':'.$payid);
			exit(header("Location: http://www.mykassa.org/api/merchant.php?m={$config['merchant']['mk_shop_id']}&oa={$cost}&o={$payid}&s={$sign}"));
			break;

		case 'unitpay':
			$sign = hash('sha256', "ID_{$payid}{up}Product purchase: {$row['name']}{up}{$cost}{up}{$config['merchant']['up_secret_key']}");
			exit(header("Location: https://unitpay.ru/pay/{$config['merchant']['up_shop_id']}?sum={$cost}&account={$payid}&desc=Product purchase: {$row['name']}&signature={$sign}"));
			break;

		case 'interkassa':
			exit(header("Location: https://sci.interkassa.com/?ik_co_id={$config['merchant']['ik_shop_id']}&ik_pm_no=ID_{$payid}&ik_am={$cost}&ik_cur=RUB&ik_desc=Product purchase {$row['name']}"));
			break;

		case 'webmoney':
		  exit('<form id=pay name=pay method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp"> 
					<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="' . $cost . '">
  					<input type="hidden" name="LMI_PAYMENT_DESC"   value="Product purchase ' . $row['name'] . '">
  					<input type="hidden" name="LMI_PAYMENT_NO"     value="' . $payid . '">
  					<input type="hidden" name="LMI_PAYEE_PURSE"    value="' . $config['merchant']['wm_purse'] . '">
  					<input type="hidden" name="LMI_SIM_MODE"       value="0">
  					<input type="submit" value="Purchase!">
				</form>
				<script type="text/javascript">
					document.getElementById(\'pay\').submit();
				</script>');
			break;

		case 'walletone':
			$fields = array();
			$fields["WMI_MERCHANT_ID"]    = $config['merchant']['wmi_shop_id'];
			$fields["WMI_PAYMENT_AMOUNT"] = $cost;
			$fields["WMI_CURRENCY_ID"]    = "643";
			$fields["WMI_PAYMENT_NO"]     = $payid;
			$fields["WMI_DESCRIPTION"]    = "Product purchase: " . $row['name'];
			$fields["WMI_SUCCESS_URL"]    = "http://" . $config['main']['mainurl'] . "/?pstatus=success";
			$fields["WMI_FAIL_URL"]       = "http://" . $config['main']['mainurl'] . "/?pstatus=fail";

			foreach($fields as $name => $val) {
				if(is_array($val)) {
					usort($val, "strcasecmp");
					$fields[$name] = $val;
				}
			}

			uksort($fields, "strcasecmp");
			$fieldValues = "";

			foreach($fields as $value) {
				if(is_array($value)) {
					foreach($value as $v) {
						$v = iconv("utf-8", "windows-1251", $v);
						$fieldValues .= $v;
					}
				} else {
					$value = iconv("utf-8", "windows-1251", $value);
					$fieldValues .= $value;
				}
			}

			$signature = base64_encode(pack("H*", md5($fieldValues . $config['merchant']['wmi_secret_key'])));

			exit('<form id="pay" name="pay" method="POST" action="https://wl.walletone.com/checkout/checkout/Index">
					<input type="hidden" name="WMI_MERCHANT_ID"    value="' . $config['merchant']['wmi_shop_id'] .'">
					<input type="hidden" name="WMI_PAYMENT_AMOUNT" value="' . $cost . '">
					<input type="hidden" name="WMI_PAYMENT_NO"     value="' . $payid . '">
					<input type="hidden" name="WMI_CURRENCY_ID"    value="643">
					<input type="hidden" name="WMI_SIGNATURE"      value="' . $signature . '">
					<input type="hidden" name="WMI_DESCRIPTION"    value="Product purchase: ' . $row['name'] . '"">
					<input type="hidden" name="WMI_SUCCESS_URL"    value="http://' . $config['main']['mainurl'] .'/?pstatus=success">
					<input type="hidden" name="WMI_FAIL_URL"       value="http://' . $config['main']['mainurl'] .'/?pstatus=fail">
					<input type="hidden" type="submit">
				</form>
				<script type="text/javascript">
					document.getElementById(\'pay\').submit();
				</script>');
			break;
	}

	exit("Err.");
} else {
	exit("Hacking attempt!");
}