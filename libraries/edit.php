<?php
$wkt = item("Dublin Core","Coverage",$item);
?>
<div id='Locate'>
	<link
		rel="stylesheet"
		href="http://dev.openlayers.org/releases/OpenLayers-2.8/theme/default/style.css"
		type="text/css" />
	<link rel="stylesheet" href="<?php echo css('edit'); ?>" />
	<script type="text/javascript"
		src="http://openlayers.org/api/OpenLayers.js">Ê</script>
	<script type="text/javascript" defer="defer">
			//<![CDATA[
				itemid = "<?php echo $item->id ?>";
				feature = new OpenLayers.Format.WKT().read("<?php echo $wkt ?>");		
				layers = new Array();
				<?php 
					foreach ($backgroundLayers as $layername => $layervalues) {
	 				   ?> 
	 				   layers.push( { "title":"<?php echo $layername ?>", 
	 		 				   			"address":"<?php echo $layervalues["serviceaddy"] ?>",
	 		 		 				   	"layername":"<?php echo $layervalues["layername"] ?>" } ) ;
	 				   <?php 
					}
				?>
				//]]> 	
			</script>
	<?php echo js("features/edit/edit"); ?>
	<?php echo js("features/edit/save"); ?>


	<div id="map"
		style="height: 400px; width: 700px; border: 1px solid #ccc; float: right;"></div>
	<script type="text/javascript" defer="defer">
		//<![CDATA[
		edit();
		//]]>
	</script>
</div>

