<?php
if (count($tpl['gallery_arr']) > 0)
{
	?>
	<div class="property-view-gallery">
		<a href="#" class="property-carousel-nav property-carousel-nav-left"></a>
		<a href="#" class="property-carousel-nav property-carousel-nav-right"></a>
		<div class="property-carousel">
			<div class="property-carousel-slide" style="left: 0px">
			<?php
			foreach ($tpl['gallery_arr'] as $k => $v)
			{
				?><div class="property-carousel-item"><a rel="lytebox[allphotos]" title="<?php echo htmlspecialchars(stripslashes($v['alt'])); ?>" href="<?php echo PJ_INSTALL_URL . $v['large_path']; ?>"><img src="<?php echo PJ_INSTALL_URL . $v['small_path']; ?>" alt="<?php echo htmlspecialchars(stripslashes($v['alt'])); ?>" /></a></div><?php
			}
			?>
			</div>
		</div>
	</div>
	<?php
}
?>