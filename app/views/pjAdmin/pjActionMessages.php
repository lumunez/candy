<?php
$months = __('months', true);
$days = __('days', true);
?>
if (jQuery_1_8_2.datagrid !== undefined) {
	jQuery_1_8_2.extend(jQuery_1_8_2.datagrid.messages, {
		empty_result: "<?php __('gridEmptyResult'); ?>",
		choose_action: "<?php __('gridChooseAction'); ?>",
		goto_page: "<?php __('gridGotoPage'); ?>",
		total_items: "<?php __('gridTotalItems'); ?>",
		items_per_page: "<?php __('gridItemsPerPage'); ?>",
		prev_page: "<?php __('gridPrevPage'); ?>",
		prev: "<?php __('gridPrev'); ?>",
		next_page: "<?php __('gridNextPage'); ?>",
		next: "<?php __('gridNext'); ?>",
		month_names: ['<?php echo $months[1]; ?>', '<?php echo $months[2]; ?>', '<?php echo $months[3]; ?>', '<?php echo $months[4]; ?>', '<?php echo $months[5]; ?>', '<?php echo $months[6]; ?>', '<?php echo $months[7]; ?>', '<?php echo $months[8]; ?>', '<?php echo $months[9]; ?>', '<?php echo $months[10]; ?>', '<?php echo $months[11]; ?>', '<?php echo $months[12]; ?>'],
		day_names: ['<?php echo $days[1]; ?>', '<?php echo $days[2]; ?>', '<?php echo $days[3]; ?>', '<?php echo $days[4]; ?>', '<?php echo $days[5]; ?>', '<?php echo $days[6]; ?>', '<?php echo $days[0]; ?>'],
		delete_title: "<?php __('gridDeleteConfirmation'); ?>",
		delete_text: "<?php __('gridConfirmationTitle'); ?>",
		action_title: "<?php __('gridActionTitle'); ?>",
		action_empty_title: "<?php __('gridActionEmptyTitle'); ?>",
		action_empty_body: "<?php __('gridActionEmptyBody'); ?>",
		btn_ok: "<?php __('gridBtnOk'); ?>",
		btn_cancel: "<?php __('gridBtnCancel'); ?>",
		btn_delete: "<?php __('gridBtnDelete'); ?>"
	});
}

if (jQuery_1_8_2.multilang !== undefined) {
	jQuery_1_8_2.extend(jQuery_1_8_2.multilang.messages, {
		tooltip: "<?php __('multilangTooltip'); ?>"
	});
}

if (jQuery_1_8_2.gallery !== undefined) {
	jQuery_1_8_2.extend(jQuery_1_8_2.gallery.messages, {
		alt: "<?php __('galleryAlt'); ?>",
		btn_delete: "<?php __('galleryBtnDelete'); ?>",
		btn_cancel: "<?php __('galleryBtnCancel'); ?>",
		btn_save: "<?php __('galleryBtnSave'); ?>",
		btn_set_watermark: "<?php __('galleryBtnSetWatermark'); ?>",
		btn_clear_current: "<?php __('galleryBtnClearCurrent'); ?>",
		btn_compress: "<?php __('galleryBtnCompress'); ?>",
		btn_recreate: "<?php __('galleryBtnRecreate'); ?>",
		compress_note: "<?php __('galleryCompressionNote'); ?>",
		compression: "<?php __('galleryCompression'); ?>",
		delete_all: "<?php __('galleryDeleteAll'); ?>",
		delete_confirmation: "<?php __('galleryDeleteConfirmation'); ?>",
		delete_confirmation_single: "<?php __('galleryConfirmationSingle'); ?>",
		delete_confirmation_multi: "<?php __('galleryConfirmationMulti'); ?>",
		edit: "<?php __('galleryEdit'); ?>",
		empty_result: "<?php __('galleryEmptyResult'); ?>",
		erase: "<?php __('galleryDelete'); ?>",
		image_settings: "<?php __('galleryImageSettings'); ?>",
		move: "<?php __('galleryMove'); ?>",
		originals: "<?php __('galleryOriginals'); ?>",
		photos: "<?php __('galleryPhotos'); ?>",
		position: "<?php __('galleryPosition'); ?>",
		resize: "<?php __('galleryResize'); ?>",
		rotate: "<?php __('galleryRotate'); ?>",
		thumbs: "<?php __('galleryThumbs'); ?>",
		upload: "<?php __('galleryUpload'); ?>",
		watermark: "<?php __('galleryWatermark'); ?>",
		watermark_position: "<?php __('galleryWatermarkPosition'); ?>",
		watermark_positions: {
			tl: "<?php __('galleryTopLeft'); ?>",
			tr: "<?php __('galleryTopRight'); ?>",
			tc: "<?php __('galleryTopCenter'); ?>",
			bl: "<?php __('galleryBottomLeft'); ?>",
			br: "<?php __('galleryBottomRight'); ?>",
			bc: "<?php __('galleryBottomCenter'); ?>",
			cl: "<?php __('galleryCenterLeft'); ?>",
			cr: "<?php __('galleryCenterRight'); ?>",
			cc: "<?php __('galleryCenterCenter'); ?>"
		}
	});
}