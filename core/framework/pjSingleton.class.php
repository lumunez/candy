<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjSingleton
{
	private static $instances = array();
	
	private function __construct()
	{
		//Locked down the constructor, therefore the class cannot be externally instantiated
	}
	
	public function __clone()
	{
		trigger_error("Cannot clone instance of Singleton pattern", E_USER_ERROR);
	}
	
	public function __wakeup()
	{
		trigger_error("Cannot deserialize instance of Singleton pattern", E_USER_ERROR);
	}
	
	public static function getInstance($className, $params=array())
	{
		if (!is_array(self::$instances))
		{
			self::$instances = array();
		}
		
		if (!isset(self::$instances[$className]))
		{
			if (count($params) === 0)
			{
				self::$instances[$className] = new $className;
			} else {
				$params = "'" . join("','", $params) . "'";
				eval('self::$instances[$className] = new $className('.$params.');');
			}
		}
		
		return self::$instances[$className];
	}
}
?>