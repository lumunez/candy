<?php
include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
include PJ_VIEWS_PATH . 'pjAdminLocales/elements/menu.php';
if (isset($_GET['err']))
{
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
}
switch ($action)
{
	case 'pjActionBackend':
		pjUtil::printNotice(__('infoLocalesBackendTitle', true), __('infoLocalesBackendBody', true));
		break;
	case 'pjActionFrontend':
		pjUtil::printNotice(__('infoLocalesFrontendTitle', true), __('infoLocalesFrontendBody', true));
		break;
	case 'pjActionArrays':
		pjUtil::printNotice(__('infoLocalesArraysTitle', true), __('infoLocalesArraysBody', true));
		break;
}
?>
<form action="<?php echo PJ_INSTALL_URL; ?>index.php" method="get" class="float_left pj-form frm-filter">
	<input type="hidden" name="controller" value="pjAdminLocales" />
	<input type="hidden" name="action" value="<?php echo $action; ?>" />
	<input type="hidden" name="tab" value="1" />
	<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" value="<?php echo isset($_GET['q']) && !empty($_GET['q']) ? htmlspecialchars($_GET['q']) : NULL; ?>" />
</form>

<div class="multilang b10"></div>
<form action="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminLocales&amp;action=pjActionSaveFields" method="post">
	<input type="hidden" name="next_action" value="<?php echo $action; ?>" />
	<input type="hidden" name="page" value="<?php echo isset($_GET['page']) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1; ?>" />
	<input type="hidden" name="locale" value="<?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : @$tpl['lp_arr'][0]['id']; ?>" />
	<input type="hidden" name="q" value="<?php echo isset($_GET['q']) && !empty($_GET['q']) ? htmlspecialchars($_GET['q']) : NULL; ?>" />
	<table class="pj-table clear_right" cellpadding="0" cellspacing="0" style="width: 100%">
		<thead>
			<tr>
				<th>Field</th>
				<th><?php __('lblValue'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($tpl[$field] as $k => $field_arr)
		{
			foreach ($field_arr['i18n'] as $locale_id => $locale_arr)
			{
				?>
				<tr class="pj-table-row-<?php echo $k % 2 === 0 ? 'odd' : 'even'; ?> pj-multilang-wrap" data-index="<?php echo $locale_id; ?>" style="display: <?php echo (int) $locale_arr['is_default'] === 0 ? 'none' : NULL; ?>">
					<td><?php echo stripslashes($field_arr['label']); ?></td>
					<td><input type="text" name="i18n[<?php echo $locale_id; ?>][<?php echo $locale_arr['foreign_id']; ?>][title]" value="<?php echo htmlspecialchars(stripslashes(@$locale_arr['content'])); ?>" class="pj-form-field w400" data-key="<?php echo $field_arr['key']; ?>" /></td>
				</tr>
				<?php
			}
		}
		?>
		</tbody>
	</table>
	<?php
	if (isset($tpl['paginator']) && (int) $tpl['paginator']['pages'] > 0)
	{
		?>
		<ul class="paginator">
		<?php
		foreach (range(1, $tpl['paginator']['pages']) as $i)
		{
			?><li><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminLocales&amp;action=<?php echo $_GET['action']; ?>&amp;tab=1&amp;q=<?php echo isset($_GET['q']) && !empty($_GET['q']) ? urlencode($_GET['q']) : NULL; ?>&amp;locale=<?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : NULL; ?>&amp;page=<?php echo $i; ?>"<?php echo $i == @$_GET['page'] || (!isset($_GET['page']) && $i == 1) ? ' class="focus"' : NULL; ?>><?php echo $i; ?></a></li><?php
		}
		?>
		</ul>
		<?php
	}
	?>
	<p>&nbsp;</p>
	<p><input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" /></p>
</form>
<script type="text/javascript">
(function ($) {
	$(function() {
		$(".multilang").multilang({
			langs: <?php echo $tpl['locale_str']; ?>,
			flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
			select: function (event, ui) {
				// Callback, e.g. ajax requests or whatever
				$("input[name='locale']").val(ui.index);
			}
		});
		<?php
		if (isset($_GET['locale']) && (int) $_GET['locale'] > 0)
		{
			?>
			$(".pj-form-langbar-item[data-index='<?php echo (int) $_GET['locale']; ?>']").trigger("click");
			<?php
		}
		?>
	});
})(jQuery_1_8_2);
</script>