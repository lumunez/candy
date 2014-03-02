<div id="tabs-7">
	<?php
	if (isset($_GET['err']))
	{
		$status = __('status', true);
		switch ($_GET['err'])
		{
			case 7:
				pjUtil::printNotice(NULL, $status[7]);
				break;
		}
	}
	pjUtil::printNotice(__('infoListingPricesTitle', true), __('infoListingPricesBody', true));
	
	$err = array();
	if (isset($tpl['price_arr']) && !empty($tpl['price_arr']))
	{
		foreach ($tpl['price_arr'] as $range)
		{
			$from = strtotime($range['date_from']);
			$to = strtotime($range['date_to']);
			
			foreach ($tpl['price_arr'] as $tmp)
			{
				if ($range['id'] == $tmp['id'])
				{
					continue;
				}
				if (strtotime($tmp['date_from']) <= $to && strtotime($tmp['date_to']) >= $from)
				{
					$err[] = array($range, $tmp);
				}
			}
		}
	}
	if (!empty($err))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice($titles['AL20'], $bodies['AL20']);
	}
	?>
	<table class="pj-table" id="tblPrices" cellpadding="0" cellspacing="0" style="width: 100%">
		<thead>
			<tr>
				<th>#</th>
				<th><?php __('lblListingPriceFrom'); ?></th>
				<th><?php __('lblListingPriceTo'); ?></th>
				<th><?php __('lblListingPriceTitle'); ?></th>
				<th style="width: 5%"></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if (isset($tpl['price_arr']) && count($tpl['price_arr']) > 0)
		{
			$i = 1;
			foreach ($tpl['price_arr'] as $v)
			{
				?>
				<tr>
					<td><?php echo $i++; ?></td>
					<td>
						<span class="pj-form-field-custom pj-form-field-custom-after">
							<input type="text" name="date_from[]" value="<?php echo pjUtil::formatDate($v['date_from'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</td>
					<td>
						<span class="pj-form-field-custom pj-form-field-custom-after">
							<input type="text" name="date_to[]" value="<?php echo pjUtil::formatDate($v['date_to'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</td>
					<td>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" name="price[]" class="pj-form-field w70 align_right" value="<?php echo $v['price']; ?>" />
						</span>
					</td>
					<td><a class="pj-table-icon-delete btnDeletePrice" title="<?php __('lblDelete'); ?>" href="#" data-id="<?php echo $v['id']; ?>"></a></td>
				</tr>
				<?php
				 /*<?php echo $v['id']; ?>&amp;listing_id=<?php echo $tpl['arr']['id']; ?>*/
			}
		} else {
			?>
			<tr class="notFound">
				<td colspan="5"><?php __('lblListingPriceNotFound'); ?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<br />
	<p>
		<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button" id="btnAddPrice" />
	</p>
	
	<div id="dialogDeletePrice" title="Delete confirmation" style="display: none"><?php __('lblSure'); ?></div>
	
	<table id="tblPricesClone" style="display: none">
		<tbody>
			<tr>
				<td>{INDEX}</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date_from[]" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date_to[]" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="price[]" class="pj-form-field w70 align_right" />
					</span>
				</td>
				<td><a class="pj-table-icon-delete btnRemovePrice" title="<?php __('lblDelete'); ?>" href="#"></a></td>
			</tr>
		</tbody>
	</table>
	
</div>