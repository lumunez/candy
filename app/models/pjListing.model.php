<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjListingModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'listings';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'owner_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'type_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'country_id', 'type' => 'int', 'default' => ':NULL'),

		array('name' => 'address_map', 'type' => 'tinyint', 'default' => '0'),
		array('name' => 'address_postcode', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'address_content', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'address_city', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'address_state', 'type' => 'varchar', 'default' => ':NULL'),
				
		array('name' => 'listing_refid', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'listing_bedrooms', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'listing_bathrooms', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'listing_adults', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'listing_children', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'listing_floor_area', 'type' => 'decimal', 'default' => ':NULL'),
		
		array('name' => 'contact_show', 'type' => 'tinyint', 'default' => '0'),
		array('name' => 'contact_phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'contact_mobile', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'contact_email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'contact_fax', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'contact_url', 'type' => 'varchar', 'default' => ':NULL'),
		
		array('name' => 'lat', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'lng', 'type' => 'varchar', 'default' => ':NULL'),
		
		array('name' => 'personal_title', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'personal_fname', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'personal_lname', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'personal_age', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'personal_gender', 'type' => 'varchar', 'default' => ':NULL'),
		
		array('name' => 'views', 'type' => 'int', 'default' => '0'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'modified', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'expire', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'last_extend', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'is_featured', 'type' => 'enum', 'default' => 'F'),
		
		array('name' => 'o_accept_bookings', 'type' => 'tinyint', 'default' => 1),
		array('name' => 'o_disable_payments', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'o_min_booking_lenght', 'type' => 'smallint', 'default' => 1),
		array('name' => 'o_max_booking_lenght', 'type' => 'smallint', 'default' => 10),
		array('name' => 'o_default_status_if_paid', 'type' => 'tinyint', 'default' => 1),
		array('name' => 'o_default_status_if_not_paid', 'type' => 'tinyint', 'default' => 2),
		array('name' => 'o_price_based_on', 'type' => 'tinyint', 'default' => 1),
		array('name' => 'o_deposit_payment', 'type' => 'decimal', 'default' => 10),
		array('name' => 'o_security_payment', 'type' => 'smallint', 'default' => 0),
		array('name' => 'o_tax_payment', 'type' => 'decimal', 'default' => 0),
		array('name' => 'o_tax_type', 'type' => 'tinyint', 'default' => 1),
		array('name' => 'o_require_all_within_days', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'o_allow_paypal', 'type' => 'tinyint', 'default' => 1),
		array('name' => 'o_allow_authorize', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'o_allow_creditcard', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'o_allow_bank', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'o_thankyou_page', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'o_authorize_merchant_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'o_authorize_transkey', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'o_authorize_tz', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'o_paypal_address', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'o_bank_account', 'type' => 'tinytext', 'default' => ':NULL'),
	);
	
	public $i18n = array('title', 'description', 'terms', 'confirm_tokens', 'confirm_subject',
		'payment_tokens', 'payment_subject', 'meta_title', 'meta_keywords', 'meta_description');
	
	protected $validate = array(
		'rules' => array(
			'owner_id' => array(
				'pjActionNumeric' => true,
				'pjActionRequired' => true
			),
			'type_id' => array(
				'pjActionNumeric' => true,
				'pjActionRequired' => true
			),
			'listing_refid' => array(
				'pjActionRequired' => true,
				'pjActionNotEmpty' => true
			),
			'expire' => array(
				'rule' => array('pjActionDate', 'ymd', '/\d{4}-\d{2}-\d{2}/'),
				'pjActionRequired' => true,
				'pjActionNotEmpty' => true
			),
			'status' => 'pjActionRequired'
		)
	);
	
	public static function factory($attr=array())
	{
		return new pjListingModel($attr);
	}
}
?>