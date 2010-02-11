<?php 
	$wkt = item("Dublin Core","Coverage",$item);
?>
<html>

	<head>
		<title>Neatline feature display</title>
		<link rel="stylesheet" href="http://dev.openlayers.org/releases/OpenLayers-2.8/theme/default/style.css" type="text/css" />

		<script type="text/javascript" src="http://openlayers.org/api/OpenLayers.js">Ê</script>
		<script type="text/javascript" defer="">
		//<![CDATA[
			feature = new OpenLayers.Format.WKT().read("<?php echo $wkt ?>");		
			//]]>		
		</script>
		<?php echo js("show-feature/init"); ?>
	</head>
	<body onload="init()">
		 <span><?php echo $backgroundMap?></span>
		 <div id="map" style="height: 400px; width: 700px; border: 1px solid #ccc;"></div>
		
		 
	</body>

</html>
