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
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	?>
	<form action="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="pj-form form">
		<input type="hidden" name="options_update" value="1" />
		<input type="hidden" name="next_action" value="pjActionNotifications" />
		
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('tabEmails'); ?></a></li>
				<li><a href="#tabs-2"><?php __('tabSms'); ?></a></li>
			</ul>
			
			<div id="tabs-1">
				<?php pjUtil::printNotice(__('infoNotificationsEmailTitle', true), __('infoNotificationsEmailBody', true)); ?>
				
				<fieldset class="fieldset white">
					<legend><?php __('opt_o_email_new_user'); ?></legend>
					<p>
						<label class="title"><?php __('opt_subject'); ?></label>
						<input type="text" name="value-string-o_email_new_user_subject" class="pj-form-field w500" value="<?php echo htmlspecialchars(stripslashes($tpl['o_arr']['o_email_new_user_subject'])); ?>" />
					</p>
					<p>
						<label class="title"><?php __('opt_body_new_user'); ?></label>
						<textarea name="value-text-o_email_new_user" class="pj-form-field w500 h150"><?php echo stripslashes($tpl['o_arr']['o_email_new_user']); ?></textarea>
					</p>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					</p>
				</fieldset>
				
				<fieldset class="fieldset white">
					<legend><?php __('opt_o_email_new_property'); ?></legend>
					<p>
						<label class="title"><?php __('opt_subject'); ?></label>
						<input type="text" name="value-string-o_email_new_property_subject" class="pj-form-field w500" value="<?php echo htmlspecialchars(stripslashes($tpl['o_arr']['o_email_new_property_subject'])); ?>" />
					</p>
					<p>
						<label class="title"><?php __('opt_body_new_property'); ?></label>
						<textarea name="value-text-o_email_new_property" class="pj-form-field w500 h150"><?php echo stripslashes($tpl['o_arr']['o_email_new_property']); ?></textarea>
					</p>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					</p>
				</fieldset>
				
				<fieldset class="fieldset white">
					<legend><?php __('opt_o_email_new_reservation'); ?></legend>
					<p>
						<label class="title"><?php __('opt_subject'); ?></label>
						<input type="text" name="value-string-o_email_new_reservation_subject" class="pj-form-field w500" value="<?php echo htmlspecialchars(stripslashes($tpl['o_arr']['o_email_new_reservation_subject'])); ?>" />
					</p>
					<p>
						<label class="title"><?php __('opt_body_new_reservation'); ?></label>
						<textarea name="value-text-o_email_new_reservation" class="pj-form-field w500 h150"><?php echo stripslashes($tpl['o_arr']['o_email_new_reservation']); ?></textarea>
					</p>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					</p>
				</fieldset>
				
				<fieldset class="fieldset white">
					<legend><?php __('opt_o_email_reservation_cancelled'); ?></legend>
					<p>
						<label class="title"><?php __('opt_subject'); ?></label>
						<input type="text" name="value-string-o_email_reservation_cancelled_subject" class="pj-form-field w500" value="<?php echo htmlspecialchars(stripslashes($tpl['o_arr']['o_email_reservation_cancelled_subject'])); ?>" />
					</p>
					<p>
						<label class="title"><?php __('opt_body_new_reservation'); ?></label>
						<textarea name="value-text-o_email_reservation_cancelled" class="pj-form-field w500 h150"><?php echo stripslashes($tpl['o_arr']['o_email_reservation_cancelled']); ?></textarea>
					</p>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					</p>
				</fieldset>
				
				<fieldset class="fieldset white">
					<legend><?php __('opt_o_email_password_reminder'); ?></legend>
					<p>
						<label class="title"><?php __('opt_subject'); ?></label>
						<input type="text" name="value-string-o_email_password_reminder_subject" class="pj-form-field w500" value="<?php echo htmlspecialchars(stripslashes($tpl['o_arr']['o_email_password_reminder_subject'])); ?>" />
					</p>
					<p>
						<label class="title"><?php __('opt_body_forgot_password'); ?></label>
						<textarea name="value-text-o_email_password_reminder" class="pj-form-field w500 h150"><?php echo stripslashes($tpl['o_arr']['o_email_password_reminder']); ?></textarea>
					</p>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					</p>
				</fieldset>
			</div>
			<div id="tabs-2">
				<?php pjUtil::printNotice(__('infoNotificationsSmsTitle', true), __('infoNotificationsSmsBody', true)); ?>
				<p>
					<label class="title"><?php __('opt_o_email_new_user'); ?></label>
					<textarea name="value-text-o_sms_new_user" class="pj-form-field w550 h50"><?php echo stripslashes($tpl['o_arr']['o_sms_new_user']); ?></textarea>
				</p>
				<p>
					<label class="title"><?php __('opt_o_email_new_property'); ?></label>
					<textarea name="value-text-o_sms_new_property" class="pj-form-field w550 h50"><?php echo stripslashes($tpl['o_arr']['o_sms_new_property']); ?></textarea>
				</p>
				<p>
					<label class="title"><?php __('opt_o_email_new_reservation'); ?></label>
					<textarea name="value-text-o_sms_new_reservation" class="pj-form-field w550 h50"><?php echo stripslashes($tpl['o_arr']['o_sms_new_reservation']); ?></textarea>
				</p>
				<p>
					<label class="title"><?php __('opt_o_email_reservation_cancelled'); ?></label>
					<textarea name="value-text-o_sms_reservation_cancelled" class="pj-form-field w550 h50"><?php echo stripslashes($tpl['o_arr']['o_sms_reservation_cancelled']); ?></textarea>
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				</p>
			</div>
		</div>
	
	</form>
	<?php
}
?>