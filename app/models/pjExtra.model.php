<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjExtraModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'extras';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'type', 'type' => 'varchar', 'default' => ':NULL')
	);
	
	protected $validate = array(
		'rules' => array(
			'type' => 'pjActionRequired'
		)
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjExtraModel($attr);
	}
}
?>