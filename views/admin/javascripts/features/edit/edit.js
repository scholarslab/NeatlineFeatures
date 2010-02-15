var edit = function() {

	wgs84 = new OpenLayers.Projection("EPSG:4326");
	
	var myStyles = new OpenLayers.StyleMap({
        "default": new OpenLayers.Style({
            fillColor: "#66ccff",
            strokeColor: "#6600ff",
            strokeWidth: 3
        }),
        "select": new OpenLayers.Style({
            fillColor: "#66ccff",
            strokeColor: "#3399ff"
        })
    });


	map = new OpenLayers.Map('map', {
		projection : wgs84,
		numZoomLevels : 128
	});

	var featurelayer = new OpenLayers.Layer.Vector("feature", { styleMap: myStyles });
	featurelayer.addFeatures(feature);
	map.addLayer(featurelayer);
	// console.log(layers);
	if (layers.length > 0) {
		for (var i = 0; i < layers.length; i++) {
				var backgroundlayer = new OpenLayers.Layer.WMS(layers[i].title,
						layers[i].address, {
							srs : "EPSG:4326",
							layers : layers[i].layername,
						})
				// console.log(backgroundlayer);
				map.addLayer(backgroundlayer);
			
		}
	}
	map.addControl(new OpenLayers.Control.NavToolbar());
	map.addControl(new OpenLayers.Control.LayerSwitcher());
    controls = {
            point: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Point),
            line: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Path),
            polygon: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Polygon),
            drag: new OpenLayers.Control.DragFeature(featurelayer)
        };

        for(var key in controls) {
            map.addControl(controls[key]);
        }



	map.zoomToExtent(feature.geometry.getBounds());
}