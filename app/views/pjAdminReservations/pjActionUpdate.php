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
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate"><?php __('lblAddReservation'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('lblUpdateReservation'); ?></a></li>
		</ul>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionUpdate" method="post" id="frmUpdateReservation" class="form pj-form">
		<input type="hidden" name="reservation_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
	
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
							if (isset($tpl['arr']['listing_id']) && $tpl['arr']['listing_id'] == $v['id'])
							{
								?><option value="<?php echo $v['id']; ?>" selected="selected"><?php echo pjMultibyte::substr(stripslashes($v['title'] . sprintf(" (%s)", $v['listing_refid'])),0,50); ?></option><?php
							} else {
								?><option value="<?php echo $v['id']; ?>"><?php echo pjMultibyte::substr(stripslashes($v['title'] . sprintf(" (%s)", $v['listing_refid'])),0,50); ?></option><?php
							}
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
						if (isset($tpl['arr']['status']) && $tpl['arr']['status'] == $k)
						{
							?><option value="<?php echo $k; ?>" selected="selected"><?php echo stripslashes($v); ?></option><?php
						} else {
							?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
						}
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationFrom'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-after">
				<input type="text" name="date_from" id="date_from" class="pj-form-field pointer w80 required" value="<?php echo pjUtil::formatDate($tpl['arr']['date_from'], "Y-m-d", $tpl['option_arr']['o_date_format']); ?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
				<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationTo'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-after">
				<input type="text" name="date_to" id="date_to" class="pj-form-field pointer w80 required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo pjUtil::formatDate($tpl['arr']['date_to'], "Y-m-d", $tpl['option_arr']['o_date_format']); ?>" />
				<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				<input type="hidden" name="dates" id="dates" value="1" />
				<input type="hidden" name="days" id="days" value="1" />
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
					?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
				}
				?>
				</select>
			</span>
		</p>
		<p class="vrCC" style="display: <?php echo $tpl['arr']['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>"><label class="title"><?php echo __('lblReservationCCType'); ?></label>
			<select name="cc_type" class="pj-form-field w200">
			<option value="">---</option>
			<?php
			foreach (__('cc_types', true) as $k => $v)
			{
				?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['cc_type'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
			}
			?>
			</select>
		</p>
		<p class="vrCC" style="display: <?php echo $tpl['arr']['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
			<label class="title"><?php __('lblReservationCCNum'); ?></label>
			<input type="text" name="cc_num" id="cc_num" class="pj-form-field w180 digits" value="<?php echo htmlspecialchars($tpl['arr']['cc_num']); ?>" />
		</p>
		<p class="vrCC" style="display: <?php echo $tpl['arr']['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
			<label class="title"><?php __('lblReservationCCCode'); ?></label>
			<input type="text" name="cc_code" id="cc_code" class="pj-form-field w180 digits" value="<?php echo htmlspecialchars($tpl['arr']['cc_code']); ?>" />
		</p>
		<p class="vrCC" style="display: <?php echo $tpl['arr']['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
			<label class="title"><?php __('lblReservationCCExp'); ?></label>
			<input type="text" name="cc_exp" id="cc_exp" class="pj-form-field w180" value="<?php echo htmlspecialchars($tpl['arr']['cc_exp']); ?>" />
		</p>
		<p>
			<label class="title"><?php __('lblReservationAmount'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" name="amount" id="amount" class="pj-form-field number w80" value="<?php echo $tpl['arr']['amount']; ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationDeposit'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" name="deposit" id="deposit" class="pj-form-field number w80" value="<?php echo $tpl['arr']['deposit']; ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationSecurity'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" name="security" id="security" class="pj-form-field number w80" value="<?php echo $tpl['arr']['security']; ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationTax'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" name="tax" id="tax" class="pj-form-field number w80" value="<?php echo $tpl['arr']['tax']; ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationName'); ?></label>
			<span class="inline_block">
				<input type="text" name="name" id="name" class="pj-form-field w200" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['name'])); ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationEmail'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
				<input type="text" name="email" id="email" class="pj-form-field email w200" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['email'])); ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationPhone'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
				<input type="text" name="phone" id="phone" class="pj-form-field w200" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['phone'])); ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservationNotes'); ?></label>
			<textarea name="notes" id="notes" class="pj-form-field w500 h80"><?php echo stripslashes($tpl['arr']['notes']); ?></textarea>
		</p>
		<p>
			<label class="title"><?php __('lblReservationCreated'); ?></label>
			<span class="left"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['created'])); ?>, <?php echo date("H:i", strtotime($tpl['arr']['created'])); ?></span>
		</p>
		<p>
			<label class="title"><?php __('lblIp'); ?></label>
			<span class="left"><?php echo $tpl['arr']['ip']; ?></span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		</p>
	
	</form>
	
	<div id="dialogMessage" title="<?php __('ResConfirmationTitle'); ?>" style="display: none">
		<p><label><input type="checkbox" value="1" name="dialog_confirm" id="dialog_confirm" /> <?php __('ResConfirmationText'); ?></label></p><br />
		<p class="b10"><input type="text" class="pj-form-field pj-form-field-readonly" style="width: 470px" readonly="readonly" /></p>
		<p><textarea class="pj-form-field pj-form-field-readonly" style="width: 470px; height: 310px; resize: none" readonly="readonly"></textarea></p>
	</div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.btn_continue = "<?php __('btnContinue'); ?>";
	myLabel.btn_cancel = "<?php __('btnCancel'); ?>";
	myLabel.dateRangeValidation = "<?php __('lblReservationDateRangeValidation'); ?>";
	myLabel.numDaysValidation = "<?php __('lblReservationNumDaysValidation'); ?>";
	</script>
	<?php
}
?>