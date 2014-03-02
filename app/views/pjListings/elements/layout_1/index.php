<div class="property-container">
<?php
include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_1/menu.php';

if (isset($tpl['arr']) && count($tpl['arr']) > 0)
{
	$floor = $tpl['option_arr']['o_floor'];
	$currency = $tpl['option_arr']['o_currency'];
		
	if ($tpl['option_arr']['o_layout'] == 'layout_1_grid')
	{
		include dirname(__FILE__) . '/_grid.php';
	} else {
		include dirname(__FILE__) . '/_list.php';
	}
	
	if (isset($tpl['paginator']))
	{
		?>
		<div class="property-state property-heading">
			<div class="vrl-float-left">
			<?php
			if ($tpl['paginator']['count'] != 1)
			{
				printf("%u %s", $tpl['paginator']['count'], __('front_index_properties_found', true));
			} else {
				printf("%u %s", $tpl['paginator']['count'], __('front_index_property_found', true));
			}
			?>
			</div>
			<ul class="property-paginator">
			<?php
			$page = isset($_GET['pjPage']) && (int) $_GET['pjPage'] > 0 ? intval($_GET['pjPage']) : 1;
			$pages = pjPaginator::numbers($tpl['paginator']['count'], $tpl['paginator']['row_count'], $page, 5, 3);
			parse_str($_SERVER['QUERY_STRING'], $output);
			echo pjPaginator::render($pages, $page, $_SERVER['SCRIPT_NAME'], $output, 'pjPage');
			?>
			</ul>
			<div class="vrl-clear-both"></div>
		</div>
		<?php
	}
} else {
	?><div class="property-index-empty"><?php __('front_index_empty'); ?></div><?php
}
?>
</div>