<div class="property-view-header">
	<h1><?php echo pjSanitize::html(stripslashes($tpl['arr']['listing_title'])); ?></h1>
	<?php
	if ($showPropertyLocation && !empty($tpl['arr']['country_title']) && !empty($tpl['arr']['address_city']))
	{
		?>
		<p class="property-location"><strong><?php echo pjUtil::concat(', ', array($tpl['arr']['country_title'], $tpl['arr']['address_city'], $tpl['arr']['address_state'], $tpl['arr']['address_postcode']));?></strong></p>
		<?php
	}
	?>
</div>