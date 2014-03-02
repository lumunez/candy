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
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminListings&amp;action=pjActionIndex"><?php __('menuBrowseProperty'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminListings&amp;action=pjActionCreate"><?php __('menuAddProperty'); ?></a></li>
		</ul>
	</div>
			
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminListings&amp;action=pjActionCreate" method="post" id="frmCreateListing" class="form pj-form">
		<input type="hidden" name="listing_create" value="1" />
		<?php pjUtil::printNotice(__('lblListingAddTitle', true), __('lblListingAddDesc', true)); ?>
		<p>
			<label class="title"><?php __('lblListingRefid'); ?></label>
			<span class="inline_block">
				<input type="text" name="listing_refid" id="listing_refid" class="pj-form-field required" value="<?php echo pjUtil::uuid(); ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblListingType'); ?></label>
			<span class="inline_block">
				<select name="type_id" id="type_id" class="pj-form-field w200 required">
					<option value="">-- <?php __('lblChoose'); ?> --</option>
					<?php
					foreach ($tpl['type_arr'] as $v)
					{
						?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<?php
		if (!$controller->isOwner())
		{
			?>
			<p>
				<label class="title"><?php __('lblListingExpire'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-after">
					<input type="text" name="expire" id="expire" class="pj-form-field pointer w80 required datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo date($tpl['option_arr']['o_date_format']); ?>" />
					<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					<a href="#" class="pj-form-langbar-tip listing-tip" title="<?php __('lblListingExpireTip'); ?>"></a>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblListingStatus'); ?></label>
				<span class="inline_block">
					<select name="status" id="status" class="pj-form-field w200 required">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach (__('publish_status', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
						}
						?>
					</select>
					<a href="#" class="pj-form-langbar-tip listing-tip" title="<?php __('lblListingStatusTip'); ?>"></a>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblListingFeatured'); ?></label>
				<span class="left">
				<?php
				foreach (__('_yesno', true) as $k => $v)
				{
					?>
					<label class="r5"><input type="radio" name="is_featured" value="<?php echo $k; ?>"<?php echo 'F' == $k ? ' checked="checked"' : NULL; ?> /> <?php echo $v; ?></label>
					<?php
				}
				?>
				</span>
			</p>
			<p style="overflow: visible">
				<label class="title"><?php __('lblListingOwner'); ?></label>
				<span class="inline_block">
					<select name="owner_id" id="owner_id" class="pj-form-field w200 required">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach ($tpl['user_arr'] as $v)
						{
							?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<?php
		} else {
			?>
			<p>
				<label class="title"><?php __('lblListingPublishPeriod'); ?></label>
				<span class="inline_block">
					<select name="period_id" id="period_id" class="pj-form-field w150">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach ($tpl['period_arr'] as $period)
						{
							?><option value="<?php echo $period['id']; ?>"><?php printf("%u %s / %s", $period['days'], __('lblDays', true), pjUtil::formatCurrencySign(number_format($period['price'], 0), $tpl['option_arr']['o_currency'])); ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<?php
		}
		?>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		</p>
	</form>
	<?php
}
?>