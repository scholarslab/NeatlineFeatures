if (typeof (Omeka) == 'undefined') {
	Omeka = new Object();
}
if (!Omeka.NeatlineFeatures) {
	Omeka.NeatlineFeatures = new Array();
}
Omeka.NeatlineFeatures.createDrawingControlPanel = function(featurelayer,inputNameStem) {
	var controls = {
	        modify: new OpenLayers.Control.ModifyFeature(featurelayer, {
	        		name: "modify",
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
	        		name: "drag",
	        		displayClass : "olControlDragFeature",
	        		title: "Move a feature around once selected"
	        }),
	        polygon: new OpenLayers.Control.DrawFeature(featurelayer,
	        			OpenLayers.Handler.Polygon,
                    { handlerOptions : {
        					multi : true
	    				},
	    				name: "polygon",
	    				displayClass : "olControlDrawFeaturePolygon",
	    		        title: "Draw a polygonal feature"
	                }),
	        line: new OpenLayers.Control.DrawFeature(featurelayer,
	                OpenLayers.Handler.Path,
	                { handlerOptions : {
	        				multi : true
	    				},
	    				name: "line",
	    				displayClass : "olControlDrawFeaturePath",
	    		        title: "Draw a linear feature"
	        }),
	        point: new OpenLayers.Control.DrawFeature(featurelayer,
		        			OpenLayers.Handler.Point,
	                    { handlerOptions : {
	            				multi : true
	        				},
	        				name: "point",
	        				displayClass : "olControlDrawFeaturePoint",
	        		        title: "Draw a point feature"
	        }),
	        save : new OpenLayers.Control.Button( {
	        			name: "save",
	                trigger : function() {
	        					jQuery(featurelayer.features).each(function(){this.geometry.transform(spherical,wgs84)});	
		                    var gml = new OpenLayers.Format.GML().write(featurelayer.features);
		                    jQuery("textarea[name='" + inputNameStem + "[text]']").html(gml);
		                    },
	                displayClass : "olControlSaveFeatures",
	                title: "Save your changes"
	        }),
	        newlayer : new OpenLayers.Control.Button( {
	        		name: "newlayer",
	            trigger : function() { addlayerdialog.dialog("open"); },
	            displayClass : "olNewLayer",
	            title: "Add new layer"
	        }),
	        selectCtrl : new OpenLayers.Control.SelectFeature(featurelayer, {
	        			name: "selectCtrl",
	        			clickout: true,
	        			displayClass: "olControlSelectFeatures",
	        			title: "Use this control to select shapes and navigate the map"}
	            )
	};
	var panel = new OpenLayers.Control.Panel();
	panel.addControls(controls);
	return panel;
}