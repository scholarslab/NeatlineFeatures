var init = function() {

	wgs84 = new OpenLayers.Projection("EPSG:4326");

	map = new OpenLayers.Map('map', {
		projection : wgs84,
		numZoomLevels : 128
	});

	var featurelayer = new OpenLayers.Layer.Vector("feature");
	featurelayer.addFeatures(feature);
	map.addLayer(featurelayer);
	if (layers.length > 0) {
		for (var layer in layers) {
			console.debug(layer);
			var backgroundlayer = new OpenLayers.Layer.WMS(layer.title,
					layer.address, {
						srs : "EPSG:4326",
						layers : layers.layername,
					})
			map.addLayer(backgroundlayer);
		}
	}
	map.addControl(new OpenLayers.Control.NavToolbar());
	map.addControl(new OpenLayers.Control.LayerSwitcher());

	map.zoomToExtent(feature.geometry.getBounds());
}