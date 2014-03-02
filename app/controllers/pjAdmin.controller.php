<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAppController.controller.php';
class pjAdmin extends pjAppController
{
	public $defaultUser = 'admin_user';
	
	public $requireLogin = true;
	
	public function __construct($requireLogin=null)
	{
		$this->setLayout('pjActionAdmin');
		
		if (!is_null($requireLogin) && is_bool($requireLogin))
		{
			$this->requireLogin = $requireLogin;
		}
		
		if ($this->requireLogin)
		{
			if (!$this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin', 'pjActionForgot', 'pjActionPreview')))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
			}
		}
	}
	
	public function beforeRender()
	{
		
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor() || $this->isOwner())
		{
			pjObject::import('Model', 'pjGallery:pjGallery');
			$pjUserModel = pjUserModel::factory();
			$pjListingModel = pjListingModel::factory();
			$pjReservationModel = pjReservationModel::factory();

			$pjListingModel
				->select(sprintf("t1.id, t1.views, t1.address_city, t2.content AS title, t3.content AS type,
					(SELECT `small_path` FROM `%s` WHERE `foreign_id` = `t1`.`id` ORDER BY `sort` ASC LIMIT 1) AS `pic`,
					(SELECT COUNT(*) FROM `%s` WHERE `listing_id` = `t1`.`id` LIMIT 1) AS `reservations`",
					pjGalleryModel::factory()->getTable(), $pjReservationModel->getTable()))
				->join('pjMultiLang', "t2.model='pjListing' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->join('pjMultiLang', "t3.model='pjType' AND t3.foreign_id=t1.type_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left')
				->limit(3)
				->orderBy('t1.views DESC');
			if ($this->isOwner())
			{
				$pjListingModel->where('t1.owner_id', $this->getUserId());
			}
			$listing_arr = $pjListingModel->findAll()->getData();
			$this->set('listing_arr', $listing_arr);
			
			if ($this->isOwner())
			{
				$pjListingModel->where('t1.is_featured', 'T');
				$this->set('featured_arr', $pjListingModel->findAll()->getData());
			}
			
			$pjReservationModel
				->select("t1.id, t1.name, t1.created, t1.date_from, t1.date_to, t2.owner_id, t3.content AS title")
				->join('pjListing', 't2.id=t1.listing_id', 'inner')
				->join('pjMultiLang', "t3.model='pjListing' AND t3.foreign_id=t1.listing_id AND t3.field='title' AND t3.locale='".$this->getLocaleId()."'", 'left')
				->limit(5)
				->orderBy('t1.created DESC');
			
			if ($this->isOwner())
			{
				$pjReservationModel->where('owner_id', $this->getUserId());
			}
			
			$reservation_arr = $pjReservationModel->findAll()->getData();
			$this->set('reservation_arr', $reservation_arr);
			
			if (!$this->isOwner())
			{
				$user_arr = $pjUserModel
					->select(sprintf("t1.id, t1.name, t1.email, t1.last_login,
						(SELECT COUNT(*) FROM `%s` WHERE `owner_id` = `t1`.`id` LIMIT 1) AS `listings`",
						$pjListingModel->getTable()))
					->orderBy('listings DESC')
					->limit(4)->findAll()->getData();
				$this->set('user_arr', $user_arr);
			}
			
			$condition = NULL;
			if ($this->isOwner())
			{
				$condition = " AND `owner_id` = :owner_id";
			}

			$sth = sprintf("SELECT 1,
				(SELECT COUNT(*) FROM `%1\$s` WHERE 1 %4\$s LIMIT 1) AS `listings`,
				(SELECT COUNT(*) FROM `%2\$s` INNER JOIN `%1\$s` AS t2 ON t2.id = `listing_id` WHERE 1 %4\$s LIMIT 1) AS `reservations`,
				(SELECT COUNT(*) FROM `%3\$s` WHERE 1 LIMIT 1) AS `users`,
				(SELECT COUNT(*) FROM `%1\$s` WHERE 1 AND `is_featured` = :is_featured %4\$s LIMIT 1) AS `featured`",
				$pjListingModel->getTable(), $pjReservationModel->getTable(), $pjUserModel->getTable(), $condition
			);
			
			$info_arr = $pjListingModel->reset()->prepare($sth)->exec(array(
				'owner_id' => $this->getUserId(),
				'is_featured' => 'T'
			))->getData();
			$this->set('info_arr', $info_arr);

		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionForgot()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['forgot_user']))
		{
			if (!isset($_POST['forgot_email']) || !pjValidation::pjActionNotEmpty($_POST['forgot_email']) || !pjValidation::pjActionEmail($_POST['forgot_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			}
			$pjUserModel = pjUserModel::factory();
			$user = $pjUserModel
				->where('t1.email', $_POST['forgot_email'])
				->limit(1)
				->findAll()
				->getData();
				
			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			} else {
				$user = $user[0];
				
				$Email = new pjEmail();
				$Email
					->setTo($user['email'])
					->setFrom($user['email'])
					->setSubject($this->option_arr['o_email_password_reminder_subject']);
				
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
				
				$body = str_replace(
					array('{Name}', '{Password}', '{Email}'),
					array($user['name'], $user['password'], $user['email']),
					$this->option_arr['o_email_password_reminder']
				);

				if ($Email->send($body))
				{
					$err = "AA11";
				} else {
					$err = "AA12";
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=$err");
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionMessages()
	{
		$this->setAjax(true);
		header("Content-Type: text/javascript; charset=utf-8");
	}
	
	public function pjActionLogin()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['login_user']))
		{
			if (!isset($_POST['login_email']) || !isset($_POST['login_password']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_email']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_password']) ||
				!pjValidation::pjActionEmail($_POST['login_email']))
			{
				// Data not validate
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=4");
			}
			$pjUserModel = pjUserModel::factory();

			$user = $pjUserModel
				->where('t1.email', $_POST['login_email'])
				->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", pjObject::escapeString($_POST['login_password']), PJ_SALT))
				->limit(1)
				->findAll()
				->getData();

			if (count($user) != 1)
			{
				# Login failed
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=1");
			} else {
				$user = $user[0];
				unset($user['password']);
															
				if (!in_array($user['role_id'], array(1,2,3)))
				{
					# Login denied
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['role_id'] == 3 && $user['is_active'] == 'F')
				{
					# Login denied
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['status'] != 'T')
				{
					# Login forbidden
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=3");
				}
				
				# Login succeed
				$last_login = date("Y-m-d H:i:s");
    			$_SESSION[$this->defaultUser] = $user;
    			
    			# Update
    			$data = array();
    			$data['last_login'] = $last_login;
    			$pjUserModel->reset()->setAttributes(array('id' => $user['id']))->modify($data);

    			if ($this->isAdmin())
    			{
	    			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");
    			}
    			
				if ($this->isEditor())
    			{
	    			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");
    			}
    			
				if ($this->isOwner())
    			{
	    			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");
    			}
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionLogout()
	{
		if ($this->isLoged())
        {
        	unset($_SESSION[$this->defaultUser]);
        }
       	pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
	}
	
	public function pjActionProfile()
	{
		$this->checkLogin();
		
		if ($this->isOwner() || $this->isEditor())
		{
			if (isset($_POST['profile_update']))
			{
				$pjUserModel = pjUserModel::factory();
				$arr = $pjUserModel->find($this->getUserId())->getData();
				$data = array();
				$data['role_id'] = $arr['role_id'];
				$data['status'] = $arr['status'];
				$post = array_merge($_POST, $data);
				if (!$pjUserModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA14");
				}
				$pjUserModel->set('id', $this->getUserId())->modify($post);
				
				$pjUserNotification = pjUserNotificationModel::factory();
				$pjUserNotification->where('user_id', $this->getUserId())->eraseAll();
				if (isset($_POST['notify_email']) && is_array($_POST['notify_email']) && count($_POST['notify_email']) > 0)
				{
					$pjUserNotification->begin();
					foreach ($_POST['notify_email'] as $notification_id)
					{
						$pjUserNotification
							->reset()
							->set('user_id', $this->getUserId())
							->set('notification_id', $notification_id)
							->set('type', 'email')
							->insert();
					}
					$pjUserNotification->commit();
				}
				
				if (isset($_POST['notify_sms']) && is_array($_POST['notify_sms']) && count($_POST['notify_sms']) > 0)
				{
					$pjUserNotification->begin();
					foreach ($_POST['notify_sms'] as $notification_id)
					{
						$pjUserNotification
							->reset()
							->set('user_id', $this->getUserId())
							->set('notification_id', $notification_id)
							->set('type', 'sms')
							->insert();
					}
					$pjUserNotification->commit();
				}
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA13");
			} else {
				$this->set('arr', pjUserModel::factory()->find($this->getUserId())->getData());
				$pjUserNotification = pjUserNotificationModel::factory();
				$this->set('email_arr', $pjUserNotification->reset()->where('t1.user_id', $this->getUserId())->where('t1.type', 'email')->findAll()->getDataPair('id', 'notification_id'));
				$this->set('sms_arr', $pjUserNotification->reset()->where('t1.user_id', $this->getUserId())->where('t1.type', 'sms')->findAll()->getDataPair('id', 'notification_id'));
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendJs('pjAdmin.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>