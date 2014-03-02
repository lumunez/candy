<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjFront.controller.php';
class pjListings extends pjFront
{
	public $defaultSearch = 'Search';
	
	private $isoDatePattern = '/\d{4}-\d{2}-\d{2}/';
	
	public function pjActionIndex()
	{
		pjObject::import('Model', 'pjGallery:pjGallery');
		
		$pjListingModel = pjListingModel::factory()
			->where("(t1.status = 'T' OR (t1.status = 'E' AND t1.expire >= CURDATE()))");

		$page = isset($_GET['pjPage']) && (int) $_GET['pjPage'] > 0 ? intval($_GET['pjPage']) : 1;

		$count = $pjListingModel->findCount()->getData();
		$row_count = (int) $this->option_arr['o_per_page'] > 0 ? intval($this->option_arr['o_per_page']) : 10;
		$pages = ceil($count / $row_count);
		$offset = ((int) $page - 1) * $row_count;

		switch ($this->option_arr['o_sort_by'])
		{
			case 'created_asc':
				$pjListingModel->orderBy("FIELD(is_featured, 'T', 'F') ASC, created ASC, listing_title ASC", false);
				break;
			case 'created_desc':
				$pjListingModel->orderBy("FIELD(is_featured, 'T', 'F') ASC, created DESC, listing_title ASC", false);
				break;
			case 'random':
				$pjListingModel->orderBy("FIELD(is_featured, 'T', 'F') ASC, RAND()", false);
				break;
			case 'popularity':
			default:
				$pjListingModel->orderBy("FIELD(is_featured, 'T', 'F') ASC, views DESC, listing_title ASC", false);
				break;
		}
		
		$arr = $pjListingModel
			->select(sprintf("t1.*, t2.content AS type_title, t3.content AS country_title, t4.content AS listing_title, t5.content AS listing_description,
				(SELECT `medium_path` FROM `%1\$s` WHERE `foreign_id` = `t1`.`id` ORDER BY `sort` ASC LIMIT 1) AS `pic`,
				(SELECT MIN(`price`) FROM `%2\$s` WHERE `listing_id` = `t1`.`id` LIMIT 1) AS `price`
			", pjGalleryModel::factory()->getTable(), pjPriceModel::factory()->getTable()))
			->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.type_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.country_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t4.model='pjListing' AND t4.foreign_id=t1.id AND t4.field='title' AND t4.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t5.model='pjListing' AND t5.foreign_id=t1.id AND t5.field='description' AND t5.locale='".$this->getLocaleId()."'", 'left')
			->limit($row_count, $offset)->findAll()->getData();
			
		$this->set('extra_arr', pjExtraModel::factory()->where('t1.status', 'T')->findAll()->getData());
		$this->set('paginator', array('pages' => $pages, 'row_count' => $row_count, 'count' => $count));

		$this->set('type_arr', pjTypeModel::factory()
			->select('t1.*, t2.content AS type_title')
			->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
			->where('t1.status', 'T')->orderBy('type_title ASC')->findAll()->getData()
		);
			
		foreach ($arr as $key => $value)
		{
			$arr[$key]['extras'] = pjListingExtraModel::factory()->where('t1.listing_id', $value['id'])->findAll()->getDataPair(NULL, 'extra_id');
		}
		$this->set('arr', $arr);
		
		if (in_array($this->option_arr['o_layout'], array('layout_2_list', 'layout_2_grid')))
		{
			$this->appendJs('calendar-1.5.1.min.js', PJ_THIRD_PARTY_PATH . 'calendarjs/');
			$this->appendCss('calendar.css', PJ_THIRD_PARTY_PATH . 'calendarjs/themes/sky-blue/');
		}
		
		$this->appendJs('jabb-0.4.3.min.js', PJ_LIBS_PATH . 'jabb/');
		$this->appendJs('pjListings.js');
	}

	public function pjActionSearch()
	{
		if (isset($_GET['listing_search']))
		{
			pjObject::import('Model', 'pjGallery:pjGallery');
			
			$pjListingModel = pjListingModel::factory();

			$date_from = pjUtil::formatDate($_GET['date_from'], $this->option_arr['o_date_format']);
			$date_to = pjUtil::formatDate($_GET['date_to'], $this->option_arr['o_date_format']);
			
			$_SESSION[$this->defaultSearch] = array_merge($_GET, compact('date_from', 'date_to'));

			$p_from = preg_match($this->isoDatePattern, $date_from) ? strtotime($date_from) : null;
			$p_to = preg_match($this->isoDatePattern, $date_to) ? strtotime($date_to) : null;
			$r_arr = array();
			
			if (!is_null($p_from) || !is_null($p_to))
			{
				$reservation_arr = pjReservationModel::factory()->where('t1.status', 'Confirmed')->findAll()->getData();

				foreach ($reservation_arr as $booking)
				{
					$b_from = strtotime($booking['date_from']);
					$b_to   = strtotime($booking['date_to']);
					if (
					($b_from <= $p_from && $b_to >= $p_to) ||
					($b_from >= $p_from && $b_to >= $p_to && $b_from <= $p_to) ||
					($b_from <= $p_from && $b_to <= $p_to && $b_to >= $p_from) ||
					($b_from >= $p_from && $b_to <= $p_to)
					)
					{
						$r_arr[] = $booking['listing_id'];
					}
				}
			}

			if (count($r_arr) > 0)
			{
				$pjListingModel->whereNotIn('t1.id', $r_arr);
			}

			$pjListingModel->where("(t1.status = 'T' OR (t1.status = 'E' AND t1.expire >= CURDATE()))");
			if (isset($_GET['type_id']) && (int) $_GET['type_id'] > 0)
			{
				$pjListingModel->where('t1.type_id', $_GET['type_id']);
			}
			if (isset($_GET['country_id']) && (int) $_GET['country_id'] > 0)
			{
				$pjListingModel->where('t1.country_id', $_GET['country_id']);
			}
			if (!empty($_GET['refid']))
			{
				$pjListingModel->where("t1.listing_refid LIKE '%" . pjObject::escapeString($_GET['refid']) . "%'");
			}
			if ((int) $_GET['bedrooms_from'] > 0)
			{
				$pjListingModel->where('t1.listing_bedrooms >=', $_GET['bedrooms_from']);
			}
			if ((int) $_GET['bedrooms_to'] > 0)
			{
				$pjListingModel->where('t1.listing_bedrooms <=', $_GET['bedrooms_to']);
			}
			if ((float) $_GET['bathrooms_from'] > 0)
			{
				$pjListingModel->where('t1.listing_bathrooms >=', $_GET['bathrooms_from']);
			}
			if ((float) $_GET['bathrooms_to'] > 0)
			{
				$pjListingModel->where('t1.listing_bathrooms <=', $_GET['bathrooms_to']);
			}
			if ((int) $_GET['adults_from'] > 0)
			{
				$pjListingModel->where('t1.listing_adults >=', $_GET['adults_from']);
			}
			if ((int) $_GET['adults_to'] > 0)
			{
				$pjListingModel->where('t1.listing_adults <=', $_GET['adults_to']);
			}
			if ((int) $_GET['children_from'] > 0)
			{
				$pjListingModel->where('t1.listing_children >=', $_GET['children_from']);
			}
			if ((int) $_GET['children_to'] > 0)
			{
				$pjListingModel->where('t1.listing_children <=', $_GET['children_to']);
			}
			if (isset($_GET['address_state']) && !empty($_GET['address_state']))
			{
				$pjListingModel->where("t1.address_state LIKE '%". pjObject::escapeString($_GET['address_state']) ."%'");
			}
			if (isset($_GET['extra_id']) && is_array($_GET['extra_id']) && count($_GET['extra_id']) > 0)
			{
				$pjListingModel->where(sprintf("t1.id IN (SELECT `listing_id` FROM `%s` WHERE `extra_id` IN ('%s'))", pjListingExtraModel::factory()->getTable(), join("','", $_GET['extra_id'])));
			}

			$page = isset($_GET['pjPage']) && (int) $_GET['pjPage'] > 0 ? intval($_GET['pjPage']) : 1;
			$count = $pjListingModel->findCount()->getData();
			$row_count = (int) $this->option_arr['o_per_page'] > 0 ? intval($this->option_arr['o_per_page']) : 10;
			$pages = ceil($count / $row_count);
			$offset = ((int) $page - 1) * $row_count;

			switch ($this->option_arr['o_sort_by'])
			{
				case 'created_asc':
					$pjListingModel->orderBy("FIELD(is_featured, 'T', 'F') ASC, created ASC, listing_title ASC", false);
					break;
				case 'created_desc':
					$pjListingModel->orderBy("FIELD(is_featured, 'T', 'F') ASC, created DESC, listing_title ASC", false);
					break;
				case 'random':
					$pjListingModel->orderBy("FIELD(is_featured, 'T', 'F') ASC, RAND()", false);
					break;
				case 'popularity':
				default:
					$pjListingModel->orderBy("FIELD(is_featured, 'T', 'F') ASC, views DESC, listing_title ASC", false);
					break;
			}
		
			$arr = $pjListingModel
				->select(sprintf("t1.*, t2.content AS type_title, t3.content AS listing_title, t4.content AS listing_description,
					(SELECT `small_path` FROM `%1\$s` WHERE `foreign_id` = `t1`.`id` ORDER BY `sort` ASC LIMIT 1) AS `pic`,
					(SELECT MIN(`price`) FROM `%2\$s` WHERE `listing_id` = `t1`.`id` LIMIT 1) AS `price`
				", pjGalleryModel::factory()->getTable(), pjPriceModel::factory()->getTable()))
				->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.type_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->join('pjMultiLang', "t3.model='pjListing' AND t3.foreign_id=t1.id AND t3.field='title' AND t3.locale='".$this->getLocaleId()."'", 'left')
				->join('pjMultiLang', "t4.model='pjListing' AND t4.foreign_id=t1.id AND t4.field='description' AND t4.locale='".$this->getLocaleId()."'", 'left')
				->limit($row_count, $offset)
				//->debug(1)
				->findAll()
				->getData();

			$this->set('paginator', array('pages' => $pages, 'row_count' => $row_count, 'count' => $count));
			$this->set('extra_arr', pjExtraModel::factory()->where('t1.status', 'T')->findAll()->getData());
			foreach ($arr as $key => $value)
			{
				$arr[$key]['extras'] = pjListingExtraModel::factory()->where('t1.listing_id', $value['id'])->findAll()->getDataPair(NULL, 'extra_id');
			}
			$this->set('arr', $arr);
		}

		$this->set('country_arr', pjCountryModel::factory()
			->select('t1.*, t2.content AS country_title')
			->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
			->where('t1.status', 'T')->orderBy('country_title ASC')->findAll()->getData()
		);
		
		$this->pjActionGetStatesByCountry();
		
		$this->set('type_arr', pjTypeModel::factory()
			->select('t1.*, t2.content AS type_title')
			->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
			->where('t1.status', 'T')->orderBy('type_title ASC')->findAll()->getData()
		);
			
		$this->set('extra_arr', pjExtraModel::factory()
			->select('t1.*, t2.content AS extra_title')
			->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
			->where('t1.status', 'T')->orderBy('extra_title ASC')->findAll()->getData()
		);
		
		$this->appendJs('jabb-0.4.3.min.js', PJ_LIBS_PATH . 'jabb/');
		$this->appendJs('calendar-1.5.1.min.js', PJ_THIRD_PARTY_PATH . 'calendarjs/');
		$this->appendCss('calendar.css', PJ_THIRD_PARTY_PATH . 'calendarjs/themes/sky-blue/');
		$this->appendJs('pjListings.js');
	}

	public function pjActionSendRequest()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			if (!isset($_POST['verification']) || empty($_POST['verification']) || strtoupper($_POST['verification']) != $_SESSION[$this->defaultCaptcha])
			{
				$this->set('status', 4);
			} else {
				$passCheck = true;
				
				$_POST['date_from'] = pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']);
				$_POST['date_to'] = pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']);
				
				if (!preg_match($this->isoDatePattern, $_POST['date_from']) || !preg_match($this->isoDatePattern, $_POST['date_to']))
				{
					# Invalid dates
					$this->set('status', 5);
					$passCheck = false;
				}

				$p_from = strtotime($_POST['date_from']);
				$p_to = strtotime($_POST['date_to']);

				if ($p_from > $p_to)
				{
					# Invalid period
					$this->set('status', 6);
					$passCheck = false;
				}

				$today = strtotime(date("Y-m-d"));
				if ($p_from < $today || $p_to < $today)
				{
					# Past period
					$this->set('status', 8);
					$passCheck = false;
				}
				
				$numOfDays = ($p_to - $p_from) / 86400;
				$listing_arr = pjListingModel::factory()
					->select('t1.*, t2.email AS email_address, t3.content AS confirm_tokens, t4.content AS payment_tokens,
						t5.content AS confirm_subject, t6.content AS payment_subject')
					->join('pjUser', 't2.id=t1.owner_id', 'inner')
					->join('pjMultiLang', "t3.model='pjListing' AND t3.foreign_id=t1.id AND t3.field='confirm_tokens' AND t3.locale='".$this->getLocaleId()."'", 'left')
					->join('pjMultiLang', "t4.model='pjListing' AND t4.foreign_id=t1.id AND t4.field='payment_tokens' AND t4.locale='".$this->getLocaleId()."'", 'left')
					->join('pjMultiLang', "t5.model='pjListing' AND t5.foreign_id=t1.id AND t5.field='confirm_subject' AND t5.locale='".$this->getLocaleId()."'", 'left')
					->join('pjMultiLang', "t6.model='pjListing' AND t6.foreign_id=t1.id AND t6.field='payment_subject' AND t6.locale='".$this->getLocaleId()."'", 'left')
					->where('t1.id', $_POST['listing_id'])
					->limit(1)
					->findAll()->getData();
				
				if (count($listing_arr) === 1)
				{
					$listing_arr = $listing_arr[0];
				}
				
				if ((int) $listing_arr['o_price_based_on'] === 1)
				{
					$numOfDays += 1;
				}
					
				if ($numOfDays < (int) $listing_arr['o_min_booking_lenght'])
				{
					$this->set('status', 9);
					$passCheck = false;
				}
				if ($listing_arr['o_max_booking_lenght'] != '' && $numOfDays > (int) $listing_arr['o_max_booking_lenght'])
				{
					$this->set('status', 10);
					$passCheck = false;
				}

				if ($passCheck)
				{
					$ReservationModel = pjReservationModel::factory();

					$reservation_arr = $ReservationModel
						->where('t1.listing_id', $_POST['listing_id'])
						->where('t1.status', 'Confirmed')
						->findAll()
						->getData();
						
					$overlap = false;
					foreach ($reservation_arr as $booking)
					{
						$b_from = strtotime($booking['date_from']);
						$b_to   = strtotime($booking['date_to']);
						if ($listing_arr['o_price_based_on'] == 1)
						{
							if (
							($b_from <= $p_from && $b_to >= $p_to) ||
							($b_from >= $p_from && $b_to >= $p_to && $b_from <= $p_to) ||
							($b_from <= $p_from && $b_to <= $p_to && $b_to >= $p_from) ||
							($b_from >= $p_from && $b_to <= $p_to)
							)
							{
								$overlap = true;
								break;
							}
						} elseif ($listing_arr['o_price_based_on'] == 2) {
							if (
							($b_from <= $p_from && $b_to >= $p_to) ||
							($b_from >= $p_from && $b_to >= $p_to && $b_from < $p_to) ||
							($b_from <= $p_from && $b_to <= $p_to && $b_to > $p_from) ||
							($b_from >= $p_from && $b_to <= $p_to)
							)
							{
								$overlap = true;
								break;
							}
						}
					}

					if (!$overlap)
					{
						$status = $listing_arr['o_default_status_if_not_paid'];
						
						$amount = $deposit = $security = $tax = 0;
						$tmp = $this->pjActionGetPrices($_POST);
						
						if ($tmp['amount'] > 0)
						{
							$amount = $tmp['amount'];
							$security = $tmp['security'];
							$tax = $tmp['tax'];
							$deposit= $tmp['deposit'];
						}
						unset($tmp);
						
						$data = array();
						$data['ip'] = $_SERVER['REMOTE_ADDR'];
						
						$reservation_id = $ReservationModel
							->reset()
							->setAttributes(array_merge($_POST, $data, compact('status', 'amount', 'deposit', 'tax', 'security')))
							->insert()
							->getInsertId();
						if ($reservation_id !== false && (int) $reservation_id > 0)
						{
							$booking_arr = $ReservationModel->reset()->find($reservation_id)->getData();

							pjListings::pjActionConfirmSend($this->option_arr, $booking_arr, $listing_arr, 'confirm');
								
							if ((int) $listing_arr['o_disable_payments'] == 1)
							{
								$this->set('status', 1);
							} else {
								$this->set('status', 11);
							}
							
							$this->notify(3, NULL, array('reservation_id' => $reservation_id));
							$this->notify(4, $listing_arr['owner_id'], array('reservation_id' => $reservation_id));
							
						} else {
							$this->set('status', 2);
						}
					} else {
						$this->set('status', 3);
					}
				}
			}
			if (isset($_SESSION[$this->defaultCaptcha]))
			{
				$_SESSION[$this->defaultCaptcha] = NULL;
				unset($_SESSION[$this->defaultCaptcha]);
			}
			pjAppController::jsonResponse(array('code' => $this->get('status'), 'payment_method' => @$_POST['payment_method'], 'booking_id' => @$reservation_id));
		}
	}
	
	public function pjActionGetPaymentForm()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$booking_arr = pjReservationModel::factory()
				->select('t1.*, t2.o_paypal_address, t2.o_thankyou_page, t2.o_authorize_merchant_id, t2.o_authorize_transkey, t2.o_authorize_tz, t2.o_bank_account')
				->join('pjListing', "t2.id=t1.listing_id", 'left')
				->find($_GET['booking_id'])
				->getData();
				
			switch ($_POST['payment_method'])
			{
				case 'paypal':
					$this->set('params', array(
						'name' => 'vrPaypal',
						'id' => 'vrPaypal',
						'business' => $booking_arr['o_paypal_address'],
						'item_name' => __('front_payment_paypal_title', true),
						'custom' => $booking_arr['id'],
						'amount' => $booking_arr['deposit'],
						'currency_code' => $this->option_arr['o_currency'],
						'return' => $booking_arr['o_thankyou_page'],
						'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjListings&action=pjActionConfirmPaypal',
						'target' => '_self'
					));
					break;
				case 'authorize':
					$this->set('params', array(
						'name' => 'vrAuthorize',
						'id' => 'vrAuthorize',
						'timezone' => $booking_arr['o_authorize_tz'],
						'transkey' => $booking_arr['o_authorize_transkey'],
						'x_login' => $booking_arr['o_authorize_merchant_id'],
						'x_description' => __('front_payment_authorize_title', true),
						'x_amount' => $booking_arr['deposit'],
						'x_invoice_num' => $booking_arr['id'],
						'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjListings&action=pjActionConfirmAuthorize'
					));
					break;
			}
			
			$this->set('booking_arr', $booking_arr);
			$this->set('post', $_POST);
			$_POST = array();
		}
	}
	
	public function pjActionGetRequest()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$arr = pjListingModel::factory()->find($_POST['listing_id'])->getData();
			
			$this->set('reservation_arr', $this->pjActionGetReservations($_POST['listing_id']));
			$this->set('calendar', $this->pjActionGetCalendar($arr['o_price_based_on']));
			$this->set('price_raw_arr', $this->pjActionGetRawPrices($_POST['listing_id']));
			$this->set('listing_id', $_POST['listing_id']);
			$this->set('arr', $arr);
			$this->set('status', $_GET['status']);
			
			if ($_GET['status'] == 1)
			{
				$_POST = array();
			}
		}
	}

	public function pjActionView()
	{
		$pjListingModel = pjListingModel::factory();
		$arr = $pjListingModel
			->select('t1.*, t2.content AS type_title, t3.content AS country_title, t4.content AS listing_title,
				t5.content AS listing_description, t6.content AS listing_terms, t7.content AS meta_title,
				t8.content AS meta_keywords, t9.content AS meta_description')
			->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.type_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.country_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t4.model='pjListing' AND t4.foreign_id=t1.id AND t4.field='title' AND t4.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t5.model='pjListing' AND t5.foreign_id=t1.id AND t5.field='description' AND t5.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t6.model='pjListing' AND t6.foreign_id=t1.id AND t6.field='terms' AND t6.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t7.model='pjListing' AND t7.foreign_id=t1.id AND t7.field='meta_title' AND t7.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t8.model='pjListing' AND t8.foreign_id=t1.id AND t8.field='meta_keywords' AND t8.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t9.model='pjListing' AND t9.foreign_id=t1.id AND t9.field='meta_description' AND t9.locale='".$this->getLocaleId()."'", 'left')
			->where('t1.id', $_GET['id'])
			->where("(t1.status = 'T' OR (t1.status = 'E' AND t1.expire >= CURDATE()))")
			//->debug(1)
			->limit(1)
			->findAll()
			->getData();

		if (count($arr) === 1)
		{
			$arr = $arr[0];
			pjObject::import('Model', 'pjGallery:pjGallery');

			$pjListingModel->reset()->setAttributes(array('id' => $arr['id']))->modify(array('views' => $arr['views'] + 1));

			$this->set('extra_arr', pjExtraModel::factory()
				->select('t1.*, t2.content AS extra_title')
				->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->where('t1.status', 'T')
				->orderBy('extra_title ASC')
				->findAll()
				->getData()
			);

			$this->set('gallery_arr', pjGalleryModel::factory()->where('t1.foreign_id', $_GET['id'])->orderBy('t1.sort ASC')->findAll()->getData());
			$arr['extras'] = pjListingExtraModel::factory()->where('t1.listing_id', $_GET['id'])->findAll()->getDataPair(NULL, 'extra_id');

			$this->set('price_arr', pjPriceModel::factory()
				->where('listing_id', $_GET['id'])
				->orderBy('date_from ASC, date_to ASC')
				->findAll()
				->getData()
			);

			$this->set('reservation_arr', $this->pjActionGetReservations($_GET['id']));
			$this->set('calendar', $this->pjActionGetCalendar($arr['o_price_based_on']));
			$this->set('listing_id', $_GET['id']);

			$this->set('arr', $arr);
			$this->set('price_raw_arr', $this->pjActionGetRawPrices($_GET['id']));
			$this->set('meta_arr', array(
				'title' => $arr['meta_title'],
				'keywords' => $arr['meta_keywords'],
				'description' => $arr['meta_description']
			));
			
			if (isset($_SESSION[$this->defaultSearch]))
			{
				if (!isset($_POST['date_from']) && isset($_SESSION[$this->defaultSearch]['date_from']))
				{
					$_POST['date_from'] = pjUtil::formatDate($_SESSION[$this->defaultSearch]['date_from'], "Y-m-d", $this->option_arr['o_date_format']);
				}
				if (!isset($_POST['date_to']) && isset($_SESSION[$this->defaultSearch]['date_to']))
				{
					$_POST['date_to'] = pjUtil::formatDate($_SESSION[$this->defaultSearch]['date_to'], "Y-m-d", $this->option_arr['o_date_format']);
				}
			}
			//if (isset($_POST['date_from']) && preg_match($this->isoDatePattern, $_POST['date_from']) && isset($_POST['date_to']) && preg_match($this->isoDatePattern, $_POST['date_to']))
			if (isset($_POST['date_from']) && !empty($_POST['date_from']) && isset($_POST['date_to']) && !empty($_POST['date_to']))
			{
				$this->set('p_arr', $this->pjActionGetPrices(array(
					'listing_id' => $_GET['id'],
					'date_from' => pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']),
					'date_to' => pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format'])
				)));
			}

			$this->appendJs('jabb-0.4.3.min.js', PJ_LIBS_PATH . 'jabb/');
			$this->appendJs('calendar-1.5.1.min.js', PJ_THIRD_PARTY_PATH . 'calendarjs/');
			$this->appendCss('calendar.css', PJ_THIRD_PARTY_PATH . 'calendarjs/themes/sky-blue/');
			$this->appendJs('pjListings.js');
			$this->appendJs('lytebox.js', PJ_THIRD_PARTY_PATH . 'lytebox/');
			$this->appendCss('lytebox.css', PJ_THIRD_PARTY_PATH . 'lytebox/');
			$this->appendCss('calendar.css');
		} else {

		}
	}

	public function pjActionAdd()
	{
		if ($this->option_arr['o_allow_add_property'] == 'Yes')
		{
			if (isset($_GET['listing_register']))
			{
				set_time_limit(0);

				if (!isset($_GET['register_email']))
				{
					$err = 9901;
				}
				if (!isset($_GET['register_password']))
				{
					$err = 9902;
				}
				if (!isset($_GET['register_password_repeat']))
				{
					$err = 9903;
				}
				if (!isset($_GET['name']))
				{
					$err = 9904;
				}
				if (!isset($_GET['verification']))
				{
					$err = 9905;
				}
				if (isset($_GET['register_email']) && !pjValidation::pjActionNotEmpty($_GET['register_email']))
				{
					$err = 9906;
				}
				if (isset($_GET['register_password']) && !pjValidation::pjActionNotEmpty($_GET['register_password']))
				{
					$err = 9907;
				}
				if (isset($_GET['register_password_repeat']) && !pjValidation::pjActionNotEmpty($_GET['register_password_repeat']))
				{
					$err = 9908;
				}
				if (isset($_GET['name']) && !pjValidation::pjActionNotEmpty($_GET['name']))
				{
					$err = 9909;
				}
				if (isset($_GET['verification']) && !pjValidation::pjActionNotEmpty($_GET['verification']))
				{
					$err = 9910;
				}
				if (isset($_GET['verification']) && isset($_SESSION[$this->defaultCaptcha]) && strtoupper($_GET['verification']) != $_SESSION[$this->defaultCaptcha])
				{
					$err = 9911;
				}
				if (isset($_GET['register_email']) && !pjValidation::pjActionEmail($_GET['register_email']))
				{
					$err = 9912;
				}
				if (isset($_GET['register_password']) && isset($_GET['register_password_repeat']) && $_GET['register_password'] != $_GET['register_password_repeat'])
				{
					$err = 9913;
				}
				if (isset($err))
				{
					if (isset($_SESSION[$this->defaultCaptcha]))
					{
						$_SESSION[$this->defaultCaptcha] = NULL;
						unset($_SESSION[$this->defaultCaptcha]);
					}
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjListings&action=pjActionAdd&err=$err");
				}
				
				/*if (!isset($_GET['register_email']) || !isset($_GET['register_password']) ||
					!isset($_GET['register_password_repeat']) || !isset($_GET['name']) || !isset($_GET['verification']) ||
					$_GET['register_password'] != $_GET['register_password_repeat'] ||
					!pjValidation::pjActionNotEmpty($_GET['register_email']) ||
					!pjValidation::pjActionNotEmpty($_GET['register_password']) ||
					!pjValidation::pjActionNotEmpty($_GET['register_password_repeat']) ||
					!pjValidation::pjActionNotEmpty($_GET['name']) ||
					!pjValidation::pjActionEmail($_GET['register_email']) ||
					strtoupper($_GET['verification']) != $_SESSION[$this->defaultCaptcha])
				{
					if (isset($_SESSION[$this->defaultCaptcha]))
					{
						$_SESSION[$this->defaultCaptcha] = NULL;
						unset($_SESSION[$this->defaultCaptcha]);
					}
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjListings&action=pjActionAdd&err=9996");
				}*/
				
				if (isset($_SESSION[$this->defaultCaptcha]))
				{
					$_SESSION[$this->defaultCaptcha] = NULL;
					unset($_SESSION[$this->defaultCaptcha]);
				}
				
				$pjUserModel = pjUserModel::factory();
				$arr = $pjUserModel->where('t1.email', $_GET['register_email'])->findAll()->getData();
	
				if (!empty($arr) && count($arr) > 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjListings&action=pjActionAdd&err=9997");
				} else {
					$data['password'] = $_GET['register_password'];
					$data['email'] = $_GET['register_email'];
					$data['role_id'] = 3;
					$data['is_active'] = $this->option_arr['o_is_active_owner'] == 'Yes' ? 'T' : 'F';
					$data['ip'] = $_SERVER['REMOTE_ADDR'];
					$data = array_merge($_GET, $data);
					
					$id = $pjUserModel->reset()->setAttributes($data)->insert()->getInsertId();
					if ($id !== false && (int) $id > 0)
					{
						$this->notify(1, NULL, array('name' => $data['name'], 'email' => $data['email']));
						# default email
						$pjEmail = new pjEmail();
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
						$pjEmail->setFrom($_GET['register_email'])
							->setTo($_GET['register_email'])
							->setSubject($this->option_arr['o_email_new_user_subject'])
							->send(str_replace(array('{Name}', '{Email}'), array($_GET['name'], $_GET['register_email']), $this->option_arr['o_email_new_user']));
						# default email
						
						if ($this->option_arr['o_is_active_owner'] == 'Yes')
						{
							pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjListings&action=pjActionAdd&err=9999");
						} else {
							pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjListings&action=pjActionAdd&err=9998");
						}
						exit;
					}
				}
			}
	
			$this->appendJs('jabb-0.4.3.min.js', PJ_LIBS_PATH . 'jabb/');
			$this->appendJs('pjListings.js');
		} else {
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjListings&action=pjActionIndex");
		}
	}

	public function pjActionConfirmAuthorize()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		$pjReservationModel = pjReservationModel::factory();

		$booking_arr = $pjReservationModel->find($_POST['x_invoice_num'])->getData();
		if (count($booking_arr) > 0)
		{
			$listing_arr = pjListingModel::factory()->find($booking_arr['listing_id'])->getData();
			
			$params = array(
				'transkey' => $booking_arr['o_authorize_transkey'],
				'x_login' => $booking_arr['o_authorize_merchant_id'],
				'key' => md5($this->option_arr['private_key'] . PJ_SALT)
			);
			
			$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
			if ($response !== FALSE && isset($response['status']) && $response['status'] === 'OK')
			{
				$map = array(1 => 'Confirmed', 2 => 'Pending', 3 => 'Cancelled');
				$this->log('Booking confirmed');
				$pjReservationModel
					->setAttributes(array('id' => $response['transaction_id']))
					->modify(array('status' => $map[$listing_arr['o_default_status_if_paid']]));
				pjListings::pjActionConfirmSend($this->option_arr, $booking_arr, $listing_arr, 'payment');
			} elseif (!$response) {
				$this->log('Authorization failed');
			} else {
				$this->log('Booking not confirmed. ' . $response['response_reason_text']);
			}
			pjUtil::redirect($listing_arr['o_thankyou_page']);
		}
	}

	public function pjActionConfirmPaypal()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		$pjReservationModel = pjReservationModel::factory();
		$booking_arr = $pjReservationModel->find($_POST['custom'])->getData();
		$listing_arr = array();
		if (count($booking_arr) > 0)
		{
			$listing_arr = pjListingModel::factory()->find($booking_arr['listing_id'])->getData();
		}
		$params = array(
			'txn_id' => @$booking_arr['txn_id'],
			'paypal_address' => @$listing_arr['o_paypal_address'],
			'deposit' => @$booking_arr['deposit'],
			'currency' => $this->option_arr['o_currency'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);

		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		if ($response !== FALSE && isset($response['status']) && $response['status'] === 'OK')
		{
			$map = array(1 => 'Confirmed', 2 => 'Pending', 3 => 'Cancelled');
			$this->log('Booking confirmed');
			$pjReservationModel->reset()->set('id', $booking_arr['id'])->modify(array(
				'status' => $map[$listing_arr['o_default_status_if_paid']],
				'txn_id' => $response['transaction_id'],
				'processed_on' => ':NOW()'
			));
			pjListings::pjActionConfirmSend($this->option_arr, $booking_arr, $listing_arr, 'payment');
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Booking not confirmed');
		}
		exit;
	}
	
	public function pjActionConfirmPayment()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		
		$pjPaymentModel = pjPaymentModel::factory();
		$pjListingModel = pjListingModel::factory();
		$listing_arr = $pjListingModel->find($_POST['custom'])->getData();
		$payment_arr = $pjPaymentModel->where('t1.listing_id', $_POST['custom'])->orderBy('t1.date_to DESC')->limit(1)->findAll()->getData();
		$period_arr = pjPeriodModel::factory()->findAll()->getData();
		$date_from = date("Y-m-d");
		if (count($payment_arr) === 1)
		{
			$date_from = $payment_arr[0]['date_to'];
		}
		
		$period = $price = NULL;
		foreach ($period_arr as $_period)
		{
			if ((float) $_period['price'] == (float) $_POST['mc_gross'])
			{
				$period = (int) $_period['days'];
				$price = (float) $_period['price'];
				break;
			}
		}
		list($year, $month, $day) = explode("-", $date_from);
		$date_to = date("Y-m-d", mktime(0, 0, 0, $month, $day + $period, $year));
		
		$params = array(
			'txn_id' => @$booking_arr['txn_id'],
			'paypal_address' => $this->option_arr['o_paypal_address'],
			'deposit' => $price,
			'currency' => $this->option_arr['o_currency'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);

		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		if ($response !== FALSE && isset($response['status']) && $response['status'] === 'OK')
		{
			$this->log('pjPaypal > pjActionConfirm > status == OK');
			$pjPaymentModel
				->reset()
				->setAttributes(array(
					'listing_id' => $listing_arr['id'],
					'date_from' => $date_from,
					'date_to' => $date_to,
					'txn_id' => $response['transaction_id'],
					'price' => $price
				))
				->insert();
			$current = time();
			if (!empty($listing_arr['expire']) && $listing_arr['expire'] != '0000-00-00')
			{
				$current = strtotime($listing_arr['expire']);
			}
			pjListingModel::factory()
				->set('id', $listing_arr['id'])
				->modify(array(
					'last_extend' => 'paid',
					'expire' => date("Y-m-d", $current + $period * 86400)
				));
			//pjListings::pjActionConfirmSend($this->option_arr, $booking_arr, $listing_arr, 'payment');
			$this->log('Payment confirmed');
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Payment not confirmed');
		}
		exit;
	}
	
	private static function pjActionConfirmSend($option_arr, $booking_arr, $listing_arr, $type)
	{
		if (!in_array($type, array('confirm', 'payment')))
		{
			return false;
		}
		
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
				->setTransport('smtp')
				->setSmtpHost($option_arr['o_smtp_host'])
				->setSmtpPort($option_arr['o_smtp_port'])
				->setSmtpUser($option_arr['o_smtp_user'])
				->setSmtpPass($option_arr['o_smtp_pass'])
			;
		}
		$tokens = pjAppController::getTokens($booking_arr, $option_arr);

		switch ($type)
		{
			case 'confirm':
				if (!isset($listing_arr['confirm_subject']) || !isset($listing_arr['confirm_tokens']) ||
					empty($listing_arr['confirm_subject']) || empty($listing_arr['confirm_tokens']))
				{
					return false;
				}
				$subject = str_replace($tokens['search'], $tokens['replace'], $listing_arr['confirm_subject']);
				$message = str_replace($tokens['search'], $tokens['replace'], $listing_arr['confirm_tokens']);
				//client
				$Email
					->setTo($booking_arr['email'])
					->setFrom($listing_arr['email_address'])
					->setSubject($subject)
					->send($message);
				break;
			case 'payment':
				if (!isset($listing_arr['payment_subject']) || !isset($listing_arr['payment_tokens']) ||
					empty($listing_arr['payment_subject']) || empty($listing_arr['payment_tokens']))
				{
					return false;
				}
				$subject = str_replace($tokens['search'], $tokens['replace'], $listing_arr['payment_subject']);
				$message = str_replace($tokens['search'], $tokens['replace'], $listing_arr['payment_tokens']);
				//client
				$Email
					->setTo($booking_arr['email'])
					->setFrom($listing_arr['email_address'])
					->setSubject($subject)
					->send($message);
				break;
		}
	}
	
	public function pjActionFeatured()
	{
		pjObject::import('Model', 'pjGallery:pjGallery');
		
		$pjListingModel = pjListingModel::factory()
			->where("(t1.status = 'T' OR (t1.status = 'E' AND t1.expire >= CURDATE()))")
			->where('t1.is_featured', 'T')
		;

		switch ($this->option_arr['o_sort_by'])
		{
			case 'created_asc':
				$pjListingModel->orderBy("FIELD(is_featured, 'T', 'F') ASC, created ASC, listing_title ASC", false);
				break;
			case 'created_desc':
				$pjListingModel->orderBy("FIELD(is_featured, 'T', 'F') ASC, created DESC, listing_title ASC", false);
				break;
			case 'random':
				$pjListingModel->orderBy("FIELD(is_featured, 'T', 'F') ASC, RAND()", false);
				break;
			case 'popularity':
			default:
				$pjListingModel->orderBy("FIELD(is_featured, 'T', 'F') ASC, views DESC, listing_title ASC", false);
				break;
		}
			
		$arr = $pjListingModel
			->select(sprintf("t1.*, t2.content AS type_title, t3.content AS country_title, t4.content AS listing_title, t5.content AS listing_description,
				(SELECT `medium_path` FROM `%1\$s` WHERE `foreign_id` = `t1`.`id` ORDER BY `sort` ASC LIMIT 1) AS `pic`,
				(SELECT MIN(`price`) FROM `%2\$s` WHERE `listing_id` = `t1`.`id` LIMIT 1) AS `price`
			", pjGalleryModel::factory()->getTable(), pjPriceModel::factory()->getTable()))
			->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.type_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.country_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t4.model='pjListing' AND t4.foreign_id=t1.id AND t4.field='title' AND t4.locale='".$this->getLocaleId()."'", 'left')
			->join('pjMultiLang', "t5.model='pjListing' AND t5.foreign_id=t1.id AND t5.field='description' AND t5.locale='".$this->getLocaleId()."'", 'left')
			->limit($this->option_arr['o_limit_featured_results'])->findAll()->getData();
		
		$this->set('country_arr', pjCountryModel::factory()
			->select('t1.*, t2.content AS country_title')
			->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
			->where('t1.status', 'T')->orderBy('country_title ASC')->findAll()->getData()
		);
		
		$this->set('type_arr', pjTypeModel::factory()
			->select('t1.*, t2.content AS type_title')
			->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
			->where('t1.status', 'T')->orderBy('type_title ASC')->findAll()->getData()
		);
		
		$params = $this->getParams();
		if (isset($params['search']) && $params['search'] === true)
		{
			$this->set('extra_arr', pjExtraModel::factory()
				->select('t1.*, t2.content AS extra_title')
				->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->where('t1.status', 'T')->orderBy('extra_title ASC')->findAll()->getData()
			);
				
			$this->appendJs('calendar-1.5.1.min.js', PJ_THIRD_PARTY_PATH . 'calendarjs/');
			$this->appendCss('calendar.css', PJ_THIRD_PARTY_PATH . 'calendarjs/themes/sky-blue/');
		}
			
		$this->set('arr', $arr);
		$this->appendJs('jabb-0.4.3.min.js', PJ_LIBS_PATH . 'jabb/');
		$this->appendJs('pjListings.js');
	}

	private function pjActionGetReservations($id)
	{
		$reservation_arr = pjReservationModel::factory()
			->select('t1.*, t2.listing_refid')
			->join('pjListing', "t2.id=t1.listing_id AND t2.status IN ('T','E')", 'inner')
			->where('t1.listing_id', $id)
			->where('t1.status', 'Confirmed')
			->orderBy('t1.date_from ASC, t1.date_to ASC')
			->findAll()
			->getData();
		
		$r_arr = array();
		$i = 0;
		$_from = $_to = $_map = array();
		foreach ($reservation_arr as $r)
		{
			$r_arr[$i]['date_from'] = strtotime($r['date_from']);
			$r_arr[$i]['date_to'] = strtotime($r['date_to']);
			$_from[] = $r_arr[$i]['date_from'];
			$_to[] = $r_arr[$i]['date_to'];
			$i++;
		}
		if (count($_from) > 0)
		{
			$min = min($_from);
			$max = max($_to);
			for ($i = $min; $i <= $max; $i += 86400)
			{
				$_map[$i] = array('start' => 0, 'in' => 0, 'end' => 0);
			}
			foreach ($reservation_arr as $r)
			{
				$from = strtotime($r['date_from']);
				$to = strtotime($r['date_to']);
				$_map[$from]['start'] += 1;
				$_map[$to]['end'] += 1;
				for ($i = $from + 86400; $i < $to; $i += 86400)
				{
					$_map[$i]['in'] += 1;
				}
			}
		}
		return array('map' => $_map, 'bookings' => $r_arr);
	}

	public function pjActionGetStates()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$this->pjActionGetStatesByCountry();
		}
	}
	
	private function pjActionGetStatesByCountry()
	{
		if (isset($_GET['country_id']) && (int) $_GET['country_id'] > 0)
		{
			$this->set('state_arr', pjListingModel::factory()
				->select('t1.address_state')
				->where('t1.country_id', $_GET['country_id'])
				->groupBy('t1.address_state')
				->orderBy('t1.address_state ASC')
				->findAll()
				->getDataPair(null, 'address_state')
			);
		}
	}
	
	private function pjActionGetCalendar($priceBasedOn=1)
	{
		$months = __('months', true);
		ksort($months);
		$dayNames = __('day_names', true);
		ksort($dayNames);
		
		return pjABCalendar::factory()
			->setStartDay(1)
			->setPriceBasedOn($priceBasedOn)
			->setMonthNames(array_values($months))
			->setDayNames($dayNames);
	}

	public function pjActionGetAvailability()
	{
		$this->setAjax(true);
		
		$arr = pjListingModel::factory()->find($_GET['listing_id'])->getData();

		$this->set('calendar', $this->pjActionGetCalendar($arr['o_price_based_on']));
		$this->set('reservation_arr', $this->pjActionGetReservations($_GET['listing_id']));
		$this->set('price_raw_arr', $this->pjActionGetRawPrices($_GET['listing_id']));
	}
	
	private function pjActionGetPrices($data)
	{
		$listing_arr = pjListingModel::factory()->find($data['listing_id'])->getData();
		$p_from = strtotime($data['date_from']);
		$p_to = strtotime($data['date_to']);
		
		$amount = pjPriceModel::factory()->getPrice($p_from, $p_to, $listing_arr['id'], $listing_arr['o_price_based_on']);
		$deposit = $security = $tax = 0;
		if ($amount > 0)
		{
			$security = (float) $listing_arr['o_security_payment'];
			$tax = 0;
			switch ($listing_arr['o_tax_type'])
			{
				case 1:
					$tax = $listing_arr['o_tax_payment'];
					break;
				case 2:
					$tax = ($amount * $listing_arr['o_tax_payment']) / 100;
					break;
			}
			$deposit = $security + (($amount + $tax) * $listing_arr['o_deposit_payment']) / 100;
		}
		if ((float) $amount > 0)
		{
			$amount_f = pjUtil::formatCurrencySign(number_format($amount, 2), $this->option_arr['o_currency']);
			$security_f = pjUtil::formatCurrencySign(number_format($security, 2), $this->option_arr['o_currency']);
			$deposit_f = pjUtil::formatCurrencySign(number_format($deposit, 2), $this->option_arr['o_currency']);
			$tax_f = pjUtil::formatCurrencySign(number_format($tax, 2), $this->option_arr['o_currency']);
			$deposit_p = $listing_arr['o_deposit_payment'];
		} else {
			$amount_f = __('front_index_na', true);
			$security_f = $deposit_f = $tax_f = $deposit_p = pjUtil::formatCurrencySign(0, $this->option_arr['o_currency']);
		}
		return compact('amount', 'deposit', 'security', 'tax', 'amount_f', 'deposit_f', 'security_f', 'tax_f', 'deposit_p');
	}
	
	private function pjActionGetRawPrices($listing_id)
	{
		$price_arr = pjPriceModel::factory()
			->where('t1.listing_id', $listing_id)
			->orderBy('t1.date_from ASC, t1.date_to ASC')
			->findAll()
			->getData();
		
		$pr_arr = array();
    	foreach ($price_arr as $period)
    	{
    		$price = pjUtil::formatCurrencySign(number_format($period['price'], 0), $this->option_arr['o_currency']);
    		$start = strtotime($period['date_from']);
    		$end = strtotime($period['date_to']);
    		for ($i = $start; $i <= $end; $i += 86400)
    		{
    			$pr_arr[$i] = $price;
    		}
    	}
    	return $pr_arr;
	}
	
	public function pjActionGetPrice()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$arr = $this->pjActionGetPrices(array_merge($_POST, array(
				'date_from' => pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']),
				'date_to' => pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format'])
			)));
			$this->set('p_arr', $arr);
			//pjAppController::jsonResponse($arr);
		}
	}
	
	public function pjActionImage()
	{
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
		
		$width = isset($_GET['width']) && (int) $_GET['width'] > 0 ? intval($_GET['width']) : 100;
		$height = isset($_GET['height']) && (int) $_GET['height'] > 0 ? intval($_GET['height']) : 100;
		
		$image = imagecreate($width, $height);
		$backgroundColor = pjUtil::html2rgb($_GET['color1']);
		$color = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
		imagefill($image, 0, 0, $color);
		
		if (isset($_GET['color2']) && !empty($_GET['color2']))
		{
			if ($_GET['color1'] == $_GET['color2'])
			{
				$backgroundColor = pjUtil::html2rgb('ffffff');
				$color = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
		
				$values = array(
						0, $height-2,
						$width-2, 0,
						$width, 0,
						$width, 1,
						1, $height,
						0, $height,
						0, $height-1
						);
				imagefilledpolygon($image, $values, 7, $color);
			} else {
				$backgroundColor = pjUtil::html2rgb($_GET['color2']);
				$color = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
				$values = array(
						$width,  0,  // Point 1 (x, y)
						$width,  $height, // Point 2 (x, y)
						0, $height,
						$width,  0
						);
				imagefilledpolygon($image, $values, 4, $color);
			}
		}

		header('Content-Type: image/jpeg');
		imagejpeg($image);
		imagedestroy($image);
		exit;
	}
}
?>