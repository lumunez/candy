<select name="address_state" id="address_state" class="<?php echo $_GET['stateClass']; ?>">
	<option value=""><?php __('front_search_choose'); ?></option>
	<?php
	if (isset($tpl['state_arr']) && is_array($tpl['state_arr']))
	{
		foreach ($tpl['state_arr'] as $state)
		{
			if (isset($_GET['address_state']) && $_GET['address_state'] == $state)
			{
				?><option value="<?php echo $state; ?>" selected="selected"><?php echo stripslashes($state); ?></option><?php
			} else {
				?><option value="<?php echo $state; ?>"><?php echo stripslashes($state); ?></option><?php
			}
		}
	}
	?>
</select>