<?php
if (!empty($tpl['arr']['listing_terms']))
{
	?>
	<div class="property-state property-selector-terms">
		<h2><?php __('front_view_terms'); ?></h2>
		<div class="vrl-lh18 property-view-content"><?php echo stripslashes(nl2br(pjSanitize::html($tpl['arr']['listing_terms']))); ?></div>
	</div>
	<?php
}
?>