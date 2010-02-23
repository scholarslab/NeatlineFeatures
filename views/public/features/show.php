<?php 
	$wkt = item("Dublin Core","Coverage",$item);
	head();
?>

		<title>Neatline feature display</title>
		<link rel="stylesheet" href="http://dev.openlayers.org/releases/OpenLayers-2.8/theme/default/style.css" type="text/css" />

		<script type="text/javascript" src="http://openlayers.org/api/OpenLayers.js">Ê</script>
		<script type="text/javascript" defer="">
		//<![CDATA[
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
		<?php echo js("features/show/init"); ?>
	</head>
	<body onload="init()">
		<?php echo $backgroundMap?>
		 <div id="map" style="height: 400px; width: 700px; 
border: 1px solid #ccc; float:right;"></div>
		
		 
	</body>

