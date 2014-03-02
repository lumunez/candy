<?php
if ($controller->isAdmin() && pjObject::getPlugin('pjOneAdmin') !== NULL)
{
	$controller->requestAction(array('controller' => 'pjOneAdmin', 'action' => 'pjActionMenu'));
}
?>

<div class="leftmenu-top"></div>
<div class="leftmenu-middle">
	<ul class="menu">
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionIndex' ? 'menu-focus' : NULL; ?>"><span class="menu-dashboard">&nbsp;</span><?php __('menuDashboard'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminListings&amp;action=pjActionIndex" class="<?php echo in_array($_GET['controller'], array('pjAdminListings', 'pjGallery')) && in_array($_GET['action'], array('pjActionIndex', 'pjActionUpdate', 'pjActionExtend', 'pjActionPayment', 'pjActionResizeGallery')) ? 'menu-focus' : NULL; ?>"><span class="menu-browse">&nbsp;</span><?php __('menuBrowseProperty'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminListings&amp;action=pjActionCreate" class="<?php echo $_GET['controller'] == 'pjAdminListings' && $_GET['action'] == 'pjActionCreate' ? 'menu-focus' : NULL; ?>"><span class="menu-add">&nbsp;</span><?php __('menuAddProperty'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminReservations' ? 'menu-focus' : NULL; ?>"><span class="menu-reservations">&nbsp;</span><?php __('menuReservations'); ?></a></li>
		<?php
		if ($controller->isAdmin())
		{
			?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionIndex" class="<?php echo ($_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionIndex', 'pjActionSubmissions', 'pjActionNotifications'))) || in_array($_GET['controller'], array('pjAdminTypes', 'pjAdminExtras', 'pjAdminCountries', 'pjAdminLocales', 'pjBackup', 'pjSms')) ? 'menu-focus' : NULL; ?>"><span class="menu-options">&nbsp;</span><?php __('menuOptions'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminUsers' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span><?php __('menuUsers'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" target="_blank"><span class="menu-preview">&nbsp;</span><?php __('menuPreview'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionInstall" class="<?php echo $_GET['controller'] == 'pjAdminOptions' && $_GET['action'] == 'pjActionInstall' ? 'menu-focus' : NULL; ?>"><span class="menu-install">&nbsp;</span><?php __('menuInstall'); ?></a></li>
			<?php
		}
		if ($controller->isOwner() || $controller->isEditor())
		{
			?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionProfile" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionProfile' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span><?php __('menuProfile'); ?></a></li><?php
		}
		?>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionLogout"><span class="menu-logout">&nbsp;</span><?php __('menuLogout'); ?></a></li>
	</ul>
</div>
<div class="leftmenu-bottom"></div>