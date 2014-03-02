<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once dirname(__FILE__) . '/pjOneAdminAppController.controller.php';
class pjOneAdmin extends pjOneAdminAppController
{
	public function pjActionDelete()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			$response = array();
			pjObject::import('Model', 'pjOneAdmin:pjOneAdmin');
			if (pjOneAdminModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjObject::import('Model', 'pjOneAdmin:pjOneAdmin');
				pjOneAdminModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			pjObject::import('Model', 'pjOneAdmin:pjOneAdmin');
			$pjOneAdminModel = pjOneAdminModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjOneAdminModel->where('t1.name LIKE', "%$q%");
			}
				
			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjOneAdminModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjOneAdminModel
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
						
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjOneAdmin.js', $this->getConst('PLUGIN_JS_PATH'));
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionMenu()
	{
		$this->checkLogin();
		
		$this->setAjax(true);
		
		pjObject::import('Model', 'pjOneAdmin:pjOneAdmin');
		$this->set('arr', pjOneAdminModel::factory()->orderBy('t1.name ASC')->findAll()->getData());
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			$response = array();
			pjObject::import('Model', 'pjOneAdmin:pjOneAdmin');
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				pjOneAdminModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
				$response['code'] = 201;
			} else {
				$insert_id = pjOneAdminModel::factory(array('name' => 'Script name', 'url' => 'http://www.example.com/'))->insert()->getInsertId();
				if ($insert_id !== false && (int) $insert_id > 0)
				{
					$response['code'] = 200;
					$response['id'] = $insert_id;
				} else {
					$response['code'] = 100;
				}
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
}
?>