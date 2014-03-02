<div class="property-container">

	<?php
	include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_3/menu.php';
	?>

	<div class="property-state vrl-float-left vrl-w49p">
		<div class="property-view-content">
		<div class="vrl-bold vrl-fs28 vrl-color-black"><?php __('front_login_title'); ?></div>
		<div class="vrl-bold vrl-fs16 vrl-color-black vrl-b20"><?php __('front_login_title_slogan'); ?></div>
		<form action="<?php echo PJ_INSTALL_URL;?>index.php?controller=pjAdmin&amp;action=pjActionLogin" method="post" target="_blank" class="property-form" name="frmPLLogin">
			<input type="hidden" name="login_user" value="1" />
			<?php
			if (isset($_GET['iframe']))
			{
				?><input type="hidden" name="iframe" value="" /><?php
			}
			?>
			<p class="vrl-lh16"><?php __('front_login_note'); ?></p>
			<p>
				<label class="property-title"><?php __('front_login_email'); ?>:</label>
				<input type="text" name="login_email" id="login_email" value="<?php echo isset($_GET['login_email']) ? htmlspecialchars(stripslashes($_GET['login_username'])) : NULL; ?>" class="property-text vrl-w200" />
			</p>
			<p>
				<label class="property-title"><?php __('front_login_password'); ?>:</label>
				<input type="password" name="login_password" id="login_password" value="<?php echo isset($_GET['login_password']) ? htmlspecialchars(stripslashes($_GET['login_password'])) : NULL; ?>" class="property-text vrl-w200" autocomplete="off" />
			</p>
			<p>
				<label class="property-title">&nbsp;</label>
				<button type="submit" onclick="return VRL.Utils.submitLoginForm('frmPLLogin');" class="property-button"><?php __('front_login_submit'); ?><abbr></abbr></button>
			</p>
		</form>
	</div>
	</div>
	
	
	<div class="property-state vrl-float-right vrl-w49p">
		<div class="property-view-content">
			<div class="vrl-bold vrl-fs28 vrl-color-black"><?php __('front_register_title'); ?></div>
			<div class="vrl-bold vrl-fs16 vrl-color-black vrl-b20"><?php __('front_register_title_slogan'); ?></div>
		
			<?php
			if (isset($_GET['err']))
			{
				$status = __('status', true);
				switch ($_GET['err'])
				{
					case 9999:
						?><p class="property-status-success"><?php echo $status[9999]; ?></p><?php
						break;
					case 9998:
						?><p class="property-status-success"><?php echo $status[9998]; ?></p><?php
						break;
					default:
						?><p class="property-status-error"><?php echo @$status[$_GET['err']]; ?></p><?php
				}
			}
			?>
		
			<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="get" class="property-form" name="frmPLRegister">
				<input type="hidden" name="controller" value="pjListings" />
				<input type="hidden" name="action" value="pjActionAdd" />
				<input type="hidden" name="listing_register" value="1" />
				
				<?php
				if (isset($_GET['iframe']))
				{
					?><input type="hidden" name="iframe" value="" /><?php
				}
				?>
				<p>
					<label class="property-title"><?php __('front_register_email'); ?>:</label>
					<input type="text" name="register_email" id="register_email" value="<?php echo isset($_GET['register_email']) ? htmlspecialchars(stripslashes($_GET['register_email'])) : NULL; ?>" class="property-text vrl-w200" />
				</p>
				<p>
					<label class="property-title"><?php __('front_register_password'); ?>:</label>
					<input type="password" name="register_password" id="register_password" value="<?php echo isset($_GET['register_password']) ? htmlspecialchars(stripslashes($_GET['register_password'])) : NULL; ?>" class="property-text vrl-w200" autocomplete="off" />
				</p>
				<p>
					<label class="property-title"><?php __('front_register_password_repeat'); ?>:</label>
					<input type="password" name="register_password_repeat" id="register_password_repeat" value="<?php echo isset($_GET['register_password_repeat']) ? htmlspecialchars(stripslashes($_GET['register_password_repeat'])) : NULL; ?>" class="property-text vrl-w200" autocomplete="off" />
				</p>
				<p>
					<label class="property-title"><?php __('front_register_name'); ?>:</label>
					<input type="text" name="name" id="name" value="<?php echo isset($_GET['name']) ? htmlspecialchars(stripslashes($_GET['name'])) : NULL; ?>" class="property-text vrl-w200" />
				</p>
				<p>
					<label class="property-title"><?php __('front_booking_captcha'); ?>:</label>
					<input type="text" name="verification" id="verification" class="property-text vrl-w100" maxlength="6" autocomplete="off" />
					<img class="property-captcha" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 999999); ?>" alt="CAPTCHA" style="vertical-align: middle" />
				</p>
				<p>
					<label class="property-title">&nbsp;</label>
					<button type="submit" onclick="return VRL.Utils.submitRegistrationForm('frmPLRegister');" class="property-button"><?php __('front_register_submit'); ?><abbr></abbr></button>
				</p>
			</form>
		</div>
	</div>
	
</div>