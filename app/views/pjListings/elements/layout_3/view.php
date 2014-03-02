<?php
$floor = $tpl['option_arr']['o_floor'];
?>
<div class="property-container">
<?php
include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_3/menu.php';
	
if (isset($tpl['arr']))
{
	?>
	<h1 class="property-selector-block"><?php echo pjSanitize::html(stripslashes($tpl['arr']['listing_title'])); ?></h1>
	<?php
	if (count($tpl['gallery_arr']) > 0)
	{
		?>
		<div class="property-view-gallery property-state property-selector-gallery">
			<a href="#" class="property-carousel-nav property-carousel-nav-left"></a>
			<a href="#" class="property-carousel-nav property-carousel-nav-right"></a>
			<div class="property-carousel">
				<div class="property-carousel-slide" style="left: 0px">
				<?php
				foreach ($tpl['gallery_arr'] as $k => $v)
				{
					?><div class="property-carousel-item"><a rel="lytebox[allphotos]" title="<?php echo htmlspecialchars(stripslashes($v['alt'])); ?>" href="<?php echo PJ_INSTALL_URL . $v['large_path']; ?>"><img src="<?php echo PJ_INSTALL_URL . $v['small_path']; ?>" alt="<?php echo htmlspecialchars(stripslashes($v['alt'])); ?>" /></a></div><?php
				}
				?>
				</div>
			</div>
		</div>
		<?php
	}
	?>
	<div class="property-selector-block">
		<div class="property-state property-selector-details">
			<div class="property-view-content vrl-lh22">
				<p><span class="vrl-float-left vrl-w35p"><?php __('front_view_refid'); ?></span><span class="vrl-float-right vrl-w60p vrl-bold vrl-color-black"><?php echo pjSanitize::html($tpl['arr']['listing_refid']); ?></span></p>
				<p><span class="vrl-float-left vrl-w35p"><?php __('front_view_type'); ?></span><span class="vrl-float-right vrl-w60p vrl-bold vrl-color-black"><?php echo pjSanitize::html($tpl['arr']['type_title']); ?></span></p>
				<?php
				if (!empty($tpl['arr']['listing_bedrooms']))
				{
					?><p><span class="vrl-float-left vrl-w35p"><?php __('front_view_bedrooms'); ?></span><span class="vrl-float-right vrl-w60p vrl-bold vrl-color-black"><?php echo $tpl['arr']['listing_bedrooms']; ?></span></p><?php
				}
				 
				if (!empty($tpl['arr']['listing_bathrooms']))
				{
					?><p><span class="vrl-float-left vrl-w35p"><?php __('front_view_bathrooms'); ?></span><span class="vrl-float-right vrl-w60p vrl-bold vrl-color-black"><?php echo $tpl['arr']['listing_bathrooms'] - (int) $tpl['arr']['listing_bathrooms'] == 0 ? round($tpl['arr']['listing_bathrooms'], 0) : round($tpl['arr']['listing_bathrooms'], 1); ?></span></p><?php
				}
				 
				if (!empty($tpl['arr']['listing_adults']))
				{
					?><p><span class="vrl-float-left vrl-w35p"><?php __('front_view_adults'); ?></span><span class="vrl-float-right vrl-w60p vrl-bold vrl-color-black"><?php echo $tpl['arr']['listing_adults']; ?></span></p><?php
				}
				 
				if (!empty($tpl['arr']['listing_children']))
				{
					?><p><span class="vrl-float-left vrl-w35p"><?php __('front_view_children'); ?></span><span class="vrl-float-right vrl-w60p vrl-bold vrl-color-black"><?php echo $tpl['arr']['listing_children']; ?></span></p><?php
				}
				
				if (!empty($tpl['arr']['listing_floor_area']))
				{
					?><p><span class="vrl-float-left vrl-w35p"><?php __('front_view_floor_area'); ?></span><span class="vrl-float-right vrl-w60p vrl-bold vrl-color-black"><?php echo pjUtil::showFloor($floor, $tpl['arr']['listing_floor_area'], __('front_view_floor_measure', true)); ?></span></p><?php
				}
				?>
				<span class="vrl-clear-both"></span>
			</div>
		</div>
		<?php
		$extra_arr = array('property' => array(), 'community' => array());
		foreach ($tpl['extra_arr'] as $extra)
		{
			if (in_array($extra['id'], $tpl['arr']['extras']))
			{
				switch ($extra['type'])
				{
					case 'property':
						$extra_arr['property'][] = sprintf('<li>%s</li>', stripslashes($extra['extra_title']));
						break;
					case 'community':
						$extra_arr['community'][] = sprintf('<li>%s</li>', stripslashes($extra['extra_title']));
						break;
				}
			}
		}
		$cnt_prop = count($extra_arr['property']);
		$cnt_comm = count($extra_arr['community']);
		if ($cnt_prop + $cnt_comm > 0)
		{
			$per_prop = ceil($cnt_prop / 3);
			$per_comm = ceil($cnt_comm / 3);
			$vet = __('extra_type_arr', true);
			?>
			<div class="property-state property-selector-features">
				<h2><?php __('front_view_extra'); ?></h2>
				<div class="property-view-content">
					<?php
					if ($cnt_prop > 0)
					{
						?>
						<div class="vrl-overflow">
							<span class="property-feature-set"><?php echo $vet['property']; ?></span>
							<ul class="property-list vrl-float-left">
							<?php echo join("", $extra_arr['property']); ?>
							</ul>
							<span class="vrl-clear-left"></span>
						</div>
						<?php
					}
					if ($cnt_comm > 0)
					{
						if ($cnt_prop > 0)
						{
							?>
							<div class="vrl-overflow vrl-t10">
							<?php
						} else {
							?>
							<div class="vrl-overflow">
							<?php
						}
						?>
							<span class="property-feature-set"><?php echo $vet['community']; ?></span>
							<ul class="property-list vrl-float-left">
							<?php echo join("", $extra_arr['community']); ?>
							</ul>
							<span class="vrl-clear-left"></span>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	
	<div class="property-selector-block">
	<?php
	# Google Maps
	if ($tpl['arr']['address_map'] == 1 && !empty($tpl['arr']['lat']) && !empty($tpl['arr']['lng']))
	{
		?>
		<div class="property-state property-selector-map" id="map">
			<h2 id="property-view-map-header" style="display: none"><?php __('front_view_map'); ?></h2>
			<div class="property-view-content">
				<?php include PJ_VIEWS_PATH . 'pjListings/elements/viewGoogleMaps.php'; ?>
			</div>
		</div>
		<?php
	}
 
	if (!empty($tpl['arr']['listing_description']))
	{
		?>
		<div class="property-state property-selector-description">
			<h2><?php __('front_view_description'); ?></h2>
			<div class="property-view-content">
				<div class="vrl-lh20"><?php echo (stripslashes(nl2br(pjUtil::convertLinks($tpl['arr']['listing_description'])))); ?></div>
			</div>
		</div>
		<?php
	}
	?>
	</div>
	
	<div class="property-selector-block">
	<?php
	# Contact (owner) detains
	if ($tpl['arr']['contact_show'] == 1)
	{
		?>
		<div class="property-state property-selector-contacts">
			<h2><?php __('front_view_contact'); ?></h2>
			<div class="property-view-content vrl-lh26"><?php include dirname(__FILE__) . '/_contacts.php'; ?></div>
		</div>
		<?php
	}
		?>
		<div id="property-view-availability" class="property-state property-selector-calendar">
			<h2><?php __('front_view_calendar'); ?></h2>
			<div class="property-view-booking-links">
				<ul class="property-buttonset">
					<li class="property-buttonset-first">
						<a href="<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjListings&amp;action=pjActionView&amp;id=<?php echo $tpl['listing_id']; ?>" onclick="var cdate = document.getElementById('property-view-calendars-date'); JABB.Ajax.sendRequest('<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjListings&action=pjActionGetAvailability&listing_id=<?php echo $tpl['listing_id']; ?>&get=prev&date='+VRL.Utils.calendarDate('prev', cdate.innerHTML, 1), VRL.Utils.calendarCallback); cdate.innerHTML=VRL.Utils.calendarDate('prev', cdate.innerHTML, 1); return false;">&laquo;</a>
					</li>
					<li class="property-buttonset-last">
						<a href="<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjListings&amp;action=pjActionView&amp;id=<?php echo $tpl['listing_id']; ?>" onclick="var cdate = document.getElementById('property-view-calendars-date'); JABB.Ajax.sendRequest('<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjListings&action=pjActionGetAvailability&listing_id=<?php echo $tpl['listing_id']; ?>&get=next&date='+VRL.Utils.calendarDate('next', cdate.innerHTML, 1), VRL.Utils.calendarCallback); cdate.innerHTML=VRL.Utils.calendarDate('next', cdate.innerHTML, 1); return false;">&raquo;</a>
					</li>
				</ul>
				<span id="property-view-calendars-date" style="display: none"><?php echo date("Y-n-j", mktime(0,0,0,date("n"),1,date("Y"))); ?></span>
			</div>
			<div class="property-view-content vrl-relative">
				<div id="property-view-calendars"><?php include PJ_VIEWS_PATH . 'pjListings/elements/viewCalendar.php'; ?></div>
			</div>
		</div>
		<?php
		# Prices
		include dirname(__FILE__) . '/_prices.php'; ?>
	</div>
	
	<div class="property-selector-block">
		<?php include dirname(__FILE__) . '/_terms.php'; ?>
		<?php if ((int) $tpl['arr']['o_accept_bookings'] === 1) : ?>
		<div class="property-state property-selector-form">
			<div id="property-view-booking-form">
				<h2><?php __('front_view_booking_form'); ?></h2>
				<div class="property-view-content">
					<div class="vrl-booking-form">
						<?php include PJ_VIEWS_PATH . 'pjListings/elements/viewBookingForm.php'; ?>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
<?php
} else {
	?><div class="property-view-na"><?php __('front_view_na'); ?></div><?php
}
?>
</div>