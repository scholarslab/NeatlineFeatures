var init = function() {
	
	map = new OpenLayers.Map('map',{
		allOverlays: true
	});
	
	layer = new OpenLayers.Layer.Vector("feature");
	layer.addFeatures( feature );
	map.addLayer(layer);
	map.addControl(new OpenLayers.Control.NavToolbar());

	map.zoomToExtent(feature.geometry.getBounds());
}