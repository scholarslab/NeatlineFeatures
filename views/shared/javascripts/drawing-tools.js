if (typeof (Omeka) == 'undefined') {
	Omeka = new Object();
}

if (Omeka.Neatline) { 
	if (!Omeka.Neatline.jQuery) {
		Omeka.Neatline.jQuery = jQuery.noConflict();
	}
}
else {
	Omeka.Neatline = new Object();
	Omeka.Neatline.jQuery = jQuery.noConflict();
}

if (!Omeka.NeatlineFeatures) {
	Omeka.NeatlineFeatures = new Array();
}
/**
 * Creates a new panel of drawing and annotation tools for use in Neatline/Omeka contexts
 * @author ajs6f
 * @param {OpenLayers.Layer.Vector} featurelayer 	An <code>OpenLayers.Layer.Vector</code> over which this panel will operate
 * @param {string} inputNameStem     				A string that will be used to find the element into which to persist shape-dc:coverages 
 * @param {div} div 									An HTML div to which this panel will be attached
 * @returns {OpenLayers.Control.Panel} 				A new panel of drawing and annotation tools
 */

Omeka.NeatlineFeatures.createDrawingControlPanel = function(featurelayer,inputNameStem,div) {
	
	// this is the form that will appear when the annotate tool is clicked on a feature
	// it is hard-wired to the code that persists its input-fields into the GML ofthat feature
	var annotatedialog = Omeka.Neatline.Omeka.Neatline.jQuery("<form id='annotatedialog'><span>Name:</span><input name='name' id='featurename'/><span>Text:</span><textarea id='featuredescription' name='description'/></form>");
	annotatedialog.appendTo(div);
	
	// we will use these references later to move data in and out of this form
	var name = Omeka.Neatline.jQuery("#featurename"), description = Omeka.Neatline.jQuery("#featuredescription");
	
	// these controls are the meat of the matter. they should be accessed via the 
	// OpenLayers panel.get
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
	        					Omeka.Neatline.jQuery(featurelayer.features).each(function(){this.geometry.transform(Omeka.NeatlineFeatures.spherical,Omeka.NeatlineFeatures.wgs84)});	
		                    var gml = new OpenLayers.Format.GML().write(featurelayer.features);
		                    Omeka.Neatline.jQuery("textarea[name='" + inputNameStem + "[text]']").html(gml);
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
	        			Omeka.Neatline.jQuery("input",annotatedialog).val(feature.attributes.name);
	        			Omeka.Neatline.jQuery("textarea",annotatedialog).val(feature.attributes.description);
	        			Omeka.Neatline.jQuery(annotatedialog).dialog({
	        				"feature": feature,
	        				"title":"Annotate this feature",
	        				"closeOnEscape": true, 
	        				"draggable": true,
	        				"height": 'auto',
	        				"buttons": { "Save": 
	        					function() { 
	        						// save the annotations
	        						feature.attributes.description = description.val();
	        						feature.attributes.name = name.val();
	        						Omeka.Neatline.jQuery(this).dialog("close"); 
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