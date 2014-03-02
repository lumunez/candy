<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once dirname(__FILE__) . '/pjGalleryApp.model.php';
class pjGalleryModel extends pjGalleryAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'plugin_gallery';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'mime_type', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'small_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'small_size', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'small_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'small_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'medium_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'medium_size', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'medium_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'medium_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'large_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'large_size', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'large_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'large_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'source_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'source_size', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'source_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'source_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'alt', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'watermark', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'sort', 'type' => 'int', 'default' => ':NULL')
	);
	
	public $i18n = array('title');
	
	public static function factory($attr=array())
	{
		return new pjGalleryModel($attr);
	}
	
	public function pjActionSetup()
	{
		$field_arr = array(
			0 => array('galleryAlt', 'Gallery / ALT'),
			1 => array('galleryWatermarkPosition', 'Gallery / Watermark position'),
			2 => array('galleryPosition', 'Gallery / Position'),
			3 => array('galleryImageSettings', 'Gallery / Image settings'),
			4 => array('galleryConfirmationMulti', 'Gallery / Delete all confirmation'),
			5 => array('galleryConfirmationSingle', 'Gallery / Delete image confirmation'),
			6 => array('galleryDeleteConfirmation', 'Gallery / Delete confirmation'),
			7 => array('galleryCompressionNote', 'Gallery / Compression note'),
			8 => array('galleryBtnDelete', 'Gallery / Button Delete'),
			9 => array('galleryBtnCancel', 'Gallery / Button Cancel'),
			10 => array('galleryBtnSave', 'Gallery / Button Save'),
			11 => array('galleryBtnSetWatermark', 'Gallery / Set watermark'),
			12 => array('galleryBtnClearCurrent', 'Gallery / Clear current one'),
			13 => array('galleryBtnCompress', 'Gallery / Button Compress'),
			14 => array('galleryBtnRecreate', 'Gallery / Button Recreate'),
			15 => array('galleryTopLeft', 'Gallery / Top Left'),
			16 => array('galleryTopCenter', 'Gallery / Top Center'),
			17 => array('galleryBottomLeft', 'Gallery / Bottom Left'),
			18 => array('galleryBottomRight', 'Gallery / Bottom Right'),
			19 => array('galleryBottomCenter', 'Gallery / Bottom Center'),
			20 => array('galleryCenterLeft', 'Gallery / Center Left'),
			21 => array('galleryCenterRight', 'Gallery / Center Right'),
			22 => array('galleryCenterCenter', 'Gallery / Center Center'),
			23 => array('galleryTopRight', 'Gallery / Top Right'),
			24 => array('galleryEmptyResult', 'Gallery / Empty result set'),
			25 => array('galleryMove', 'Gallery / Move'),
			26 => array('galleryEdit', 'Gallery / Edit'),
			27 => array('galleryDelete', 'Gallery / Delete'),
			28 => array('galleryResize', 'Gallery / Resize'),
			29 => array('galleryRotate', 'Listing / '),
			30 => array('galleryWatermark', 'Gallery / Watermark'),
			31 => array('galleryCompression', 'Gallery / Compression'),
			32 => array('galleryDeleteAll', 'Gallery / Delete All'),
			33 => array('galleryOriginals', 'Gallery / Originals'),
			34 => array('galleryThumbs', 'Gallery / Thumbs'),
			35 => array('galleryPhotos', 'Gallery / photos'),
			36 => array('galleryUpload', 'Gallery / Upload'),
			37 => array('galleryRecreate', 'Gallery / Recreate from original')
		);
		
		$multi_arr = array(
			0 => array('ALT'),
			1 => array('Watermark position'),
			2 => array('Position'),
			3 => array('Image settings'),
			4 => array('Are you sure you want to delete all images?'),
			5 => array('Are you sure you want to delete selected image?'),
			6 => array('Delete confirmation'),
			7 => array('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur id consectetur magna. Nulla facilisi. Sed id dolor ante.'),
			8 => array('Delete'),
			9 => array('Cancel'),
			10 => array('Save'),
			11 => array('Set watermark'),
			12 => array('Clear current one'),
			13 => array('Compress'),
			14 => array('Re-create thumbs'),
			15 => array('Top Left'),
			16 => array('Top Center'),
			17 => array('Bottom Left'),
			18 => array('Bottom Right'),
			19 => array('Bottom Center'),
			20 => array('Center Left'),
			21 => array('Center Right'),
			22 => array('Center Center'),
			23 => array('Top Right'),
			24 => array('No images uploaded yet.'),
			25 => array('Move'),
			26 => array('Edit'),
			27 => array('Delete'),
			28 => array('Resize/Crop'),
			29 => array('Rotate'),
			30 => array('Watermark'),
			31 => array('Compression'),
			32 => array('Delete All'),
			33 => array('Originals'),
			34 => array('Thumbs'),
			35 => array('photos'),
			36 => array('Upload'),
			37 => array('re-create from original')
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