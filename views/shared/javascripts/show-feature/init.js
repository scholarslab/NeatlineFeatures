var init = function() {
	
	map = new OpenLayers.Map('map',{
		allOverlays: true,
		maxExtent: new OpenLayers.Bounds(
                0, 0, 50, 50
            )
	});
	
	layer = new OpenLayers.Layer.Vector("feature");
	layer.addFeatures( feature );
	map.addLayer(layer);
	map.addControl(new OpenLayers.Control.NavToolbar());

	map.zoomToExtent(feature.geometry.getBounds());
}