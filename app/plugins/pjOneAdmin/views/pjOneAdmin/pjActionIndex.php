<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	include dirname(__FILE__) . '/elements/menu.php';
	?>
	<div class="b10">
		<a href="#" class="pj-button btn-add"><?php __('plugin_one_admin_btn_add'); ?></a>
	</div>

	<div id="grid"></div>
	<?php
}
?>