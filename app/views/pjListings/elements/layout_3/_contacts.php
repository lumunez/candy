<?php
if ($tpl['arr']['contact_show'] == 1)
{
	ob_start();
	if (!empty($tpl['arr']['personal_title']) or !empty($tpl['arr']['personal_fname']) or !empty($tpl['arr']['personal_lname']))
	{
		?><p><span class="vrl-float-left vrl-w45p"><?php __('front_view_name'); ?></span><span class="vrl-float-left vrl-w53p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['personal_title'])); ?> <?php echo htmlspecialchars(stripslashes($tpl['arr']['personal_fname'] . " " . $tpl['arr']['personal_lname'])); ?></span></p><?php
	}
	if (!empty($tpl['arr']['contact_phone']))
	{
		?><p><span class="vrl-float-left vrl-w45p"><?php __('front_view_phone'); ?></span><span class="vrl-float-left vrl-w53p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['contact_phone'])); ?></span></p><?php
	}
	if (!empty($tpl['arr']['contact_mobile']))
	{
		?><p><span class="vrl-float-left vrl-w45p"><?php __('front_view_mobile'); ?></span><span class="vrl-float-left vrl-w53p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['contact_mobile'])); ?></span></p><?php
	}
	if (!empty($tpl['arr']['contact_fax']))
	{
		?><p><span class="vrl-float-left vrl-w45p"><?php __('front_view_fax'); ?></span><span class="vrl-float-left vrl-w53p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['contact_fax'])); ?></span></p><?php
	}
	if (!empty($tpl['arr']['contact_email']))
	{
		?><p><span class="vrl-float-left vrl-w45p"><?php __('front_view_email'); ?></span><span class="vrl-float-left vrl-w53p vrl-bold vrl-color-black"><?php echo !preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i', $tpl['arr']['contact_email']) ? $tpl['arr']['contact_email'] : '<a href="mailto:'.pjUtil::encodeEmail($tpl['arr']['contact_email']).'">'.pjUtil::encodeEmail($tpl['arr']['contact_email']).'</a>'; ?></span></p><?php
	}
	if (!empty($tpl['arr']['contact_url']))
	{
		?><p><span class="vrl-float-left vrl-w45p"><?php __('front_view_url'); ?></span><span class="vrl-float-left vrl-w53p vrl-bold vrl-color-black"><?php echo preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank" rel="nofollow">$2</a>', $tpl['arr']['contact_url']); ?></span></p><?php
	}
	if (!empty($tpl['arr']['address_postcode']))
	{
		?><p><span class="vrl-float-left vrl-w45p"><?php __('front_view_postcode'); ?></span><span class="vrl-float-left vrl-w53p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['address_postcode'])); ?></span></p><?php
	}
	if (!empty($tpl['arr']['address_content']))
	{
		?><p><span class="vrl-float-left vrl-w45p"><?php __('front_view_address'); ?></span><span class="vrl-float-left vrl-w53p vrl-bold vrl-color-black"><?php echo stripslashes(nl2br(pjSanitize::html($tpl['arr']['address_content']))); ?></span></p><?php
	}
	if (!empty($tpl['arr']['country_title']))
	{
		?><p><span class="vrl-float-left vrl-w45p"><?php __('front_view_country'); ?></span><span class="vrl-float-left vrl-w53p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['country_title'])); ?></span></p><?php
	}
	if (!empty($tpl['arr']['address_state']))
	{
		?><p><span class="vrl-float-left vrl-w45p"><?php __('front_view_state'); ?></span><span class="vrl-float-left vrl-w53p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['address_state'])); ?></span></p><?php
	}
	if (!empty($tpl['arr']['address_city']))
	{
		?><p><span class="vrl-float-left vrl-w45p"><?php __('front_view_city'); ?></span><span class="vrl-float-left vrl-w53p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['address_city'])); ?></span></p><?php
	}
	$ob_contact = ob_get_contents();
	ob_end_clean();
		
	if (!empty($ob_contact))
	{
		echo $ob_contact;
	}
}
?>