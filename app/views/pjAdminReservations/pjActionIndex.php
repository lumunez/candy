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
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionIndex"><?php __('menuReservations'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate"><?php __('lblAddReservation'); ?></a></li>
		</ul>
	</div>
	
	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<select name="listing_id" id="listing_id" class="pj-form-field w200">
				<option value="">-- <?php __('lblChoose'); ?> --</option>
				<?php
				foreach ($tpl['listing_arr'] as $item)
				{
					?><option value="<?php echo $item['id']; ?>"><?php echo stripslashes($item['listing_refid']); ?></option><?php
				}
				?>
			</select>
		</form>
		
		<?php
		$rs = __('reservation_statuses', true);
		?>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all">All</a>
			<a href="#" class="pj-button btn-confirmed"><?php echo $rs['Confirmed']; ?></a>
			<a href="#" class="pj-button btn-pending"><?php echo $rs['Pending']; ?></a>
			<a href="#" class="pj-button btn-cancelled"><?php echo $rs['Cancelled']; ?></a>
		</div>
		<br class="clear_both" />
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jqDateFormat = "<?php echo pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['listing_id']) && (int) $_GET['listing_id'] > 0)
	{
		?>pjGrid.queryString += "&listing_id=<?php echo (int) $_GET['listing_id']; ?>";<?php
	}
	$statuses = __('reservation_statuses', true);
	?>
	var myLabel = myLabel || {};
	myLabel.date_from = "<?php __('res_date_from'); ?>";
	myLabel.date_to = "<?php __('res_date_to'); ?>";
	myLabel.listing = "<?php __('res_listing'); ?>";
	myLabel.btn_continue = "<?php __('btnContinue'); ?>";
	myLabel.btn_cancel = "<?php __('btnCancel'); ?>";
	myLabel.export_selected = "<?php __('vr_export_selected'); ?>";
	myLabel.delete_selected = "<?php __('vr_delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('vr_delete_confirmation'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.pending = "<?php echo $statuses['Pending']; ?>";
	myLabel.confirmed = "<?php echo $statuses['Confirmed']; ?>";
	myLabel.cancelled = "<?php echo $statuses['Cancelled']; ?>";
	</script>
	<?php
}
?>