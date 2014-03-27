/*
 * # Neatline Features
 *
 * Component widget that controls the map.
 *
 * This handles interfacing with OpenLayers, handling editing (if requested),
 * and serializing the data.
 *
 * This widget supports two use cases.
 *
 * ## WMS Service
 *
 * Using a WMS backend implies that there the rest of Neatline is also
 * available. To work this way, do these things:
 *
 * + set `options.map.wmsAddress` in the configuration;
 * + call `loadData()` to pull data into the map;
 * + call `edit()` to enter edit mode (optional, see the documentation for this
 * method).
 *
 * ## Stand-alone
 *
 * This can also be used without the rest of Neatline and without a WMS
 * service. To do this, you'll need to do these things:
 *
 * + leave `options.map.wmsAddress` out of the config, set to `null`, or set to
 * `undefined`;
 * + call `loadLocalData()` to populate the map (see the documentation for that
 * method for details on how to use it); and
 * + call `editJson()` to enter editing mode (optional, see the documentation
 * for this method).
 *
 * ## Editing
 *
 * Turning on editing mode differs, depending on the context. In a Neatline/WMS
 * context, use the `edit()` method; in a stand-alone context, use
 * `editJson()`.
 *
 * ## Configuration
 *
 * There are a lot of configuration options. See the code below for information
 * about them.
 *
 * ## Builder
 *
 * `window.NLFeatures` module/static class has some functions that make setting
 * up the features control and the rest of the stuff around it easier. See the
 * documentation in `nlfeatures-init.coffee` for more information.
 *
 * @package     omeka
 * @subpackage  nlfeatures
 * @author      Scholars' Lab <>
 * @author      Bethany Nowviskie <bethany@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

(function($, undefined) {
    $.widget('nlfeatures.nlfeatures', {
        options: {
            // Markup hooks.
            markup: {
                // The CSS class for the editing toolbar.
                toolbar_class: 'olControlEditingToolbar',
                id_prefix: 'nlf-'
            },

            // Animation constants.
            animation: {
                fade_duration: 500
            },

            // These are options for the `OpenLayers.StyleMap`.
            styles: {
                default_opacity: 0.4,
                default_color: '#ffb80e',
                select_point_radius: 10,
                select_stroke_color: '#ea3a3a',
                point_graphic: {
                    normal   : undefined,
                    selected : undefined
                }
            },

            // These are added to document options for the map.
            map: {
                boundingBox: '90,0,-90,360',
                center: undefined,
                zoom: undefined,
                epsg: undefined,
                wmsAddress: undefined,
                raw_update: undefined
            },

            // If given and set to 'edit' and if the json data is given, this
            // causes the control to be set up to edit that data.
            mode: null,

            // If given, this loads this item and sets up the widget to edit
            // it. If given, it should be a JavaScript object like the
            // `.editJson` method expects.
            json: null,

            // If given, this sets the zoom level for the map.
            zoom: null,

            // If given, this sets the center for the map. This hsould be a JS
            // object with the properties lon, lat, and srs (optional).
            center: null
        },

        /*
         * Grab the instantiate OpenLayers and load data.
         */
        _create: function() {
            var self = this;

            // Ignition.
            this._instantiateOpenLayers();

            // Trackers and buckets.
            this._currentVectorLayers = [];
            this._currentEditItem = null;
            this._currentEditLayer = null;
            this.clickedFeature = null;
            this.idToLayer = {};
            this.requestData = null;

            // Load data. Maybe set edit mode.
            if (typeof this.options.json !== 'undefined' &&
                this.options.json !== null) {
                this.loadLocalData([this.options.json]);
                this.setViewport();
                if (this.options.mode === 'edit') {
                    this.editJson(this.options.json, true);
                }
            } else {
                this.loadData();
            }
        },

        /*
         * Instantiate OpenLayers.
         *
         * This does these things:
         *
         * 1. Instantiate OpenLayers with the current configuration;
         * 2. Set the bounds;
         * 3. Set the projection;
         * 4. Create controls for viewing the map;
         * 5. Attach the WMS service; and
         * 6. Set the center and zoom.
         */
        _instantiateOpenLayers: function() {
            // Set OL global attributes.
            OpenLayers.IMAGE_RELOAD_ATTEMTPS = 3;
            OpenLayers.Util.onImageLoadErrorColor = "transparent";
            OpenLayers.ImgPath = 'http://js.mapbox.com/theme/dark/';

            var tiled;
            var bounds, boundsArray;
            var pureCoverage = true;

            // Pink tile avoidance.
            OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;

            // Make OL compute scale according to WMS spec.
            OpenLayers.DOTS_PER_INCH = 25.4 / 0.28;

            // Set tile image format.
            format = pureCoverage ? 'image/png8' : 'image/png';

            // Build the default bounds array.
            if (this.options.map.boundingBox === undefined) {
                bounds = new OpenLayers.Bounds();
            } else {
                boundsArray = this.options.map.boundingBox.split(',');
                bounds = new OpenLayers.Bounds(
                    parseFloat(boundsArray[0]),
                    parseFloat(boundsArray[1]),
                    parseFloat(boundsArray[2]),
                    parseFloat(boundsArray[3]));
            }

            // Starting options.
            var proj = (this.options.map.epsg !== undefined) ?
                       this.options.map.epsg[0] :
                       'EPSG:4326';
            var controls = [
                new OpenLayers.Control.Attribution(),
                new OpenLayers.Control.Navigation(),
                new OpenLayers.Control.PanZoomBar()
            ];
            if (this.options.mode === 'edit') {
                controls = controls.concat(
                    [
                        new OpenLayers.Control.MousePosition(),
                        new OpenLayers.Control.LayerSwitcher()
                    ]);
            }

            var options = {
                controls: controls,
                maxExtent: bounds,
                maxResolution: 'auto',
                projection: proj,
                units: 'm'
            };

            // Instantiate the map.
            this.map = new OpenLayers.Map(this.element.attr('id'), options);

            if (this.options.map.wmsAddress !== undefined) {
                this.baseLayer = new OpenLayers.Layer.WMS(
                    this.options.name, this.options.map.wmsAddress,
                    {
                        LAYERS: this.options.map.layers,
                        STYLES: '',
                        format: 'image/jpeg',
                        tiled: !pureCoverage,
                        tilesOrigin : this.map.maxExtent.left + ',' + this.map.maxExtent.bottom
                    },
                    {
                        buffer: 0,
                        displayOutsideMaxExtent: true,
                        isBaseLayer: true
                    }
                );
            } else {
                this.baseLayers = this._getBaseLayers();
                if (this.baseLayers[this.options.base_layer] !== undefined) {
                    this.baseLayer = this.baseLayers[this.options.base_layer];
                } else {
                    this.baseLayer = this.baseLayers.osm;
                }
            }

            this.map.addLayers([
                this.baseLayers.osm,
                this.baseLayers.gphy,
                this.baseLayers.gmap,
                this.baseLayers.ghyb,
                this.baseLayers.gsat
            ]);
            if (this.baseLayers.swc !== undefined) {
                this.map.addLayers([
                    this.baseLayers.stn,
                    this.baseLayers.str,
                    this.baseLayers.swc
                ]);
            }
            this.map.setBaseLayer(this.baseLayer);

            // If there is a default bounding box set for the exhibit, construct
            // a second Bounds object to use as the starting zoom target.
            if (this.exists(this.options.default_map_bounds)) {
                boundsArray = this.options.default_map_bounds.split(',');
                bounds = new OpenLayers.Bounds(
                    parseFloat(boundsArray[0]),
                    parseFloat(boundsArray[1]),
                    parseFloat(boundsArray[2]),
                    parseFloat(boundsArray[3]));
            }

            if (this.options.map.center !== undefined) {
                var z = (this.options.map.zoom === undefined) ? 3 : this.options.map.zoom;
                var ll = new OpenLayers.LonLat(this.options.map.center[0],
                                               this.options.map.center[1]);

                this.map.setCenter(ll, z);
            } else {
                // Set starting zoom focus.
                this.map.zoomToExtent(bounds);
            }
        },

        /*
         * This returns the map.
         */
        getMap: function() { return this.map; },

        /*
         * This creates the base layers.
         */
        _getBaseLayers: function() {
            var baseLayers = {};

            baseLayers.gphy = new OpenLayers.Layer.Google(
                "Google Physical",
                {type: google.maps.MapTypeId.TERRAIN}
            );
            baseLayers.gmap = new OpenLayers.Layer.Google(
                "Google Streets",
                {numZoomLevels: 20}
            );
            baseLayers.ghyb = new OpenLayers.Layer.Google(
                "Google Hybrid",
                {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20}
            );
            baseLayers.gsat = new OpenLayers.Layer.Google(
                "Google Satellite",
                {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
            );
            baseLayers.osm = new OpenLayers.Layer.OSM();

            if (OpenLayers.Layer.Stamen !== undefined) {
                var attribution =
                    'Map tiles by <a href="http://stamen.com">Stamen ' +
                    'Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC ' +
                    'BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, ' +
                    'under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a>.';
                baseLayers.swc = new OpenLayers.Layer.Stamen(
                    "Stamen Watercolor",
                    {
                        provider: 'watercolor',
                        attribution: attribution,
                        tileOptions: { crossOriginKeyword: null }
                    }
                );
                baseLayers.stn = new OpenLayers.Layer.Stamen(
                    "Stamen Toner",
                    {
                        provider: 'toner',
                        attribution: attribution,
                        tileOptions: { crossOriginKeyword: null }
                    }
                );
                baseLayers.str = new OpenLayers.Layer.Stamen(
                    "Stamen Terrain",
                    {
                        provider: 'terrain',
                        attribution: attribution,
                        tileOptions: { crossOriginKeyword: null }
                    }
                );
            }

            return baseLayers;
        },

        /*
         * This loads data from the AJAX data sources.
         */
        loadData: function() {
            var self = this;

            this._resetData();

            // Abort the request if it is running.
            if (this.exists(this.requestData)) {
                this.requestData.abort();
            }

            // Hit the json server.
            if (this.options.dataSources !== undefined &&
                this.options.dataSources.maps !== undefined) {
                this.requestData = $.ajax({
                    url: this.options.dataSources.map,
                    dataType: 'json',

                    success: function(data) {
                        // Build the new layers and add default click controls.
                        self._buildVectorLayers(data);
                        self._addClickControls();

                        // If a layer was being edited before the save,
                        // make that layer the active edit layer again.
                        if (self.exists(self._currentEditItem)) {
                            self.editJson(self._currentEditItem, true);
                        }
                    }
                });
            }
        },

        /*
         * This loads raw data directly.
         *
         * `data` should be a list of objects with these fields:
         * - `id`
         * - `title`
         * - `color` (optional)
         * - `geo`
         */
        loadLocalData: function(data) {
            var self = this;

            this._resetData();

            this._buildVectorLayers(data);
            if (this.options.mode === 'edit') {
                this._addClickControls();
            }
        },

        /*
         * This clears the current features from the map and removes the
         * controls.
         */
        _resetData: function() {
            var self = this;

            // If there are existing click and highlight controls, destroy them.
            this._removeControls();

            // Clear existing vectors.
            $.each(this._currentVectorLayers, function(i, layer) {
                self.map.removeLayer(layer);
                layer.destroy();
            });

            // Empty out the containers.
            this._currentVectorLayers = [];
            this.idToLayer = {};
        },

        /*
         * This takes the input data and actually builds the vector layers,
         * features, and geometries.
         *
         * Invalid WKT geometries are skipped, and empty features aren't added,
         * either.
         *
         * `data` should be a list of objects with these fields:
         * - `id`
         * - `title`
         * - `color` (optional)
         * - `geo`
         */
        _buildVectorLayers: function(data) {
            var self         = this;
            var needsUpgrade = false;

            // Instantiate associations objects.
            this.idToLayer = {};
            this.layerToId = {};

            $.each(data, function(i, item) {
                var itemId       = item.id;
                var color        = (item.color !== '') ?
                                   item.color          :
                                   self.options.styles.default_color;
                var style        = self._getStyleMap(color);
                var vectorLayer  = new OpenLayers.Layer.Vector(item.title, {
                    styleMap: style
                });

                if (item.geo !== null && item.geo !== undefined) {
                    var kml      = new OpenLayers.Format.KML();
                    var features = kml.read(item.geo);

                    if (features.length === 0) {
                        var reader = new OpenLayers.Format.WKT();
                        $.each(item.geo.split('|'), function(i, wkt) {
                            if (reader.read(wkt) !== undefined) {
                                var geometry = new OpenLayers.Geometry.fromWKT(wkt);
                                var feature  = new OpenLayers.Feature.Vector(geometry);
                                features.push(feature);
                            }
                        });
                        needsUpgrade = (features.length > 0);
                    }

                    if (features.length > 0) {
                        vectorLayer.addFeatures(features);
                    }
                }
                vectorLayer.setMap(self.map);

                // Add to associations.
                self.idToLayer[itemId] = vectorLayer;
                self.layerToId[vectorLayer.id] = itemId;

                // Add to the layers array and add to map.
                self._currentVectorLayers.push(vectorLayer);
                self.map.addLayer(vectorLayer);
            });

            if (needsUpgrade) {
                // Have to wait for the events to get wired up.
                setTimeout(function() {
                    self.element.trigger('refresh.nlfeatures');
                }, 250);
            }
        },

        /*
         * This selects a feature.
         */
        selectFeature: function(feature) {
            if (this.isFocusLocked()) {
                return;
            }

            this.modifyFeatures.selectFeature(feature);
            this.clickControl.highlight(feature);
            this.listenToFeature(feature);
            this.clickedFeature = feature;

            this.element.trigger('select.nlfeatures', feature);
        },

        /*
         * This returns the SVG element for the feature.
         */
        getFeatureElement: function(feature) {
            var gid;
            gid = ('#' + feature.geometry.id).replace(/\./g, '\\.');
            return $(gid);
        },

        /*
         * This attaches mouse event listeners, if there aren't already some.
         */
        listenToFeature: function(feature) {
            var fid, self, el;
            fid = feature.id;
            self = this;

            el = this.getFeatureElement(feature);
            el.on({
                mousedown : function() { self.lockFocus(feature); },
                mouseup   : function() { self.unlockFocus(feature); }
            });
        },

        unlistenToFeature: function(feature, el) {
            var self;
            self = this;

            if (el === null || el === undefined) {
                el = this.getFeatureElement(feature);
            }
            el.off('mouseup').off('mousedown');
        },

        /*
         * This deselects the current feature.
         */
        deselectFeature: function(feature, force) {
            if (feature === null || feature === undefined) {
                feature = this.clickedFeature;
            }
            if (this.isFocusLocked(feature) && !force) {
                return;
            }

            this.clickControl.unhighlight(feature);
            this.modifyFeatures.unselectFeature(feature);
            this.unlistenToFeature(feature);
            this.resetModifyFeatures();
            if (feature.nlfeatures) {
                feature.nlfeatures.focusLocked = false;
            }

            if (feature === this.clickedFeature) {
                this.clickedFeature = null;
            }

            this.element.trigger('deselect.nlfeatures', feature);
        },

        lockFocus: function(feature) {
            if (feature === null || feature === undefined) {
                feature = this.clickedFeature;
            }

            if (feature !== null && feature !== undefined) {
                if (feature.nlfeatures === null || feature.nlfeatures === undefined) {
                    feature.nlfeatures = {
                        focusLocked: true
                    };
                } else {
                    feature.nlfeatures.focusLocked = true;
                }
            }
        },

        unlockFocus: function(feature) {
            if (feature === null || feature === undefined) {
                feature = this.clickedFeature;
            }

            if (feature !== null && feature !== undefined &&
                feature.nlfeatures !== null &&
                feature.nlfeatures !== undefined) {
                feature.nlfeatures.focusLocked = false;
            }
        },

        isFocusLocked: function(feature) {
            if (feature === null || feature === undefined) {
                feature = this.clickedFeature;
            }
            return (feature !== null && feature !== undefined &&
                    feature.nlfeatures !== null &&
                    feature.nlfeatures !== undefined &&
                    feature.nlfeatures.focusLocked);
        },

        /*
         * This adds the click control (`OpenLayers.Control.SelectFeature`) and
         * handlers for them.
         */
        _addClickControls: function() {
            var self = this;

            // If there are existing click and highlight controls, destroy them.
            this._removeControls();

            this.clickControl = new OpenLayers.Control.SelectFeature(this._currentVectorLayers, {
                hover: true,
                highlightOnly: true,

                overFeature: function(feature) {
                    // This checks for ad-hoc features created by OL for edit
                    // handles on a real feature.
                    if (feature.geometry.parent !== null &&
                        feature.geometry.parent !== undefined) {
                        return;
                    }

                    if (self.modifyFeatures !== undefined &&
                        self.clickedFeature !== null &&
                        self.clickedFeature !== undefined &&
                        feature.id !== self.clickedFeature.id) {

                        self.deselectFeature();
                    }

                    if (self.modifyFeatures !== undefined &&
                        (self.clickedFeature === null ||
                         self.clickedFeature === undefined ||
                         feature.id !== self.clickedFeature.id)) {

                        self.selectFeature(feature);
                    }
                },

                outFeature: function(feature) {
                    return false;
                }

            });

            // Add and activate.
            this.map.addControl(this.clickControl);
            this.clickControl.activate();

            // Handle clicks on the map to remove focus.
            this.map.events.register('click', this.map, function(e) {
                if (self.clickedFeature !== null &&
                    self.clickedFeature !== undefined) {
                    self.deselectFeature(self.clickedFeature, true);
                }
            });
        },

        /*
         * This removes all defined controls.
         */
        _removeControls: function() {
            if (this.modifyFeatures !== undefined) {
                this.map.removeControl(this.modifyFeatures);
                this.modifyFeatures.destroy();
                delete this.modifyFeatures;
            }

            if (this.editToolbar !== undefined) {
                this.map.removeControl(this.editToolbar);
                this.editToolbar.destroy();
                delete this.editToolbar;
            }

            if (this.clickControl !== undefined) {
                this.map.removeControl(this.clickControl);
                this.clickControl.destroy();
                delete this.clickControl;
            }

            if (this.highlightControl !== undefined) {
                this.map.removeControl(this.highlightControl);
                this.highlightControl.destroy();
                delete this.highlightControl;
            }
        },

        /*
         * This triggers edit mode.
         *
         * `item` is a jQuery DOM element containing the information about the
         * element to be edited.
         *
         * `immediate` is a boolean indicating whether to fade the controls in
         * or not.
         *
         * This version of the function is only useful within the context of
         * the full Neatline plugin. Other use cases should use `editJson()`
         * (which this method calls).
         */
        edit: function(item, immediate) {
            var js = {
                id: item.attr('recordid'),
                name: item.find('span.item-title-text').text()
            };
            this.editJson(js, immediate);
        },

        /*
         * This actually sets up the editing function.
         *
         * `item` is a JS object with these parameters:
         *
         * + `id`
         * + `name`
         *
         * `immediate` is a boolean indicating whether to fade the controls in
         * or not.
         *
         * This sets the current editing layer and sets up the panel of
         * controls for drawing new features or editing existing ones. It also
         * sets up an `editgeometry` widget and handlers for its events.
         *
         * If `options.map.raw_update` is set, this sets up event handlers to
         * propagate changes to it.
         */
        editJson: function(item, immediate) {
            var self = this;

            if (this.highlightControl !== undefined) {
                this.highlightControl.deactivate();
            }

            // Get the id of the item and try to fetch the layer.
            var itemId = item.id;
            this._currentEditLayer = this.idToLayer[itemId];
            this._currentEditId = itemId;

            // Record the id of the current edit layer, so that the layer can be
            // reactivated as the current layer after save.
            this._currentEditItem = item;

            // If the item does not have an existing vector layer, create a new one.
            if (!this._currentEditLayer) {
                var itemName = item.name;
                this._currentEditLayer = new OpenLayers.Layer.Vector(itemName);
                this.map.addLayer(this._currentEditLayer);
                this._currentEditLayer.setMap(this.map);

                // Push the edit layer onto the non-base layers stack.
                this._currentVectorLayers.push(this._currentEditLayer);
                this.idToLayer[itemId] = this._currentEditLayer;
                this.layerToId[this._currentEditLayer.id] = itemId;
            }

            // Create the controls and toolbar.
            var panelControls = [
                // Panning.
                new OpenLayers.Control.Navigation(),

                // Draw lines.
                new OpenLayers.Control.DrawFeature(this._currentEditLayer, OpenLayers.Handler.Path, {
                    displayClass: 'olControlDrawFeaturePath',
                    featureAdded: function() {
                        self.element.trigger('featureadded.nlfeatures');
                    }
                }),

                // Draw points.
                new OpenLayers.Control.DrawFeature(this._currentEditLayer, OpenLayers.Handler.Point, {
                    displayClass: 'olControlDrawFeaturePoint',
                    featureAdded: function() {
                        self.element.trigger('featureadded.nlfeatures');
                    }
                }),

                // Draw polygons.
                new OpenLayers.Control.DrawFeature(this._currentEditLayer, OpenLayers.Handler.Polygon, {
                    displayClass: 'olControlDrawFeaturePolygon',
                    featureAdded: function() {
                        self.element.trigger('featureadded.nlfeatures');
                    }
                })
            ];


            // Instantiate the modify feature control.
            this.modifyFeatures = new OpenLayers.Control.ModifyFeature(this._currentEditLayer, {
                // OL marks this callback as deprecated, but I can't find
                // any alternative and kosher way of hooking on to this.
                onModification: function() {
                    self.element.trigger('featureadded.nlfeatures');
                },

                standalone: true
            });
            // Instantiate the edit toolbar.
            this.editToolbar = new OpenLayers.Control.Panel({
                defaultControl: panelControls[0],
                displayClass: this.options.markup.toolbar_class
            });

            // Add the controls.
            this.editToolbar.addControls(panelControls);

            // Show the toolbar, add and activate the other controls.
            this.map.addControl(this.editToolbar);
            this.map.addControl(this.modifyFeatures);
            this.modifyFeatures.activate();

            // Instantiate the geometry editor.
            this.element.editfeatures({
                markup: {
                    id_prefix: this.options.markup.id_prefix
                }
            });
            // On update.
            this.element.bind({
                'update.nlfeatures': function(event, obj) {
                    // Default to reshape.
                    self.modifyFeatures.mode = OpenLayers.Control.ModifyFeature.RESHAPE;

                    // Rotation.
                    if (obj.rotate) {
                        self.modifyFeatures.mode |= OpenLayers.Control.ModifyFeature.ROTATE;
                    }

                    // Resize.
                    if (obj.scale) {
                        self.modifyFeatures.mode |= OpenLayers.Control.ModifyFeature.RESIZE;
                    }

                    // Drag.
                    if (obj.drag) {
                        self.modifyFeatures.mode |= OpenLayers.Control.ModifyFeature.DRAG;
                    }

                    // If rotate or drag, pop off reshape.
                    if (obj.drag || obj.rotate) {
                        self.modifyFeatures.mode &= -OpenLayers.Control.ModifyFeature.RESHAPE;
                    }

                    var feature = self.modifyFeatures.feature;

                    // If there is a selected feature, unselect and reselect it to apply
                    // the new configuration.
                    if (self.exists(feature)) {
                        self.modifyFeatures.unselectFeature(feature);
                        self.modifyFeatures.selectFeature(feature);
                    }
                },

                'lockfocus.nlfeatures': function() {
                    self.lockFocus();
                },

                'unlockfocus.nlfeatures': function() {
                    self.unlockFocus();
                },

                'delete.nlfeatures': function() {
                    if (self.modifyFeatures.feature) {
                        var feature = self.modifyFeatures.feature;
                        self.clickedFeature = null;
                        self.modifyFeatures.unselectFeature(feature);
                        self._currentEditLayer.destroyFeatures([ feature ]);
                    }
                }
            });

            // Only do the fade if the form open does not coincide with another
            // form close.
            if (!immediate) {
                // Insert the edit geometry button.
                this.element.editfeatures('showButtons', immediate);

                // Fade up the toolbar.
                $('.' + this.options.markup.toolbar_class).animate({
                    'opacity': 1
                }, this.options.animation.fade_duration);
            } else {
                // Pop up the toolbar.
                $('.' + this.options.markup.toolbar_class).css('opacity', 1);
            }

            // If there is an update target for raw edits, wire up the handlers
            // here.
            if (this.options.map.raw_update !== undefined) {
                var update_target = this.options.map.raw_update;
                this.element.bind({
                    'featureadded.nlfeatures': function() {
                        self.updateRaw();
                    },
                    'update.nlfeatures': function() {
                        self.updateRaw();
                    },
                    'delete.nlfeatures': function() {
                        self.updateRaw();
                    }
                });
            }

            // If the last selected features is among the features in the
            // new currentEditLayer, mark it as selected by default. Notably,
            // this would be the case of the edit flow was triggered by a
            // feature click in the editor.
            var inLayer = false;
            $.each(this._currentEditLayer.features, function(i, feature) {
                if (feature == self.clickedFeature) {
                    inLayer = true;
                }
            });

            if (inLayer) {
                this.modifyFeatures.selectFeature(this.clickedFeature);
            }
        },

        /*
         * This resets the modes on ModifyFeatures.
         */
        resetModifyFeatures: function() {
            this.modifyFeatures.mode = OpenLayers.Control.ModifyFeature.RESHAPE;
        },

        /*
         * This sets up the control to edit a JSON object, taking care of some
         * of the other tasks like loading the data and setting the viewport.
         *
         * `item` is a JS object such as what `.editJson` expected.
         */
         editJsonItem: function(item) {
             this.loadLocalData([item]);
             this.setViewport();
             this.editJson(item, true);
         },

        /*
         * This sets the viewport to either the user's current location or to
         * the a view of the features added to the map.
         */
        setViewport: function() {
            if (this.viewportOptionsValid()) {
                this._setViewportFromOptions();
            } else {
                this._setViewportFromData();
            }
        },

        /*
         * This tests whether the viewport options are valid.
         */
        viewportOptionsValid: function() {
            var good = true;

            good = good && (this.options.zoom !== null && this.options.zoom);
            good = good && this.options.zoom > 0;

            good = good && (this.options.center !== null && this.options.center !== undefined);
            good = good && (this.options.center.lon !== null && this.options.center.lon !== undefined);
            good = good && (this.options.center.lat !== null && this.options.center.lat !== undefined);
            good = good && !isNaN(parseFloat(this.options.center.lon));
            good = good && !isNaN(parseFloat(this.options.center.lat));

            return good;
        },

        /*
         * This sets the data from the options.
         */
        _setViewportFromOptions: function() {
            var zoom   = this.options.zoom,
                center = this.options.center,
                lonlat = new OpenLayers.LonLat(center.lon, center.lat);
            var proj, wsg;

            if (center.srs !== null && center.srs !== undefined) {
                wsg    = new OpenLayers.Projection(center.srs);
                proj   = this.map.getProjectionObject();
                lonlat = lonlat.transform(wsg, proj);
            }

            this.map.setCenter(lonlat, zoom, false, false);
        },

        /*
         * This sets the viewport from the data.
         */
        _setViewportFromData: function() {
            var self, featureCount, i, vlen, vlayer, j, flen, geometry, bounds, geolocate;

            self = this;

            bounds = new OpenLayers.Bounds();
            featureCount = 0;
            vlen = this._currentVectorLayers.length;
            for (i=0; i<vlen; i++) {
                vlayer = this._currentVectorLayers[i];
                flen = vlayer.features.length;
                for (j=0; j<flen; j++) {
                    featureCount++;
                    geometry = vlayer.features[j].geometry;
                    bounds.extend(geometry.getBounds());
                }
            }

            if (featureCount === 0) {
                geolocate = new OpenLayers.Control.Geolocate({
                    bind: true,
                    watch: false
                });
                geolocate.events.on({
                    locationfailed: function() {
                          self.map.setCenter(
                              new OpenLayers.LonLat(-8738850.21367, 4584105.47978),
                              3,
                              false,
                              false);
                      }
                });
                this.map.addControl(geolocate);
                this.map.zoomTo(3);
                geolocate.activate();
            } else {
                this.map.zoomToExtent(bounds, false);
            }
        },

        /*
         * This updates the raw target element's value. Newlines are added to
         * WKTs to make them more readable.
         */
        updateRaw: function() {
            var updateEl = this.options.map.raw_update;
            if (this.exists(updateEl)) {
                var text = this.getWktForSave();
                text = text.replace(/\|/g, "|\n");
                updateEl.val(text);
            }
        },

        /*
         * This turns off editing without saving anything.
         */
        endEditWithoutSave: function(id, immediate) {
            // Before OpenLayers axes the toolbar controls, clone the div so
            // that it can be faded down in unison with the buttons.
            var toolbarClone = $('.' + this.options.markup.toolbar_class).clone();

            // Remove controls.
            this.modifyFeatures.unselectFeature(this.clickedFeature);
            this.map.removeControl(this.modifyFeatures);
            this.map.removeControl(this.editToolbar);

            // If the form is immediately switching to another form, do not do
            // the fade down, as as to avoid a little opacity dip in the buttons
            // when the form switches.
            if (!immediate) {
                this.element.editfeatures('hideButtons');

                // Reinsert the dummy toolbar and fade it down.
                this.element.append(toolbarClone);
                toolbarClone.animate({
                    'opacity': 0
                }, this.options.animation.fade_duration, function() {
                    toolbarClone.remove();
                });
            }

            // Reactivate the default selection controls.
            this._addClickControls();

            if (this._currentEditLayer.features.length === 0) {
                // Pop off the layer, remove the id-layer association.
                this.map.removeLayer(this._currentEditLayer);
                this._currentVectorLayers.remove(this._currentEditLayer);
                delete this.idToLayer[id];
                delete this.layerToId[this._currentEditLayer.id];
                this._currentEditLayer = null;
            }

            // Clear the item tracker.
            this._currentEditItem = null;
        },

        /*
         * This returns a code for the current base layer.
         */
        getBaseLayerCode: function() {
            var c, clen, code, codes, i, name;

            name = this.map.baseLayer.name;
            code = null;
            codes = [
                'osm', 'gphy', 'gmap', 'ghyb', 'gsat', 'swc', 'stn', 'str'
            ];

            for (i=0, clen=codes.length; i<clen; i++) {
                c = codes[i];
                if (name === this.baseLayers[c].name) {
                    code = c;
                    break;
                }
            }

            return code;
        },

        /*
         * This returns the WKT for the current edit layer.
         *
         * The current feature is temporarily unselected because otherwise the
         * WKT contains points for the selection handles.
         */
        getWktForSave: function() {
            var wkts = [];

            // Push the wkt's onto the array.
            this._getFeatures(function(i, feature) {
                wkts.push(feature.geometry.toString());
            });

            return wkts.join('|');
        },

        /*
         * This returns the features as KML.
         */
        getKml: function() {
            var kml      = new OpenLayers.Format.KML();
            var features = [];

            this._getFeatures(function (i, f) {
                features.push(f);
            });

            return kml.write(features);
        },

        /*
         * This gets the current set of features, after deselecting the clicked
         * feature.
         *
         * This handles them with the callback, which gets passed to `$.each`.
         */
        _getFeatures: function(callback) {
            var isClicked = this.exists(this.clickedFeature);

            if (isClicked) {
                this.modifyFeatures.unselectFeature(this.clickedFeature);
            }

            $.each(this._currentEditLayer.features, callback);

            if (isClicked) {
                this.modifyFeatures.selectFeature(this.clickedFeature);
            }
        },

        /*
         * This gets the current extent of the map as a string.
         */
        getExtentForSave: function() {
            return this.map.getExtent().toString();
        },

        /*
         * This gets the current zoom for the map.
         */
        getZoomForSave: function() {
            return this.map.getZoom();
        },

        /*
         * This zooms to one item's features.
         *
         * `id` the ID of the item (from the `loadData()` or `loadLocalData()`
         * input parameter) to zoom to.
         */
        zoomToItemVectors: function(id) {
            var layer = this.idToLayer[id];

            if (this.exists(layer) && layer.features.length > 0) {
                this.map.zoomToExtent(layer.getDataExtent());
            }
        },

        /*
         * This builds and returns a new `OpenLayers.StyleMap` based on the
         * options.
         */
        _getStyleMap: function(fillColor) {
            return new OpenLayers.StyleMap({
                'default': new OpenLayers.Style({
                    fillColor: fillColor,
                    fillOpacity: this.options.styles.default_opacity,
                    strokeColor: fillColor,
                    strokeWidth: 1,
                    pointRadius: 10
                    // graphicWidth: 15,
                    // graphicHeight: 48,
                    // externalGraphic: this.options.styles.point_graphic.normal
                }),
                'select': new OpenLayers.Style({
                    fillColor: fillColor,
                    fillOpacity: this.options.styles.default_opacity,
                    strokeColor: this.options.styles.select_stroke_color,
                    strokeWidth: 2,
                    pointRadius: 10
                    // graphicWidth: 15,
                    // graphicHeight: 48,
                    // externalGraphic: this.options.styles.point_graphic.selected
                })
            });
        },

        /*
         * This sets the color for an item.
         */
        setItemColor: function(color) {
            // Rebuild the style map.
            this._currentEditLayer.styleMap = this._getStyleMap(color);

            // Rerender the layer to manifest the change.
            this._currentEditLayer.redraw();
        },

        /*
         * These are some query functions to call during testing.
         */

         /*
          * This gets the current center of the map reprojected into EPSG:4326
          * and returned as an object with `lat` and `lon` properties.
          */
        getCenterLonLat: function() {
            var wsg  = new OpenLayers.Projection('EPSG:4326'),
                proj = this.map.getProjectionObject();
            return this.map.getCenter().transform(proj, wsg);
        },

        /*
         * This moves the center-point and zoom to the place specified.
         */
        setCenterLonLat: function(lon, lat) {
            var lonLat = new OpenLayers.LonLat(lon, lat),
                wsg    = new OpenLayers.Projection('EPSG:4326'),
                proj   = this.map.getProjectionObject();
            return this.map.panTo(lonLat.transform(wsg, proj));
        },

         /*
          * This sets the zoom level.
          */
         setZoom: function(zoom) {
             return this.map.zoomTo(zoom);
         },

        /*
         * This tests whether the map has any layer with a point feature.
         */
        hasPoint: function() {
            return this.hasFeature('OpenLayers.Geometry.Point');
        },

        /*
         * This tests whether the map has any layer with a line feature.
         */
        hasLine: function() {
            return this.hasFeature('OpenLayers.Geometry.LineString');
        },

        /*
         * This tests whether the map has any layer with a polygon feature.
         */
        hasPolygon: function() {
            return this.hasFeature('OpenLayers.Geometry.Polygon');
        },

        /*
         * This tests whether any layer has a given feature type.
         *
         * `className` is the name of the class of the feature (taken from the
         * `CLASS_NAME` property.
         */
        hasFeature: function(className) {
            result = false;

            $.each(this._currentVectorLayers, function(i, layer) {
                $.each(layer.features, function(j, feature) {
                    result = result || (feature.geometry.CLASS_NAME == className);
                });
            });

            return result;
        },

        /*
         * This method provides a more reliable way to test whether a value is not undefined and not null.
         *
         * This duplicates the CoffeeScript `?` operator.
         */
        exists: function(slot) {
            return (typeof slot !== 'undefined' && slot !== null);
        },

        /*
         * This returns the saved zoom setting.
         */
        getSavedZoom: function() {
            return this.options.zoom;
        },

        /*
         * This returns the saved center setting. This will either be null or
         * an object the lat and lon properties.
         */
        getSavedCenter: function() {
            return this.options.center;
        },

        /*
         * This saves the current viewport to the options.
         */
        saveViewport: function() {
            var center = this.map.getCenter();
            var zoom   = this.map.getZoom();

            this.options.zoom   = zoom;
            this.options.center = {
                lon: center.lon,
                lat: center.lat
            };
        }

    });
})( jQuery );
