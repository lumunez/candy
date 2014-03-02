<?php
$params = $controller->getParams();
if (!isset($params['menu']) || $params['menu'] !== false)
{
	?>
	<div class="property-menu-wrap">
		<ul class="property-menu">
		<?php
		if (@$_GET['controller'] == 'pjListings' && @$_GET['action'] == 'pjActionView')
		{
			$back = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['SCRIPT_NAME'] .'?controller=pjListings&amp;action=pjActionIndex'. (isset($_GET['iframe']) ? '&amp;iframe' : NULL);
			?>
			<li><a class="property-menu-back" href="<?php echo htmlspecialchars($back); ?>"><abbr></abbr><?php __('front_menu_back_no'); ?></a></li>
			<?php
		}
		?>
		<li><a class="property-menu-home" href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?controller=pjListings&amp;action=pjActionIndex<?php echo isset($_GET['iframe']) ? '&amp;iframe' : NULL; ?>"><abbr></abbr><?php __('front_menu_home'); ?></a></li>
		<li><a class="property-menu-search" href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?controller=pjListings&amp;action=pjActionSearch<?php echo isset($_GET['iframe']) ? '&amp;iframe' : NULL; ?>"><abbr></abbr><?php __('front_menu_search'); ?></a></li>
		<?php
		if ($tpl['option_arr']['o_allow_add_property'] == 'Yes') {
			?>
			<li><a class="property-menu-add" href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?controller=pjListings&amp;action=pjActionAdd<?php echo isset($_GET['iframe']) ? '&amp;iframe' : NULL; ?>"><abbr></abbr><?php __('front_menu_add'); ?></a></li>
			<?php
		}
		if (isset($tpl['locale_arr']))
		{
			$cnt = count($tpl['locale_arr']);
			if ($cnt > 1)
			{
				foreach ($tpl['locale_arr'] as $k => $locale)
				{
					?>
					<li class="property-menu-right">
						<a class="property-menu-locale" href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?controller=pjListings&amp;action=pjActionSetLocale&amp;locale=<?php echo $locale['id']; ?><?php echo isset($_GET['iframe']) ? '&amp;iframe' : NULL; ?>">
							<img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/<?php echo $locale['file']; ?>" alt="" />
						</a>
					</li>
					<?php
				}
			}
		}
		?>
		</ul>
	</div>
	<div style="clear: both"></div>
	<?php
}
?>