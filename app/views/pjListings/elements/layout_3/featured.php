<div class="property-container">
	<?php
	if (isset($tpl['arr']) && count($tpl['arr']) > 0)
	{
		$floor = $tpl['option_arr']['o_floor'];
		include dirname(__FILE__) . '/_list.php';
	} else {
		?><div class="property-index-empty"><?php __('front_index_empty'); ?></div><?php
	}
	?>
</div>