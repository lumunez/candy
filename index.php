<?php
if (!headers_sent())
{
	session_name('VacationRentals');
	@session_start();
}
if (in_array($_SERVER['SERVER_ADDR'], array('127.0.0.1', '192.168.1.99', '::1')))
{
	ini_set("display_errors", "On");
	ini_set("display_startup_errors", "On");
	error_reporting(E_ALL);
} else {
	ini_set("display_errors", "Off");
	ini_set("display_startup_errors", "Off");
	error_reporting(0);
}
header("Content-type: text/html; charset=utf-8");
if (!defined("ROOT_PATH"))
{
	define("ROOT_PATH", dirname(__FILE__) . '/');
}
require_once ROOT_PATH . 'app/controllers/components/pjUtil.component.php';
require ROOT_PATH . 'app/config/options.inc.php';
if (!isset($_GET['controller']) || empty($_GET['controller']))
{
	header("HTTP/1.1 301 Moved Permanently");
	pjUtil::redirect(PJ_INSTALL_URL . basename($_SERVER['PHP_SELF'])."?controller=pjAdmin&action=pjActionIndex");
}

if (isset($_GET['controller']))
{
	require_once PJ_FRAMEWORK_PATH . 'pjObserver.class.php';
	$pjObserver = pjObserver::factory();
	$pjObserver->init();
}
?>