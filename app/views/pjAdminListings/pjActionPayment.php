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
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminListings&amp;action=pjActionPayment&amp;id=<?php echo $_GET['id']; ?>"><?php __('lblListingExtend'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoListingExtendTitle', true), __('infoListingExtendBody', true));
	?>
	<div class="form pj-form b10">
		<p>
			<label class="title"><?php __('lblListingProperty'); ?>:</label>
			<span class="left"><?php echo stripslashes($tpl['arr']['listing_title']); ?> / <a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminListings&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php echo $tpl['arr']['id']; ?></a></span>
		</p>
		<p>
			<label class="title"><?php __('lblListingExpire'); ?>:</label>
			<span class="left"><?php echo pjUtil::formatDate($tpl['arr']['expire'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?></span>
		</p>
	</div>
	<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
		<thead>
			<tr>
				<th><?php __('listing_payment_period'); ?></th>
				<th><?php __('listing_payment_price'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$pjPaypal = pjObject::getPlugin('pjPaypal') !== NULL;
		foreach ($tpl['period_arr'] as $period)
		{
			if ((int) $period['days'] > 0)
			{
				if ((float) $period['price'] > 0 && (int) $tpl['arr']['o_disable_payments'] == 0)
				{
					?>
					<tr>
						<td><?php echo $period['days']; ?> <?php __('lblDays'); ?></td>
						<td><?php echo $period['price']; ?> <?php echo $tpl['option_arr']['o_currency']; ?></td>
						<td>
						<?php
						if ($pjPaypal)
						{
							$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => array(
								'name' => 'vrPaypal',
								'id' => 'vrPaypal_' . $period['id'],
								'business' => $tpl['option_arr']['o_paypal_address'],
								'item_name' => __('listing_payment_period', true) .'(#'.$period['id'].'): '. $period['days'] .' '. __('o_days', true),
								'custom' => $_GET['id'],
								'amount' => number_format($period['price'], 2, '.', ''),
								'currency_code' => $tpl['option_arr']['o_currency'],
								'return' => PJ_INSTALL_URL . "index.php?controller=pjAdminListings&amp;action=pjActionIndex",
								'notify_url' => PJ_INSTALL_URL . "index.php?controller=pjListings&amp;action=pjActionConfirmPayment",
								'submit' => __('listing_payment_renew_paypal', true),
								'submit_class' => 'pj-button',
								'target' => '_blank'
							)));
						}
						?>
						</td>
					</tr>
					<?php
				} else {
					?>
					<tr>
						<td><?php echo $period['days']; ?> <?php __('lblDays'); ?></td>
						<td><?php __('listing_payment_free'); ?></td>
						<td>
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminListings&amp;action=pjActionExtend" method="post">
								<input type="hidden" name="extend" value="1" />
								<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
								<input type="hidden" name="period_id" value="<?php echo $period['id']; ?>" />
								<input type="submit" value="<?php __('listing_payment_renew_free'); ?>" class="pj-button" />
							</form>
						</td>
					</tr>
					<?php
				}
			}
		}
		?>
		</tbody>
	</table>
	<?php
}
?>