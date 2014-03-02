<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjListingExtraModel extends pjAppModel
{
	protected $primaryKey = NULL;
	
	protected $table = 'listings_extras';
	
	protected $schema = array(
		array('name' => 'listing_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'extra_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjListingExtraModel($attr);
	}
}
?>