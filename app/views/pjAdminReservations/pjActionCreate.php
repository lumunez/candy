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
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionIndex"><?php __('menuReservations'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate"><?php __('lblAddReservation'); ?></a></li>
		</ul>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate" method="post" id="frmCreateReservation" class="form pj-form">
		<input type="hidden" name="reservation_create" value="1" />
		<p style="overflow: visible">
			<label class="title"><?php __('lblReservationListing'); ?></label>
			<span class="inline_block">
				<select name="listing_id" id="listing_id" class="pj-form-field w300 required">
					<option value="">-- <?php __('lblChoose'); ?> --</option>
					<?php
					if (isset($tpl['listing_arr']) && count($tpl['listing_arr']) > 0)
					{
						foreach ($tpl['listing_arr'] as $v)
						{
							?><option value="<?php echo $v['id']; ?>"><?php echo pjMultibyte::substr(stripslashes($v['title'] . sprintf(" (%s)", $v['listing_refid'])), 0, 50); ?></option><?php
						}
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationStatus'); ?></label>
			<span class="inline_block">
				<select name="status" id="status" class="pj-form-field w220 required">
					<option value="">-- <?php __('lblChoose'); ?> --</option>
					<?php
					foreach (__('reservation_statuses', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationFrom'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-after">
				<input type="text" name="date_from" id="date_from" class="pj-form-field pointer w80 required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
				<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationTo'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-after">
				<input type="text" name="date_to" id="date_to" class="pj-form-field pointer w80 required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
				<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				<input type="hidden" name="dates" id="dates" value="0" />
				<input type="hidden" name="days" id="days" value="0" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationPayment'); ?></label>
			<span class="inline_block">
				<select name="payment_method" id="payment_method" class="pj-form-field w200">
					<option value="">-- <?php __('lblChoose'); ?> --</option>
					<?php
					foreach (__('payment_methods', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p class="vrCC" style="display: none">
			<label class="title"><?php __('lblReservationCCType'); ?></label>
			<span class="inline_block">
				<select name="cc_type" class="pj-form-field w200">
					<option value="">---</option>
					<?php
					foreach (__('cc_types', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p class="vrCC" style="display: none">
			<label class="title"><?php __('lblReservationCCNum'); ?></label>
			<span class="inline_block">
				<input type="text" name="cc_num" id="cc_num" class="pj-form-field w180 digits" />
			</span>
		</p>
		<p class="vrCC" style="display: none">
			<label class="title"><?php __('lblReservationCCCode'); ?></label>
			<span class="inline_block">
				<input type="text" name="cc_code" id="cc_code" class="pj-form-field w180 digits" />
			</span>
		</p>
		<p class="vrCC" style="display: none">
			<label class="title"><?php __('lblReservationCCExp'); ?></label>
			<span class="inline_block">
				<input type="text" name="cc_exp" id="cc_exp" class="pj-form-field w180" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationAmount'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" name="amount" id="amount" class="pj-form-field number w80" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationDeposit'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" name="deposit" id="deposit" class="pj-form-field number w80" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationSecurity'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" name="security" id="security" class="pj-form-field number w80" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationTax'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" name="tax" id="tax" class="pj-form-field number w80" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationName'); ?></label>
			<span class="inline_block">
				<input type="text" name="name" id="name" class="pj-form-field w200 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationEmail'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
				<input type="text" name="email" id="email" class="pj-form-field email w200" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationPhone'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
				<input type="text" name="phone" id="phone" class="pj-form-field w200" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationNotes'); ?></label>
			<span class="inline_block">
				<textarea name="notes" id="notes" class="pj-form-field w500 h80"></textarea>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		</p>
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.dateRangeValidation = "<?php __('lblReservationDateRangeValidation'); ?>";
	myLabel.numDaysValidation = "<?php __('lblReservationNumDaysValidation'); ?>";
	</script>
	<?php
}
?>