<?php
pjUtil::printNotice(__('lblGalleryNoticeTitle', true), __('lblGalleryNoticeBody', true));
?>
<div id="pj-crop-control" class="overflow">
	<div class="float_left">
	<input type="button" value="<?php __('lblGalleryOriginal'); ?>" class="pj-button btn-original" />
	<input type="button" value="<?php __('lblGalleryThumb'); ?>" class="pj-button btn-thumb" />
	<input type="button" value="<?php __('lblGalleryPreview'); ?>" class="pj-button btn-preview" />
	<p class="t5">
		<?php
		printf("%u x %u, %s", $tpl['arr']['source_width'], $tpl['arr']['source_height'], pjUtil::formatSize($tpl['arr']['source_size']));
		?> (<a href="#" class="btn-recreate no-decor"><?php __('galleryRecreate'); ?></a>)
	</p>
	</div>
	<form action="<?php echo PJ_INSTALL_URL; ?>index.php" method="get" class="float_right inline">
		<input type="hidden" name="controller" value="pjAdminListings" />
		<input type="hidden" name="action" value="pjActionUpdate" />
		<input type="hidden" name="id" value="<?php echo @$_GET['foreign_id']; ?>" />
		<input type="hidden" name="tab_id" value="tabs-4" />
		<input type="submit" value="<?php __('btnBack'); ?>" class="pj-button" />
	</form>
</div>

<div id="pj-crop-image" class="b10 t10" style="border: solid 1px #bbb; padding: 10px; height: 555px; width: 718px; overflow: auto">
	
</div>

<form action="<?php echo PJ_INSTALL_URL; ?>" method="post" class="form pj-form" id="frmMetaInfo">
	<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
	<input type="hidden" name="src" value="" />
	<input type="hidden" name="dst" value="" />
	<input type="hidden" name="x" value="" />
	<input type="hidden" name="x2" value="" />
	<input type="hidden" name="y" value="" />
	<input type="hidden" name="y2" value="" />
	<input type="hidden" name="w" value="" />
	<input type="hidden" name="h" value="" />
	<input type="button" value="<?php __('btnSave'); ?>" class="pj-button btn-save" />
</form>

<script type="text/javascript">
var pjGallery = {
	small_path: "<?php echo $tpl['arr']['small_path']; ?>",
	medium_path: "<?php echo $tpl['arr']['medium_path']; ?>",
	large_path: "<?php echo $tpl['arr']['large_path']; ?>",
	source_path: "<?php echo $tpl['arr']['source_path']; ?>",
	source_width: <?php echo (int) $tpl['arr']['source_width']; ?>,
	source_height: <?php echo (int) $tpl['arr']['source_height']; ?>,
	source_size: <?php echo (int) $tpl['arr']['source_size']; ?>,
	small_width: <?php echo (int) $tpl['imageSizes']['small'][0]; ?>,
	small_height: <?php echo (int) $tpl['imageSizes']['small'][1]; ?>,
	medium_width: <?php echo (int) $tpl['imageSizes']['medium'][0]; ?>,
	medium_height: <?php echo (int) $tpl['imageSizes']['medium'][1]; ?>
};
</script>