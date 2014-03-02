<?php
switch ($tpl['option_arr']['o_layout']) {
	case 'layout_1_list':
	case 'layout_1_grid':
		include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_1/add.php';
		break;
	case 'layout_2_list':
	case 'layout_2_grid':
		include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_2/add.php';
		break;
	case 'layout_3_list':
		include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_3/add.php';
		break;
	default:
		include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_1/add.php';
		break;
}
?>
<script type="text/javascript">
var VRL = VRL || {};
VRL.Msg = {};
<?php
foreach (__('front_sys', true) as $k => $v)
{
	if (strpos($k, 'log_') === 0 || strpos($k, 'reg_') === 0)
	{
		printf("VRL.Msg.%s = '%s';\n", $k, addslashes($v));
	}
}
?>
</script>