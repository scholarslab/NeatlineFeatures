var init = function() {
	
	map = new OpenLayers.Map( {
		projection : new OpenLayers.Projection("EPSG:4326")
	});
	
	layer = new OpenLayers.Layer.Vector("feature");
	layer.addFeature(feature);
	map.addLayer(layer);
	map.zoomToExtent(feature.geometry.getBounds());
}