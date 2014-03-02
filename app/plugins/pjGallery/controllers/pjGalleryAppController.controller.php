<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjGalleryAppController extends pjPlugin
{
	public function __construct()
	{
		$this->setLayout('pjActionAdmin');
	}
	
	public static function getConst($const)
	{
		$registry = pjRegistry::getInstance();
		$store = $registry->get('pjGallery');
		return isset($store[$const]) ? $store[$const] : NULL;
	}
	
	public function pjActionBeforeInstall()
	{
		$this->setLayout('pjActionEmpty');
		
		$result = array('code' => 200, 'info' => array());
		$folders = array('app/web/upload', 'app/web/upload/large', 'app/web/upload/medium', 'app/web/upload/small', 'app/web/upload/source');
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$result['code'] = 101;
				$result['info'][] = sprintf('You need to set write permissions (chmod 777) to directory located at %s', $dir);
			}
		}
		
		return $result;
	}
}
?>