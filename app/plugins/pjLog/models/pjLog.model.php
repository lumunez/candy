<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once dirname(__FILE__) . '/pjLogApp.model.php';
class pjLogModel extends pjLogAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'plugin_log';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'filename', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'function', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'value', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public static function factory($attr=array())
	{
		return new pjLogModel($attr);
	}
	
	public function pjActionSetup()
	{
		$field_arr = array(
			0 => array('plugin_log_menu_log', 'Log plugin / Menu Log'),
			1 => array('plugin_log_menu_config', 'Log plugin / Menu Config'),
			2 => array('plugin_log_btn_empty', 'Log plugin / Empty button'),
			3 => array('error_titles_ARRAY_PLG01', 'error_titles_ARRAY_PLG01', 'arrays'),
			4 => array('error_bodies_ARRAY_PLG01', 'error_bodies_ARRAY_PLG01', 'arrays')
		);
		
		$multi_arr = array(
			0 => array('Log'),
			1 => array('Config log'),
			2 => array('Empty log'),
			3 => array('Config log updated.'),
			4 => array('The config log have been updated.')
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