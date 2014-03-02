<?php
if (!empty($tpl['arr']['listing_terms']))
{
	?>
	<div class="property-view-terms">
		<div class="vrl-bold vrl-b10"><?php __('front_view_terms'); ?></div>
		<div class="vrl-lh18 property-view-terms-content"><?php echo stripslashes(nl2br(pjSanitize::html($tpl['arr']['listing_terms']))); ?></div>
	</div>
	<?php
}
?>