<?php if ((int) $tpl['arr']['o_accept_bookings'] == 1) : ?>
<form action="?controller=pjListings&amp;action=pjActionView&amp;id=<?php echo $tpl['listing_id']; ?>" method="post" id="frmPLBooking" name="frmPLBooking" class="property-form property-view-form">
	<input type="hidden" name="booking_form" value="1" />
	<input type="hidden" name="booking_url" value="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjListings&amp;action=pjActionSendRequest" />
	<input type="hidden" name="folder" value="<?php echo PJ_INSTALL_FOLDER; ?>" />
	<input type="hidden" name="listing_id" value="<?php echo $tpl['listing_id']; ?>" />

	<?php
	if (isset($_SESSION[$controller->defaultSearch]))
	{
		if (!isset($_POST['date_from']) && isset($_SESSION[$controller->defaultSearch]['date_from']))
		{
			$_POST['date_from'] = $_SESSION[$controller->defaultSearch]['date_from'];
		}
		if (!isset($_POST['date_to']) && isset($_SESSION[$controller->defaultSearch]['date_to']))
		{
			$_POST['date_to'] = $_SESSION[$controller->defaultSearch]['date_to'];
		}
	}
	$today = date("Y-m-d");
	$fToday = pjUtil::formatDate($today, "Y-m-d", $tpl['option_arr']['o_date_format']);
	?>
	<p>
		<label><?php __('front_booking_from'); ?></label>
		<span class="property-datepicker-wrap vrl-l10 vrl-r20">
			<input type="text" name="date_from" id="vrl_date_from" class="property-datepicker-input" readonly="readonly" value="<?php echo isset($_POST['date_from']) ? htmlspecialchars($_POST['date_from']) : NULL; ?>" />
			<input type="button" class="property-datepicker-icon vrl-l10" id="vrl_datepicker_from" value="" />
			<abbr></abbr>
		</span>
		<label class="vrl-l20"><?php __('front_booking_to'); ?></label>
		<span class="property-datepicker-wrap vrl-l10">
			<input type="text" name="date_to" id="vrl_date_to" class="property-datepicker-input" readonly="readonly" value="<?php echo isset($_POST['date_to']) ? htmlspecialchars($_POST['date_to']) : NULL; ?>" />
			<input type="button" class="property-datepicker-icon vrl-l10" id="vrl_datepicker_to" value="" />
			<abbr></abbr>
		</span>
	</p>
	<div class="vrPriceBox"><?php include PJ_VIEWS_PATH . 'pjListings/pjActionGetPrice.php'; ?></div>
	<p>
		<label class="property-title"><?php __('front_booking_name'); ?></label>
		<input type="text" name="name" class="property-text vrl-w70p" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : NULL; ?>" />
	</p>
	<p>
		<label class="property-title"><?php __('front_booking_email'); ?></label>
		<input type="text" name="email" class="property-text vrl-w70p" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : NULL; ?>" />
	</p>
	<p>
		<label class="property-title"><?php __('front_booking_phone'); ?></label>
		<input type="text" name="phone" class="property-text vrl-w70p" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : NULL; ?>" />
	</p>
	<p>
		<label class="property-title"><?php __('front_booking_notes'); ?></label>
		<textarea name="notes" class="property-textarea vrl-w70p" style="height: 100px"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : NULL; ?></textarea>
	</p>
	<?php if ((int) $tpl['arr']['o_disable_payments'] == 0) : ?>
	<p>
		<label class="property-title"><?php __('front_booking_pm'); ?></label>
		<select name="payment_method" class="property-select vrl-w70p" onchange="VRL.Utils.changePM(this);">
		<option value=""><?php __('front_booking_pm_empty'); ?></option>
		<?php
		foreach (__('payment_methods', true) as $k => $v)
		{
			if (!isset($tpl['arr']['o_allow_'.$k]) || $tpl['arr']['o_allow_'.$k] == 0) continue;
			?><option value="<?php echo $k; ?>"<?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
		}
		?>
		</select>
	</p>
	<p class="vrCC" style="display: <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
		<label class="property-title"><?php __('front_booking_cc_type'); ?></label>
		<select name="cc_type" class="property-select">
		<?php
		foreach (__('cc_types', true) as $k => $v)
		{
			?><option value="<?php echo $k; ?>"<?php echo isset($_POST['cc_type']) && $_POST['cc_type'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
		}
		?>
		</select>
	</p>
	<p class="vrCC" style="display: <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
		<label class="property-title"><?php __('front_booking_cc_num'); ?></label>
		<input type="text" name="cc_num" class="property-text" value="<?php echo isset($_POST['cc_num']) ? (int) $_POST['cc_num'] : NULL; ?>" />
	</p>
	<p class="vrCC" style="display: <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
		<label class="property-title"><?php __('front_booking_cc_code'); ?></label>
		<input type="text" name="cc_code" class="property-text" value="<?php echo isset($_POST['cc_code']) ? (int) $_POST['cc_code'] : NULL; ?>" />
	</p>
	<p class="vrCC" style="display: <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
		<label class="property-title"><?php __('front_booking_cc_exp'); ?></label>
		<input type="text" name="cc_exp" class="property-text" value="<?php echo isset($_POST['cc_exp']) ? htmlspecialchars($_POST['cc_exp']) : NULL; ?>" />
	</p>
	<p class="vrBank" style="display: <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'bank' ? 'block' : 'none'; ?>">
		<label class="property-title"><?php __('front_booking_bank'); ?></label>
		<span class="vrl-float-left vrl-w70p"><?php echo stripslashes(nl2br($tpl['arr']['o_bank_account'])); ?></span>
	</p>
	<?php endif; ?>
	<p>
		<label class="property-title"><?php __('front_booking_captcha'); ?></label>
		<img class="property-captcha" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 999999); ?>" alt="CAPTCHA" style="vertical-align: middle" />
		<input type="text" name="verification" id="verification" class="property-text" maxlength="6" autocomplete="off" />
		<label class="boxStatusCode" style="display: none"></label>
	</p>
	<p>
		<label class="property-title">&nbsp;</label>
		<button type="submit" class="submitRequestButton property-button" onclick="VRL.Utils.submitRequest(event, 'frmPLBooking', 'property-view-availability'); return false;"><abbr></abbr><?php __('front_booking_submit'); ?></button>
	</p>
	<?php
	if (isset($tpl['status']))
	{
		$front_bs = __('front_booking_status', true);
		$status = __('status', true);
		$listing_b_dn = __('listing_b_dn', true);
		switch ($tpl['status'])
		{
			case 1:
				?><p><label class="property-title">&nbsp;</label><span class="property-status-success"><?php echo $front_bs[1]; ?></span></p><?php
				break;
			case 2:
				?><p><label class="property-title">&nbsp;</label><span class="property-status-error"><?php echo $front_bs[2]; ?></span></p><?php
				break;
			case 3:
				?><p><label class="property-title">&nbsp;</label><span class="property-status-error"><?php echo $front_bs[3]; ?></span></p><?php
				break;
			case 4:
				?><p><label class="property-title">&nbsp;</label><span class="property-status-error"><?php echo $front_bs[4]; ?></span></p><?php
				break;
			case 5:
				?><p><label class="property-title">&nbsp;</label><span class="property-status-error"><?php echo $front_bs[5]; ?></span></p><?php
				break;
			case 6:
				?><p><label class="property-title">&nbsp;</label><span class="property-status-error"><?php echo $front_bs[6]; ?></span></p><?php
				break;
			case 8:
				?><p><label class="property-title">&nbsp;</label><span class="property-status-error"><?php echo $front_bs[8]; ?></span></p><?php
				break;
			case 9:
				?><p><label class="property-title">&nbsp;</label><span class="property-status-error"><?php printf($front_bs[9], $tpl['arr']['o_min_booking_lenght'], @$listing_b_dn[$tpl['arr']['o_price_based_on']]); ?></span></p><?php
				break;
			case 10:
				?><p><label class="property-title">&nbsp;</label><span class="property-status-error"><?php printf($front_bs[10], $tpl['arr']['o_max_booking_lenght'], @$listing_b_dn[$tpl['arr']['o_price_based_on']]); ?></span></p><?php
				break;
			case 7:
				?><p><label class="property-title">&nbsp;</label><span class="property-status-error"><?php echo $status[7]; ?></span></p><?php
				break;
		}
	}
	?>
</form>
<?php endif; ?>