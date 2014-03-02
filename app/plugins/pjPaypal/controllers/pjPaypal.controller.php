<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once dirname(__FILE__) . '/pjPaypalAppController.controller.php';
class pjPaypal extends pjPaypalAppController
{
	public function pjActionConfirm()
	{
		$params = $this->getParams();
		if (!isset($params['key']) || $params['key'] != md5($this->option_arr['private_key'] . PJ_SALT))
		{
			return FALSE;
		}
		
		$response = array(
			'status' => 'FAIL'
		);

		$url = PJ_TEST_MODE ? 'ssl://sandbox.paypal.com' : 'ssl://www.paypal.com';
		$host = PJ_TEST_MODE ? 'www.sandbox.paypal.com' : 'www.paypal.com';
		$port = 443;
		$timeout = 30;
		
		$this->log('PayPal');

		if (count($params) == 0)
		{
			$this->log('No such booking');
			exit;
		}

		// STEP 1: Read POST data
		$req = 'cmd=_notify-validate';
		if (function_exists('get_magic_quotes_gpc'))
		{
			$get_magic_quotes_exists = true;
		}
		foreach ($_POST as $key => $value)
		{
			if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1)
			{
				$value = urlencode(stripslashes($value));
			} else {
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}

		// STEP 2: Post IPN data back to paypal to validate
		$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
		$header .= "Host: $host\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$fp = fsockopen($url, $port, $errno, $errstr, $timeout);
		
		// assign posted variables to local variables
		$txn_id           = $_POST['txn_id'];
		$payment_status   = $_POST['payment_status'];
		$payment_amount   = $_POST['mc_gross'];
		$receiver_email   = $_POST['receiver_email'];
		$payment_currency = $_POST['mc_currency'];
		
		$response['transaction_id'] = $_POST['txn_id'];
		
		if (!$fp)
		{
			$this->log('HTTP error');
		} else {
			fwrite($fp, $header . $req);
			while (!feof($fp))
			{
				$buffer = fgets($fp, 1024);
				// STEP 3: Inspect IPN validation result and act accordingly
				if (strcasecmp(trim($buffer), "VERIFIED") == 0)
				{
					$this->log('VERIFIED');
					//if ($payment_status == "Completed")
					//{
						//$this->log('Completed');
						if ($txn_id != $params['txn_id'])
						{
							$this->log('TXN_ID is OK');
							if ($receiver_email == $params['paypal_address'])
							{
								$this->log('EMAIL address is OK');
								if ($payment_amount == $params['deposit'] && $payment_currency == $params['currency'])
								{
									$this->log('AMOUNT is OK, proceed with booking update');
									$response['status'] = 'OK';
									return $response;
								} else {
									$this->log('AMOUNT or CURRENCY didn\'t match');
								}
							} else {
								$this->log('EMAIL address didn\'t match');
							}
						} else {
							$this->log('TXN_ID is the same.');
						}
					//} else {
						//$this->log('Not Completed');
					//}
			    } elseif (strcasecmp($buffer, "INVALID") == 0) {
			    	$this->log('INVALID');
			  	}
			}
			fclose($fp);
		}
		return $response;
	}
		
	public function pjActionForm()
	{
		$this->setAjax(true);
		//KEYS:
		//-------------
		//name
		//id
		//business
		//item_name
		//custom
		//amount
		//currency_code
		//return
		//notify_url
		//submit
		//submit_class
		//target
		$this->set('arr', $this->getParams());
	}
}
?>