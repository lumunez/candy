<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjReservationModel extends pjAppModel
{
	protected $primaryKey = 'id';

	protected $table = 'reservations';

	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'listing_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'date_from', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'date_to', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'notes', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'ip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'payment_method', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'amount', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'deposit', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'tax', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'security', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'cc_type', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'cc_num', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'cc_exp', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'cc_code', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'txn_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'processed_on', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => ':NULL')
	);

	protected $validate = array(
		'rules' => array(
			'listing_id' => array(
				'pjActionNumeric' => true,
				'pjActionRequired' => true
			),
			'date_from' => array(
				'rule' => array('pjActionDate', 'ymd', '/\d{4}-\d{2}-\d{2}/'),
				'pjActionRequired' => true,
				'pjActionNotEmpty' => true
			),
			'date_to' => array(
				'rule' => array('pjActionDate', 'ymd', '/\d{4}-\d{2}-\d{2}/'),
				'pjActionRequired' => true,
				'pjActionNotEmpty' => true
			),
			'name' => array(
				'pjActionRequired' => true,
				'pjActionNotEmpty' => true
			),
			'status' => 'pjActionRequired'
		)
	);

	public static function factory($attr=array())
	{
		return new pjReservationModel($attr);
	}
}
?>