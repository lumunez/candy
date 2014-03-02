<?php
$active = ' ui-tabs-active ui-state-active';
?>
<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top<?php echo $_GET['action'] == 'pjActionIndex' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocales&amp;action=pjActionIndex&amp;tab=1"><?php __('locales'); ?></a></li>
		<li class="ui-state-default ui-corner-top<?php echo $_GET['action'] == 'pjActionBackend' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocales&amp;action=pjActionBackend&amp;tab=1"><?php __('backend'); ?></a></li>
		<li class="ui-state-default ui-corner-top<?php echo $_GET['action'] == 'pjActionFrontend' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocales&amp;action=pjActionFrontend&amp;tab=1"><?php __('frontend'); ?></a></li>
		<li class="ui-state-default ui-corner-top<?php echo $_GET['action'] == 'pjActionArrays' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocales&amp;action=pjActionArrays&amp;tab=1"><?php __('localeArrays'); ?></a></li>
	</ul>
</div>