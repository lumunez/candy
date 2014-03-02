<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	pjUtil::printNotice(__('infoExtrasUpdateTitle', true), __('infoExtrasUpdateBody', true));
	?>
	<div class="multilang"></div>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionUpdate" method="post" id="frmUpdateExtra" class="form pj-form">
		<input type="hidden" name="extra_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
	
		<?php
		foreach ($tpl['lp_arr'] as $v)
		{
			?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('lblExtra'); ?>:</label>
				<span class="inline_block">
					<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['name'])); ?>" />
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
				</span>
			</p>
			<?php
		}
		$eta = __('extra_type_arr', true);
		?>
		<p><label class="title"><?php __('lblExtraType'); ?></label>
			<select name="type" class="pj-form-field required">
				<option value="">-- <?php __('lblChoose'); ?> --</option>
				<?php
				foreach ($eta as $k => $v)
				{
					if (isset($tpl['arr']['type']) && $tpl['arr']['type'] == $k)
					{
						?><option value="<?php echo $k; ?>" selected="selected"><?php echo $v; ?></option><?php
					} else {
						?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
					}
				}
				?>
			</select>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		</p>
	
	</form>
	
	<script type="text/javascript">
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				select: function (event, ui) {
					// Callback, e.g. ajax requests or whatever
				}
			});
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>