var init = function() {

	wgs84 = new OpenLayers.Projection("EPSG:4326");
	
	var myStyles = new OpenLayers.StyleMap({
        "default": new OpenLayers.Style({
            fillColor: "none",
            strokeColor: "blue",
            strokeWidth: 3
        }),
        "select": new OpenLayers.Style({
            fillColor: "red",
            strokeColor: "red"
        })
    });


	map = new OpenLayers.Map('map', {
		projection : wgs84,
		numZoomLevels : 128
	});

	var featurelayer = new OpenLayers.Layer.Vector("feature", { styleMap: myStyles });
	featurelayer.addFeatures(feature);
	map.addLayer(featurelayer);
	//console.log(layers);
	if (layers.length > 0) {
		for (var i = 0; i < layers.length; i++) {
				var backgroundlayer = new OpenLayers.Layer.WMS(layers[i].title,
						layers[i].address, {
							srs : "EPSG:4326",
							layers : layers[i].layername,
						})
				//console.log(backgroundlayer);
				map.addLayer(backgroundlayer);
			
		}
	}
	map.addControl(new OpenLayers.Control.NavToolbar());
	map.addControl(new OpenLayers.Control.LayerSwitcher());

	map.zoomToExtent(feature.geometry.getBounds());
}