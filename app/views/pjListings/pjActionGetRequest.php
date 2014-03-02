<?php include PJ_VIEWS_PATH . 'pjListings/elements/viewCalendar.php'; ?>
--LIMITER--
<?php
switch ($tpl['option_arr']['o_layout'])
{
	case 'layout_2_list':
	case 'layout_2_grid':
		?>
<div class="property-state-header"><h2><?php __('front_view_booking_form'); ?></h2></div>
<div class="property-state-content vrl-booking-form">
<?php include PJ_VIEWS_PATH . 'pjListings/elements/viewBookingForm.php'; ?>
</div>
		<?php
		break;
	case 'layout_1_list':
	case 'layout_1_grid':
	default:
		?>
<div class="property-view-content">
	<h2><?php __('front_view_booking_form'); ?></h2>
	<div class="vrl-booking-form">
		<?php include PJ_VIEWS_PATH . 'pjListings/elements/viewBookingForm.php'; ?>
	</div>
</div>
		<?php
		break;
}
?>