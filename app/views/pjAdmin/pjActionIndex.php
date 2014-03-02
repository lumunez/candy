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
	?>
	<div class="dashboard_header">
		<div class="dashboard_header_item">
			<div class="dashboard_icon dashboard_properties"></div>
			<div class="dashboard_info"><abbr><?php echo (int) @$tpl['info_arr'][0]['listings']; ?></abbr><?php (int) @$tpl['info_arr'][0]['listings'] !== 1 ? __('lblDashProperties') : __('lblDashProperty'); ?></div>
		</div>
		<div class="dashboard_header_item">
			<div class="dashboard_icon dashboard_reservations"></div>
			<div class="dashboard_info"><abbr><?php echo (int) @$tpl['info_arr'][0]['reservations']; ?></abbr><?php (int) @$tpl['info_arr'][0]['reservations'] !== 1 ? __('lblDashReservations') : __('lblDashReservation'); ?></div>
		</div>
		<div class="dashboard_header_item dashboard_header_item_last">
			<div class="dashboard_icon dashboard_users"></div>
			<div class="dashboard_info">
			<?php
			if ($controller->isOwner())
			{
				?><abbr><?php echo (int) @$tpl['info_arr'][0]['featured']; ?></abbr><?php (int) @$tpl['info_arr'][0]['featured'] !== 1 ? __('lblDashProperties') : __('lblDashProperty'); ?><?php
			} else {
				?><abbr><?php echo (int) @$tpl['info_arr'][0]['users']; ?></abbr><?php (int) @$tpl['info_arr'][0]['users'] !== 1 ? __('lblDashUsers') : __('lblDashUser'); ?><?php
			}
			?>
			</div>
		</div>
	</div>
	
	<div class="dashboard_box">
		<div class="dashboard_top">
			<div class="dashboard_column_top"><?php __('lblDashMostPopular'); ?></div>
			<div class="dashboard_column_top"><?php __('lblDashLatestReservations'); ?></div>
			<div class="dashboard_column_top dashboard_column_top_last"><?php $controller->isOwner() ? __('lblDashFeatured') : __('lblDashTopUsers'); ?></div>
		</div>
		<div class="dashboard_middle">
			<div class="dashboard_column">
				<?php
				$cnt = count($tpl['listing_arr']);
				if ($cnt === 0)
				{
					?><p class="m10"><?php __('lblListingNotFound'); ?></p><?php
				}
				foreach ($tpl['listing_arr'] as $k => $item)
				{
					?>
					<div class="dashboard_row<?php echo $k + 1 !== $cnt ? NULL : ' dashboard_row_last'; ?>">
						<div class="dashboard_listing_left">
							<div class=""><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminListings&amp;action=pjActionUpdate&amp;id=<?php echo $item['id']; ?>"><img src="<?php echo PJ_INSTALL_URL . (!empty($item['pic']) ? $item['pic'] :  PJ_IMG_PATH . 'backend/no_img.png'); ?>" alt="" /></a></div>
							<div class="t5"><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminListings&amp;action=pjActionUpdate&amp;id=<?php echo $item['id']; ?>" class="no-decor"><?php echo pjSanitize::html(stripslashes($item['title'])); ?></a></div>
							<div class="t5"><?php
							$tmp = array($item['type'], $item['address_city']);
							foreach ($tmp as $k => $v)
							{
								if (empty($v))
								{
									unset($tmp[$k]);
								} else {
									$tmp[$k] = pjSanitize::html($v);
								}
							}
							echo join(", ", array_map('stripslashes', $tmp)); ?></div>
						</div>
						<div class="dashboard_listing_right">
							<div class="dashboard_listing_stat"><abbr><?php echo (int) $item['views']; ?></abbr><?php __('lblDashViews'); ?></div>
							<div class="dashboard_listing_stat"><abbr><?php echo (int) $item['reservations']; ?></abbr><?php (int) $item['reservations'] !== 1 ? __('lblDashReservations') : __('lblDashReservation'); ?></div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<div class="dashboard_column">
				<?php
				$cnt = count($tpl['reservation_arr']);
				if ($cnt === 0)
				{
					?><p class="m10"><?php __('lblReservationNotFound'); ?></p><?php
				}
				foreach ($tpl['reservation_arr'] as $k => $item)
				{
					$nights = (strtotime($item['date_to']) - strtotime($item['date_from'])) / 86400;
					?>
					<div class="dashboard_row<?php echo $k + 1 !== $cnt ? NULL : ' dashboard_row_last'; ?>">
						<div class="dashboard_resr_left">
							<div class="bold fs13 lh19 verdana"><?php echo pjSanitize::html(stripslashes($item['name'])); ?></div>
							<div class="t5 b5 gray"><a class="no-decor" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminReservations&amp;action=pjActionUpdate&amp;id=<?php echo $item['id']; ?>"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($item['created'])); ?></a></div>
							<?php echo pjSanitize::html(stripslashes($item['title'])); ?>
						</div>
						<div class="dashboard_resr_right"><abbr><?php echo $nights; ?></abbr><?php (int) $nights !== 1 ? __('lblDashNights') : __('lblDashNight'); ?></div>
					</div>
					<?php
				}
				?>
			</div>
			<div class="dashboard_column dashboard_column_last">
				<?php
				if (!$controller->isOwner())
				{
					$cnt = count($tpl['user_arr']);
					foreach ($tpl['user_arr'] as $k => $item)
					{
						?>
						<div class="dashboard_row<?php echo $k + 1 !== $cnt ? NULL : ' dashboard_row_last'; ?>">
							<div class="bold fs13 lh19 verdana"><?php echo pjSanitize::html(stripslashes($item['name'])); ?></div>
							<div class="t10"><a class="no-decor" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminUsers&amp;action=pjActionUpdate&amp;id=<?php echo $item['id']; ?>"><?php echo stripslashes($item['email']); ?></a></div>
							<div class="t5 gray"><?php __('lblDashLastLogin'); ?>: <?php echo date($tpl['option_arr']['o_date_format'], strtotime($controller->getUserId() != $item['id'] ? $item['last_login'] : $_SESSION[$controller->defaultUser]['last_login'])); ?></div>
							<div class="t5"><a class="no-decor" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminListings&amp;action=pjActionIndex&amp;user_id=<?php echo $item['id']; ?>"><?php echo (int) $item['listings']; ?></a> <?php (int) $item['listings'] !== 1 ? __('lblDashProperties') : __('lblDashProperty'); ?></div>
						</div>
						<?php
					}
				} else {
					$cnt = count($tpl['featured_arr']);
					if ($cnt === 0)
					{
						?><p class="m10"><?php __('lblListingNotFound'); ?></p><?php
					}
					foreach ($tpl['featured_arr'] as $k => $item)
					{
						?>
						<div class="dashboard_row<?php echo $k + 1 !== $cnt ? NULL : ' dashboard_row_last'; ?>">
							<div class="dashboard_listing_left">
								<div class=""><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminListings&amp;action=pjActionUpdate&amp;id=<?php echo $item['id']; ?>"><img src="<?php echo PJ_INSTALL_URL . (!empty($item['pic']) ? $item['pic'] :  PJ_IMG_PATH . 'backend/no_img.png'); ?>" alt="" /></a></div>
								<div class="t5"><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminListings&amp;action=pjActionUpdate&amp;id=<?php echo $item['id']; ?>" class="no-decor"><?php echo stripslashes($item['title']); ?></a></div>
								<div class="t5"><?php
								$tmp = array($item['type'], $item['address_city']);
								foreach ($tmp as $k => $v)
								{
									if (empty($v))
									{
										unset($tmp[$k]);
									}
								}
								echo join(", ", array_map('stripslashes', $tmp)); ?></div>
							</div>
							<div class="dashboard_listing_right">
								<div class="dashboard_listing_stat"><abbr><?php echo (int) $item['views']; ?></abbr><?php __('lblDashViews'); ?></div>
								<div class="dashboard_listing_stat"><abbr><?php echo (int) $item['reservations']; ?></abbr><?php (int) $item['reservations'] !== 1 ? __('lblDashReservations') : __('lblDashReservation'); ?></div>
							</div>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<div class="dashboard_bottom"></div>
	</div>
	
	<div class="clear_left t20 overflow">
		<div class="float_left black pt15"><span class="gray"><?php echo ucfirst(__('lblDashLastLogin', true)); ?>:</span> <?php echo date("F d, Y H:i", strtotime($_SESSION[$controller->defaultUser]['last_login'])); ?></div>
		<div class="float_right overflow">
		<?php
		list($hour, $day, $other) = explode("_", date("H:i_l_F d, Y"));
		?>
			<div class="dashboard_date">
				<abbr><?php echo $day; ?></abbr>
				<?php echo $other; ?>
			</div>
			<div class="dashboard_hour"><?php echo $hour; ?></div>
		</div>
	</div>
	<?php
}
?>