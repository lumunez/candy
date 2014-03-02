<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAppController.controller.php';
class pjFront extends pjAppController
{
	public $defaultCaptcha = 'StivaSoftCaptcha';
	
	public $defaultLocale = 'front_locale_id';
	
	public function __construct()
	{
		$this->setLayout('pjActionFront');
	}
	
	public function afterFilter()
	{
		switch ($this->option_arr['o_layout'])
		{
			case 'layout_1_list':
			case 'layout_1_grid':
				$this->appendCss('front_layout_1.css');
				break;
			case 'layout_2_list':
			case 'layout_2_grid':
				$this->appendCss('front_layout_2.css');
				break;
			case 'layout_3_list':
				$this->appendCss('front_layout_3.css');
				break;
			default:
				$this->appendCss('front_layout_1.css');
				break;
		}
		$this->appendCss('front_lib.css');
		
		$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
			->join('pjLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')->findAll()->getData();
		
		$this->set('locale_arr', $locale_arr);
	}
	
	public function beforeFilter()
	{
		$OptionModel = pjOptionModel::factory();
		$this->option_arr = $OptionModel->getPairs($this->getForeignId());
		$this->set('option_arr', $this->option_arr);
		$this->setTime();
		
		//$this->appendCss('front.css');
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
	
	public function beforeRender()
	{
		if (isset($_GET['iframe']))
		{
			$this->setLayout('pjActionIframe');
		}
	}
	
	public function pjActionCaptcha()
	{
		$this->setAjax(true);
		
		$Captcha = new pjCaptcha('app/web/obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$Captcha->setImage('app/web/img/button.png')->init(isset($_GET['rand']) ? $_GET['rand'] : null);
	}

	public function pjActionSetLocale()
	{
		$this->setLocaleId(@$_GET['locale']);
		pjUtil::redirect($_SERVER['HTTP_REFERER']);
	}
}
?>