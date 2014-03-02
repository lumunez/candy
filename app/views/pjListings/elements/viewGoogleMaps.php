<?php
if ($tpl['arr']['address_map'] == 1)
{
	if (!empty($tpl['arr']['lat']) && !empty($tpl['arr']['lng']))
	{
		$points = array($tpl['arr']['country_title'], $tpl['arr']['address_city'], $tpl['arr']['address_state'], $tpl['arr']['address_content'], $tpl['arr']['address_postcode']);
		$points = pjSanitize::clean($points);
		foreach ($points as $k => $v)
		{
			if (empty($v))
			{
				unset($points[$k]);
			} else {
				$points[$k] = preg_replace('/\r\n|\n/', ' ', $v);
			}
		}
		?>
		<div id="property-view-map-canvas" class="property-view-map" style="display: none"></div>
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
		if (document.getElementById("property-view-map-canvas")) {
			document.getElementById("property-view-map-canvas").style.display = '';
		}
		if (document.getElementById("property-view-map-header")) {
			document.getElementById("property-view-map-header").style.display = '';
		}
		var map;
		(function initialize() {
			var myLatlng = new google.maps.LatLng(<?php echo $tpl['arr']['lat']; ?>, <?php echo $tpl['arr']['lng']; ?>);
			map = new google.maps.Map(document.getElementById('property-view-map-canvas'), {
				zoom: 8,
				center: myLatlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			});
			var infowindow = new google.maps.InfoWindow({
				content: '<?php echo join(", ", array_map('addslashes', $points)); ?>'
			});
			var marker = new google.maps.Marker({
				position: myLatlng,
				map: map,
				title: '<?php echo pjSanitize::html(addslashes($tpl['arr']['listing_title'])); ?>'
			});
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map, marker);
			});
		})();
		</script>
		<?php
	}
}
?>