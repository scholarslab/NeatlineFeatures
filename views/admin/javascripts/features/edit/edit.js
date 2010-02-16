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

	featurelayer = new OpenLayers.Layer.Vector("feature", { styleMap: myStyles });
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
            				displayClass : "olControlDrawFeaturePoint",
            		        title: "Draw a point feature"
                        }),
            line: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Path,
                        { handlerOptions : {
            				multi : true
        				},
        				displayClass : "olControlDrawFeaturePath",
        		        title: "Draw a linear feature"
                    }),
            polygon: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Polygon,
                        { handlerOptions : {
            				multi : true
        				},
        				displayClass : "olControlDrawFeaturePolygon",
        		        title: "Draw a polygonal feature"
                    }),
            modify : new OpenLayers.Control.ModifyFeature(featurelayer, {
                onModificationEnd : function(feature) {
                /* the UPDATE state is modified here!!!! */
                feature.state = OpenLayers.State.UPDATE;
				        },
				        onDelete : function(feature) {
				        },
				        displayClass : "olControlModifyFeature",
				        title: "Modify a feature on the image"
				}),
            drag: new OpenLayers.Control.DragFeature(featurelayer, {
            		displayClass : "olControlDragFeature",
            		title: "Move a feature around once selected"
            }),
            /*
			 * highlightCtrl: new OpenLayers.Control.SelectFeature(featurelayer, {
			 * hover: true, highlightOnly: true, renderIntent: "temporary",
			 * eventListeners: { beforefeaturehighlighted: report,
			 * featurehighlighted: report, featureunhighlighted: report } }),
			 */
            selectCtrl : new OpenLayers.Control.SelectFeature(featurelayer,
                    { clickout: true,
            			displayClass: "olControlSelectFeatures",
            			title: "Use this control to select shapes"}
                ),
            save : new OpenLayers.Control.Button( {
                    trigger : closesave,
                    displayClass : "olControlSaveFeatures",
                    title: "Save your changes"
            })
        };
    		var panel = new OpenLayers.Control.Panel({
    	        displayClass: "olControlEditingToolbar"
    	    });
        for(var key in controls) {
            panel.addControls(controls[key]);
        }
    map.addControl(panel);
  // controls.highlightCtrl.activate();
    controls.selectCtrl.activate();

	map.zoomToExtent(feature.geometry.getBounds());
}

var closesave = function() {
	save(itemid, new OpenLayers.Format.WKT().write(featurelayer.features));
}
