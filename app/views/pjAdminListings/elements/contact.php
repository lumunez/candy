<div id="tabs-5">
	<?php
	pjUtil::printNotice(__('infoListingContactTitle', true), __('infoListingContactBody', true));
	?>
	<p>
		<label class="title"><?php __('lblListingContactShow'); ?></label>
		<input type="radio" name="contact_show" id="contact_show_1" value="1"<?php echo $tpl['arr']['contact_show'] == 1 ? ' checked="checked"' : NULL; ?> /> <label for="contact_show_1"><?php __('lblYes'); ?></label>
		<input type="radio" name="contact_show" id="contact_show_0" value="0"<?php echo $tpl['arr']['contact_show'] == 0 ? ' checked="checked"' : NULL; ?> /> <label for="contact_show_0"><?php __('lblNo'); ?></label>
	</p>
	<p>
		<label class="title"><?php __('lblListingTitle'); ?></label>
		<select name="personal_title" id="personal_title" class="pj-form-field">
			<option value="">-- <?php __('lblChoose'); ?> --</option>
		<?php
		foreach (__('personal_titles', true) as $k => $v)
		{
			if (isset($tpl['arr']['personal_title']) && $tpl['arr']['personal_title'] == $k)
			{
				?><option value="<?php echo $k; ?>" selected="selected"><?php echo stripslashes($v); ?></option><?php
			} else {
				?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
			}
		}
		?>
		</select>
	</p>
	<p>
		<label class="title"><?php __('lblListingFirstName'); ?></label>
		<input type="text" name="personal_fname" id="personal_fname" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['personal_fname'])); ?>" class="pj-form-field w200" />
	</p>
	<p>
		<label class="title"><?php __('lblListingLastName'); ?></label>
		<input type="text" name="personal_lname" id="personal_lname" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['personal_lname'])); ?>" class="pj-form-field w200" />
	</p>
	<p>
		<label class="title"><?php __('lblListingPhone'); ?></label>
		<span class="pj-form-field-custom pj-form-field-custom-before">
			<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
			<input type="text" name="contact_phone" id="contact_phone" class="pj-form-field w150" placeholder="(123) 456-7890" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['contact_phone'])); ?>" />
		</span>
	</p>
	<p>
		<label class="title"><?php __('lblListingMobile'); ?></label>
		<span class="pj-form-field-custom pj-form-field-custom-before">
			<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
			<input type="text" name="contact_mobile" id="contact_mobile" class="pj-form-field w150" placeholder="(123) 456-7890" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['contact_mobile'])); ?>" />
		</span>
	</p>
	<p>
		<label class="title"><?php __('lblListingEmail'); ?></label>
		<span class="pj-form-field-custom pj-form-field-custom-before">
			<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
			<input type="text" name="contact_email" id="contact_email" class="pj-form-field email w150" placeholder="info@domain.com" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['contact_email'])); ?>" />
		</span>
	</p>
	<p>
		<label class="title"><?php __('lblListingFax'); ?></label>
		<span class="pj-form-field-custom pj-form-field-custom-before">
			<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
			<input type="text" name="contact_fax" id="contact_fax" class="pj-form-field w150" placeholder="(123) 456-7890" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['contact_fax'])); ?>" />
		</span>
	</p>
	<p>
		<label class="title"><?php __('lblListingUrl'); ?></label>
		<span class="pj-form-field-custom pj-form-field-custom-before">
			<span class="pj-form-field-before"><abbr class="pj-form-field-icon-url"></abbr></span>
			<input type="text" name="contact_url" id="contact_url" class="pj-form-field w300 url" placeholder="http://www.domain.com" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['contact_url'])); ?>" />
		</span>
	</p>
	<p>
		<label class="title">&nbsp;</label>
		<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
	</p>
</div>