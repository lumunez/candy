<?php
$params = $controller->getParams();
if (!isset($params['menu']) || $params['menu'] !== false)
{
	?>
	<div class="property-state property-heading">
		<div class="vrl-float-left">
		<?php
		if (@$_GET['controller'] == 'pjListings' && @$_GET['action'] == 'pjActionView')
		{
			$back = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['SCRIPT_NAME'] .'?controller=pjListings&amp;action=pjActionIndex'. (isset($_GET['iframe']) ? '&amp;iframe' : NULL);
			?>
			<a class="property-anchor" href="<?php echo htmlspecialchars($back); ?>"><abbr></abbr><?php __('front_menu_back'); ?></a>
			<?php
		}
		?>
		<a class="property-anchor" href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?controller=pjListings&amp;action=pjActionIndex<?php echo isset($_GET['iframe']) ? '&amp;iframe' : NULL; ?>"><abbr></abbr><?php __('front_menu_home'); ?></a>
		<a class="property-anchor" href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?controller=pjListings&amp;action=pjActionSearch<?php echo isset($_GET['iframe']) ? '&amp;iframe' : NULL; ?>"><abbr></abbr><?php __('front_menu_search'); ?></a>
		<?php
		if ($tpl['option_arr']['o_allow_add_property'] == 'Yes') {
			?>
			<a class="property-anchor" href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?controller=pjListings&amp;action=pjActionAdd<?php echo isset($_GET['iframe']) ? '&amp;iframe' : NULL; ?>"><abbr></abbr><?php __('front_menu_add'); ?></a>
			<?php
		}
		?>
		</div>
		<?php include PJ_VIEWS_PATH . 'pjListings/elements/locale.php'; ?>
		<div class="vrl-clear-both"></div>
	</div>
	<?php
}
?>