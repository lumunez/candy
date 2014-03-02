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
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	include PJ_VIEWS_PATH . 'pjAdminLocales/elements/menu.php';
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	pjUtil::printNotice(__('infoLocalesTitle', true), __('infoLocalesBody', true));
	?>
	<div class="b10">
		<a href="#" class="pj-button btn-add"><?php __('addLocale'); ?></a>
	</div>
	
	<div id="grid"></div>
	<?php
	$languages = array();
	foreach ($tpl['language_arr'] as $item)
	{
		$languages[] = '{value: "'.$item['iso'].'", label: "'.$item['title'].'"}';
	}
	?>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.languages = [];
	<?php
	if (count($languages) > 0)
	{
		printf('pjGrid.languages.push('.join(",", $languages).');');
	}
	?>
	var myLabel = myLabel || {};
	myLabel.title = "<?php __('locale_title'); ?>";
	myLabel.flag = "<?php __('locale_flag'); ?>";
	myLabel.is_default = "<?php __('locale_is_default'); ?>";
	myLabel.order = "<?php __('locale_order'); ?>";
	</script>
	<?php
}
?>