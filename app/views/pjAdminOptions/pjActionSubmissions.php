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
	
	pjUtil::printNotice(__('infoSubmissionsTitle', true), __('infoSubmissionsBody', true));
	
	if (isset($tpl['arr']))
	{
		if (is_array($tpl['arr']))
		{
			$count = count($tpl['arr']);
			if ($count > 0)
			{
				?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form" id="frmOptions">
					<input type="hidden" name="options_update" value="1" />
					<input type="hidden" name="next_action" value="pjActionSubmissions" />
					<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
						<thead>
							<tr>
								<th><?php __('lblOption'); ?></th>
								<th colspan="2"><?php __('lblValue'); ?></th>
							</tr>
						</thead>
						<tbody>
	
				<?php
				for ($i = 0; $i < $count; $i++)
				{
					if ($tpl['arr'][$i]['tab_id'] != 2 || (int) $tpl['arr'][$i]['is_visible'] === 0) continue;
					?>
					<tr class="pj-table-row-odd">
						<td><?php __('opt_' . $tpl['arr'][$i]['key']); ?></td>
						<td class="tblError" colspan="2">
							<?php
							switch ($tpl['arr'][$i]['type'])
							{
								case 'string':
									switch ($tpl['arr'][$i]['key'])
									{
										case 'o_paypal_address':
											?>
											<span class="pj-form-field-custom pj-form-field-custom-before">
												<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
												<input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w200" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" />
											</span>
											<?php
											break;
										default:
											?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w200" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
									}
									break;
								case 'text':
									?><textarea name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field" style="width: 400px; height: 80px;"><?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?></textarea><?php
									break;
								case 'int':
									?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-int w60 digits" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
									break;
								case 'float':
									?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-float w60 number" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
									break;
								case 'enum':
									?><select name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field">
									<?php
									$default = explode("::", $tpl['arr'][$i]['value']);
									$enum = explode("|", $default[0]);
									foreach ($enum as $el)
									{
										if ($default[1] == $el)
										{
											?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo stripslashes($el); ?></option><?php
										} else {
											?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo stripslashes($el); ?></option><?php
										}
									}
									?>
									</select>
									<?php
									break;
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>
				<tr class="pj-table-row-odd">
					<td colspan="3"><?php __('opt_period_note'); ?></td>
				</tr>
				<tr class="pj-table-row-even">
					<td class="bold"><?php __('listing_payment_period'); ?></td>
					<td class="bold" colspan="2"><?php __('listing_payment_price'); ?></td>
				</tr>
				<?php
				foreach ($tpl['period_arr'] as $period)
				{
					?>
					<tr>
						<td><input type="text" name="days[<?php echo $period['id']; ?>]" class="pj-form-field align_right w50 digits" value="<?php echo $period['days']; ?>" /> <?php __('lblDays'); ?></td>
						<td>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="price[<?php echo $period['id']; ?>]" class="pj-form-field align_right w70 number" value="<?php echo $period['price']; ?>" />
							</span>
						</td>
						<td class="w30"><a class="pj-table-icon-delete btnDeletePeriod" data-id="<?php echo $period['id']; ?>" href="#"></a></td>
					</tr>
					<?php
				}
				?>
						</tbody>
					</table>
					<p>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
						<input type="button" value="<?php __('btnAddPeriod'); ?>" class="pj-button btnAddPeriod" />
					</p>
				</form>
				
				<table id="tblPeriodClone" style="display: none">
					<tbody>
						<tr>
							<td><input type="text" name="days[{INDEX}]" class="pj-form-field align_right w50 digits" /> <?php __('lblDays'); ?></td>
							<td>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" name="price[{INDEX}]" class="pj-form-field align_right w70 number" />
								</span>
							</td>
							<td class="w30"><a class="pj-table-icon-delete btnRemovePeriod" href="#"></a></td>
						</tr>
					</tbody>
				</table>
				
				<div id="dialogDeletePeriod" style="display: none" title="Delete confirmation">Are you sure you want to delete selected period/price?</div>
				<?php
			}
		}
	}
	?>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.btn_delete = "<?php __('btnDelete'); ?>";
	myLabel.btn_cancel = "<?php __('btnCancel'); ?>";
	</script>
	<?php
}
?>