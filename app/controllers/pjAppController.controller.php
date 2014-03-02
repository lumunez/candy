<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
if (is_file(PJ_FRAMEWORK_PATH . 'pjController.class.php'))
{
	require_once PJ_FRAMEWORK_PATH . 'pjController.class.php';
}
class pjAppController extends pjController
{
	public $models = array();

	public $defaultLocale = 'admin_locale_id';
	
	public static function setTimezone($timezone="UTC")
    {
    	if (in_array(version_compare(phpversion(), '5.1.0'), array(0,1)))
		{
			date_default_timezone_set($timezone);
		} else {
			$safe_mode = ini_get('safe_mode');
			if ($safe_mode)
			{
				putenv("TZ=".$timezone);
			}
		}
    }

	public static function setMySQLServerTime($offset="-0:00")
    {
		mysql_query("SET SESSION time_zone = '$offset';");
    }
    
	public function setTime()
	{
		if (isset($this->option_arr['o_timezone']))
		{
			$offset = $this->option_arr['o_timezone'] / 3600;
			if ($offset > 0)
			{
				$offset = "-".$offset;
			} elseif ($offset < 0) {
				$offset = "+".abs($offset);
			} elseif ($offset === 0) {
				$offset = "+0";
			}
	
			pjAppController::setTimezone('Etc/GMT' . $offset);
			if (strpos($offset, '-') !== false)
			{
				$offset = str_replace('-', '+', $offset);
			} elseif (strpos($offset, '+') !== false) {
				$offset = str_replace('+', '-', $offset);
			}
			pjAppController::setMySQLServerTime($offset . ":00");
		}
	}
    
    public function beforeFilter()
    {
    	$this->appendJs('jquery-1.8.2.min.js', PJ_THIRD_PARTY_PATH . 'jquery/');
    	$this->appendJs('pjAdminCore.js');
    	$this->appendCss('reset.css');
    	
    	$this->appendJs('jquery-ui.custom.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/js/');
		$this->appendCss('jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/css/smoothness/');
				
		$this->appendCss('admin.css');
		$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		
    	if ($_GET['controller'] != 'pjInstaller')
		{
			$this->models['Option'] = pjOptionModel::factory();
			$this->option_arr = $this->models['Option']->getPairs($this->getForeignId());
			$this->set('option_arr', $this->option_arr);
			$this->setTime();
		}
    
		if (!isset($_SESSION[$this->defaultLocale]))
		{
			$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
			if (count($locale_arr) === 1)
			{
				$this->setLocaleId($locale_arr[0]['id']);
			}
		}
		
		pjAppController::setFields($this->getLocaleId());
    }
    
    public function getForeignId()
    {
    	return 1;
    }
    
    public static function getTokens($booking_arr, $option_arr)
    {
    	$search = array(
			'{Name}', '{Email}', '{Phone}', '{Notes}',
			'{CCType}', '{CCNum}', '{CCExp}', '{CCSec}',
			'{PaymentMethod}', '{ListingID}',
			'{StartDate}', '{EndDate}', '{ReservationID}',
			'{Deposit}', '{Price}', '{Tax}', '{Security}', '{Total}');
		$replace = array(
			$booking_arr['name'], $booking_arr['email'], $booking_arr['phone'], $booking_arr['notes'],
			$booking_arr['cc_type'], $booking_arr['cc_num'], ($booking_arr['payment_method'] == 'creditcard' ? $booking_arr['cc_exp'] : NULL), $booking_arr['cc_code'],
			$booking_arr['payment_method'], $booking_arr['listing_id'],
			$booking_arr['date_from'], $booking_arr['date_to'], $booking_arr['id'],
			$booking_arr['deposit'] . " " . $option_arr['o_currency'], $booking_arr['amount'] . " " . $option_arr['o_currency'], $booking_arr['tax'] . " " . $option_arr['o_currency'], $booking_arr['security'] . " " . $option_arr['o_currency'], ($booking_arr['amount'] + $booking_arr['security'] + $booking_arr['tax']) . " " . $option_arr['o_currency']
		);
		return compact('search', 'replace');
    }
    
    public static function setFields($locale)
    {
		$fields = pjMultiLangModel::factory()
			->select('t1.content, t2.key')
			->join('pjField', "t2.id=t1.foreign_id", 'inner')
			->where('t1.locale', $locale)
			->where('t1.model', 'pjField')
			->where('t1.field', 'title')
			->findAll()
			->getDataPair('key', 'content');
		$registry = pjRegistry::getInstance();
		$tmp = array();
		if ($registry->is('fields'))
		{
			$tmp = $registry->get('fields');
		}
		$arrays = array();
		foreach ($fields as $key => $value)
		{
			if (strpos($key, '_ARRAY_') !== false)
			{
				list($prefix, $suffix) = explode("_ARRAY_", $key);
				if (!isset($arrays[$prefix]))
				{
					$arrays[$prefix] = array();
				}
				$arrays[$prefix][$suffix] = $value;
			}
		}
		require PJ_CONFIG_PATH . 'settings.inc.php';
		$fields = array_merge($tmp, $fields, $settings, $arrays);
		$registry->set('fields', $fields);
    }

    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}
	
	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}
	
	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}

	public function isEditor()
	{
		return $this->getRoleId() == 2;
	}

	public function isOwner()
	{
		return $this->getRoleId() == 3;
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}
	
	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}
	
	public function friendlyURL($str, $divider='-')
	{
		$str = mb_strtolower($str, mb_detect_encoding($str)); // change everything to lowercase
		$str = trim($str); // trim leading and trailing spaces
		$str = preg_replace('/[_|\s]+/', $divider, $str); // change all spaces and underscores to a hyphen
		$str = preg_replace('/\x{00C5}/u', 'AA', $str);
		$str = preg_replace('/\x{00C6}/u', 'AE', $str);
		$str = preg_replace('/\x{00D8}/u', 'OE', $str);
		$str = preg_replace('/\x{00E5}/u', 'aa', $str);
		$str = preg_replace('/\x{00E6}/u', 'ae', $str);
		$str = preg_replace('/\x{00F8}/u', 'oe', $str);
		$str = preg_replace('/[^a-z\x{0400}-\x{04FF}0-9-]+/u', '', $str); // remove all non-cyrillic, non-numeric characters except the hyphen
		$str = preg_replace('/[-]+/', $divider, $str); // replace multiple instances of the hyphen with a single instance
		$str = preg_replace('/^-+|-+$/', '', $str); // trim leading and trailing hyphens
		return $str;
	}

	public function notify($notification_id, $user_id=NULL, $params=array())
	{
		$map = array(
			1 => array('o_email_new_user_subject', 'o_email_new_user', 'o_sms_new_user'),
			2 => array('o_email_new_property_subject', 'o_email_new_property', 'o_sms_new_property'),
			3 => array('o_email_new_reservation_subject', 'o_email_new_reservation', 'o_sms_new_reservation'),
			4 => array('o_email_new_reservation_subject', 'o_email_new_reservation', 'o_sms_new_reservation'),
			5 => array('o_email_reservation_cancelled_subject', 'o_email_reservation_cancelled', 'o_sms_reservation_cancelled'),
			6 => array('o_email_reservation_cancelled_subject', 'o_email_reservation_cancelled', 'o_sms_reservation_cancelled')
		);
		
		$pjUserNotificationModel = pjUserNotificationModel::factory()
			->select('t1.type, t2.email, t2.phone')
			->join('pjUser', "t2.id=t1.user_id AND t2.status='T'", 'inner')
			->where('t1.notification_id', $notification_id);

		if (!is_null($user_id))
		{
			$pjUserNotificationModel->where('t1.user_id', $user_id);
		}
		$recipients = $pjUserNotificationModel->findAll()->getData();
		
		$pjEmail = new pjEmail();
		$smsPlugin = (pjObject::getPlugin('pjSms') !== NULL);
		
		foreach ($recipients as $recipient)
		{
			switch ($recipient['type'])
			{
				case 'email':
					if (empty($recipient['email']))
					{
						continue;
					}
					if ($this->option_arr['o_send_email'] == 'smtp')
					{
						$pjEmail
							->setTransport('smtp')
							->setSmtpHost($this->option_arr['o_smtp_host'])
							->setSmtpPort($this->option_arr['o_smtp_port'])
							->setSmtpUser($this->option_arr['o_smtp_user'])
							->setSmtpPass($this->option_arr['o_smtp_pass'])
						;
					}
					
					$body = $this->option_arr[@$map[$notification_id][1]];
					switch ($notification_id)
					{
						case 1:
							$body = str_replace(array('{Name}', '{Email}'), array(@$params['name'], @$params['email']), $body);
							break;
						case 2:
							$body = str_replace(array('{PropertyID}', '{RefID}'), array(@$params['property_id'], @$params['listing_refid']), $body);
							break;
						default:
							$body = str_replace(array('{ReservationID}'), array(@$params['reservation_id']), $body);
							break;
					}
					
					$pjEmail->setFrom($recipient['email'])
						->setTo($recipient['email'])
						->setSubject($this->option_arr[@$map[$notification_id][0]])
						->send($body);
					break;
				case 'sms':
					if (empty($recipient['phone']) || !$smsPlugin)
					{
						continue;
					}
					$this->requestAction(array(
						'controller' => 'pjSms',
						'action' => 'pjActionSend',
						'params' => array(
							'number' => $recipient['phone'],
							'text' => $this->option_arr[@$map[$notification_id][2]],
							'key' => md5($this->option_arr['private_key'] . PJ_SALT)
						)
					), array('return'));
					break;
			}
		}
	}
}
?>