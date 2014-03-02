<?php
if (isset($tpl['price_arr']) && count($tpl['price_arr']) > 0)
{
	?>
	<div class="property-state vrl-float-left vrl-w54p vrl-t10">
		<div class="property-view-content">
			<h2><?php __('front_view_price_quotes'); ?></h2>
			<table style="width: 100%" cellspacing="0" cellpadding="0" class="property-table">
				<tr>
					<th class="align_left"><?php __('front_view_price_from'); ?></th>
					<th class="align_left"><?php __('front_view_price_to'); ?></th>
					<th class="align_right"><?php __('front_view_price_value'); ?></th>
				</tr>
			<?php
			foreach ($tpl['price_arr'] as $v)
			{
				?>
				<tr>
					<td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['date_from'])); ?></td>
					<td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['date_to'])); ?></td>
					<td class="align_right"><strong><?php echo pjUtil::formatCurrencySign(number_format($v['price'], 2), $tpl['option_arr']['o_currency']); ?></strong></td>
				</tr>
				<?php
			}
			?>
			</table>
		</div>
	</div>
	<?php
}
?>