<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjLanguageModel extends pjAppModel
{
	protected $primaryKey = 'iso';
	
	protected $table = 'languages';
	
	protected $schema = array(
		array('name' => 'iso', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'title', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'file', 'type' => 'varchar', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjLanguageModel($attr);
	}
}
?>