<div id="tabs-6">
	<?php
	pjUtil::printNotice(__('infoListingAddressTitle', true), __('infoListingAddressBody', true));
	?>
	<p><label class="title"><?php __('lblListingShowMap'); ?></label>
		<input type="radio" name="address_map" id="address_map_1" value="1"<?php echo $tpl['arr']['address_map'] == 1 ? ' checked="checked"' : NULL; ?> /> <label for="address_map_1"><?php echo __('lblYes'); ?></label>
		<input type="radio" name="address_map" id="address_map_0" value="0"<?php echo $tpl['arr']['address_map'] == 0 ? ' checked="checked"' : NULL; ?> /> <label for="address_map_0"><?php echo __('lblNo'); ?></label>
	</p>
	<p><label class="title"><?php __('lblListingZip'); ?></label><input type="text" name="address_postcode" id="address_postcode" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['address_postcode'])); ?>" class="pj-form-field" /></p>
	<p><label class="title"><?php __('lblListingAddress'); ?></label><textarea name="address_content" id="address_content" class="pj-form-field w500 h80"><?php echo stripslashes($tpl['arr']['address_content']); ?></textarea></p>
	<p style="overflow: visible">
		<label class="title"><?php __('lblListingCountry'); ?></label>
		<select name="country_id" id="country_id" class="pj-form-field w300">
			<option value="">-- <?php __('lblChoose'); ?> --</option>
			<?php
			foreach ($tpl['country_arr'] as $v)
			{
				if ($tpl['arr']['country_id'] == $v['id'])
				{
					?><option value="<?php echo $v['id']; ?>" selected="selected"><?php echo stripslashes($v['name']); ?></option><?php
				} else {
					?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
				}
			}
			?>
		</select>
	</p>
	<p><label class="title"><?php __('lblListingState'); ?></label><input type="text" name="address_state" id="address_state" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['address_state'])); ?>" class="pj-form-field w200" /></p>
	<p><label class="title"><?php __('lblListingCity'); ?></label><input type="text" name="address_city" id="address_city" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['address_city'])); ?>" class="pj-form-field w200" /></p>
	<p>
		<label class="title">&nbsp;</label>
		<span><?php __('lblListingGMapNote'); ?></span>
	</p>
	<p>
		<label class="title">&nbsp;</label>
		<span class="inline_block">
			<input type="button" value="<?php __('btnGoogleMapsApi'); ?>" class="pj-button btnGoogleMapsApi" />
			<span style="color: red; display: none"></span>
		</span>
	</p>
	<p>
		<label class="title"><?php __('lblListingLat'); ?></label>
		<input type="text" name="lat" id="lat" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['lat'])); ?>" class="pj-form-field w200 number" />
	</p>
	<p>
		<label class="title"><?php __('lblListingLng'); ?></label>
		<input type="text" name="lng" id="lng" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['lng'])); ?>" class="pj-form-field w200 number" />
	</p>
	<p>
		<label class="title">&nbsp;</label>
		<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
	</p>
</div>