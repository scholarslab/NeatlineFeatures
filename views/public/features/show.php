<?php 
	$wkt = item("Dublin Core","Coverage",$item);
?>
<html>

	<head>
		<title>Neatline feature display</title>
		
		<script type="text/javascript" src="http://openlayers.org/api/OpenLayers.js">Ê</script>
		<script type="text/javascript" defer="">
		//<![CDATA[
			feature = new OpenLayers.Format.WKT().read("<?php echo $wkt ?>");		
			//]]>		
		</script>
		<?php echo js("show-feature/init"); ?>
	</head>
	<body onload="init()">
		 
		 <div id="map"></div>
		
		 
	</body>

</html>
