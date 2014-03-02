<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAppController.controller.php';
class pjPlugin extends pjAppController
{
	public function pjActionBeforeInstall()
	{
		
	}
}
?>