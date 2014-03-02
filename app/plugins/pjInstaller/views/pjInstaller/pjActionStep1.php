<?php
include dirname(__FILE__) . '/elements/progress.php';
$STORAGE = @$_SESSION[$controller->defaultInstaller];
$missing = array();
if (!PJ_DISABLE_MYSQL_CHECK && !$tpl['mysql_check'])
{
	$missing[] = 'MySQL v4.1 is required';
}
if (!$tpl['folder_check'])
{
	foreach ($tpl['folder_arr'] as $err)
	{
		$missing[] = sprintf('%1$s \'<span class="bold">%2$s</span>\' is not writable. %3$s \'<span class="bold">%2$s</span>\'', ucfirst($err[0]), $err[1], $err[2]);
	}
}
?>
<div class="i-wrap">
	<?php
	if (count($missing) > 0)
	{
		?>
		<div class="i-status i-status-error">
			<div class="i-status-icon"><abbr></abbr></div>
			<div class="i-status-txt">
				<h2>Installation error!</h2>
				<?php
				foreach ($missing as $item)
				{
					?><p class="t10"><?php echo $item; ?></p><?php
				}
				?>
			</div>
		</div>
		<?php
	}
	?>
	<p>Bellow you can see server software required to install our product. This is server based software and should be supported by your hosting company. If any of the software below is not supported you should contact your hosting company and ask them to upgrade your hosting plan.</p>
	
	<form action="index.php?controller=pjInstaller&amp;action=pjActionStep2&amp;install=1" method="post" id="frmStep1" class="i-form">
		<input type="hidden" name="step1" value="1" />
	
		<table cellpadding="0" cellspacing="0" class="i-table t20 b20">
			<thead>
				<tr>
					<th>Software</th>
					<th>Minimum version required</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="bold">PHP</td>
					<td><span class="bold">5.0.0</span><input type="hidden" name="php_version" value="<?php echo $tpl['php_check'] === true ? 1 : 0; ?>" /></td>
					<td><span class="i-option i-option-<?php echo $tpl['php_check'] === true ? 'ok' : 'err'; ?>"></span></td>
				</tr>
				<?php if (!PJ_DISABLE_MYSQL_CHECK) : ?>
				<tr>
					<td class="bold">MySQL</td>
					<td><span class="bold">4.1</span><input type="hidden" name="mysql_version" value="<?php echo $tpl['mysql_check'] === true ? 1 : 0; ?>" /></td>
					<td><span class="i-option i-option-<?php echo $tpl['mysql_check'] === true ? 'ok' : 'err'; ?>"></span></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
		
		<div class="t20">
			<p class="float_left">Need help? <a href="http://www.phpjabbers.com/contact.php" target="_blank">Contact us</a></p>
			<?php if (count($missing) === 0) : ?>
			<input type="submit" tabindex="1" value="Continue &raquo;" class="pj-button float_right" />
			<?php endif; ?>
			<br class="clear_both" />
		</div>
	</form>
</div>