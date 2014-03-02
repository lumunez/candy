<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjUserNotificationModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'users_notifications';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'notification_id', 'type' => 'tinyint', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'enum', 'default' => ':NULL')
	);
	
	protected $validate = array(
		'rules' => array(
			'user_id' => array(
				'pjActionNumeric' => true,
				'pjActionRequired' => true
			),
			'notification_id' => array(
				'pjActionNumeric' => true,
				'pjActionRequired' => true
			),
			'type' => array(
				'pjActionRequired' => true,
				'pjActionNotEmpty' => true
			)
		)
	);
	
	public static function factory($attr=array())
	{
		return new pjUserNotificationModel($attr);
	}
}
?>