<div class="property-state">
	<div class="property-state-content property-state-content-top">
		<p><span class="vrl-w49p vrl-inline-block"><?php __('front_view_refid'); ?></span><span class="vrl-bold"><?php echo pjSanitize::html($tpl['arr']['listing_refid']); ?></span></p>
		<p><span class="vrl-w49p vrl-inline-block"><?php __('front_view_type'); ?></span><span class="vrl-bold"><?php echo pjSanitize::html($tpl['arr']['type_title']); ?></span></p>
		<?php
		if (!empty($tpl['arr']['listing_bedrooms']))
		{
			?><p><span class="vrl-w49p vrl-inline-block"><?php __('front_view_bedrooms'); ?></span><span class="vrl-bold"><?php echo $tpl['arr']['listing_bedrooms']; ?></span></p><?php
		}
		 
		if (!empty($tpl['arr']['listing_bathrooms']))
		{
			?><p><span class="vrl-w49p vrl-inline-block"><?php __('front_view_bathrooms'); ?></span><span class="vrl-bold"><?php echo $tpl['arr']['listing_bathrooms'] - (int) $tpl['arr']['listing_bathrooms'] == 0 ? round($tpl['arr']['listing_bathrooms'], 0) : round($tpl['arr']['listing_bathrooms'], 1); ?></span></p><?php
		}
		 
		if (!empty($tpl['arr']['listing_adults']))
		{
			?><p><span class="vrl-w49p vrl-inline-block"><?php __('front_view_adults'); ?></span><span class="vrl-bold"><?php echo $tpl['arr']['listing_adults']; ?></span></p><?php
		}
		 
		if (!empty($tpl['arr']['listing_children']))
		{
			?><p><span class="vrl-w49p vrl-inline-block"><?php __('front_view_children'); ?></span><span class="vrl-bold"><?php echo $tpl['arr']['listing_children']; ?></span></p><?php
		}
		
		if (!empty($tpl['arr']['listing_floor_area']))
		{
			?><p><span class="vrl-w49p vrl-inline-block"><?php __('front_view_floor_area'); ?></span><span class="vrl-bold"><?php echo pjUtil::showFloor($floor, $tpl['arr']['listing_floor_area'], __('front_view_floor_measure', true)); ?></span></p><?php
		}
		?>
	</div>
</div>