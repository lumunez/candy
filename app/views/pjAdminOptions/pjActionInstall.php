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
	?>
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php __('lblInstallListing'); ?></a></li>
			<li><a href="#tabs-2"><?php __('lblInstallSearch'); ?></a></li>
			<li><a href="#tabs-3"><?php __('lblInstallFeatured'); ?></a></li>
			<li><a href="#tabs-4"><?php __('lblInstallOptional'); ?></a></li>
		</ul>
		<div id="tabs-1">
			<?php pjUtil::printNotice(NULL, __('lblInstallPhp1Title', true), false, false); ?>
			
			<form action="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form b20">
				<input type="hidden" name="options_update" value="1" />
				<input type="hidden" name="next_action" value="pjActionInstall" />
				<?php
				$listing_page = NULL;
				foreach ($tpl['o_arr'] as $item)
				{
					if ($item['key'] == 'o_listing_page')
					{
						$listing_page = $item['value'];
						?>
						<p>
							<label class="float_left w300 pt5"><?php __('opt_' . $item['key']); ?></label>
							<span class="pj-form-field-custom pj-form-field-custom-before float_left">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-url"></abbr></span>
								<input type="text" name="value-<?php echo $item['type']; ?>-<?php echo $item['key']; ?>" class="pj-form-field w250" value="<?php echo htmlspecialchars(stripslashes($item['value'])); ?>" />
							</span>
							<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button float_left l5 align_middle" />
						</p>
						<?php
						break;
					}
				}
				?>
			</form>

			<p style="margin: 0 0 10px; font-weight: bold"><?php __('lblInstallPhp1_1'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:50px">
&lt;?php
ob_start();
?&gt;</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallPhp1_2'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:30px">{VRL_LISTINGS}</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallPhp1_2a'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:30px">{VRL_META}</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallPhp1_3'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:30px">
&lt;?php include '<?php echo dirname($_SERVER['SCRIPT_FILENAME']); ?>/app/views/pjLayouts/pjActionListings.php'; ?&gt;</textarea>
		</div>
		
		<div id="tabs-2">
			<?php pjUtil::printNotice(NULL, __('lblInstallPhp2Title', true), false, false); ?>
			<p style="margin: 0 0 10px; font-weight: bold"><?php __('lblInstallSearch_1'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:50px">
&lt;?php
ob_start();
?&gt;</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallSearch_2'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:30px">{VRL_SEARCH}</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallSearch_3'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:30px">
&lt;?php include '<?php echo dirname($_SERVER['SCRIPT_FILENAME']); ?>/app/views/pjLayouts/pjActionSearch.php'; ?&gt;</textarea>
		</div>
		
		<div id="tabs-3">
			<?php pjUtil::printNotice(NULL, __('lblInstallPhp3Title', true), false, false); ?>
			<p style="margin: 0 0 10px; font-weight: bold"><?php __('lblInstallFeat_1'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:50px">
&lt;?php
ob_start();
?&gt;</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallFeat_2'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:30px">{VRL_FEATURED}</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallPhp1_2a'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:30px">{VRL_META}</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallFeat_3'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:30px">
&lt;?php include '<?php echo dirname($_SERVER['SCRIPT_FILENAME']); ?>/app/views/pjLayouts/pjActionFeatured.php'; ?&gt;</textarea>
		</div>
		<div id="tabs-4">
			<?php pjUtil::printNotice(NULL, __('lblInstallPhp4Title', true), false, false); ?>
			
			<form action="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form b20">
				<input type="hidden" name="options_update" value="1" />
				<input type="hidden" name="next_action" value="pjActionInstall" />
				<input type="hidden" name="tab" value="3" />
				<?php
				foreach ($tpl['o_arr'] as $item)
				{
					if ($item['key'] == 'o_seo_url')
					{
						?>
						<p>
							<label class="float_left w150 pt5"><?php __('opt_' . $item['key']); ?></label>
							<select name="value-<?php echo $item['type']; ?>-<?php echo $item['key']; ?>" class="pj-form-field float_left">
							<?php
							$default = explode("::", $item['value']);
							$enum = explode("|", $default[0]);
							
							$enumLabels = array();
							if (!empty($item['label']) && strpos($item['label'], "|") !== false)
							{
								$enumLabels = explode("|", $item['label']);
							}
							
							foreach ($enum as $k => $el)
							{
								if ($default[1] == $el)
								{
									?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
								} else {
									?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
								}
							}
							?>
							</select>
							<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button float_left l5 t5 align_middle" />
						</p>
						<?php
						break;
					}
				}
				?>
			</form>
			
			<?php
			$parts = parse_url($listing_page);
			$prefix = NULL;
			if (substr($parts['path'], -1) !== "/")
			{
				$prefix = basename($parts['path']);
			}
			if (isset($parts['query']) && !empty($parts['query']))
			{
				$prefix .= "?" . $parts['query'];
			}
			$prefix .= (strpos($prefix, "?") === false) ? "?" : "&";
			?>
			<p style="margin: 0 0 10px; font-weight: bold"><?php __('lblInstallPhp1_4'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:35px">
RewriteEngine On
RewriteRule ^(.*)-(\d+).html$ <?php echo $prefix; ?>controller=pjListings&action=pjActionView&id=$2</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallPhp1_5'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:35px">
&lt;base href="<?php echo $listing_page; ?>" /&gt;</textarea>
			<p style="margin: 20px 0 10px; font-weight: bold"><?php __('lblInstallFeat_4'); ?></p>
		<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:65px">
&lt;?php
$VRL_SearchForm = true;
include '<?php echo dirname($_SERVER['SCRIPT_FILENAME']); ?>/app/views/pjLayouts/pjActionFeatured.php';
?&gt;</textarea>
		</div>
	</div>
	<?php
	if (isset($_GET['tab']))
	{
		?>
		<script type="text/javascript">
		(function ($, undefined) {
			$(function () {
				$("#tabs").tabs('option', 'selected', <?php echo (int) $_GET['tab']; ?>);
			});
		})(jQuery_1_8_2);
		</script>
		<?php
	}
}
?>