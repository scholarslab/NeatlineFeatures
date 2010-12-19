if (typeof (Omeka) == 'undefined') {
	Omeka = new Object();
}
if (!Omeka.NeatlineFeatures) {
	Omeka.NeatlineFeatures = new Array();
}
Omeka.NeatlineFeatures.createDrawingControlPanel = function(featurelayer,inputNameStem,div) {
	var annotatedialog = jQuery("<form><fieldset><span>Name:</span><input name='name' id='featurename' value='Put a name here'/><span>Text:</span><textarea id='featuredescription' name='description'>Put a description here</fieldset></form>");
	annotatedialog.appendTo(div);
	var name = jQuery("#featurename"), description = jQuery("#featuredescription");
	var controls = [
	        new OpenLayers.Control.ModifyFeature(featurelayer, {
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
	        new OpenLayers.Control.DragFeature(featurelayer, {
	        		name: "drag",
	        		displayClass : "olControlDragFeature",
	        		title: "Move a feature around once selected"
	        }),
	        new OpenLayers.Control.DrawFeature(featurelayer,
	        			OpenLayers.Handler.Polygon,
                    { handlerOptions : {
        					multi : true
	    				},
	    				name: "polygon",
	    				displayClass : "olControlDrawFeaturePolygon",
	    		        title: "Draw a polygonal feature"
	                }),
	        new OpenLayers.Control.DrawFeature(featurelayer,
	                OpenLayers.Handler.Path,
	                { handlerOptions : {
	        				multi : true
	    				},
	    				name: "line",
	    				displayClass : "olControlDrawFeaturePath",
	    		        title: "Draw a linear feature"
	        }),
	        new OpenLayers.Control.DrawFeature(featurelayer,
		        			OpenLayers.Handler.Point,
	                    { handlerOptions : {
	            				multi : true
	        				},
	        				name: "point",
	        				displayClass : "olControlDrawFeaturePoint",
	        		        title: "Draw a point feature"
	        }),
	        new OpenLayers.Control.Button( {
	        			name: "save",
	                trigger : function() {
	        					jQuery(featurelayer.features).each(function(){this.geometry.transform(Omeka.NeatlineFeatures.spherical,Omeka.NeatlineFeatures.wgs84)});	
		                    var gml = new OpenLayers.Format.GML().write(featurelayer.features);
		                    jQuery("textarea[name='" + inputNameStem + "[text]']").html(gml);
		                    },
	                displayClass : "olControlSaveFeatures",
	                title: "Save your changes"
	        }),
	        new OpenLayers.Control.Button( {
	        		name: "newlayer",
	            trigger : function() { addlayerdialog.dialog("open"); },
	            displayClass : "olNewLayer",
	            title: "Add new layer"
	        }),
	        new OpenLayers.Control.SelectFeature(featurelayer, {
        			name: "selectCtrl",
        			displayClass: "olControlSelectFeatures",
        			title: "Use this control to select shapes and navigate the map"}
	        ),
	        new OpenLayers.Control.SelectFeature(featurelayer, {
        			name: "annotateCtrl",
        			displayClass: "olControlAnnotateFeatures",
        			title: "Use this control to annotate features",
        			box: false,
        			onSelect: function(feature) {
	        			jQuery(annotatedialog).dialog({
	        				"title":"Annotate this feature",
	        				"closeOnEscape": false, // would conflict with Coverage editor esc-close
	        				"draggable": true,
	        				"height": 'auto',
	        				"buttons": { "Save": 
	        					function() { 
	        						// save the annotations
	        						alert("Description is " + description.val());
	        						jQuery(this).dialog("close"); 
	        					} 
	        				}
	        			});
	        		}
	        })
	];
	var panel = new OpenLayers.Control.Panel({"div": div});
	panel.addControls(controls);
	return panel;
}