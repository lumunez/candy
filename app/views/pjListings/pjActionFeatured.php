<?php
$params = $controller->getParams();
if (isset($params['search']) && $params['search'] === true)
{
	include_once PJ_VIEWS_PATH . 'pjListings/pjActionSearch.php';
	echo '<br />';
}
switch ($tpl['option_arr']['o_layout']) {
	case 'layout_1_list':
	case 'layout_1_grid':
		include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_1/featured.php';
		break;
	case 'layout_2_list':
	case 'layout_2_grid':
		include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_2/featured.php';
		break;
	case 'layout_3_list':
		include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_3/featured.php';
		break;
	default:
		include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_1/featured.php';
		break;
}
?>