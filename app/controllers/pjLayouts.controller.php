<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAppController.controller.php';
class pjLayouts extends pjAppController
{
	public function pjActionAdminLogin(){}
	
	public function pjActionAdmin(){}
	
	public function pjActionEmpty(){}
	
	public function pjActionFeatured(){}
	
	public function pjActionFront(){}
	
	public function pjActionIframe(){}
	
	public function pjActionListings(){}
	
	public function pjActionPlugin(){}
	
	public function pjActionSearch(){}
}
?>