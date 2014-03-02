<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjRegistry
{
	private static $instance;
	
	private $objects = array();
	
	private function __construct()
	{
		//prevent directly access.
	}
	
	public function __clone()
	{
		trigger_error("Clone is not allowed.", E_USER_ERROR);
	}
	
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	public function get($key)
	{
		if (isset($this->objects[$key]))
		{
			return $this->objects[$key];
		}
		
		return NULL;
	}
	
	public function set($key, $val)
	{
		$this->objects[$key] = $val;
	}
	
	public function is($key)
	{
		return ($this->get($key) !== null);
	}
}
?>