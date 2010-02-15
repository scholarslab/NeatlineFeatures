var edit = function() {

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
		controls: [new OpenLayers.Control.NavToolbar(), new OpenLayers.Control.LayerSwitcher()], 
		numZoomLevels : 128
	});

	var featurelayer = new OpenLayers.Layer.Vector("feature", { styleMap: myStyles });
	featurelayer.addFeatures(feature);
	map.addLayer(featurelayer);

	if (layers.length > 0) {
		for (var i = 0; i < layers.length; i++) {
				var backgroundlayer = new OpenLayers.Layer.WMS(layers[i].title,
						layers[i].address, {
							srs : "EPSG:4326",
							layers : layers[i].layername,
						});				
				map.addLayer(backgroundlayer);
			
		}
	}
    var report = function(e) {
      // OpenLayers.Console.log(e.type, e.feature.id);
    };

controls = {
            point: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Point,
                        { handlerOptions : {
                				multi : true
            				},
            				displayClass : "olControlDrawFeaturePoint"
                        }),
            line: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Path,
                        { handlerOptions : {
            				multi : true
        				},
        				displayClass : "olControlDrawFeaturePath"
                    }),
            polygon: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Polygon,
                        { handlerOptions : {
            				multi : true
        				},
        				displayClass : "olControlDrawFeaturePolygon"
                    }),
            modify : new OpenLayers.Control.ModifyFeature(shapes, {
                onModificationEnd : function(feature) {
                /* the UPDATE state is modified here!!!! */
                feature.state = OpenLayers.State.UPDATE;
				        },
				        onDelete : function(feature) {
				        },
				        displayClass : "olControlModifyFeature"
				})
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
            panel.addControls(controls[key]);
        }
        map.addControl(panel);
        controls.highlightCtrl.activate();
        controls.selectCtrl.activate();




	map.zoomToExtent(feature.geometry.getBounds());
}