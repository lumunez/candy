<div class="property-container">
<?php
include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_3/menu.php';

if (isset($tpl['arr']) && count($tpl['arr']) > 0)
{
	$floor = $tpl['option_arr']['o_floor'];
	include dirname(__FILE__) . '/_list.php';
	
	if (isset($tpl['paginator']))
	{
		?>
		<ul class="property-paginator">
		<?php
		$page = isset($_GET['pjPage']) && (int) $_GET['pjPage'] > 0 ? intval($_GET['pjPage']) : 1;
		$pages = pjPaginator::numbers($tpl['paginator']['count'], $tpl['paginator']['row_count'], $page, 5, 3);
		parse_str($_SERVER['QUERY_STRING'], $output);
		echo pjPaginator::render($pages, $page, $_SERVER['SCRIPT_NAME'], $output, 'pjPage');
		?>
		</ul>
		<?php
	}
} else {
	?><div class="property-index-empty"><?php __('front_index_empty'); ?></div><?php
}
?>
</div>