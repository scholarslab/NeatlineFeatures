var init = function() {
	
	map = new OpenLayers.Map( {
		allOverlays: true,
		maxExtent: new OpenLayers.Bounds(
                1549471.9221, 6403610.94, 1550001.32545, 6404015.8
            )
	});
	
	layer = new OpenLayers.Layer.Vector("feature");
	layer.addFeatures([ feature ]);
	map.addLayer(layer);
	map.addControl(new OpenLayers.Control.NavToolbar());

	map.zoomToExtent(feature.geometry.getBounds());
}