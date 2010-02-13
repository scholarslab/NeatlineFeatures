var init = function() {

	wgs84 = new OpenLayers.Projection("EPSG:4326");

	map = new OpenLayers.Map('map', {
		projection : wgs84,
		numZoomLevels : 128
	});

	var featurelayer = new OpenLayers.Layer.Vector("feature");
	featurelayer.addFeatures(feature);
	map.addLayer(featurelayer);
	console.log(layers);
	if (layers.length > 0) {
		for (var i = 0; i < layers.length; i++) {
				var backgroundlayer = new OpenLayers.Layer.WMS(layer[i].title,
						layer[i].address, {
							srs : "EPSG:4326",
							layers : layer[i].layername,
						})
				console.log(layer);
				map.addLayer(backgroundlayer);
			
		}
	}
	map.addControl(new OpenLayers.Control.NavToolbar());
	map.addControl(new OpenLayers.Control.LayerSwitcher());

	map.zoomToExtent(feature.geometry.getBounds());
}