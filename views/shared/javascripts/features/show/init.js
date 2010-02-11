var init = function() {
	
	wgs84 = new OpenLayers.Projection("EPSG:4326");
	
	map = new OpenLayers.Map('map',{
		projection : wgs84
	});
	
	layer = new OpenLayers.Layer.Vector("feature");
	layer.addFeatures( feature );
	map.addLayer(layer);
	if (backgroundMap.length > 0) {
		var backgroundlayer =
			new OpenLayers.Layer.WMS(backgroundMap,"http://scholarslab.org:8080/geoserver", { srs:"EPSG:4326", layers:"neatline:"  + backgroundMap })	
		map.addLayer(backgroundlayer);
	}
	map.addControl(new OpenLayers.Control.NavToolbar());
	map.addControl(new OpenLayers.Control.LayerSwitcher());

	map.zoomToExtent(feature.geometry.getBounds());
}