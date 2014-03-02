<?php
if (isset($_GET['date']) && preg_match('/\d{4}-\d{1,2}-\d{1,2}/', $_GET['date']))
{
	list($startYear, $startMonth,) = explode("-", $_GET['date']);
} else {
	list($startYear, $startMonth,) = explode("-", date("Y-n-j"));
}
if (in_array($tpl['option_arr']['o_layout'], array('layout_2_list', 'layout_2_grid')))
{
	$months = 2;
}
if (in_array($tpl['option_arr']['o_layout'], array('layout_3_list')))
{
	$months = 1;
}
$months = isset($months) ? $months : 3;

$month[1] = intval($startMonth);
$month[2] = ($month[1] + 1) > 12 ? $month[1] + 1 - 12 : $month[1] + 1;
$month[3] = ($month[1] + 2) > 12 ? $month[1] + 2 - 12 : $month[1] + 2;

$year[1] = intval($startYear);
$year[2] = ($month[1] + 1) > 12 ? $year[1] + 1 : $year[1];
$year[3] = ($month[1] + 2) > 12 ? $year[1] + 1 : $year[1];

echo $tpl['calendar']->getMonthView($month[1], $year[1], $tpl['reservation_arr'], $tpl['price_raw_arr']);
if ($months > 1)
{
	echo $tpl['calendar']->getMonthView($month[2], $year[2], $tpl['reservation_arr'], $tpl['price_raw_arr']);
}
if ($months == 3)
{
	echo $tpl['calendar']->getMonthView($month[3], $year[3], $tpl['reservation_arr'], $tpl['price_raw_arr']);
}
?>
<div class="clear_left"></div>