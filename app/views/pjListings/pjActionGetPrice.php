<?php
if (isset($tpl['p_arr']))
{
	if (isset($tpl['p_arr']['deposit_f']))
	{
		?>
		<table style="width: 100%" cellspacing="0" cellpadding="0" class="property-form-tbl">
			<tr>
				<td><?php __('front_booking_price'); ?></td>
				<td><?php echo $tpl['p_arr']['amount_f']; ?></td>
			</tr>
			<tr>
				<td><?php __('front_booking_price_security'); ?></td>
				<td><?php echo $tpl['p_arr']['security_f']; ?></td>
			</tr>
			<tr>
				<td><?php __('front_booking_price_tax'); ?></td>
				<td><?php echo $tpl['p_arr']['tax_f']; ?></td>
			</tr>
			<tr>
				<td><?php __('front_booking_price_deposit'); ?> (<?php echo $tpl['p_arr']['deposit_p']; ?>% + <?php __('front_booking_price_security'); ?>)</td>
				<td><?php echo $tpl['p_arr']['deposit_f']; ?></td>
			</tr>
		</table>
		<?php
	} else {
		?>
		<table style="width: 100%" cellspacing="0" cellpadding="0" class="property-form-tbl">
			<tr>
				<td><?php __('front_booking_price'); ?></td>
				<td><?php echo $tpl['p_arr']['amount_f']; ?></td>
			</tr>
		</table>
		<?php
	}
}
?>