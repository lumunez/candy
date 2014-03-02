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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	pjUtil::printNotice(__('infoExtrasTitle', true), __('infoExtrasBody', true));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="hidden" name="controller" value="pjAdminExtras" />
		<input type="hidden" name="action" value="pjActionCreate" />
		<input type="submit" class="pj-button" value="<?php __('btnAdd'); ?>" />
		<p>&nbsp;</p>
	</form>

	<div id="grid"></div>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.extra = "<?php __('lblExtra'); ?>";
	myLabel.type = "<?php __('lblExtraType'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.extra_community = "<?php __('vr_extra_community'); ?>";
	myLabel.extra_property = "<?php __('vr_extra_property'); ?>";
	myLabel.active = "<?php __('vr_active'); ?>";
	myLabel.inactive = "<?php __('vr_inactive'); ?>";
	myLabel.delete_selected = "<?php __('vr_delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('vr_delete_confirmation'); ?>";
	</script>
	<?php
}
?>