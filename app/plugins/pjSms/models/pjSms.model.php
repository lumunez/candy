<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once dirname(__FILE__) . '/pjSmsApp.model.php';
class pjSmsModel extends pjSmsAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'plugin_sms';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'number', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'text', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public static function factory($attr=array())
	{
		return new pjSmsModel($attr);
	}
	
	public function pjActionSetup()
	{
		$field_arr = array(
			0 => array('plugin_sms_menu_sms', 'Sms plugin / Menu Sms'),
			1 => array('plugin_sms_config', 'Sms plugin / Sms config'),
			2 => array('plugin_sms_number', 'Sms plugin / Number'),
			3 => array('plugin_sms_text', 'Sms plugin / Text'),
			4 => array('plugin_sms_status', 'Sms plugin / Status'),
			5 => array('plugin_sms_created', 'Sms plugin / Date & Time'),
			6 => array('plugin_sms_api', 'Sms plugin / API Key'),
			7 => array('error_titles_ARRAY_PSS01', 'Sms plugin / Info title', 'arrays'),
			8 => array('error_bodies_ARRAY_PSS01', 'Sms plugin / Info body', 'arrays'),
			9 => array('error_titles_ARRAY_PSS02', 'Sms plugin / Api key updates info title', 'arrays'),
			10 => array('error_bodies_ARRAY_PSS02', 'Sms plugin / Api key updates info body', 'arrays')
		);
		
		$multi_arr = array(
			0 => array('SMS'),
			1 => array('SMS Config'),
			2 => array('Phone number'),
			3 => array('Message'),
			4 => array('Status'),
			5 => array('Date/Time'),
			6 => array('API Key'),
			7 => array('SMS'),
			8 => array('In order to configure the script to send text messages you need an SMS Api Key. Contact Stivasoft to get your own Api Key.'),
			9 => array('SMS Api key updated!'),
			10 => array('All changes has been saved.')
		);
		
		$pjFieldModel = pjFieldModel::factory();
		$pjMultiLangModel = pjMultiLangModel::factory();
		pjObject::import('Model', 'pjLocale:pjLocale');
		$locale_arr = pjLocaleModel::factory()->findAll()->getDataPair('id', 'id');
		
		foreach ($field_arr as $key => $field)
		{
			$insert_id = $pjFieldModel->reset()->setAttributes(array(
				'key' => $field[0],
				'type' => !isset($field[2]) ? 'backend' : $field[2],
				'label' => $field[1]
			))->insert()->getInsertId();
			if ($insert_id !== false && (int) $insert_id > 0)
			{
				foreach ($locale_arr as $locale)
				{
					$pjMultiLangModel->reset()->setAttributes(array(
						'foreign_id' => $insert_id,
						'model' => 'pjField',
						'locale' => $locale,
						'field' => 'title',
						'content' => $multi_arr[$key][0]
					))->insert();
				}
			}
		}
	}
}
?>