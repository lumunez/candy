<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjObserver
{
	private $controller;
	
	public static function factory($attr=array())
	{
		return new pjObserver($attr);
	}
	
	public function init()
	{
		require_once PJ_FRAMEWORK_PATH . 'pjObject.class.php';
		require_once PJ_FRAMEWORK_PATH . 'pjDispatcher.class.php';
		
		if (isset($GLOBALS['CONFIG']['plugins']))
		{
			pjObject::import('Plugin', $GLOBALS['CONFIG']['plugins']);
		}
		
		$Dispatcher = new pjDispatcher();
		$Dispatcher->dispatch($_GET, array());
		$this->controller = $Dispatcher->getController();
	}
	
	public function getController()
	{
		return $this->controller;
	}
}
?>