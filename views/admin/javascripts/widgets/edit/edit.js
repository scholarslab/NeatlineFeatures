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
		controls: [new OpenLayers.Control.Navigation(),new OpenLayers.Control.PanZoom(), new OpenLayers.Control.LayerSwitcher()], 
		numZoomLevels : 128
	});
	
	map.addLayer(new OpenLayers.Layer.WMS(
            "NASA Blue Marble WMS",
            "http://wms.jpl.nasa.gov/wms.cgi",
            {layers: 'BMNG', srs:"EPSG:4326"}, {projection: wgs84}
        ));
	//map.addLayer(new OpenLayers.Layer.OSM("OpenStreetMap"));
	
	var wkt = jQuery("textarea[name='" + inputNameStem + "[text]']").html();
	features = new OpenLayers.Format.WKT().read(wkt);	
	featurelayer = new OpenLayers.Layer.Vector("feature", { styleMap: myStyles, projection: wgs84 });
	if (features) {
		featurelayer.addFeatures(features);
	}
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

var controls = {
            modify: new OpenLayers.Control.ModifyFeature(featurelayer, {
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
            polygon: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Polygon,
                        { handlerOptions : {
            				multi : true
        				},
        				displayClass : "olControlDrawFeaturePolygon",
        		        title: "Draw a polygonal feature"
                    }),
            line: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Path,
                        { handlerOptions : {
            				multi : true
        				},
        				displayClass : "olControlDrawFeaturePath",
        		        title: "Draw a linear feature"
            }),
            point: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Point,
                        { handlerOptions : {
                				multi : true
            				},
            				displayClass : "olControlDrawFeaturePoint",
            		        title: "Draw a point feature"
            }),
            save : new OpenLayers.Control.Button( {
                    trigger : function() {
		                    var wkt = new OpenLayers.Format.WKT().write(featurelayer.features);
		                    	jQuery("textarea[name='" + inputNameStem + "[text]']").html(wkt);
		                    },
                    displayClass : "olControlSaveFeatures",
                    title: "Save your changes"
            }),
            newlayer : new OpenLayers.Control.Button( {
                trigger : function() { addlayerdialog.dialog("open"); },
                displayClass : "olNewLayer",
                title: "Add new layer"
            }),
            selectCtrl : new OpenLayers.Control.SelectFeature(featurelayer,
                    { clickout: true,
            			displayClass: "olControlSelectFeatures",
            			title: "Use this control to select shapes and navigate the map"}
                )
        };

    		var panel = new OpenLayers.Control.Panel({
				div: document.getElementById('mappanel')
    	    });
        for(var key in controls) {
            panel.addControls(controls[key]);
        }
    map.addControl(panel);
    
	var addlayerdialog = jQuery("#addlayerdialog").dialog( {
		"autoOpen": false,
		"draggable": true,
		"height": 'auto',
		"width": 500,
		"title": "Add a Layer...",
		"closeOnEscape": true,
		"buttons": { "Add": 
				function() { 
					var id = jQuery("#layerselect")[0].value;
					jQuery.get("/maps/serviceaddy/" + id, function(serviceaddy){ 
						jQuery.get("/maps/layername/" + id, function(layername) {
							var label =jQuery("#layerselect option")[jQuery("#layerselect")[0].selectedIndex].label;
							map.addLayers([new OpenLayers.Layer.WMS( label, serviceaddy, {"layers": layername, "srs": "EPSG:4326"}, {projection:wgs84})]);
						});
					});
					jQuery(this).dialog("close"); } }
		});

    controls.selectCtrl.activate();
    if (features) {  	
    		var coll = new OpenLayers.Geometry.Collection(features.pluck("geometry"));
    		coll.calculateBounds();
    		map.zoomToExtent(coll.getBounds());
	}
    else {
    		map.zoomToMaxExtent();
    }
    
}

