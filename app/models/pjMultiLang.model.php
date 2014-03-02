<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjMultiLangModel extends pjAppModel
{
	protected $primaryKey = 'id';

	protected $table = 'multi_lang';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'model', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'locale', 'type' => 'tinyint', 'default' => ':NULL'),
		array('name' => 'field', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'content', 'type' => 'text', 'default' => ':NULL')
	);
	
	public function saveMultiLang($data, $foreign_id, $model)
	{
		foreach ($data as $locale => $locale_arr)
		{
			foreach ($locale_arr as $field => $content)
			{
				$this->reset()->setAttributes(array(
					'foreign_id' => $foreign_id,
					'model' => $model,
					'locale' => $locale,
					'field' => $field,
					'content' => $content
				))->insert();
			}
		}
	}
	
	public function updateMultiLang($data, $foreign_id, $model)
	{
		foreach ($data as $locale => $locale_arr)
		{
			foreach ($locale_arr as $field => $content)
			{
				$sql = sprintf("INSERT INTO `%1\$s` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`)
					VALUES (NULL, :foreign_id, :model, :locale, :field, :content)
					ON DUPLICATE KEY UPDATE `content` = :content;",
					$this->getTable()
				);
				$this->prepare($sql)->exec(compact('foreign_id', 'model', 'locale', 'field', 'content'));
			}
		}
	}
	
	public function getMultiLang($foreign_id, $model)
	{
		$arr = array();
		$_arr = $this->where('foreign_id', $foreign_id)->where('model', $model)->findAll()->getData();
		foreach ($_arr as $_k => $_v)
		{
			$arr[$_v['locale']][$_v['field']] = $_v['content'];
		}
		return $arr;
	}
	
	public static function factory($attr=array())
	{
		return new pjMultiLangModel($attr);
	}
}
?>