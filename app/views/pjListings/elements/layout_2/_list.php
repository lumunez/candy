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
	<div class="property-state">
		<div class="property-state-header">
			<a href="<?php echo $url; ?>" class="property-state-lead"><?php echo $property_title; ?></a>
			<div class="property-state-literal">
			<?php
			if (!empty($v['listing_floor_area']))
			{
				__('front_index_floor'); ?>: <span><?php echo pjUtil::showFloor($floor, $v['listing_floor_area'], __('front_view_floor_measure', true)); ?></span><?php
			}
			if (!empty($v['type_title']))
			{
				__('front_index_type'); ?>: <span><?php echo stripslashes($v['type_title']); ?></span><?php
			}
			if (!empty($v['listing_bedrooms']))
			{
				__('front_index_bedrooms'); ?>: <span><?php echo $v['listing_bedrooms']; ?></span><?php
			}
			if (!empty($v['listing_bathrooms']))
			{
				__('front_index_bathrooms'); ?>: <span><?php echo $v['listing_bathrooms'] - (int) $v['listing_bathrooms'] == 0 ? round($v['listing_bathrooms'], 0) : round($v['listing_bathrooms'], 1); ?></span><?php
			}
			?>
			</div>
		</div>
		<div class="property-state-content">
			<div class="property-list-pic">
				<a href="<?php echo $url; ?>"><img src="<?php echo PJ_INSTALL_URL . (!empty($v['pic']) ? $v['pic'] : PJ_IMG_PATH . 'no-image.png'); ?>" alt="<?php echo $property_title; ?>" /></a>
			</div>
			<div class="property-list-desc">
				<?php echo pjMultibyte::substr(stripslashes(strip_tags($v['listing_description'])), 0, 350); ?>
			</div>
			<div class="property-list-foot"></div>
		</div>
		<a href="<?php echo $url; ?>" class="property-list-details"><?php __('front_index_details'); ?> +</a>
	</div>
	<?php
}
?>