<?php
if (isset($tpl['post']['payment_method']))
{
	$status = __('front_booking_status', true);
	switch ($tpl['post']['payment_method'])
	{
		case 'paypal':
			?><p><?php echo $status[11]; ?></p><?php
			if (pjObject::getPlugin('pjPaypal') !== NULL)
			{
				$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
			}
			break;
		case 'authorize':
			?><p><?php echo $status[11]; ?></p><?php
			if (pjObject::getPlugin('pjAuthorize') !== NULL)
			{
				$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
			}
			break;
		case 'creditcard':
			?><p><?php echo $status[1]; ?></p><?php
			break;
		case 'bank':
			?><p><?php echo $status[1]; ?></p><p><?php echo stripslashes(nl2br($tpl['booking_arr']['o_bank_account'])); ?></p><?php
			break;
	}
}
?>