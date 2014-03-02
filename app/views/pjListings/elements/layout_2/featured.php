<?php
$listingPage = $tpl['option_arr']['o_listing_page'];
?>
<div class="property-container property-container-featured">
	<?php
	if (isset($tpl['arr']) && count($tpl['arr']) > 0)
	{
		$floor = $tpl['option_arr']['o_floor'];
		$currency = $tpl['option_arr']['o_currency'];
		?>
		<div class="property-state">
			<div class="property-state-header"><h2><?php __('front_featured_title'); ?></h2></div>
		</div>
		
		<?php
		if ($tpl['option_arr']['o_layout'] == 'layout_1_grid')
		{
			include dirname(__FILE__) . '/_grid.php';
		} else {
			include dirname(__FILE__) . '/_list.php';
		}
	} else {
		?>
		<div class="property-state">
			<div class="property-state-header"><h2><?php __('front_index_empty'); ?></h2></div>
		</div>
		<?php
	}
	?>
</div>