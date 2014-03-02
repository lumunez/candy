<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAdmin.controller.php';
class pjAdminReservations extends pjAdmin
{
	private function pjActionGetDays($data, $format=true)
	{
		$response = array('code' => 100);
		if (isset($data['listing_id']) && (int) $data['listing_id'] > 0 &&
			isset($data['date_from']) && isset($data['date_to']) &&
			!empty($data['date_from']) && !empty($data['date_to']))
		{
			if ($format)
			{
				$date_from = pjUtil::formatDate($data['date_from'], $this->option_arr['o_date_format']);
				$date_to = pjUtil::formatDate($data['date_to'], $this->option_arr['o_date_format']);
			} else {
				$date_from = $data['date_from'];
				$date_to = $data['date_to'];
			}
			$from = strtotime($date_from);
			$to = strtotime($date_to);
			if ($from > $to)
			{
				$tmp = $from;
				$from = $to;
				$to = $tmp;
			}
			
			$arr = pjListingModel::factory()->find($data['listing_id'])->getData();
			if (!empty($arr))
			{
				$nights = ceil(abs($to - $from) / 86400);
				if ($arr['o_price_based_on'] == 'days')
				{
					$nights += 1;
				}
				
				if ($arr['o_min_booking_lenght'] <= $nights && $arr['o_max_booking_lenght'] >= $nights)
				{
					$response['code'] = 200;
				}
			}
		}
		return $response;
	}
	
	private function pjActionGetAvailability($data, $format=true)
	{
		$response = array('code' => 100);
		if (isset($data['listing_id']) && (int) $data['listing_id'] > 0 &&
			isset($data['date_from']) && isset($data['date_to']) &&
			!empty($data['date_from']) && !empty($data['date_to']))
		{
			if ($format)
			{
				$date_from = pjUtil::formatDate($data['date_from'], $this->option_arr['o_date_format']);
				$date_to = pjUtil::formatDate($data['date_to'], $this->option_arr['o_date_format']);
			} else {
				$date_from = $data['date_from'];
				$date_to = $data['date_to'];
			}
			if (strtotime($date_from) > strtotime($date_to))
			{
				$tmp = $date_from;
				$date_from = $date_to;
				$date_to = $tmp;
			}
			
			$pjReservationModel = pjReservationModel::factory()
				->where('t1.listing_id', $data['listing_id'])
				->where('t1.status', 'Confirmed')
				->where('t1.date_from <=', $date_to)
				->where('t1.date_to >=', $date_from)
			;
			if (isset($data['id']) && (int) $data['id'])
			{
				$pjReservationModel->where('t1.id !=', $data['id']);
			}
			
			if (0 == $pjReservationModel->findCount()->getData())
			{
				$response['code'] = 200;
			}
		}
		return $response;
	}
	
	public function pjActionCheckDays()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			pjAppController::jsonResponse($this->pjActionGetDays($_POST));
		}
		exit;
	}
	
	public function pjActionCheckAvailability()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			pjAppController::jsonResponse($this->pjActionGetAvailability($_POST));
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isOwner() || $this->isEditor())
		{
			if (isset($_POST['reservation_create']))
			{
				$response = $this->pjActionGetDays($_POST);
				if ($response['code'] != 200)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR04");
				}
				$response = $this->pjActionGetAvailability($_POST);
				if ($response['code'] != 200)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR04");
				}
				
				$data = array();
				$data['date_from'] = pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']);
				$data['date_to'] = pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']);
				
				if (strtotime($data['date_from']) > strtotime($data['date_to']))
				{
					$tmp = $data['date_from'];
					$data['date_from'] = $data['date_to'];
					$data['date_to'] = $tmp;
				}
				
				$data['ip'] = $_SERVER['REMOTE_ADDR'];
				
				$pjReservationModel = pjReservationModel::factory();
				$post = array_merge($_POST, $data);

				if (!$pjReservationModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR04");
				}

				$insert_id = $pjReservationModel->setAttributes($post)->insert()->getInsertId();
				if ($insert_id !== false && (int) $insert_id > 0)
				{
					$this->notify(3, NULL, array('reservation_id' => $insert_id));
					$listing_arr = pjListingModel::factory()->find($_POST['listing_id'])->getData();
					if (count($listing_arr) > 0)
					{
						$this->notify(4, $listing_arr['owner_id'], array('reservation_id' => $insert_id));
					}
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR03");
				} else {
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR04");
				}
			}
			
			$pjListingModel = pjListingModel::factory();
			if ($this->isOwner())
			{
				$pjListingModel->where('t1.owner_id', $this->getUserId());
			}
			$listing_arr = $pjListingModel
				->select('t1.id, t1.listing_refid, t2.content AS title')
				->join('pjMultiLang', "t2.model='pjListing' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->orderBy('title ASC')
				->findAll()->getData();
			
			$listing_arr = pjSanitize::clean($listing_arr);
			$this->set('listing_arr', $listing_arr);

			$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminReservations.js');
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionDeleteReservation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjReservationModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjReservation')->where('foreign_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteReservationBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjReservationModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjReservation')->whereIn('foreign_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportReservation()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjReservationModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Reservations-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetMessage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$listing_arr = pjListingModel::factory()
				->select("t2.content AS confirm_tokens, t3.content AS confirm_subject")
				->join('pjMultiLang', "t2.model='pjListing' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='confirm_tokens'", 'inner')
				->join('pjMultiLang', "t3.model='pjListing' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='confirm_subject'", 'inner')
				->find($_POST['listing_id'])->getData();

			$tokens = pjAppController::getTokens($_POST, $this->option_arr);

			$response = array(
				'subject' => str_replace($tokens['search'], $tokens['replace'], @$listing_arr['confirm_subject']),
				'body' => str_replace($tokens['search'], $tokens['replace'], @$listing_arr['confirm_tokens'])
			);
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionGetReservation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjReservationModel = pjReservationModel::factory()->join('pjListing', 't2.id=t1.listing_id');
			
			if (isset($_GET['listing_id']) && (int) $_GET['listing_id'] > 0)
			{
				$pjReservationModel->where('t1.listing_id', $_GET['listing_id']);
			}
				
			if (isset($_GET['status']) && !empty($_GET['status']))
			{
				$pjReservationModel->where('t1.status', $_GET['status']);
			}
			
			if ($this->isOwner())
			{
				$pjReservationModel->where('t2.owner_id', $this->getUserId());
			}
			
			$column = 'date_from';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjReservationModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjReservationModel->select('t1.id, t1.listing_id, t1.date_from, t1.date_to, t1.status, t2.listing_refid')
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();

			$data = pjSanitize::clean($data);
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
			
		if ($this->isAdmin() || $this->isOwner() || $this->isEditor())
		{
			$pjListingModel = pjListingModel::factory()
				->select('t1.id, t1.listing_refid, t2.content AS title')
				->join('pjMultiLang', "t2.model='pjListing' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->orderBy('title ASC, t1.listing_refid ASC');
			if ($this->isOwner())
			{
				$pjListingModel->where('t1.owner_id', $this->getUserId());
			}
			$listing_arr = $pjListingModel->findAll()->getData();
			$listing_arr = pjSanitize::clean($listing_arr);
			$this->set('listing_arr', $listing_arr);

			$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminReservations.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveReservation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjReservationModel = pjReservationModel::factory();
			if (!in_array($_POST['column'], $pjReservationModel->getI18n()))
			{
				$reservation = $pjReservationModel
					->select('t1.*, t2.owner_id')
					->join('pjListing', 't2.id=t1.listing_id')
					->find($_GET['id'])->getData();
					
				if ($_POST['column'] == 'status' && $_POST['value'] == 'Confirmed')
				{
					$response = $this->pjActionGetAvailability($reservation, false);
					if ($response['code'] != 200)
					{
						exit;
					}
				}
					
				if (in_array($_POST['column'], array('date_from', 'date_to')))
				{
					$_POST['value'] = pjUtil::formatDate($_POST['value'], $this->option_arr['o_date_format']);
					
					$data = array(
						'listing_id' => $_GET['id'],
						'date_from' => ($_POST['column'] == 'date_from' ? $_POST['value'] : $reservation['date_from']),
						'date_to' => ($_POST['column'] == 'date_to' ? $_POST['value'] : $reservation['date_to'])
					);
					$response = $this->pjActionGetDays($data, false);
					if ($response['code'] != 200)
					{
						exit;
					}
					
					$data['id'] = $_GET['id'];
					$response = $this->pjActionGetAvailability($data, false);
					if ($response['code'] != 200)
					{
						exit;
					}
				}
				$pjReservationModel->reset()->set('id', $_GET['id'])->modify(array($_POST['column'] => $_POST['value']));
				
				if ($_POST['column'] == 'status' && $_POST['value'] == 'Cancelled' && $reservation['status'] != 'Cancelled')
				{
					$this->notify(5, NULL, array('reservation_id' => $_GET['id']));
					$this->notify(6, $reservation['owner_id'], array('reservation_id' => $_GET['id']));
				}
				
			} else {
				MultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjReservation');
			}
		}
		exit;
	}
	
	public function pjActionSendMessage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (get_magic_quotes_gpc())
			{
				$_POST = array_map("stripslashes", $_POST);
			}

			$Email = new pjEmail();
			pjAppController::setFields($this->getLocaleId());
			$Email
				->setTo($_POST['email'])
				->setFrom($_SESSION[$this->defaultUser]['email'])
				->setSubject($_POST['subject'])
			;
			
			if ($this->option_arr['o_send_email'] == 'smtp')
			{
				$Email
					->setTransport('smtp')
					->setSmtpHost($this->option_arr['o_smtp_host'])
					->setSmtpPort($this->option_arr['o_smtp_port'])
					->setSmtpUser($this->option_arr['o_smtp_user'])
					->setSmtpPass($this->option_arr['o_smtp_pass'])
				;
			}
			
			$Email->send($_POST['message']);
		}
		exit;
	}
		
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isOwner() || $this->isEditor())
		{
			$pjReservationModel = pjReservationModel::factory();

			$reservation = $pjReservationModel
				->select(sprintf("t1.*,
					AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
					AES_DECRYPT(t1.cc_exp, '%1\$s') AS `cc_exp`,
					AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
					t2.owner_id", PJ_SALT))
				->join('pjListing', 't2.id=t1.listing_id')
				->find($_REQUEST['id'])->getData();

			if (empty($reservation) || count($reservation) == 0)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR08");
			}
			
			$property = pjListingModel::factory()->find($reservation['listing_id'])->getData();
			
			if (empty($property) || count($property) == 0)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR09");
			}
			
			if ($this->isOwner())
			{
				if ($property['owner_id'] != $this->getUserId())
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR10");
				}
			}
			
			if (isset($_POST['reservation_update']))
			{
				$response = $this->pjActionGetDays($_POST);
				if ($response['code'] != 200)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR04");
				}
				$response = $this->pjActionGetAvailability($_POST);
				if ($response['code'] != 200)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR04");
				}
				
				$data = array();
				$data['date_from'] = pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']);
				$data['date_to'] = pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']);
				$post = array_merge($_POST, $data);
				
				if (!$pjReservationModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR02");
				}
				$pjReservationModel->reset()->set('id', $_POST['id'])->modify($post);
				
				if ($reservation['status'] != 'Cancelled' && $_POST['status'] == 'Cancelled')
				{
					$this->notify(5, NULL, array('reservation_id' => $_POST['id']));
					$this->notify(6, $reservation['owner_id'], array('reservation_id' => $_POST['id']));
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR01");
			} else {
				$this->set('arr', $reservation);
			}
			
			$pjListingModel = pjListingModel::factory();
			if ($this->isOwner())
			{
				$pjListingModel->where('t1.owner_id', $this->getUserId());
			}
			$this->set('listing_arr', $pjListingModel
				->select('t1.id, t1.listing_refid, t2.content AS title')
				->join('pjMultiLang', "t2.model='pjListing' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->orderBy('title ASC')
				->findAll()->getData()
			);
			
			$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminReservations.js');
		} else {
			$this->set('status', 2);
		}
	}
}
?>