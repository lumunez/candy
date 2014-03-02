<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAdmin.controller.php';
class pjAdminLocales extends pjAdmin
{
	public function pjActionArrays()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin())
		{
			$this->set('status', 2);
			return;
		}
		
		$this->pjActionInitFields('arrays');
	}
	
	public function pjActionBackend()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin())
		{
			$this->set('status', 2);
			return;
		}
		
		$this->pjActionInitFields('backend');
	}
	
	public function pjActionFrontend()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin())
		{
			$this->set('status', 2);
			return;
		}
		
		$this->pjActionInitFields('frontend');
	}
	
	public function pjActionSaveFields()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin())
		{
			$this->set('status', 2);
			return;
		}
		
		if (isset($_POST['i18n']) && count($_POST['i18n']) > 0)
		{
			$MultiLangModel = new pjMultiLangModel();
			$MultiLangModel->begin();
			foreach ($_POST['i18n'] as $locale_id => $arr)
			{
				foreach ($arr as $foreign_id => $locale_arr)
				{
					$data = array();
					$data[$locale_id] = array();
					foreach ($locale_arr as $name => $content)
					{
						$data[$locale_id][$name] = $content;
					}
					$MultiLangModel->updateMultiLang($data, $foreign_id, 'pjField');
				}
			}
			$MultiLangModel->commit();
		}
		pjUtil::redirect(sprintf("%sindex.php?controller=pjAdminLocales&action=%s&err=ALC01&tab=1&q=%s&locale=%u&page=%u", PJ_INSTALL_URL, $_POST['next_action'], urlencode($_POST['q']), $_POST['locale'], $_POST['page']));
		exit;
	}

	private function pjActionInitFields($field)
	{
		$this->set('field_arr', pjFieldModel::factory()->findAll()->getDataPair('id', 'label'));
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
		
		$pjFieldModel = pjFieldModel::factory()
			->join('pjMultiLang', "t2.model='pjField' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."'", 'left')
			->where('t1.type', $field);
		if (isset($_GET['q']) && !empty($_GET['q']))
		{
			$q = pjObject::escapeString($_GET['q']);
			$pjFieldModel->where("(t1.label LIKE '%$q%' OR t2.content LIKE '%$q%')");
		}
		$pjMultiLangModel = pjMultiLangModel::factory()->where('model', 'pjField')->where('field', 'title');
		
		$total = $pjFieldModel->findCount()->getData();
		$row_count = 15;
		$pages = ceil($total / $row_count);
		$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
		$offset = ((int) $page - 1) * $row_count;
		
		$_arr = $pjFieldModel->select("t1.*")->limit($row_count, $offset)->findAll()->getData();

		foreach ($_arr as $_k => $_v)
		{
			$tmp = $pjMultiLangModel->reset()
				->select('t1.*, t2.is_default')
				->join('pjLocale', 't2.id=t1.locale', 'left')
				->where('model', 'pjField')
				->where('field', 'title')
				->where('foreign_id', $_v['id'])
				->findAll()
				->getData();
			$_arr[$_k]['i18n'] = array();
			foreach ($tmp as $item)
			{
				$_arr[$_k]['i18n'][$item['locale']] = $item;
			}
		}

		$this->set($field, $_arr);
		$this->set('paginator', compact('pages'));
		
		$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
		$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
		$this->appendJs('pjAdminLocales.js');
		$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
	}
	
	private function pjActionCheckDefault()
	{
		if (0 == pjLocaleModel::factory()->where('is_default', 1)->findCount()->getData())
		{
			pjLocaleModel::factory()->limit(1)->modifyAll(array('is_default' => 1));
		}
	}
	
	public function pjActionDeleteLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			if (pjLocaleModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('locale', $_GET['id'])->eraseAll();
				$response['code'] = 200;
				
				$this->pjActionCheckDefault();
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionGetLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjLocaleModel = pjLocaleModel::factory();
			
			$column = 't1.sort';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjLocaleModel->findCount()->getData();
			$rowCount = 100;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjLocaleModel->select('t1.*, t2.title, t2.file')
				->join('pjLanguage', 't2.iso=t1.language_iso', 'left')
				->orderBy("$column $direction")->findAll()->getData();
						
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin())
		{
			$this->set('status', 2);
			return;
		}
		
		$this->set('language_arr', pjLanguageModel::factory()->where('t1.file IS NOT NULL')->orderBy('t1.title ASC')->findAll()->getData());
			
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminLocales.js');
		$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
	}
		
	public function pjActionSaveLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				pjLocaleModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
				$response['code'] = 201;
			} else {
				$arr = pjLocaleModel::factory()->select('t1.sort')->orderBy('t1.sort DESC')->limit(1)->findAll()->getData();
				$sort = 1;
				if (count($arr) === 1)
				{
					$sort = (int) $arr[0]['sort'] + 1;
				}
				$insert_id = pjLocaleModel::factory(array('sort' => $sort, 'is_default' => '0'))->insert()->getInsertId();
				if ($insert_id !== false && (int) $insert_id > 0)
				{
					$response['code'] = 200;
					$response['id'] = $insert_id;
					
					$locale_id = NULL;
					$arr = pjLocaleModel::factory()->findAll()->getData();
					foreach ($arr as $locale)
					{
						if ($locale['language_iso'] == 'en')
						{
							$locale_id = $locale['id'];
							break;
						}
					}
					if (is_null($locale_id) && count($arr) > 0)
					{
						$locale_id = $arr[0]['id'];
					}
					if (!is_null($locale_id))
					{
						$sql = sprintf("INSERT INTO `%1\$s` (`foreign_id`, `model`, `locale`, `field`, `content`)
							SELECT t1.foreign_id, t1.model, :insert_id, t1.field, t1.content
							FROM `%1\$s` AS t1
							WHERE t1.locale = :locale", pjMultiLangModel::factory()->getTable());
						pjMultiLangModel::factory()->prepare($sql)->exec(array(
							'insert_id' => $insert_id,
							'locale' => (int) $locale_id
						));
					}
				} else {
					$response['code'] = 100;
				}
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionSaveDefault()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjLocaleModel::factory()
				->where(1,1)
				->modifyAll(array('is_default' => '0'))
				->reset()
				->set('id', $_POST['id'])
				->modify(array('is_default' => 1));
				
			$this->setLocaleId($_POST['id']);
		}
		exit;
	}
	
	public function pjActionSortLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$LocaleModel = new pjLocaleModel();
			$arr = $LocaleModel->whereIn('id', $_POST['sort'])->orderBy("t1.sort ASC")->findAll()->getDataPair('id', 'sort');
			$fliped = array_flip($_POST['sort']);
			$combined = array_combine(array_keys($fliped), $arr);
			$LocaleModel->begin();
			foreach ($combined as $id => $sort)
			{
				$LocaleModel->setAttributes(compact('id'))->modify(compact('sort'));
			}
			$LocaleModel->commit();
		}
		exit;
	}
}
?>