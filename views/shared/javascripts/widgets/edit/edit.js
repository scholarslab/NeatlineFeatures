if (typeof (Omeka) == 'undefined') {
	Omeka = new Object();
}
if (!Omeka.NeatlineFeatures) {
	Omeka.NeatlineFeatures = new Array();
}

Omeka.NeatlineFeatures.initializeWidget = function() {

	var wgs84 = new OpenLayers.Projection("EPSG:4326");
	var spherical = new OpenLayers.Projection("EPSG:900913");
	
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
		projection : wgs84,
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
	var gml = jQuery("textarea[name='" + inputNameStem + "[text]']").val();
	features = gml ? new OpenLayers.Format.GML().read(gml) : new Array();
	jQuery(features).each(function(){this.geometry.transform(wgs84,spherical)});
	featurelayer = new OpenLayers.Layer.Vector("feature", { styleMap: myStyles, projection: wgs84 });
	if (features) {
		featurelayer.addFeatures(features);
	}
	map.addLayer(featurelayer);
	
	// get some actual drawing controls 
	var panel = Omeka.NeatlineFeatures.createDrawingControlPanel(
			featurelayer,inputNameStem, getElementById("mappanel"));
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
							map.addLayer(new OpenLayers.Layer.WMS( label, serviceaddy, {"layers": layername}));
						});
					});
					jQuery(this).dialog("close"); } }
		});
	
    panel.getControlsByName("selectCtrl")[0].activate();
    
    // we now try to zoom to an appropriate ROI
    if (features.length > 0) {  	
    		var coll = new OpenLayers.Geometry.Collection();
        jQuery(features).each(function() {
        		coll.addComponents([this.geometry]);
        });
    		coll.calculateBounds();
    		map.zoomToExtent(coll.getBounds());
	}
    else {
    		map.zoomToMaxExtent();
    }
    
}

