<div class="property-grid-wrap">
	<?php
	foreach ($tpl['arr'] as $v)
	{
		$property_title = pjSanitize::html(stripslashes($v['listing_title'] . " / " . $v['listing_refid']));
		if ($tpl['option_arr']['o_seo_url'] == 'No')
		{
			$url = $_SERVER['SCRIPT_NAME'] . '?controller=pjListings&amp;action=pjActionView&amp;id=' . $v['id'] .(isset($_GET['iframe']) ? '&amp;iframe' : NULL);
		} else {
			//$url = $_SERVER['SCRIPT_NAME'] . "/" . $v['id'] . "/" . $controller->friendlyURL($v['type_title']) . ".html";
			$path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
			$path = $path == '/' ? '' : $path;
			$url = $path .'/'. $controller->friendlyURL($v['listing_title']) . "-". $v['id'] . ".html";
		}
		?>
		<div class="property-grid">
			<div class="property-grid-title"><a href="<?php echo $url; ?>"><?php echo $property_title; ?></a></div>
			<div class="property-grid-pic"><a href="<?php echo $url; ?>"><img src="<?php echo PJ_INSTALL_URL . (!empty($v['pic']) ? $v['pic'] : PJ_IMG_PATH . 'no-image2.png'); ?>" alt="<?php echo $property_title; ?>" /></a></div>
			<div class="property-grid-details">
			<?php
			if (!empty($v['listing_floor_area']))
			{
				?><span class="property-item-wrap"><span class="property-item-label"><?php __('front_index_floor'); ?>:</span> <span class="property-item-value"><?php echo pjUtil::showFloor($floor, $v['listing_floor_area'], __('front_view_floor_measure', true)); ?></span></span><?php
			}
			/*if (!empty($v['type_title']))
			{
				?><span class="property-item-wrap"><span class="property-item-label"><?php __('front_index_type'); ?>:</span> <span class="property-item-value"><?php echo stripslashes($v['type_title']); ?></span></span><?php
			}*/
			if (!empty($v['listing_bedrooms']))
			{
				?><span class="property-item-wrap"><span class="property-item-label"><?php __('front_index_bedrooms'); ?>:</span> <span class="property-item-value"><?php echo $v['listing_bedrooms']; ?></span></span><?php
			}
			if (!empty($v['listing_bathrooms']))
			{
				?><span class="property-item-wrap"><span class="property-item-label"><?php __('front_index_bathrooms'); ?>:</span> <span class="property-item-value"><?php echo $v['listing_bathrooms'] - (int) $v['listing_bathrooms'] == 0 ? round($v['listing_bathrooms'], 0) : round($v['listing_bathrooms'], 1); ?></span></span><?php
			}
			?>
			</div>
			<div class="property-grid-btn">
				<a href="<?php echo $url; ?>" class="property-button property-grid-inner"><?php __('front_view_details'); ?><abbr></abbr></a>
			</div>
		</div>
		<?php
	}
	if (count($tpl['arr']) > 0)
	{
		?>
		<div class="vrl-clear-left" style="height: 98px"></div>
		<div class="property-grid-mask"></div>
		<?php
	}
	?>
</div>
