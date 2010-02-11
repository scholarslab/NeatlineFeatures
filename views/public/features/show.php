<?php 

	

?>
<html>

	<head>
		<title>Neatline feature display</title>
		
		<script type="text/javascript" src="http://openlayers.org/api/OpenLayers.js">Ê</script>
		<script type="text/javascript">
			feature = new OpenLayers.Format.GML().read("<?php echo $gml ?>");		
		</script>
		<?php echo js("showfeature/init"); ?>
	</head>
	<body onload="init()">
		<?php
		 echo item("Dublin Core", "Coverage", $item); ?>
		 
		 <div id="map"/>
		
		 
	</body>

</html>
