var init = function() {

	wgs84 = new OpenLayers.Projection("EPSG:4326");

	map = new OpenLayers.Map('map', {
		projection : wgs84,
		numZoomLevels : 128
	});

	layer = new OpenLayers.Layer.Vector("feature");
	layer.addFeatures(feature);
	map.addLayer(layer);
	if (backgroundMap.length > 0) {
		var backgroundWMS;
		new Ajax.Request("/maps/wms/" + backgroundMap, {
			asynchronous : false,
			onSuccess : function(transport) {
				backgroundWMS = transport.responseText;
			}
		});
		var backgroundlayer = new OpenLayers.Layer.WMS(backgroundMap,
				backgroundWMS, {
					srs : "EPSG:4326",
					layers : "neatline:" + backgroundMap
				})
		map.addLayer(backgroundlayer);
	}
	map.addControl(new OpenLayers.Control.NavToolbar());
	map.addControl(new OpenLayers.Control.LayerSwitcher());

	map.zoomToExtent(feature.geometry.getBounds());
}