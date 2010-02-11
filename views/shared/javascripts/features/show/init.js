var init = function() {
	
	wgs84 = new OpenLayers.Projection("EPSG:4326");
	
	map = new OpenLayers.Map('map',{
		allOverlays: true,
		projection : wgs84
	});
	
	layer = new OpenLayers.Layer.Vector("feature");
	layer.addFeatures( feature );
	map.addLayer(layer);
	if (backgroundMap) {
		var backgroundlayer = new OpenLayers.Layer.WMS(background,"http://scholarslab.org:8080/geoserver", { srs:"EPSG:4326", layers:"neatline:"  + background })	
		map.addLayer(backgroundlayer);
	}
	map.addControl(new OpenLayers.Control.NavToolbar());

	map.zoomToExtent(feature.geometry.getBounds());
}