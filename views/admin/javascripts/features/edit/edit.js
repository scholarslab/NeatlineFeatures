var edit = function() {

	wgs84 = new OpenLayers.Projection("EPSG:4326");
	
	var myStyles = new OpenLayers.StyleMap({
        "default": new OpenLayers.Style({
            fillColor: "#3333ff",
            strokeColor: "#3399ff",
            strokeWidth: 3
        }),
        "select": new OpenLayers.Style({
            fillColor: "#ff3366",
            strokeColor: "#330033"
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
    var report = function(e) {
      //  OpenLayers.Console.log(e.type, e.feature.id);
    };

	
	map.addControl(new OpenLayers.Control.NavToolbar());
	map.addControl(new OpenLayers.Control.LayerSwitcher());
    controls = {
            point: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Point),
            line: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Path),
            polygon: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Polygon),
            drag: new OpenLayers.Control.DragFeature(featurelayer),
            highlightCtrl: new OpenLayers.Control.SelectFeature(featurelayer, {
                hover: true,
                highlightOnly: true,
                renderIntent: "temporary",
                eventListeners: {
                    beforefeaturehighlighted: report,
                    featurehighlighted: report,
                    featureunhighlighted: report
                }
            }),
            selectCtrl : new OpenLayers.Control.SelectFeature(featurelayer,
                    {clickout: true}
                )
        };
    		var panel = new OpenLayers.Control.Panel();
        for(var key in controls) {
            panel.addControl(controls[key]);
        }
        map.addControl(panel);
        controls.highlightCtrl.activate();
        controls.selectCtrl.activate();




	map.zoomToExtent(feature.geometry.getBounds());
}