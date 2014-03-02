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
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminListings&amp;action=pjActionIndex"><?php __('menuBrowseProperty'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminListings&amp;action=pjActionCreate"><?php __('menuAddProperty'); ?></a></li>
		</ul>
	</div>
	
	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
			<button type="button" class="pj-button pj-button-detailed"><span class="pj-button-detailed-arrow"></span></button>
		</form>
		<?php
		$filter = __('filter', true);
		?>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all">All</a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="T"><?php echo $filter['active']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="F"><?php echo $filter['inactive']; ?></a>
			<a href="#" class="pj-button btn-filter btn-featured" data-column="is_featured" data-value="T"><?php echo $filter['featured']; ?></a>
		</div>
		<br class="clear_both" />
	</div>
	
	<div class="pj-form-filter-advanced" style="display: none">
		<span class="pj-menu-list-arrow"></span>
		<form action="" method="get" class="form pj-form pj-form-search frm-filter-advanced">
			<div class="float_left w350">
				<p>
					<label class="title"><?php __('lblListingRefid'); ?></label>
					<input type="text" name="listing_refid" id="listing_refid" class="pj-form-field w150" />
				</p>
				<?php
				if ($controller->isAdmin() && $tpl['option_arr']['o_allow_add_property'] == 'Yes')
				{
					?>
					<p style="overflow: visible;">
						<label class="title"><?php __('lblListingOwner'); ?></label>
						<select name="user_id" id="user_id" class="pj-form-field w150">
							<option value="">-- <?php __('lblChoose'); ?> --</option>
							<?php
							foreach ($tpl['user_arr'] as $v)
							{
								?><option value="<?php echo $v['id']; ?>"<?php echo isset($_GET['user_id']) && (int) $_GET['user_id'] == $v['id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v['name']); ?></option><?php
							}
							?>
						</select>
					</p>
					<?php
				}
				?>
				<p>
					<label class="title"><?php __('lblListingAdults'); ?></label>
					<input type="text" name="adults_from" class="pj-form-field w50 digits spin" readonly="readonly" />
					<input type="text" name="adults_to" class="pj-form-field w50 digits spin" readonly="readonly" />
				</p>
				<p>
					<label class="title"><?php __('lblListingChildren'); ?></label>
					<input type="text" name="children_from" class="pj-form-field w50 digits spin" readonly="readonly" />
					<input type="text" name="children_to" class="pj-form-field w50 digits spin" readonly="readonly" />
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSearch'); ?>" class="pj-button" />
					<input type="reset" value="<?php __('btnCancel'); ?>" class="pj-button" />
				</p>
			</div>
			<div class="float_right w350">
				<p>
					<label class="title"><?php __('lblListingType'); ?></label>
					<select name="type_id" id="type_id" class="pj-form-field w150">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach ($tpl['type_arr'] as $v)
						{
							?><option value="<?php echo $v['id']; ?>"<?php echo isset($_GET['type_id']) && (int) $_GET['type_id'] == $v['id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v['name']); ?></option><?php
						}
						?>
					</select>
				</p>
				<p>
					<label class="title"><?php __('lblListingBedrooms'); ?></label>
					<input type="text" name="bedrooms_from" class="pj-form-field w50 digits spin" readonly="readonly" />
					<input type="text" name="bedrooms_to" class="pj-form-field w50 digits spin" readonly="readonly" />
				</p>
				<p>
					<label class="title"><?php __('lblListingBathrooms'); ?></label>
					<input type="text" name="bathrooms_from" class="pj-form-field w50 digits spin" readonly="readonly" />
					<input type="text" name="bathrooms_to" class="pj-form-field w50 digits spin" readonly="readonly" />
				</p>
				<p>
					<label class="title"><?php __('lblListingFloorArea'); ?></label>
					<input type="text" name="floor_area_from" class="pj-form-field w50 number spin" readonly="readonly" />
					<input type="text" name="floor_area_to" class="pj-form-field w50 number spin" readonly="readonly" />
				</p>
			</div>
			<br class="clear_both" />
		</form>
	</div>
	
	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jqDateFormat = "<?php echo pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.queryString = "";
	pjGrid.isOwner = <?php echo $controller->isOwner() ? 'true' : 'false'; ?>;
	<?php
	if (isset($_GET['user_id']) && (int) $_GET['user_id'] > 0)
	{
		?>pjGrid.queryString += "&user_id=<?php echo (int) $_GET['user_id']; ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.exp_date_plus_30 = "<?php __('vr_exp_date_plus_30'); ?>";
	myLabel.view_reservations = "<?php __('vr_view_reservations'); ?>";
	myLabel.image = "<?php __('vr_image'); ?>";
	myLabel.ref_id = "<?php __('vr_ref_id'); ?>";
	myLabel.owner = "<?php __('vr_owner'); ?>";
	myLabel.expire = "<?php __('vr_expire'); ?>";
	myLabel.publish = "<?php __('vr_publish'); ?>";
	myLabel.active = "<?php __('vr_active'); ?>";
	myLabel.inactive = "<?php __('vr_inactive'); ?>";
	myLabel.exp_date = "<?php __('vr_exp_date'); ?>";
	myLabel.delete_selected = "<?php __('vr_delete_selected'); ?>";
	myLabel.published = "<?php __('vr_published'); ?>";
	myLabel.not_published = "<?php __('vr_not_published'); ?>";
	myLabel.extend_exp_date = "<?php __('vr_extend_exp_date'); ?>";
	myLabel.delete_confirm = "<?php __('vr_delete_confirm'); ?>";
	</script>
	<?php
}
?>