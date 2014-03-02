<?php
$extra_arr = array('property' => array(), 'community' => array());
foreach ($tpl['extra_arr'] as $extra)
{
	if (in_array($extra['id'], $tpl['arr']['extras']))
	{
		switch ($extra['type'])
		{
			case 'property':
				$extra_arr['property'][] = stripslashes($extra['extra_title']);
				break;
			case 'community':
				$extra_arr['community'][] = stripslashes($extra['extra_title']);
				break;
		}
	}
}
$cnt_prop = count($extra_arr['property']);
$cnt_comm = count($extra_arr['community']);
if ($cnt_prop + $cnt_comm > 0)
{
	$vet = __('extra_type_arr', true);
	if ($extrasLayout == 1)
	{
		?>
		<div class="property-state">
			<div class="property-state-header"><h2><?php __('front_view_extra'); ?></h2></div>
			<div class="property-state-content">
				<p><label class="bold"><?php echo $vet['property']; ?></label>: <?php echo join(", ", $extra_arr['property']); ?></p>
				<p><label class="bold"><?php echo $vet['community']; ?></label>: <?php echo join(", ", $extra_arr['community']); ?></p>
			</div>
		</div>
		<?php
	} else {
		if ($cnt_prop > 0)
		{
			?>
			<div class="property-state vrl-float-left vrl-w49p">
				<div class="property-state-header"><h2><?php echo $vet['property']; ?></h2></div>
				<div class="property-state-content">
				<?php
				foreach ($extra_arr['property'] as $val)
				{
					?><p><?php echo $val;?></p><?php
				}
				?>
				</div>
			</div>
			<?php
		}
		if ($cnt_comm > 0)
		{
			?>
			<div class="property-state vrl-float-right vrl-w49p">
				<div class="property-state-header"><h2><?php echo $vet['community']; ?></h2></div>
				<div class="property-state-content">
				<?php
				foreach ($extra_arr['community'] as $val)
				{
					?><p><?php echo $val; ?></p><?php
				}
				?>
				</div>
			</div>
			<?php
		}
		?>
		<div class="vrl-clear-both"></div>
		<?php
	}
}
?>