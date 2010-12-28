if (typeof (Omeka) == 'undefined') {
	Omeka = new Object();
}

if (Omeka.Neatline) { 
	Omeka.Neatline.jQuery = jQuery.noConflict();
}
else {
	Omeka.Neatline = new Object();
	Omeka.Neatline.jQuery = jQuery.noConflict();
}
	
if (!Omeka.NeatlineFeatures) {
	Omeka.NeatlineFeatures = new Array();
}

Omeka.NeatlineFeatures.wgs84 = new OpenLayers.Projection("EPSG:4326");
Omeka.NeatlineFeatures.spherical = new OpenLayers.Projection("EPSG:900913");

Omeka.NeatlineFeatures.initializeWidget = function() {

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

	// a map with very basic controls
	map = new OpenLayers.Map('map', {
		projection : Omeka.NeatlineFeatures.wgs84,
		controls: [new OpenLayers.Control.Navigation(),new OpenLayers.Control.PanZoom(), new OpenLayers.Control.LayerSwitcher()], 
		numZoomLevels : 128
	});
	
	// a simple background
	map.addLayer(new OpenLayers.Layer.OSM("OpenStreetMap"));
	
/*
 * map.addLayer(new OpenLayers.Layer.WMS( "Terraserver", "
 * http://terraservice.net/ogcmap.ashx", {layers: 'DOQ', srs:"EPSG:4326"},
 * {projection: wgs84} ));
 */

	// retrieve our shape information from the editing form
	// notice that we transform out of wgs84 into "Google projection"
	// we'll have to transform back to persist back into the form
	// but that happens in the drawing tools "save" tool
	var gml = Omeka.Neatline.jQuery("textarea[name='" + inputNameStem + "[text]']").val();
	features = gml ? new OpenLayers.Format.GML().read(gml) : new Array();
	Omeka.Neatline.jQuery(features).each(function(){this.geometry.transform(Omeka.NeatlineFeatures.wgs84,Omeka.NeatlineFeatures.spherical)});
	featurelayer = new OpenLayers.Layer.Vector("feature", { styleMap: myStyles, projection: Omeka.NeatlineFeatures.wgs84 });
	if (features) {
		featurelayer.addFeatures(features);
	}
	map.addLayer(featurelayer);
	
	// get some actual drawing controls 
	var panel = Omeka.NeatlineFeatures.createDrawingControlPanel(
			featurelayer,inputNameStem, document.getElementById("mappanel"));
    map.addControl(panel);
    
	var addlayerdialog = Omeka.Neatline.jQuery("#addlayerdialog").dialog( {
		"autoOpen": false,
		"draggable": true,
		"height": 'auto',
		"width": 500,
		"title": "Add a Layer...",
		"closeOnEscape": true,
		"buttons": { "Add": 
				function() { 
					var id = Omeka.Neatline.jQuery("#layerselect")[0].value;
					Omeka.Neatline.jQuery.get("/maps/serviceaddy/" + id, function(serviceaddy){ 
						Omeka.Neatline.jQuery.get("/maps/layername/" + id, function(layername) {
							var label =Omeka.Neatline.jQuery("#layerselect option")[Omeka.Neatline.jQuery("#layerselect")[0].selectedIndex].label;
							map.addLayer(new OpenLayers.Layer.WMS( label, serviceaddy, {"layers": layername}));
						});
					});
					Omeka.Neatline.jQuery(this).dialog("close"); } }
		});
	
    panel.getControlsByName("selectCtrl")[0].activate();
    
    // we now try to zoom to an appropriate ROI
    if (features.length > 0) {  	
    		var coll = new OpenLayers.Geometry.Collection();
        Omeka.Neatline.jQuery(features).each(function() {
        		coll.addComponents([this.geometry]);
        });
    		coll.calculateBounds();
    		map.zoomToExtent(coll.getBounds());
	}
    else {
    		map.zoomToMaxExtent();
    }
    
}

