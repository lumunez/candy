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
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionIndex"><?php __('menuUsers'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionCreate"><?php __('lblAddUser'); ?></a></li>
		</ul>
	</div>
	
	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		<?php
		$filter = __('filter', true);
		?>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all">All</a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="T"><?php echo $filter['active']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="F"><?php echo $filter['inactive']; ?></a>
		</div>
		<br class="clear_both" />
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.currentUserId = <?php echo (int) $_SESSION[$controller->defaultUser]['id']; ?>;
	var myLabel = myLabel || {};
	myLabel.name = "<?php __('lblName'); ?>";
	myLabel.email = "<?php __('email'); ?>";
	myLabel.created = "<?php __('lblUserCreated'); ?>";
	myLabel.role = "<?php __('lblRole'); ?>";
	myLabel.confirmed = "<?php __('lblIsActive'); ?>";
	myLabel.revert_status = "<?php __('revert_status'); ?>";
	myLabel.exported = "<?php __('lblExport'); ?>";
	myLabel.active = "<?php __('vr_active'); ?>";
	myLabel.inactive = "<?php __('vr_inactive'); ?>";
	myLabel.delete_selected = "<?php __('vr_delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('vr_delete_confirmation'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	</script>
	<?php
}
?>