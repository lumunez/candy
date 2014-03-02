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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionProfile"><?php __('menuProfile'); ?></a></li>
		</ul>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionProfile" method="post" id="frmUpdateProfile" class="form pj-form">
		<input type="hidden" name="profile_update" value="1" />
		<p>
			<label class="title"><?php __('email'); ?>:</label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
				<input type="text" name="email" id="email" class="pj-form-field required email w200" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['email'])); ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('pass'); ?>:</label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-password"></abbr></span>
				<input type="text" name="password" id="password" class="pj-form-field required w200" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['password'])); ?>" autocomplete="off" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblName'); ?></label>
			<span class="inline_block">
				<input type="text" name="name" id="name" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['name'])); ?>" class="pj-form-field w250 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblPhone'); ?></label>
			<span class="inline_block">
				<input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['phone'])); ?>" class="pj-form-field w250" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblNotifyEmail'); ?></label>
			<span id="boxEmail">
				<select name="notify_email[]" multiple="multiple" size="5">
				<?php
				foreach (__('notify_email', true) as $k => $v)
				{
					if (in_array($k, array(4,6)))
					{
						?><option value="<?php echo $k; ?>"<?php echo in_array($k, $tpl['email_arr']) ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
					}
				}
				?>
				</select>
			</span>
			<a href="#" class="pj-form-langbar-tip listing-tip" title="<?php __('lblUserEmailTip'); ?>"></a>
		</p>
		<p>
			<label class="title"><?php __('lblNotifySms'); ?></label>
			<span id="boxSms">
				<select name="notify_sms[]" multiple="multiple" size="5">
				<?php
				foreach (__('notify_email', true) as $k => $v)
				{
					if (in_array($k, array(4,6)))
					{
						?><option value="<?php echo $k; ?>"<?php echo in_array($k, $tpl['sms_arr']) ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
					}
				}
				?>
				</select>
			</span>
			<a href="#" class="pj-form-langbar-tip listing-tip" title="<?php __('lblUserSmsTip'); ?>"></a>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		</p>
	</form>
	<?php
}
?>