<div id="tabs-8">
	<?php
	pjUtil::printNotice(__('infoListingBookingsTitle', true), __('infoListingBookingsBody', true));
	?>
	<table cellpadding="0" cellspacing="0" class="pj-table" style="width: 100%">
		<tr>
			<td width="60%"><span class="block bold"><?php __('lblListingAcceptBookings'); ?></span><span class="fs10"><?php __('lblListingAcceptBookingsText'); ?></span></td>
			<td width="40%"><input type="checkbox" name="o_accept_bookings" value="1" <?php echo @$tpl['arr']["o_accept_bookings"] == 1 ? 'checked="checked"' : NULL; ?> />
		</tr>
		<tr>
			<td><span class="block bold"><?php __('lblListingBookItNow'); ?></span><span class="fs10"><?php __('lblListingBookItNowText'); ?></span></td>
			<td><input type="checkbox" name="o_disable_payments" value="1" <?php echo @$tpl['arr']["o_disable_payments"] == 1 ? 'checked="checked"' : NULL; ?> /></td>
		</tr>
		<tr>
			<td><span class="block bold"><?php __('lblListingMinimumBookingLength'); ?>:</span><span class="fs10"><?php __('lblListingMinimumBookingLengthText'); ?></span></td>
			<td class="tblError"><input type="text" name="o_min_booking_lenght" value="<?php echo @$tpl['arr']["o_min_booking_lenght"]; ?>" class="pj-form-field w60 field-int digits" readonly="readonly" /></td>
		</tr>
		<tr>
			<td><span class="block bold"><?php __('lblListingMaximumBookingLength'); ?></span><span class="fs10"><?php __('lblListingMaximumBookingLengthText'); ?> </span></td>
			<td class="tblError"><input type="text" name="o_max_booking_lenght" value="<?php echo @$tpl['arr']["o_max_booking_lenght"]; ?>" class="pj-form-field w60 field-int digits" readonly="readonly" /></td>
		</tr>
		<tr>
			<td><span class="block bold"><?php __('lblListingDefaultStatusIfPaid'); ?></span><span class="fs10"><?php __('lblListingDefaultStatusIfPaidText'); ?> </span></td>
			<td>
				<select name="o_default_status_if_paid" class="pj-form-field">
				<?php
				foreach (__('listing_b_statuses', true) as $k => $v)
				{
					if (isset($tpl['arr']['o_default_status_if_paid']) && $tpl['arr']['o_default_status_if_paid'] == $k)
					{
						?><option value="<?php echo $k; ?>" selected="selected"><?php echo $v; ?></option><?php
					} else {
						?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
					}
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><span class="block bold"><?php __('lblListingDefaultStatusIfNotPaid'); ?></span><span class="fs10"><?php __('lblListingDefaultStatusIfNotPaidText'); ?> </span></td>
			<td>
				<select name="o_default_status_if_not_paid" class="pj-form-field">
				<?php
				foreach (__('listing_b_statuses', true) as $k => $v)
				{
					if (isset($tpl['arr']['o_default_status_if_not_paid']) && $tpl['arr']['o_default_status_if_not_paid'] == $k)
					{
						?><option value="<?php echo $k; ?>" selected="selected"><?php echo $v; ?></option><?php
					} else {
						?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
					}
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><span class="block bold"><?php __('lblListingCalculatePriceBasedOnNumber'); ?></span><span class="fs10"><?php __('lblListingCalculatePriceBasedOnNumberText'); ?> </span></td>
			<td>
				<select name="o_price_based_on" class="pj-form-field w100">
				<?php
				foreach (__('listing_b_dn', true) as $k => $v)
				{
					?><option value="<?php echo $k; ?>"<?php echo @$tpl['arr']["o_price_based_on"] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><span class="block bold"><?php __('lblListingDepositPayment'); ?></span><span class="fs10"><?php __('lblListingDepositPaymentText'); ?></span></td>
			<td class="tblError"><input type="text" name="o_deposit_payment" value="<?php echo floatval(@$tpl['arr']["o_deposit_payment"]); ?>" class="pj-form-field w80 field-int number" readonly="readonly" /> <span>%</span></td>
		</tr>
		<tr>
			<td><span class="block bold"><?php __('lblListingSecurityPayment'); ?></span><span class="fs10"><?php __('lblListingSecurityPaymentText'); ?></span></td>
			<td class="tblError">
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="o_security_payment" value="<?php echo @$tpl['arr']["o_security_payment"]; ?>" class="pj-form-field w50 align_right number" />
				</span>
			</td>
		</tr>
		<tr>
			<td><span class="block bold"><?php __('lblListingTaxPayment'); ?></span><span class="fs10"><?php __('lblListingTaxPaymentText'); ?></span></td>
			<td class="tblError">
				<input type="text" name="o_tax_payment" value="<?php echo floatval(@$tpl['arr']["o_tax_payment"]); ?>" class="pj-form-field w80 field-int number" readonly="readonly" />
				<select name="o_tax_type" class="pj-form-field">
				<?php
				foreach (__('listing_b_tax_type', true) as $k => $v)
				{
					?><option value="<?php echo $k; ?>"<?php echo @$tpl['arr']["o_tax_type"] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><span class="block bold"><?php __('lblListingRequireAllWithinDays'); ?></span><span class="fs10"><?php __('lblListingRequireAllWithinDaysText'); ?></span></td>
			<td><input type="text" name="o_require_all_within_days" value="<?php echo @$tpl['arr']["o_require_all_within_days"]; ?>" class="pj-form-field w50 align_right" /> <?php __('lblListingDays');?></td>
		</tr>
		<tr>
			<td><span class="block bold"><?php __('lblListingAllowPayments'); ?></span><span class="fs10"><?php __('lblListingAllowPaymentsText'); ?> </span></td>
			<td>
				<label><input type="checkbox" name="o_allow_paypal" value="1" <?php echo @$tpl['arr']["o_allow_paypal"] == 1 ? 'checked="checked"' : NULL; ?> /> <?php __('lblListingAllowPaypalPayments'); ?></label>
				<br/><br/>
				<label><input type="checkbox" name="o_allow_authorize" value="1" <?php echo @$tpl['arr']["o_allow_authorize"] == 1 ? 'checked="checked"' : NULL; ?> /> <?php __('lblListingAllowAuthorizePayments'); ?></label>
				<br/><br/>
				<label><input type="checkbox" name="o_allow_creditcard" value="1" <?php echo @$tpl['arr']["o_allow_creditcard"] == 1 ? 'checked="checked"' : NULL; ?> /> <?php __('lblListingAllowCreditCardPayments'); ?></label>
				<br/><br/>
				<label><input type="checkbox" name="o_allow_bank" value="1" <?php echo @$tpl['arr']["o_allow_bank"] == 1 ? 'checked="checked"' : NULL; ?> /> <?php __('lblListingAllowBankPayments'); ?></label>
			</td>
		</tr>
		<tr class="AuthorizeNet" style="<?php echo @$tpl['arr']['o_allow_authorize'] != 1 ? 'display: none' : NULL; ?>">
			<td><span class="block bold"><?php __('lblListingAuthorizeMerchantId'); ?></span><span class="fs10"><?php __('lblListingAuthorizeMerchantIdText'); ?> </span></td>
			<td class="tblError"><span><input type="text" id="o_authorize_merchant_id" name="o_authorize_merchant_id" value="<?php echo str_replace('"','&quot;',stripslashes(utf8_decode(@$tpl['arr']["o_authorize_merchant_id"]))); ?>" class="pj-form-field w150<?php echo @$tpl['arr']['o_allow_authorize'] == 1 ? ' required' : '';?>" /></span></td>
		</tr>
		<tr class="AuthorizeNet" style="<?php echo @$tpl['arr']['o_allow_authorize'] != 1 ? 'display: none' : NULL; ?>">
			<td><span class="block bold"><?php __('lblListingAuthorizeTransKey'); ?></span><span class="fs10"><?php __('lblListingAuthorizeTransKeyText'); ?> </span></td>
			<td class="tblError"><span><input type="text" id="o_authorize_transkey" name="o_authorize_transkey" value="<?php echo str_replace('"','&quot;',stripslashes(utf8_decode(@$tpl['arr']["o_authorize_transkey"]))); ?>" class="pj-form-field w150<?php echo @$tpl['arr']['o_allow_authorize'] == 1 ? ' required' : '';?>" /></span></td>
		</tr>
		<tr class="AuthorizeNet" style="<?php echo @$tpl['arr']['o_allow_authorize'] != 1 ? 'display: none' : NULL; ?>">
			<td><span class="block bold"><?php __('lblListingAuthorizeTZone'); ?></span><span class="fs10"><?php __('lblListingAuthorizeTZoneText'); ?> </span></td>
			<td><select name="o_authorize_tz" class="pj-form-field w150">
			<?php
			foreach (__('timezones', true) as $k => $v)
			{
				?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['o_authorize_tz'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
			}
			?>
			</select></td>
		</tr>
		<tr class="PayPal" style="<?php echo @$tpl['arr']['o_allow_paypal'] != 1 ? 'display: none' : NULL; ?>">
			<td><span class="block bold"><?php __('lblListingPaypalAddress'); ?></span><span class="fs10"><?php __('lblListingPaypalAddressText'); ?> </span></td>
			<td class="tblError">
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
					<input type="text" id="o_paypal_address" name="o_paypal_address" value="<?php echo str_replace('"','&quot;',stripslashes(utf8_decode(@$tpl['arr']["o_paypal_address"]))); ?>" class="pj-form-field w200<?php echo @$tpl['arr']['o_allow_paypal'] == 1 ? ' email required' : NULL; ?>" />
				</span>
			</td>
		</tr>
		<tr class="BankAccount" style="<?php echo @$tpl['arr']['o_allow_bank'] != 1 ? 'display: none' : NULL; ?>">
			<td><span class="block bold"><?php __('lblListingBankAccount'); ?></span><span class="fs10"><?php __('lblListingBankAccountText'); ?> </span></td>
			<td class="tblError"><span><textarea id="o_bank_account" name="o_bank_account" class="pj-form-field w300 h70<?php echo @$tpl['arr']['o_allow_bank'] == 1 ? ' required' : NULL; ?>"><?php echo str_replace('"','&quot;',stripslashes(utf8_decode(@$tpl['arr']["o_bank_account"]))); ?></textarea></span></td>
		</tr>
		<tr>
			<td><span class="block bold"><?php __('lblListingThankYouPageLocation'); ?></span><span class="fs10"><?php __('lblListingThankYouPageLocationText'); ?> </span></td>
			<td>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-url"></abbr></span>
					<input name="o_thankyou_page" type="text" value="<?php echo str_replace('"','&quot;',stripslashes(utf8_decode(@$tpl['arr']["o_thankyou_page"]))); ?>" class="pj-form-field w200" />
				</span>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" /></td>
		</tr>
	</table>
</div>