<div class="property-container">
<?php
include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_2/menu.php';

if (isset($tpl['arr']) && count($tpl['arr']) > 0)
{
	$floor = $tpl['option_arr']['o_floor'];
	$currency = $tpl['option_arr']['o_currency'];
	
	include dirname(__FILE__) . '/filter.php';
	
	?>
	<div class="property-listings">
	<?php
	if ($tpl['option_arr']['o_layout'] == 'layout_2_grid')
	{
		include dirname(__FILE__) . '/_grid.php';
	} else {
		include dirname(__FILE__) . '/_list.php';
	}

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
	?>
	</div>
	<script type="text/javascript">
	var VRL = VRL || {};
	VRL.Opts = {
		dateFormat: "<?php echo $tpl['option_arr']['o_date_format']; ?>",
		startDay: <?php echo (int) $tpl['option_arr']['o_week_start']; ?>
	};
	</script>
	<?php
} else {
	?>
	<div class="property-state vrl-t10">
		<div class="property-state-header"><h2><?php __('front_index_empty'); ?></h2></div>
	</div>
	<?php
}
?>
</div>