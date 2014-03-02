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
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	?>
	<style type="text/css">
	/*.ui-widget-content{
		border: medium none;
	}
	.ui-tabs .ui-tabs-nav li a {
		padding: 0.5em 0.8em;
	}*/
	.mceEditor > table{
		width: 570px !important;
	}
	.ui-menu{
		height: 230px;
		overflow-y: scroll;
	}
	.ui-tabs .ui-tabs-panel{
		overflow: visible;
	}
	</style>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminListings&amp;action=pjActionUpdate" method="post" id="frmUpdateListing" class="form pj-form" enctype="multipart/form-data">
		<input type="hidden" name="listing_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<input type="hidden" name="tab_id" value="<?php echo isset($_GET['tab_id']) && !empty($_GET['tab_id']) ? $_GET['tab_id'] : 'tabs-1'; ?>" />
		<?php $locale = isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : @$tpl['lp_arr'][0]['id']; ?>
		<input type="hidden" name="locale" value="<?php echo $locale; ?>" />

		<div id="tabs">
		
			<ul>
				<li><a href="#tabs-1"><?php __('lblListingSummary'); ?></a></li>
				<li><a href="#tabs-2"><?php __('lblListingDetails'); ?></a></li>
				<li><a href="#tabs-3"><?php __('lblListingExtras'); ?></a></li>
				<li><a href="#tabs-4"><?php __('lblListingPhotos'); ?></a></li>
				<li><a href="#tabs-5"><?php __('lblListingContact'); ?></a></li>
				<li><a href="#tabs-6"><?php __('lblListingAddress'); ?></a></li>
				<li><a href="#tabs-7"><?php __('lblListingPrices'); ?></a></li>
				<li><a href="#tabs-8"><?php __('lblListingBooking'); ?></a></li>
				<li><a href="#tabs-9"><?php __('lblListingTerms'); ?></a></li>
				<li><a href="#tabs-10"><?php __('lblListingSeo'); ?></a></li>
			</ul>
		
			<div id="tabs-1">
					
				<p><label class="title"><?php __('lblListingCreated'); ?></label><span class="left"><?php echo date("D, jS F Y, H:i:s A", strtotime($tpl['arr']['created'])); ?></span></p>
				<p><label class="title"><?php __('lblListingModified'); ?></label><span class="left"><?php echo !empty($tpl['arr']['modified']) ? date("D, jS F Y, H:i:s A", strtotime($tpl['arr']['modified'])) : __('lblNotYet'); ?></span></p>
				<p><label class="title"><?php __('lblListingViews'); ?></label><span class="left"><?php echo $tpl['arr']['views']; ?></span></p>
				<p><label class="title"><?php __('menuReservations'); ?></label><span class="left"><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminReservations&amp;action=pjActionIndex&amp;listing_id=<?php echo $tpl['arr']['id']; ?>"><?php echo (int) $tpl['arr']['reservations']; ?></a></span></p>
				<p>
					<label class="title"><?php __('lblListingRefid'); ?></label>
					<span class="inline_block">
						<input type="text" name="listing_refid" id="listing_refid" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['listing_refid'])); ?>" class="pj-form-field required" />
					</span>
				</p>
				<?php
				if (!$controller->isOwner())
				{
					?>
					<p><label class="title"><?php __('lblListingStatus'); ?></label>
						<span class="inline_block">
							<select name="status" id="status" class="pj-form-field required">
								<option value="">-- <?php __('lblChoose'); ?> --</option>
								<?php
								foreach (__('publish_status', true) as $k => $v)
								{
									if ($tpl['arr']['status'] == $k)
									{
										?><option value="<?php echo $k; ?>" selected="selected"><?php echo stripslashes($v); ?></option><?php
									} else {
										?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
									}
								}
								?>
							</select>
							<a href="#" class="pj-form-langbar-tip listing-tip" title="<?php __('lblListingStatusTip'); ?>"></a>
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblListingExpire'); ?></label>
						<span class="pj-form-field-custom pj-form-field-custom-after">
							<input type="text" name="expire" id="expire" class="pj-form-field pointer w80 required datepick" value="<?php echo pjUtil::formatDate($tpl['arr']['expire'], "Y-m-d", $tpl['option_arr']['o_date_format']); ?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
						<a href="#" class="pj-form-langbar-tip listing-tip" title="<?php __('lblListingExpireTip'); ?>"></a>
					</p>
					<?php
				} else {
					?>
					<p>
						<label class="title"><?php __('lblListingExpire'); ?></label>
						<span class="left float_left"><?php echo date("D, jS F Y", strtotime($tpl['arr']['expire'])); ?></span>
						<a class="pj-button float_left l10" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminListings&amp;action=pjActionPayment&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('lblListingExtend'); ?></a>
					</p>
					<?php
				}
				if (!$controller->isOwner())
				{
					?>
					<p>
						<label class="title"><?php __('lblListingFeatured'); ?></label>
						<span class="left">
						<?php
						foreach (__('_yesno', true) as $k => $v)
						{
							?>
							<label class="r5"><input type="radio" name="is_featured" value="<?php echo $k; ?>"<?php echo $tpl['arr']['is_featured'] == $k ? ' checked="checked"' : NULL; ?> /> <?php echo $v; ?></label>
							<?php
						}
						?>
						</span>
					</p>
					<?php
				}
				if ($controller->isAdmin()/* && $tpl['option_arr']['o_allow_add_property'] == 'Yes'*/)
				{
					?>
					<p style="overflow: visible">
						<label class="title"><?php __('lblListingOwner'); ?></label>
						<select name="owner_id" id="owner_id" class="pj-form-field required w200">
							<option value="">-- <?php __('lblChoose'); ?> --</option>
							<?php
							foreach ($tpl['user_arr'] as $val) {
								?>
								<option value="<?php echo $val['id'];?>" <?php echo $tpl['arr']['owner_id'] == $val['id'] ? 'selected="selected"' : '';?> ><?php echo $val['name'];?></option>
								<?php
							}
							?>
						</select>
					<?php
				}
				?>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				</p>
			</div>
		
			<div id="tabs-2">
				<div class="multilang b10"></div>
				<div class="clear_both">
					<p>
						<label class="title"><?php __('lblListingType'); ?></label>
						<span class="inline_block" id="boxType">
							<select name="type_id" id="type_id" class="pj-form-field required">
								<option value="">-- <?php __('lblChoose'); ?> --</option>
								<?php
								foreach ($tpl['type_arr'] as $v)
								{
									if ($tpl['arr']['type_id'] == $v['id'])
									{
										?><option value="<?php echo $v['id']; ?>" selected="selected"><?php echo stripslashes($v['name']); ?></option><?php
									} else {
										?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
									}
								}
								?>
							</select>
						</span>
					</p>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title"><?php __('lblListingTitle'); ?></label>
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][title]" class="pj-form-field w500<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['title'])); ?>" />
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						</span>
					</p>
					<?php
				}
				?>
				<p>
					<label class="title"><?php __('lblListingBedrooms'); ?></label>
					<input type="text" name="listing_bedrooms" id="listing_bedrooms" value="<?php echo intval($tpl['arr']['listing_bedrooms']); ?>" class="pj-form-field w80 digits" />
				</p>
				<p><label class="title"><?php __('lblListingBathrooms'); ?></label><input type="text" name="listing_bathrooms" id="listing_bathrooms" value="<?php echo round($tpl['arr']['listing_bathrooms'], 1); ?>" class="pj-form-field w80 digits" /></p>
				<p><label class="title"><?php __('lblListingAdults'); ?></label><input type="text" name="listing_adults" id="listing_adults" value="<?php echo intval($tpl['arr']['listing_adults']); ?>" class="pj-form-field w80 digits" /></p>
				<p><label class="title"><?php __('lblListingChildren'); ?></label><input type="text" name="listing_children" id="listing_children" value="<?php echo intval($tpl['arr']['listing_children']); ?>" class="pj-form-field w80 digits" /></p>
				<p><label class="title"><?php __('lblListingFloorArea'); ?></label><input type="text" name="listing_floor_area" id="listing_floor_area" value="<?php echo $tpl['arr']['listing_floor_area']; ?>" class="pj-form-field w80 align_right number" /> <?php echo $tpl['option_arr']['o_floor']; ?></p>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title"><?php __('lblListingDescription'); ?></label>
						<span class="inline_block">
							<textarea name="i18n[<?php echo $v['id']; ?>][description]" class="mceEditor<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" style="width: 570px; height: 400px"><?php echo stripslashes(@$tpl['arr']['i18n'][$v['id']]['description']); ?></textarea>
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						</span>
					</p>
					<?php
				}
				?>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					</p>
				</div>
			</div>
			
			<div id="tabs-3">
				<?php
				$eta = __('extra_type_arr', true);
				?>
				<div class="extra_header b10"><?php echo $eta['property']; ?></div>
				<?php
				$i = 1;
				foreach ($tpl['extra_arr'] as $v)
				{
					if ($v['type'] == 'property')
					{
						$is_open = true;
						?>
						<div class="float_left w200 b5 r25 pj-checkbox gradient<?php echo in_array($v['id'], $tpl['listing_extra_arr']) ? ' pj-checkbox-checked' : NULL; ?>">
							<input type="checkbox"  style="vertical-align: middle" name="extra[]" id="extra_<?php echo $v['id']; ?>" value="<?php echo $v['id']; ?>"<?php echo in_array($v['id'], $tpl['listing_extra_arr']) ? ' checked="checked"' : NULL; ?> />
							<label for="extra_<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></label>
						</div>
						<?php
						if ($i % 3 === 0)
						{
							$is_open = false;
							?><div class="clear_left"></div><?php
						}
						$i++;
					}
				}
				if ($is_open) {
					?><div class="clear_left"></div><?php
				}
				?>
				<div class="extra_header t20 b10"><?php echo $eta['community']; ?></div>
				<?php
				$i = 1;
				foreach ($tpl['extra_arr'] as $v)
				{
					if ($v['type'] == 'community')
					{
						$is_open = true;
						?>
						<div class="float_left w200 b5 r25 pj-checkbox gradient<?php echo in_array($v['id'], $tpl['listing_extra_arr']) ? ' pj-checkbox-checked' : NULL; ?>">
							<input type="checkbox" name="extra[]" id="extra_<?php echo $v['id']; ?>" value="<?php echo $v['id']; ?>"<?php echo in_array($v['id'], $tpl['listing_extra_arr']) ? ' checked="checked"' : NULL; ?> />
							<label for="extra_<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></label>
						</div>
						<?php
						if ($i % 3 === 0)
						{
							$is_open = false;
							?><div class="clear_left"></div><?php
						}
						$i++;
					}
				}
				if ($is_open) {
					?><div class="clear_left"></div><?php
				}
				?>
				<p>
					<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				</p>

			</div>
			
			<div id="tabs-4">
				<?php
				pjUtil::printNotice(@$titles['AL41'], @$bodies['AL41']);
				?>
				<div id="gallery"></div>
			</div>
				
			<?php
			include PJ_VIEWS_PATH . 'pjAdminListings/elements/contact.php';
			
			include PJ_VIEWS_PATH . 'pjAdminListings/elements/address.php';
			
			include PJ_VIEWS_PATH . 'pjAdminListings/elements/prices.php';
			
			include PJ_VIEWS_PATH . 'pjAdminListings/elements/bookings.php';
			?>
			
			<div id="tabs-9" style="padding: 10px 0">
				<?php pjUtil::printNotice(@$titles['AL91'], @$bodies['AL91']); ?>
				<div class="multilang b10"></div>
				<div class="clear_both">
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<div class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<div class="t10">
							<span class="inline_block pt5"><?php __('lblListingTerms'); ?></span>
							<a class="pj-form-langbar-tip listing-tip" href="#" title="<?php echo nl2br(__('lblListingTermsTip', true)); ?>"></a>
						</div>
						<span class="block t5">
							<textarea name="i18n[<?php echo $v['id']; ?>][terms]" class="pj-form-field w700 h200"><?php echo stripslashes(@$tpl['arr']['i18n'][$v['id']]['terms']); ?></textarea>
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						</span>
					</div>
					<?php
				}
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<div class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<div class="t10">
							<span class="inline_block pt5"><?php __('lblListingConfirmEmail'); ?></span>
							<a class="pj-form-langbar-tip listing-tip" href="#" title="<?php echo nl2br(__('lblListingConfirmTokens', true)); ?>"></a>
						</div>
						<span class="block t5">
							<input name="i18n[<?php echo $v['id']; ?>][confirm_subject]" class="pj-form-field w500 b10" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['confirm_subject'])); ?>" />
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						</span>
						<span class="block t5">
							<textarea name="i18n[<?php echo $v['id']; ?>][confirm_tokens]" class="pj-form-field w700 h200"><?php echo stripslashes(@$tpl['arr']['i18n'][$v['id']]['confirm_tokens']); ?></textarea>
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						</span>
					</div>
					<?php
				}
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<div class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<div class="t10">
							<span class="inline_block pt5"><?php __('lblListingPaymentEmail'); ?></span>
							<a class="pj-form-langbar-tip listing-tip" href="#" title="<?php echo nl2br(__('lblListingPaymentTokens', true)); ?>"></a>
						</div>
						<span class="block t5">
							<input name="i18n[<?php echo $v['id']; ?>][payment_subject]" class="pj-form-field w500 b10" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['payment_subject'])); ?>" />
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						</span>
						<span class="block t5">
							<textarea name="i18n[<?php echo $v['id']; ?>][payment_tokens]" class="pj-form-field w700 h200"><?php echo stripslashes(@$tpl['arr']['i18n'][$v['id']]['payment_tokens']); ?></textarea>
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						</span>
					</div>
					<?php
				}
				?>
					<p>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					</p>
				</div>
			</div>
			<div id="tabs-10">
				<div class="multilang b10"></div>
				<div class="clear_both">
					<?php
					foreach ($tpl['lp_arr'] as $v)
					{
						?>
						<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
							<label class="title"><?php __('lblListingMetaTitle'); ?></label>
							<span class="inline_block">
								<input type="text" name="i18n[<?php echo $v['id']; ?>][meta_title]" class="pj-form-field w500<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['meta_title'])); ?>" />
								<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
							</span>
						</p>
						<?php
					}
					foreach ($tpl['lp_arr'] as $v)
					{
						?>
						<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
							<label class="title"><?php __('lblListingMetaKeywords'); ?></label>
							<span class="inline_block">
								<input type="text" name="i18n[<?php echo $v['id']; ?>][meta_keywords]" class="pj-form-field w500<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['meta_keywords'])); ?>" />
								<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
							</span>
						</p>
						<?php
					}
					foreach ($tpl['lp_arr'] as $v)
					{
						?>
						<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
							<label class="title"><?php __('lblListingMetaDesc'); ?></label>
							<span class="inline_block">
								<input type="text" name="i18n[<?php echo $v['id']; ?>][meta_description]" class="pj-form-field w500<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['meta_description'])); ?>" />
								<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
							</span>
						</p>
						<?php
					}
					?>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					</p>
				</div>
			</div>
	
		</div> <!-- #tabs -->
	</form>
	
	<script type="text/javascript">
	var myGallery = myGallery || {};
	myGallery.foreign_id = "<?php echo $tpl['arr']['id']; ?>";
	var myLabel = myLabel || {};
	myLabel.address_not_found = "<?php __('vr_address_not_found'); ?>";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				select: function (event, ui) {
					$("input[name='locale']").val(ui.index);
					$.get("index.php?controller=pjAdminListings&action=pjActionGetLocale", {
						"locale" : ui.index
					}).done(function (data) {
						tid = $("#type_id").find("option:selected").val();
						$("#boxType").html(data.type);
						$("#type_id").find("option[value='"+tid+"']").prop("selected", true);
					});
				}
			});
			$(".multilang").find("a[data-index='<?php echo $locale; ?>']").trigger("click");
		});
	})(jQuery_1_8_2);
	</script>
	
	<?php
	if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
	{
		$tab_id = explode("-", $_GET['tab_id']);
		$tab_id = (int) $tab_id[1] - 1;
		$tab_id = $tab_id < 0 ? 0 : $tab_id;
		?>
		<script type="text/javascript">
		(function ($) {
			$(function () {
				$("#tabs").tabs("option", "selected", <?php echo $tab_id; ?>);
			});
		})(jQuery_1_8_2);
		</script>
		<?php
	}
}
?>