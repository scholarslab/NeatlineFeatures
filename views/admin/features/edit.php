<?php
$wkt = item("Dublin Core","Coverage",$item);
head();
?>

<title>Neatline feature edit</title>
<link
	rel="stylesheet"
	href="http://dev.openlayers.org/releases/OpenLayers-2.8/theme/default/style.css"
	type="text/css" />
<style type="text/css">
.olControlEditingToolbar  .olControlModifyFeatureItemActive {
	background-image:
		url("http://dev.openlayers.org/releases/OpenLayers-2.8/theme/default/img/remove_point_on.png")
		;
	background-repeat: no-repeat;
	background-position: 0px 1px;
}

.olControlEditingToolbar .olControlModifyFeatureItemInactive {
	background-image:
		url("http://dev.openlayers.org/releases/OpenLayers-2.8/theme/default/img/remove_point_off.png")
		;
	background-repeat: no-repeat;
	background-position: 0px 1px;
}

.olControlEditingToolbar  .olControlSaveFeaturesItemActive {
	background-image:
		url("http://dev.openlayers.org/releases/OpenLayers-2.8/theme/default/img/save_features_on.png")
		;
	background-repeat: no-repeat;
	background-position: 0px 1px;
}

.olControlEditingToolbar .olControlSaveFeaturesItemInactive {
	background-image:
		url("http://dev.openlayers.org/releases/OpenLayers-2.8/theme/default/img/save_features_off.png")
		;
	background-repeat: no-repeat;
	background-position: 0px 1px;
}

.olControlEditingToolbar .olControlDragFeatureItemActive {
	background-image:
		url("http://dev.openlayers.org/releases/OpenLayers-2.8/theme/default/img/move_feature_on.png")
		;		background-repeat: no-repeat;
}

.olControlEditingToolbar  .olControlDragFeatureItemInactive {
	background-image:
		url("http://dev.openlayers.org/releases/OpenLayers-2.8/theme/default/img/move_feature_off.png")
		; 		background-repeat: no-repeat;
}

.olControlEditingToolbar .olControlSelectFeaturesItemActive {
	background-image:
		url("http://www.developertutorials.com/images/flashtutorial/arrow.jpg")
		;
		background-repeat: no-repeat;
		
}

.olControlEditingToolbar .olControlSelectFeaturesItemInactive {
	background-image:
		url("http://wiki.altium.com/download/attachments/3080266/Button_Arrow.png")
		;
		background-repeat: no-repeat;
		
}
</style>
<script type="text/javascript"
	src="http://openlayers.org/api/OpenLayers.js">Ê</script>
<script type="text/javascript" defer="">
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
</head>
<body onload="edit()">
			<?php echo $backgroundMap?>
<div id="map"
	style="height: 400px; width: 700px; border: 1px solid #ccc; float: right;"></div>


</body>
