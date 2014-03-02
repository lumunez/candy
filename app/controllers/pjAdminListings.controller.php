<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAdmin.controller.php';
class pjAdminListings extends pjAdmin
{
	private $imageFiles = array('small_path', 'medium_path', 'large_path', 'source_path');
	
	public function pjActionCheckRefId()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && isset($_GET['listing_refid']))
		{
			$pjListingModel = pjListingModel::factory();
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjListingModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjListingModel->where('t1.listing_refid', $_GET['listing_refid'])->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCloneListing()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjMultiLangModel = new pjMultiLangModel();

				$data = pjListingModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
				foreach ($data as $item)
				{
					$item_id = $item['id'];
					unset($item['id']);

					$id = pjListingModel::factory($item)->insert()->getInsertId();
					if ($id !== false && (int) $id > 0)
					{
						$_data = pjMultiLangModel::factory()->getMultiLang($item_id, 'pjListing');
						$pjMultiLangModel->saveMultiLang($_data, $id, 'pjListing');
					}
				}
			}
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor() || $this->isOwner())
		{
			if (isset($_POST['listing_create']))
			{
				$data = array();
				if (isset($_POST['expire']))
				{
					$data['expire'] = pjUtil::formatDate($_POST['expire'], $this->option_arr['o_date_format']);
				}
				$data['last_extend'] = 'free';
				if ($this->isOwner())
				{
					$data['owner_id'] = $this->getUserId();
					$data['status'] = 'E';
					$data['is_featured'] = 'F';
					$data['expire'] = date("Y-m-d", strtotime("-1 day"));
				}
				$data = array_merge($_POST, $data);
				$pjListingModel = pjListingModel::factory();
				if (!$pjListingModel->validates($data))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminListings&action=pjActionCreate&err=1");
				}
				
				if ($pjListingModel->where('t1.listing_refid', $data['listing_refid'])->findCount()->getData() > 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminListings&action=pjActionCreate&err=1");
				}
				
				//$data = pjSanitize::clean($data);
				$id = $pjListingModel->reset()->setAttributes($data)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AC03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjListing');
					}
					$this->notify(2, NULL, array('property_id' => $id, 'listing_refid' => $data['listing_refid']));
				} else {
					$err = 'AC04';
				}
				
				if ($id !== false && (int) $id > 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminListings&action=pjActionUpdate&id=" . $id);
				} else {
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminListings&action=pjActionCreate&err=1");
				}
			}
			
			if ($this->isOwner())
			{
				$this->set('period_arr', pjPeriodModel::factory()->orderBy('t1.days ASC')->findAll()->getData());
			}

			$type_arr = pjTypeModel::factory()->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'")
				->where('t1.status', 'T')->orderBy('name ASC')->findAll()->getData();
			$this->set('type_arr', pjSanitize::clean($type_arr));

			$user_arr = pjUserModel::factory()->orderBy('t1.name ASC')->findAll()->getData();
			$this->set('user_arr', pjSanitize::clean($user_arr));
				
			$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminListings.js');
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionDeletePrice()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array('code' => 100);
			if (isset($_POST['id']))
			{
				$pjPriceModel = pjPriceModel::factory();
				$arr = $pjPriceModel
					->select('t1.*, t2.owner_id')
					->join('pjListing', 't2.id=t1.listing_id', 'inner')
					->where('t1.id', $_POST['id'])
					->limit(1)
					->findAll()
					->getData();
				if (count($arr) === 0)
				{
					$response['code'] = 101;
				} elseif ($this->isOwner() && $arr[0]['owner_id'] != $this->getUserId()) {
					$response['code'] = 102;
				} else {
					$response['code'] = 103;
					if ($pjPriceModel->reset()->setAttributes(array('id' => $arr[0]['id']))->erase()->getAffectedRows() == 1)
					{
						$response['code'] = 200;
					}
				}
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
		
	public function pjActionDeleteListing()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjListingModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjListing')->where('foreign_id', $_GET['id'])->eraseAll();
				
				pjObject::import('Model', 'pjGallery:pjGallery');
				
				pjListingExtraModel::factory()->where('listing_id', $_GET['id'])->eraseAll();
				pjReservationModel::factory()->where('listing_id', $_GET['id'])->eraseAll();
				pjPriceModel::factory()->where('listing_id', $_GET['id'])->eraseAll();
				
				$pjGalleryModel = pjGalleryModel::factory();
				$arr = $pjGalleryModel->where('foreign_id', $_GET['id'])->findAll()->getData();
				if (count($arr) > 0)
				{
					foreach ($arr as $item)
					{
						foreach ($this->imageFiles as $file)
						{
							@clearstatcache();
							if (!empty($item[$file]) && is_file($item[$file]))
							{
								@unlink($item[$file]);
							}
						}
					}
					$pjGalleryModel->eraseAll();
				}
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteListingBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjObject::import('Model', 'pjGallery:pjGallery');
				pjListingModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjListing')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				
				pjListingExtraModel::factory()->whereIn('listing_id', $_POST['record'])->eraseAll();
				$arr = pjGalleryModel::factory()->whereIn('foreign_id', $_POST['record'])->findAll()->getData();
				if (count($arr) > 0)
				{
					pjGalleryModel::factory()->whereIn('foreign_id', $_POST['record'])->eraseAll();
					foreach ($arr as $item)
					{
						foreach ($this->imageFiles as $file)
						{
							@clearstatcache();
							if (!empty($item[$file]) && is_file($item[$file]))
							{
								@unlink($item[$file]);
							}
						}
					}
				}
			}
		}
		exit;
	}
	
	public function pjActionExpireListing()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjListingModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array('expire' => ':DATE_ADD(`expire`, INTERVAL 30 DAY)'));
			} elseif (isset($_GET['id']) && (int) $_GET['id'] > 0) {
				pjListingModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array('expire' => ':DATE_ADD(`expire`, INTERVAL 30 DAY)'));
			}
		}
		exit;
	}
	
	public function pjActionGetLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['locale']) && (int) $_GET['locale'] > 0)
			{
				pjAppController::setFields($_GET['locale']);
				
				$this->set('type_arr', pjTypeModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$_GET['locale']."'", 'inner')
					->where('t1.status', 'T')->orderBy('name ASC')->findAll()->getData()
				);
			}
		}
	}
	
	public function pjActionGetListing()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjObject::import('Model', 'pjGallery:pjGallery');
			$pjListingModel = pjListingModel::factory()
				->join('pjUser', 't2.id=t1.owner_id', 'left');
			
			if (isset($_GET['status']) && !empty($_GET['status']))
			{
				$pjListingModel->where('t1.status', $_GET['status']);
			}
			if (isset($_GET['is_featured']) && !empty($_GET['is_featured']))
			{
				$pjListingModel->where('t1.is_featured', $_GET['is_featured']);
			}
			
			if ($this->isOwner())
			{
				$pjListingModel->where('t1.owner_id', $this->getUserId());
			} else {
				if (isset($_GET['user_id']) && (int) $_GET['user_id'] > 0)
				{
					$pjListingModel->where('t1.owner_id', $_GET['user_id']);
				}
			}
			if (isset($_GET['type_id']) && (int) $_GET['type_id'] > 0)
			{
				$pjListingModel->where('t1.type_id', $_GET['type_id']);
			}
			if (isset($_GET['country_id']) && (int) $_GET['country_id'] > 0)
			{
				$pjListingModel->where('t1.country_id', $_GET['country_id']);
			}
			if (isset($_GET['listing_refid']) && !empty($_GET['listing_refid']))
			{
				$q = pjObject::escapeString($_GET['listing_refid']);
				$pjListingModel->where('t1.listing_refid LIKE', "%$q%");
			}
			if (isset($_GET['bedrooms_from']) && (int) $_GET['bedrooms_from'] > 0)
			{
				$pjListingModel->where('t1.listing_bedrooms >=', $_GET['bedrooms_from']);
			}
			if (isset($_GET['bedrooms_to']) && (int) $_GET['bedrooms_to'] > 0)
			{
				$pjListingModel->where('t1.listing_bedrooms <=', $_GET['bedrooms_to']);
			}
			if (isset($_GET['bathrooms_from']) && (int) $_GET['bathrooms_from'] > 0)
			{
				$pjListingModel->where('t1.listing_bathrooms >=', $_GET['bathrooms_from']);
			}
			if (isset($_GET['bathrooms_to']) && (int) $_GET['bathrooms_to'] > 0)
			{
				$pjListingModel->where('t1.listing_bathrooms <=', $_GET['bathrooms_to']);
			}
			if (isset($_GET['adults_from']) && (int) $_GET['adults_from'] > 0)
			{
				$pjListingModel->where('t1.listing_adults >=', $_GET['adults_from']);
			}
			if (isset($_GET['adults_to']) && (int) $_GET['adults_to'] > 0)
			{
				$pjListingModel->where('t1.listing_adults <=', $_GET['adults_to']);
			}
			if (isset($_GET['children_from']) && (int) $_GET['children_from'] > 0)
			{
				$pjListingModel->where('t1.listing_children >=', $_GET['children_from']);
			}
			if (isset($_GET['children_to']) && (int) $_GET['children_to'] > 0)
			{
				$pjListingModel->where('t1.listing_children <=', $_GET['children_to']);
			}
			if (isset($_GET['floor_area_from']) && (float) $_GET['floor_area_from'] > 0)
			{
				$pjListingModel->where('t1.listing_floor_area >=', $_GET['floor_area_from']);
			}
			if (isset($_GET['floor_area_to']) && (float) $_GET['floor_area_to'] > 0)
			{
				$pjListingModel->where('t1.listing_floor_area <=', $_GET['floor_area_to']);
			}
			
			$column = 'id';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjListingModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjListingModel->select(sprintf('t1.id, t1.listing_refid, t1.expire,	t1.status, t1.owner_id, t2.name AS owner_name,
				(SELECT `small_path` FROM `%s` WHERE foreign_id = t1.id ORDER BY `sort` ASC LIMIT 1) AS `image`', pjGalleryModel::factory()->getTable()))
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();

			$data = pjSanitize::clean($data);
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionGetGeocode()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$geo = pjAdminListings::pjActionGeocode($_POST);
			$response = array('code' => 100);
			if (isset($geo['lat']) && !is_array($geo['lat']))
			{
				$response = $geo;
				$response['code'] = 200;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	private static function pjActionGeocode($post)
	{
		$address = array();
		$address[] = $post['address_postcode'];
		$address[] = $post['address_content'];
		$address[] = $post['address_city'];
		$address[] = $post['address_state'];

		foreach ($address as $key => $value)
		{
			$tmp = preg_replace('/\s+/', '+', $value);
			$address[$key] = $tmp;
		}
		$_address = join(",+", $address);
							
		//http://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&sensor=false
		$gfile = "http://maps.googleapis.com/maps/api/geocode/json?address=$_address&sensor=false";
		$Http = new pjHttp();
		$response = $Http->request($gfile)->getResponse();

		$geoObj = pjAppController::jsonDecode($response);
		
		$data = array();
		$geoArr = (array) $geoObj;
		if ($geoArr['status'] == 'OK')
		{
			$geoArr['results'][0] = (array) $geoArr['results'][0];
			$geoArr['results'][0]['geometry'] = (array) $geoArr['results'][0]['geometry'];
			$geoArr['results'][0]['geometry']['location'] = (array) $geoArr['results'][0]['geometry']['location'];
			
			$data['lat'] = $geoArr['results'][0]['geometry']['location']['lat'];
			$data['lng'] = $geoArr['results'][0]['geometry']['location']['lng'];
		} else {
			$data['lat'] = NULL;
			$data['lng'] = NULL;
		}
		return $data;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor() || $this->isOwner())
		{
			$type_arr = pjTypeModel::factory()->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
			$this->set('type_arr', pjSanitize::clean($type_arr));
			
			$user_arr = pjUserModel::factory()->orderBy('t1.name ASC')->findAll()->getData();
			$this->set('user_arr', pjSanitize::clean($user_arr));
			
			$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
					
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminListings.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionExtend()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor() || $this->isOwner())
		{
			if (isset($_POST['extend']))
			{
				$pjListingModel = pjListingModel::factory();
			
				$arr = $pjListingModel->find($_POST['id'])->getData();
				$period_arr = pjPeriodModel::factory()->find($_POST['period_id'])->getData();

				if (count($arr) > 0 && count($period_arr) > 0)
				{
					$current = time();
					if ($arr['last_extend'] == 'paid' && !empty($arr['expire']) && $arr['expire'] != '0000-00-00')
					{
						$current = strtotime($arr['expire']);
					}
					$pjListingModel->modify(array(
						'last_extend' => 'free',
						'expire' => date("Y-m-d", $current + (int) $period_arr['days'] * 86400)
					));
				}
			}
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminListings&action=pjActionIndex&err=AL10");
			
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionPayment()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor() || $this->isOwner())
		{
			$arr = pjListingModel::factory()
				->select('t1.*, t2.content AS listing_title')
				->join('pjMultiLang', "t2.model='pjListing' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->find($_GET['id'])->getData();
				
			if (count($arr) === 0)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminListings&action=pjActionIndex&err=AL08");
			} elseif ($this->isOwner() && $arr['owner_id'] != $this->getUserId()) {
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminListings&action=pjActionIndex&err=AL09");
			}
			$this->set('arr', $arr);
			$this->set('period_arr', pjPeriodModel::factory()->orderBy('t1.days ASC')->findAll()->getData());
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionStatusListing()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0 && isset($_GET['status']) && in_array($_GET['status'], array('T', 'F', 'E')))
			{
				pjListingModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array('status' => $_GET['status']));
			}
		}
		exit;
	}
	
	public function pjActionSaveListing()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjListingModel = pjListingModel::factory();
			if (!in_array($_POST['column'], $pjListingModel->getI18n()))
			{
				if (in_array($_POST['column'], array('expire')))
				{
					$_POST['value'] = pjUtil::formatDate($_POST['value'], $this->option_arr['o_date_format']);
				}
				$value = $_POST['value'];
				//$value = pjSanitize::clean($_POST['value']);
				$stop = false;
				if ($_POST['column'] == 'listing_refid' && $pjListingModel->where('t1.id !=', $_GET['id'])->where('t1.listing_refid', $_POST['value'])->findCount()->getData() > 0)
				{
					$stop = true;
				}
				if (!$stop)
				{
					$pjListingModel->reset()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $value));
				}
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjListing');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
			
		if ($this->isAdmin() || $this->isEditor() || $this->isOwner())
		{
			$ListingExtraModel = new pjListingExtraModel();
				
			if (isset($_POST['listing_update']))
			{
				$arr = pjListingModel::factory()->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminListings&action=pjActionIndex&err=AL08");
				}
				
				$err = NULL;
				$data = array();
				
				if ($this->isOwner())
				{
					if ($this->option_arr['o_allow_add_property'] == 'No')
					{
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminListings&action=pjActionIndex&err=AL09");
					}
					
					if ($arr['owner_id'] != $this->getUserId())
					{
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminListings&action=pjActionIndex&err=AL09");
					}
					// unset owner id in post
					unset($_POST['owner_id']);
					$data['owner_id'] = $arr['owner_id'];
					$data['expire'] = $arr['expire'];
					$data['status'] = $arr['status'];
				}
				
				if ($this->isEditor())
				{
					$data['owner_id'] = $arr['owner_id'];
				}
				
				$data['o_accept_bookings'] = isset($_POST['o_accept_bookings']) ? 1 : 0;
				$data['o_disable_payments'] = isset($_POST['o_disable_payments']) ? 1 : 0;
				$data['o_allow_paypal'] = isset($_POST['o_allow_paypal']) ? 1 : 0;
				$data['o_allow_authorize'] = isset($_POST['o_allow_authorize']) ? 1 : 0;
				$data['o_allow_creditcard'] = isset($_POST['o_allow_creditcard']) ? 1 : 0;
				$data['o_allow_bank'] = isset($_POST['o_allow_bank']) ? 1 : 0;
				$data['modified'] = date("Y-m-d H:i:s");
				if (!$this->isOwner())
				{
					$data['expire'] = pjUtil::formatDate($_POST['expire'], $this->option_arr['o_date_format']);
				}
				$geo = array();
				if (!isset($_POST['lat']) || empty($_POST['lat']) || !isset($_POST['lng']) || empty($_POST['lng']))
				{
					$geo = pjAdminListings::pjActionGeocode($_POST);
				}

				$pjListingModel = pjListingModel::factory();
				$post = array_merge($_POST, $data, $geo);

				if (!$pjListingModel->validates($post))
				{
					pjUtil::redirect(sprintf("%s?controller=pjAdminListings&action=pjActionUpdate&id=%u&locale=%u&tab_id=%s&err=AL02", $_SERVER['PHP_SELF'], $_POST['id'], $_POST['locale'], $_POST['tab_id']));
				}
				
				if ($pjListingModel->where('t1.id !=', $_POST['id'])->where('t1.listing_refid', $post['listing_refid'])->findCount()->getData() > 0)
				{
					pjUtil::redirect(sprintf("%s?controller=pjAdminListings&action=pjActionUpdate&id=%u&locale=%u&tab_id=%s&err=AL02", $_SERVER['PHP_SELF'], $_POST['id'], $_POST['locale'], $_POST['tab_id']));
				}
				
				//$post = pjSanitize::clean($post, array('encode' => false));
				
				$pjListingModel->set('id', $_POST['id'])->modify($post);

				if (isset($_POST['i18n']))
				{
					/*$i18n = array();
					foreach ($_POST['i18n'] as $i => $stack)
					{
						foreach ($stack as $key => $value)
						{
							if ($key == 'description')
							{
								$i18n[$i][$key] = $value;
							} else {
								$i18n[$i][$key] = pjSanitize::clean($value, array('encode' => false));
							}
						}
					}
				
					pjMultiLangModel::factory()->updateMultiLang($i18n, $_POST['id'], 'pjListing');*/
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjListing');
				}
				
				pjListingExtraModel::factory()->where('listing_id', $_POST['id'])->eraseAll();
				if (isset($_POST['extra']) && is_array($_POST['extra']) && count($_POST['extra']) > 0)
				{
					$ListingExtraModel->begin();
					foreach ($_POST['extra'] as $extra_id)
					{
						$ListingExtraModel->setAttributes(array(
							'listing_id' => $_POST['id'],
							'extra_id' => $extra_id
						))->insert();
					}
					$ListingExtraModel->commit();
				}
				
				$PriceModel = pjPriceModel::factory();
				
				$PriceModel->where('listing_id', $_POST['id'])->eraseAll();
				if (isset($_POST['price']))
				{
					$data = array();
					$data['id'] = NULL;
					$data['listing_id'] = $_POST['id'];
					foreach ($_POST['price'] as $i => $price)
					{
						if (!empty($_POST['date_from'][$i]) && !empty($_POST['date_to'][$i]) && (float) $_POST['price'][$i] > 0)
						{
							$data['date_from'] = pjUtil::formatDate($_POST['date_from'][$i], $this->option_arr['o_date_format']);
							$data['date_to'] = pjUtil::formatDate($_POST['date_to'][$i], $this->option_arr['o_date_format']);
							$data['price'] = $_POST['price'][$i];
							$PriceModel->reset()->setAttributes(array_merge($_POST, $data))->insert();
						}
					}
				}
				$err = "AL01";
				pjUtil::redirect(sprintf("%s?controller=pjAdminListings&action=pjActionUpdate&id=%u&locale=%u&tab_id=%s&err=%s", $_SERVER['PHP_SELF'], $_POST['id'], $_POST['locale'], $_POST['tab_id'], $err));
				
			} else {
				$arr = pjListingModel::factory()
					->select(sprintf("t1.*, (SELECT COUNT(*) FROM `%s` WHERE `listing_id` = t1.id LIMIT 1) AS `reservations`", pjReservationModel::factory()->getTable()))
					->find($_GET['id'])->getData();
				
				if (count($arr) === 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminListings&action=pjActionIndex&err=AL08");
				}
				if ($this->isOwner() && $arr['owner_id'] != $this->getUserId())
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminListings&action=pjActionIndex&err=AL09");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjListing');
				$this->set('arr', $arr);
				
				pjObject::import('Model', 'pjGallery:pjGallery');
									
				$type_arr = pjTypeModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
				$this->set('type_arr', pjSanitize::clean($type_arr));
				
				$this->set('gallery_arr', pjGalleryModel::factory()->where('foreign_id', $arr['id'])->findAll()->getData());
				
				$extra_arr = pjExtraModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
				$this->set('extra_arr', pjSanitize::clean($extra_arr));
					
				$this->set('listing_extra_arr', pjListingExtraModel::factory()->where('t1.listing_id', $arr['id'])->findAll()->getDataPair(NULL, 'extra_id'));
				
				$country_arr = pjCountryModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
				$this->set('country_arr', pjSanitize::clean($country_arr));
				
				$this->set('price_arr', pjPriceModel::factory()->where('listing_id', $arr['id'])->orderBy('t1.date_from ASC')->findAll()->getData());
				$user_arr = pjUserModel::factory()->findAll()->getData();
				$this->set('user_arr', pjSanitize::clean($user_arr));
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
			
				# jQuery Fancybox
				$this->appendJs('jquery.fancybox.pack.js', PJ_THIRD_PARTY_PATH . 'fancybox/js/');
				$this->appendCss('jquery.fancybox.css', PJ_THIRD_PARTY_PATH . 'fancybox/css/');
				
				# TinyMCE
				$this->appendJs('tiny_mce.js', PJ_THIRD_PARTY_PATH . 'tiny_mce/');
				
				# Gallery plugin
				$this->appendCss('pj-gallery.css', pjObject::getConstant('pjGallery', 'PLUGIN_CSS_PATH'));
				$this->appendJs('ajaxupload.js', pjObject::getConstant('pjGallery', 'PLUGIN_JS_PATH'));
				$this->appendJs('jquery.gallery.js', pjObject::getConstant('pjGallery', 'PLUGIN_JS_PATH'));
				
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminListings.js');
				$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>