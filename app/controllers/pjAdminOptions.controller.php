<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAdmin.controller.php';
class pjAdminOptions extends pjAdmin
{
	public function pjActionDeletePeriod()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array('code' => 100);
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$response['code'] = 101;
				if (pjPeriodModel::factory($_POST['id'])->erase()->getAffectedRows() == 1)
				{
					$response['code'] = 200;
				}
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			
			$this->set('arr', $arr);
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionInstall()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->set('o_arr', $this->models['Option']
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.key ASC')
				->findAll()
				->getData()
			);
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionNotifications()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->set('o_arr', pjOptionModel::factory()->getPairs($this->getForeignId()));
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionPreview()
	{
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
	}
	
	public function pjActionPreviewSearch()
	{
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
	}
	
	public function pjActionPreviewFeatured()
	{
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
	}
	
	public function pjActionSubmissions()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$OptionModel = new pjOptionModel();
			$this->set('arr', $OptionModel->where('t1.foreign_id', $this->getForeignId())->orderBy('t1.order ASC')->findAll()->getData());
			$this->set('o_arr', $OptionModel->getPairs($this->getForeignId()));
			$this->set('period_arr', pjPeriodModel::factory()->orderBy('t1.days ASC')->findAll()->getData());
			
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_POST['options_update']))
			{
				$OptionModel = new pjOptionModel();
			
				foreach ($_POST as $key => $value)
				{
					if (preg_match('/value-(string|text|int|float|enum|bool|color)-(.*)/', $key) === 1)
					{
						list(, $type, $k) = explode("-", $key);
						if (!empty($k))
						{
							$OptionModel
								->reset()
								->where('foreign_id', $this->getForeignId())
								->where('`key`', $k)
								->limit(1)
								->modifyAll(array('value' => $value));
						}
					}
				}
				
				if (isset($_POST['price']) && count($_POST['price']) > 0 && isset($_POST['days']) && count($_POST['days']) > 0)
				{
					$pjPeriodModel = pjPeriodModel::factory();
					foreach ($_POST['price'] as $key => $value)
					{
						if (strpos($key, 'new_') === 0)
						{
							$pjPeriodModel->reset()->setAttributes(array('days' => $_POST['days'][$key], 'price' => $_POST['price'][$key]))->insert();
						} else {
							$pjPeriodModel->reset()->setAttributes(array('id' => $key))->modify(array('days' => $_POST['days'][$key], 'price' => $_POST['price'][$key]));
						}
					}
				}
				$err = NULL;
				if (isset($_POST['next_action']))
				{
					switch ($_POST['next_action'])
					{
						case 'pjActionIndex':
							$err = 'AO01';
							break;
						case 'pjActionSubmissions':
							$err = 'AO02';
							break;
						case 'pjActionInstall':
							$err = 'AO03';
							break;
						case 'pjActionNotifications':
							$err = 'AO04';
							break;
					}
				}
				$tab = NULL;
				if (isset($_POST['tab']))
				{
					$tab = "&tab=" . $_POST['tab'];
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=" . @$_POST['next_action'] . "&err=$err" . $tab);
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>