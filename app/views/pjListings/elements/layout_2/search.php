<?php
if (@$showMenu === false) {
	$listingPage = $tpl['option_arr']['o_listing_page'];
} else {
	$listingPage = $_SERVER['SCRIPT_NAME'];
}
?>
<div class="property-container">
	<?php
	include_once PJ_VIEWS_PATH . 'pjListings/elements/layout_2/menu.php';
	$today = date("Y-m-d");
	$fToday = pjUtil::formatDate($today, "Y-m-d", $tpl['option_arr']['o_date_format']);
	?>
	<div class="property-pattern">
		<div class="property-pattern-header vrl-align-left"><?php __('front_search_title'); ?></div>
		<div class="property-pattern-content">
	
		<form action="<?php echo $listingPage; ?>" method="get" class="property-search-form">
			<input type="hidden" name="controller" value="pjListings" />
			<input type="hidden" name="action" value="pjActionSearch" />
			<input type="hidden" name="listing_search" value="1" />
			<?php
			if (isset($_GET['iframe']))
			{
				?><input type="hidden" name="iframe" value="" /><?php
			}
			?>
			
			<div class="vrl-overflow">
				<p class="vrl-float-left vrl-w49p">
					<label class="property-form-label"><?php __('front_search_date_from'); ?>:</label>
					<span class="property-datepicker-wrap vrl-w300">
						<input type="text" name="date_from" id="vrl_d_from" class="property-datepicker-input vrl-w250" readonly="readonly" value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : NULL; ?>" />
						<input type="button" class="property-datepicker-icon vrl-l10" id="vrl_dpicker_from" value="" />
					</span>
				</p>
				<p class="vrl-float-left vrl-w49p">
					<label class="property-form-label"><?php __('front_search_date_to'); ?>:</label>
					<span class="property-datepicker-wrap vrl-w300">
						<input type="text" name="date_to" id="vrl_d_to" class="property-datepicker-input vrl-w250" readonly="readonly" value="<?php echo isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : NULL; ?>" />
						<input type="button" class="property-datepicker-icon vrl-l10" id="vrl_dpicker_to" value="" />
					</span>
				</p>
			</div>
			
			<div class="vrl-float-left vrl-w25p">
				<p>
					<label class="property-form-label"><?php __('front_search_type'); ?>:</label>
					<select name="type_id" id="type_id" class="property-select vrl-w160">
						<option value=""><?php __('front_search_choose'); ?></option>
						<?php
						foreach ($tpl['type_arr'] as $v)
						{
							if (isset($_GET['type_id']) && $_GET['type_id'] == $v['id'])
							{
								?><option value="<?php echo $v['id']; ?>" selected="selected"><?php echo stripslashes($v['type_title']); ?></option><?php
							} else {
								?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['type_title']); ?></option><?php
							}
						}
						?>
					</select>
				</p>
				<p>
					<label class="property-form-label"><?php __('front_search_country'); ?>:</label>
					<select name="country_id" id="country_id" class="property-select vrl-w160" onchange="VRL.Utils.changeCountry(this, '<?php echo PJ_INSTALL_FOLDER; ?>', 'property-select vrl-w160');">
						<option value=""><?php __('front_search_choose'); ?></option>
						<?php
						if (isset($tpl['country_arr']) && is_array($tpl['country_arr']))
						{
							foreach ($tpl['country_arr'] as $v)
							{
								if (isset($_GET['country_id']) && $_GET['country_id'] == $v['id'])
								{
									?><option value="<?php echo $v['id']; ?>" selected="selected"><?php echo stripslashes($v['country_title']); ?></option><?php
								} else {
									?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['country_title']); ?></option><?php
								}
							}
						}
						?>
					</select>
				</p>
				<p>
					<label class="property-form-label"><?php __('front_search_state'); ?>:</label>
					<span id="vrlStateBox"><?php $_GET['stateClass'] = 'property-select vrl-w160'; include PJ_VIEWS_PATH . 'pjListings/pjActionGetStates.php'; ?></span>
				</p>
				<p>
					<label class="property-form-label"><?php __('front_search_refid'); ?>:</label>
					<input type="text" name="refid" id="refid" value="<?php echo isset($_GET['refid']) ? htmlspecialchars(stripslashes($_GET['refid'])) : NULL; ?>" class="property-text vrl-w150" />
				</p>
			</div>
			
			<div class="vrl-float-left vrl-w25p">
				<p>
					<label class="property-form-label"><?php __('front_search_bedrooms'); ?>:</label>
					<select name="bedrooms_from" id="bedrooms_from" class="property-select vrl-w80">
						<option value=""><?php __('front_search_choose'); ?></option>
						<?php
						foreach (range(0,10) as $v)
						{
							if (isset($_GET['bedrooms_from']) && $_GET['bedrooms_from'] != "" && $_GET['bedrooms_from'] == $v)
							{
								?><option value="<?php echo $v; ?>" selected="selected"><?php echo number_format($v); ?></option><?php
							} else {
								?><option value="<?php echo $v; ?>"><?php echo number_format($v); ?></option><?php
							}
						}
						?>
					</select>
					<?php __('front_search_to'); ?>
					<select name="bedrooms_to" id="bedrooms_to" class="property-select vrl-w80">
						<option value=""><?php __('front_search_choose'); ?></option>
						<?php
						foreach (range(0,10) as $v)
						{
							if (isset($_GET['bedrooms_to']) && $_GET['bedrooms_to'] != "" && $_GET['bedrooms_to'] == $v)
							{
								?><option value="<?php echo $v; ?>" selected="selected"><?php echo number_format($v); ?></option><?php
							} else {
								?><option value="<?php echo $v; ?>"><?php echo number_format($v); ?></option><?php
							}
						}
						?>
						<option value="999999"<?php echo isset($_GET['bedrooms_to']) && (int) $_GET['bedrooms_to'] == 999999 ? ' selected="selected"' : NULL; ?>><?php __('front_search_above'); ?> 10</option>
					</select>
				</p>
				
				<p>
					<label class="property-form-label"><?php __('front_search_bathrooms'); ?>:</label>
					<select name="bathrooms_from" id="bathrooms_from" class="property-select vrl-w80">
						<option value=""><?php __('front_search_choose'); ?></option>
						<?php
						foreach (range(0,10) as $v)
						{
							if (isset($_GET['bathrooms_from']) && $_GET['bathrooms_from'] != "" && $_GET['bathrooms_from'] == $v)
							{
								?><option value="<?php echo $v; ?>" selected="selected"><?php echo number_format($v); ?></option><?php
							} else {
								?><option value="<?php echo $v; ?>"><?php echo number_format($v); ?></option><?php
							}
						}
						?>
					</select>
					<?php __('front_search_to'); ?>
					<select name="bathrooms_to" id="bathrooms_to" class="property-select vrl-w80">
						<option value=""><?php __('front_search_choose'); ?></option>
						<?php
						foreach (range(0,10) as $v)
						{
							if (isset($_GET['bathrooms_to']) && $_GET['bathrooms_to'] != "" && $_GET['bathrooms_to'] == $v)
							{
								?><option value="<?php echo $v; ?>" selected="selected"><?php echo number_format($v); ?></option><?php
							} else {
								?><option value="<?php echo $v; ?>"><?php echo number_format($v); ?></option><?php
							}
						}
						?>
						<option value="999999"<?php echo isset($_GET['bathrooms_to']) && (int) $_GET['bathrooms_to'] == 999999 ? ' selected="selected"' : NULL; ?>><?php __('front_search_above'); ?> 10</option>
					</select>
				</p>
				<p>
					<label class="property-form-label"><?php __('front_search_adults'); ?>:</label>
					<select name="adults_from" id="adults_from" class="property-select vrl-w80">
						<option value=""><?php __('front_search_choose'); ?></option>
						<?php
						foreach (range(0,10) as $v)
						{
							if (isset($_GET['adults_from']) && $_GET['adults_from'] != "" && $_GET['adults_from'] == $v)
							{
								?><option value="<?php echo $v; ?>" selected="selected"><?php echo number_format($v); ?></option><?php
							} else {
								?><option value="<?php echo $v; ?>"><?php echo number_format($v); ?></option><?php
							}
						}
						?>
					</select>
					<?php __('front_search_to'); ?>
					<select name="adults_to" id="adults_to" class="property-select vrl-w80">
						<option value=""><?php __('front_search_choose'); ?></option>
						<?php
						foreach (range(0,10) as $v)
						{
							if (isset($_GET['adults_to']) && $_GET['adults_to'] != "" && $_GET['adults_to'] == $v)
							{
								?><option value="<?php echo $v; ?>" selected="selected"><?php echo number_format($v); ?></option><?php
							} else {
								?><option value="<?php echo $v; ?>"><?php echo number_format($v); ?></option><?php
							}
						}
						?>
						<option value="999999"<?php echo isset($_GET['adults_to']) && (int) $_GET['adults_to'] == 999999 ? ' selected="selected"' : NULL; ?>><?php __('front_search_above'); ?> 10</option>
					</select>
				</p>
				<p>
					<label class="property-form-label"><?php __('front_search_children'); ?>:</label>
					<select name="children_from" id="children_from" class="property-select vrl-w80">
						<option value=""><?php __('front_search_choose'); ?></option>
						<?php
						foreach (range(0,10) as $v)
						{
							if (isset($_GET['children_from']) && $_GET['children_from'] != "" && $_GET['children_from'] == $v)
							{
								?><option value="<?php echo $v; ?>" selected="selected"><?php echo number_format($v); ?></option><?php
							} else {
								?><option value="<?php echo $v; ?>"><?php echo number_format($v); ?></option><?php
							}
						}
						?>
					</select>
					<?php __('front_search_to'); ?>
					<select name="children_to" id="children_to" class="property-select vrl-w80">
						<option value=""><?php __('front_search_choose'); ?></option>
						<?php
						foreach (range(0,10) as $v)
						{
							if (isset($_GET['children_to']) && $_GET['children_to'] != "" && $_GET['children_to'] == $v)
							{
								?><option value="<?php echo $v; ?>" selected="selected"><?php echo number_format($v); ?></option><?php
							} else {
								?><option value="<?php echo $v; ?>"><?php echo number_format($v); ?></option><?php
							}
						}
						?>
						<option value="999999"<?php echo isset($_GET['children_to']) && (int) $_GET['children_to'] == 999999 ? ' selected="selected"' : NULL; ?>><?php __('front_search_above'); ?> 10</option>
					</select>
				</p>
			</div>
			
			<div class="vrl-float-right vrl-w45p">
				<p>
					<label class="property-form-label"><?php __('front_view_extra'); ?></label>
				</p>
				<div class="property-search-extras-wrap">
					<div class="property-search-extras-slider">
					<?php
					$perColumn = 8;
					$total = count($tpl['extra_arr']);
					$columns = ceil($total / $perColumn);
					for ($i = 1; $i <= $columns; $i++)
					{
						?>
						<ul class="property-search-extras">
						<?php
						$start = ($i - 1) * $perColumn;
						$end = $i * $perColumn - 1;
						foreach ($tpl['extra_arr'] as $k => $item)
						{
							if ($k < $start || $k > $end)
							{
								continue;
							}
							?><li><label><input type="checkbox" name="extra_id[]" value="<?php echo $item['id']; ?>"<?php echo isset($_GET['extra_id']) && in_array($item['id'], $_GET['extra_id']) ? ' checked="checked"' : NULL; ?> /> <?php echo stripslashes($item['extra_title']); ?></label></li><?php
						}
						?>
						</ul>
						<?php
					}
					?>
					</div>
				</div>
			</div>
			
			<p class="vrl-clear-left">
				<button type="submit" class="property-button"><?php __('front_search_submit'); ?><abbr></abbr></button>
			</p>
		</form>
		
		</div>
	</div>
</div>

<?php
if (isset($_GET['listing_search']))
{
	?><br /><?php
	include PJ_VIEWS_PATH . 'pjListings/pjActionIndex.php';
}
?>