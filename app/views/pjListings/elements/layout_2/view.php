<?php
$floor = $tpl['option_arr']['o_floor'];
?>
<div class="property-container">
<?php
include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_2/menu.php';
	
if (isset($tpl['arr']))
{
	# Property heading
	$showPropertyLocation = true;
	include PJ_VIEWS_PATH . 'pjListings/elements/viewPropertyHeading.php';
		
	# Gallery
	include PJ_VIEWS_PATH . 'pjListings/elements/viewGallery.php';
	?>
	<div class="vrl-w32p vrl-float-left">
		<?php
		# Property data
		include PJ_VIEWS_PATH . 'pjListings/elements/viewProperty.php';
		
		# Google Maps
		if ($tpl['arr']['address_map'] == 1 && !empty($tpl['arr']['lat']) && !empty($tpl['arr']['lng']))
		{
			?>
			<div class="property-state">
				<div class="property-state-header"><h2><?php __('front_view_map'); ?></h2></div>
				<div class="property-state-content"><?php include PJ_VIEWS_PATH . 'pjListings/elements/viewGoogleMaps.php'; ?></div>
			</div>
			<?php
		}
		# Contact (owner) detains
		if ($tpl['arr']['contact_show'] == 1)
		{
			?>
			<div class="property-state">
				<div class="property-state-header"><h2><?php __('front_view_contact'); ?></h2></div>
				<div class="property-state-content vrl-lh20">
				<?php
				if (!empty($tpl['arr']['personal_title']) or !empty($tpl['arr']['personal_fname']) or !empty($tpl['arr']['personal_lname']))
				{
					?><p class="vrl-overflow"><span class="vrl-float-left vrl-w35p"><?php __('front_view_name'); ?></span><span class="vrl-float-left vrl-w64p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['personal_title'] . " " .$tpl['arr']['personal_fname'] . " " . $tpl['arr']['personal_lname'])); ?></span></p><?php
				}
				if (!empty($tpl['arr']['contact_phone']))
				{
					?><p class="vrl-overflow"><span class="vrl-float-left vrl-w35p"><?php __('front_view_phone'); ?></span><span class="vrl-float-left vrl-w64p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['contact_phone'])); ?></span></p><?php
				}
				if (!empty($tpl['arr']['contact_mobile']))
				{
					?><p class="vrl-overflow"><span class="vrl-float-left vrl-w35p"><?php __('front_view_mobile'); ?></span><span class="vrl-float-left vrl-w64p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['contact_mobile'])); ?></span></p><?php
				}
				if (!empty($tpl['arr']['contact_fax']))
				{
					?><p class="vrl-overflow"><span class="vrl-float-left vrl-w35p"><?php __('front_view_fax'); ?></span><span class="vrl-float-left vrl-w64p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['contact_fax'])); ?></span></p><?php
				}
				if (!empty($tpl['arr']['contact_email']))
				{
					?><p class="vrl-overflow"><span class="vrl-float-left vrl-w35p"><?php __('front_view_email'); ?></span><span class="vrl-float-left vrl-w64p vrl-bold vrl-color-black"><?php echo !preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i', $tpl['arr']['contact_email']) ? $tpl['arr']['contact_email'] : '<a href="mailto:'.pjUtil::encodeEmail($tpl['arr']['contact_email']).'">'.pjUtil::encodeEmail($tpl['arr']['contact_email']).'</a>'; ?></span></p><?php
				}
				if (!empty($tpl['arr']['contact_url']))
				{
					?><p class="vrl-overflow"><span class="vrl-float-left vrl-w35p"><?php __('front_view_url'); ?></span><span class="vrl-float-left vrl-w64p vrl-bold vrl-color-black"><?php echo preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank" rel="nofollow">$2</a>', $tpl['arr']['contact_url']); ?></span></p><?php
				}
				if (!empty($tpl['arr']['address_postcode']))
				{
					?><p class="vrl-overflow"><span class="vrl-float-left vrl-w35p"><?php __('front_view_postcode'); ?></span><span class="vrl-float-left vrl-w64p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['address_postcode'])); ?></span></p><?php
				}
				if (!empty($tpl['arr']['address_content']))
				{
					?><p class="vrl-overflow"><span class="vrl-float-left vrl-w35p"><?php __('front_view_address'); ?></span><span class="vrl-float-left vrl-w64p vrl-bold vrl-color-black"><?php echo stripslashes(nl2br(pjSanitize::html($tpl['arr']['address_content']))); ?></span></p><?php
				}
				if (!empty($tpl['arr']['country_title']))
				{
					?><p class="vrl-overflow"><span class="vrl-float-left vrl-w35p"><?php __('front_view_country'); ?></span><span class="vrl-float-left vrl-w64p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['country_title'])); ?></span></p><?php
				}
				if (!empty($tpl['arr']['address_state']))
				{
					?><p class="vrl-overflow"><span class="vrl-float-left vrl-w35p"><?php __('front_view_state'); ?></span><span class="vrl-float-left vrl-w64p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['address_state'])); ?></span></p><?php
				}
				if (!empty($tpl['arr']['address_city']))
				{
					?><p class="vrl-overflow"><span class="vrl-float-left vrl-w35p"><?php __('front_view_city'); ?></span><span class="vrl-float-left vrl-w64p vrl-bold vrl-color-black"><?php echo htmlspecialchars(stripslashes($tpl['arr']['address_city'])); ?></span></p><?php
				}
				?>
				</div>
			</div>
			<?php
		}
		?>
		
	</div>
	<div class="vrl-w65p vrl-float-right">
		<?php
		# Extras
		$extrasLayout = 2;
		include PJ_VIEWS_PATH . 'pjListings/elements/viewExtras.php';
		
		# Description
		include PJ_VIEWS_PATH . 'pjListings/elements/viewDescription.php';
		
		# Prices
		include dirname(__FILE__) . '/prices.php';
		?>
		
		<div class="property-state">
			<div class="property-state-header vrl-overflow">
				<h2 class="vrl-float-left"><?php __('front_view_calendar'); ?></h2>
				<ul class="property-paginator vrl-float-right">
					<li><a href="<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjListings&amp;action=pjActionView&amp;id=<?php echo $tpl['listing_id']; ?>" onclick="var cdate = document.getElementById('property-view-calendars-date'); JABB.Ajax.sendRequest('<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjListings&action=pjActionGetAvailability&listing_id=<?php echo $tpl['listing_id']; ?>&get=prev&date='+VRL.Utils.calendarDate('prev', cdate.innerHTML, 2), VRL.Utils.calendarCallback); cdate.innerHTML=VRL.Utils.calendarDate('prev', cdate.innerHTML, 2); return false;" class="prev">&laquo;</a></li>
					<li><a href="<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjListings&amp;action=pjActionView&amp;id=<?php echo $tpl['listing_id']; ?>" onclick="var cdate = document.getElementById('property-view-calendars-date'); JABB.Ajax.sendRequest('<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjListings&action=pjActionGetAvailability&listing_id=<?php echo $tpl['listing_id']; ?>&get=next&date='+VRL.Utils.calendarDate('next', cdate.innerHTML, 2), VRL.Utils.calendarCallback); cdate.innerHTML=VRL.Utils.calendarDate('next', cdate.innerHTML, 2); return false;" class="next">&raquo;</a></li>
				</ul>
				<span class="vrl-clear-both"></span>
				<span id="property-view-calendars-date" style="display: none"><?php echo date("Y-n-j", mktime(0,0,0,date("n"),1,date("Y"))); ?></span>
			</div>
			<div class="property-state-content" id="property-view-availability">
				<div id="property-view-calendars"><?php include PJ_VIEWS_PATH . 'pjListings/elements/viewCalendar.php'; ?></div>
			</div>
		</div>
		
	</div>
	<div class="vrl-clear-both"></div>
	
	<?php if ((int) $tpl['arr']['o_accept_bookings'] === 1) : ?>
	<?php if (!empty($tpl['arr']['listing_terms'])) : ?>
	<div class="vrl-w32p vrl-float-left">
		<div class="property-state">
			<div class="property-state-header"><h2><?php __('front_view_terms'); ?></h2></div>
			<div class="property-state-content property-view-terms-content"><?php echo stripslashes(nl2br(pjSanitize::html($tpl['arr']['listing_terms']))); ?></div>
		</div>
	</div>
	<?php endif; ?>
	
	<div class="vrl-w65p vrl-float-right">
		<div class="property-state" id="property-view-booking-form">
			<div class="property-state-header"><h2><?php __('front_view_booking_form'); ?></h2></div>
			<div class="property-state-content vrl-booking-form">
			<?php include PJ_VIEWS_PATH . 'pjListings/elements/viewBookingForm.php'; ?>
			</div>
		</div>
	</div>
	<div class="vrl-clear-both"></div>
	<?php endif; ?>
	
<?php
} else {
	?><div class="property-view-na"><?php __('front_view_na'); ?></div><?php
}
?>
</div>