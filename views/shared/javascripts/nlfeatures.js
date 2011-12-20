/*
 * Component widget that controls the map. Instantiated by the parent Neatline
 * widget.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package     omeka
 * @subpackage  neatline
 * @author      Scholars' Lab <>
 * @author      Bethany Nowviskie <bethany@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

(function($, undefined) {
    $.widget('neatline.nlfeatures', {
        options: {
            mode: 'edit',
            wkt_delimiter: '|',

            // Markup hooks.
            markup: {
                toolbar_class: 'olControlEditingToolbar'
            },

            // Animation constants.
            animation: {
                fade_duration: 500
            },

            // Hexes.
            colors: {
                neatline_purple: '#724E85',
                highlight_red: '#d04545'
            },

            styles: {
                default_opacity: 0.4,
                default_color: '#ffb80e',
                select_point_radius: 10,
                select_stroke_color: '#ea3a3a'
            },

            // These are added to document options that may be taken from a
            // Neatline global or something.
            map: {
                boundingBox: '90,0,-90,360',
                center: undefined,
                zoom: undefined,
                epsg: undefined,
                wmsAddress: undefined,
                raw_update: undefined
            }
        },

        /*
         * Grab the Neatline global, shell out trackers, startup.
         */
        _create: function() {
            var self = this;

            // Getters.
            if (window.Neatline !== undefined) {
                this.params = Neatline;
            } else {
                this.params = this.options;
            }

            // Ignition.
            this._instantiateOpenLayers();

            // Trackers and buckets.
            this._currentVectorLayers = [];
            this._currentEditItem = null;
            this._currentEditLayer = null;
            this._clickedFeature = null;
            this.idToLayer = {};
            this.requestData = null;

            // Load data.
            this.loadData();
        },

        /*
         * Grab the Neatline global, shell out trackers, startup.
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
            if (this.params.map.boundingBox === undefined) {
                bounds = new OpenLayers.Bounds();
            } else {
                boundsArray = this.params.map.boundingBox.split(',');
                bounds = new OpenLayers.Bounds(
                    parseFloat(boundsArray[0]),
                    parseFloat(boundsArray[1]),
                    parseFloat(boundsArray[2]),
                    parseFloat(boundsArray[3])
                );
            }

            // Starting options.
            var proj = (this.params.map.epsg !== undefined) ?
                       this.params.map.epsg[0] :
                       'EPSG:4326';
            var options = {
                controls: [
                  new OpenLayers.Control.PanZoomBar(),
                  new OpenLayers.Control.Permalink('permalink'),
                  new OpenLayers.Control.MousePosition(),
                  new OpenLayers.Control.LayerSwitcher(),
                  new OpenLayers.Control.Navigation(),
                  new OpenLayers.Control.ScaleLine()
                ],
                maxExtent: bounds,
                maxResolution: 'auto',
                projection: proj,
                units: 'm'
            };

            // Instantiate the map.
            this.map = new OpenLayers.Map(this.element.attr('id'), options);

            if (this.params.map.wmsAddress !== undefined) {
                this.baseLayer = new OpenLayers.Layer.WMS(
                    this.params.name, this.params.map.wmsAddress,
                    {
                        LAYERS: this.params.map.layers,
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
                this.baseLayer = new OpenLayers.Layer.OSM();
            }

            this.map.addLayers([this.baseLayer]);

            // If there is a default bounding box set for the exhibit, construct
            // a second Bounds object to use as the starting zoom target.
            if (this.params.default_map_bounds !== null &&
                this.params.default_map_bounds !== undefined) {
                boundsArray = this.params.default_map_bounds.split(',');
                bounds = new OpenLayers.Bounds(
                    parseFloat(boundsArray[0]),
                    parseFloat(boundsArray[1]),
                    parseFloat(boundsArray[2]),
                    parseFloat(boundsArray[3])
                );
            }


            if (this.params.map.center !== undefined) {
                var z = (this.params.map.zoom === undefined) ? 3 : this.params.map.zoom;
                var ll = new OpenLayers.LonLat(this.params.map.center[0],
                                               this.params.map.center[1]);

                this.map.setCenter(ll, z);
            } else {
                // Set starting zoom focus.
                this.map.zoomToExtent(bounds);
            }
        },

        loadData: function() {
            var self = this;

            this._resetData();

            // Abort the request if it is running.
            if (this.requestData !== null) {
                this.requestData.abort();
            }

            // Hit the json server.
            if (this.params.dataSources !== undefined &&
                this.params.dataSources.maps !== undefined) {
                this.requestData = $.ajax({
                    url: this.params.dataSources.map,
                    dataType: 'json',

                    success: function(data) {
                        // Build the new layers and add default click controls.
                        self._buildVectorLayers(data);
                        self._addClickControls();

                        // If a layer was being edited before the save,
                        // make that layer the active edit layer again.
                        if (self._currentEditItem !== null) {
                            self.editJson(self._currentEditItem, true);
                        }
                    }
                });
            }
        },

        /*
         * data should be a list of objects with these fields:
         * - id
         * - title
         * - color (optional)
         * - wkt
         */
        loadLocalData: function(data) {
            var self = this;

            this._resetData();

            this._buildVectorLayers(data);
            this._addClickControls();
        },

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

        _buildVectorLayers: function(data) {
            var self = this;

            // Instantiate associations objects.
            this.idToLayer = {};
            this.layerToId = {};

            $.each(data, function(i, item) {
                // Get the id of the item.
                var itemId = item.id;

                // Try to get a color from the JSON, revert to default if no color is set..
                var color = (item.color !== '') ? item.color : self.options.styles.default_color;

                // Build the layer styles.
                var style = self._getStyleMap(color);

                // Build the layers.
                var vectorLayer = new OpenLayers.Layer.Vector(item.title, {
                    styleMap: style
                });

                // Empty array to hold features objects.
                var features = [];

                // Build the features.
                $.each(item.wkt.split(self.options.wkt_delimiter), function(i, wkt) {
                    if (wkt !== "") {
                        var geometry = OpenLayers.Geometry.fromWKT(wkt);
                        if (geometry !== undefined) {
                            var feature = new OpenLayers.Feature.Vector(geometry);
                            features.push(feature);
                        }
                    }
                });

                // Add the vectors to the layer.
                if (features.length > 0) {
                    vectorLayer.addFeatures(features);
                }
                vectorLayer.setMap(self.map);

                // Add to associations.
                self.idToLayer[itemId] = vectorLayer;
                self.layerToId[vectorLayer.id] = itemId;

                // Add to the layers array and add to map.
                self._currentVectorLayers.push(vectorLayer);
                self.map.addLayer(vectorLayer);
            });
        },

        _addClickControls: function() {
            var self = this;

            // If there are existing click and highlight controls, destroy them.
            this._removeControls();

            this.clickControl = new OpenLayers.Control.SelectFeature(this._currentVectorLayers, {
                box: true,

                onSelect: function(feature) {
                    // Store the feature in the tracker.
                    self._clickedFeature = feature;

                    // Trigger out to the deployment code.
                    self.element.trigger('featureclick.neatline', {}, {
                        'itemId': self.layerToId[feature.layer.id]
                    });

                    if (self.modifyFeatures !== undefined) {
                        self.modifyFeatures.selectFeature(feature);
                    }
                },

                onUnselect: function(feature) {
                    if (self.modifyFeatures !== undefined) {
                        self.modifyFeatures.unselectFeature(feature);
                    }
                }
            });

            // Add and activate.
            this.map.addControl(this.clickControl);
            this.clickControl.activate();
        },

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

        edit: function(item, immediate) {
            var js = {
                id: item.attr('recordid'),
                name: item.find('span.item-title-text').text()
            };
            this.editJson(js, immediate);
        },

        /*
         * This actually sets up the editing function. It expects item to be a
         * JS object with these parameters:
         *
         * * id
         * * name
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
                        self.element.trigger('featureadded.neatline');
                    }
                }),

                // Draw points.
                new OpenLayers.Control.DrawFeature(this._currentEditLayer, OpenLayers.Handler.Point, {
                    displayClass: 'olControlDrawFeaturePoint',
                    featureAdded: function() {
                        self.element.trigger('featureadded.neatline');
                    }
                }),

                // Draw polygons.
                new OpenLayers.Control.DrawFeature(this._currentEditLayer, OpenLayers.Handler.Polygon, {
                    displayClass: 'olControlDrawFeaturePolygon',
                    featureAdded: function() {
                        self.element.trigger('featureadded.neatline');
                    }
                })
            ];

            // Instantiate the modify feature control.
            this.modifyFeatures = new OpenLayers.Control.ModifyFeature(this._currentEditLayer, {
                // OL marks this callback as deprecated, but I can't find
                // any alternative and kosher way of hooking on to this.
                onModification: function() {
                    self.element.trigger('featureadded.neatline');
                },

                standalone: true
            });

            // Instantiate the edit toolbar.
            this.editToolbar = new OpenLayers.Control.Panel({
                defaultControl: panelControls[0],
                displayClass: 'olControlEditingToolbar'
            });

            // Add the controls.
            this.editToolbar.addControls(panelControls);

            // Show the toolbar, add and activate the other controls.
            this.map.addControl(this.editToolbar);
            this.map.addControl(this.modifyFeatures);
            this.modifyFeatures.activate();

            // Instantiate the geometry editor.
            this.element.editgeometry();
            // On update.
            this.element.bind('update.neatline',
                function(event, obj) {
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
                    if (feature !== null) {
                        self.modifyFeatures.unselectFeature(feature);
                        self.modifyFeatures.selectFeature(feature);
                    }
                });

            this.element.bind('delete.neatline',
                function() {
                    if (self.modifyFeatures.feature) {
                        var feature = self.modifyFeatures.feature;
                        self._clickedFeature = null;
                        self.modifyFeatures.unselectFeature(feature);
                        self._currentEditLayer.destroyFeatures([ feature ]);
                    }
                });

            // Only do the fade if the form open does not coincide with
            // another form close.
            if (!immediate) {
                // Insert the edit geometry button.
                this.element.editgeometry('showButtons', immediate);

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
            if (this.params.map.raw_update !== undefined) {
                var update_target = this.params.map.raw_update;
                this.element.bind({
                    'featureadded.neatline': function() {
                        self.updateRaw();
                    },
                    'update.neatline': function() {
                        self.updateRaw();
                    },
                    'delete.neatline': function() {
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
                if (feature == self._clickedFeature) {
                    inLayer = true;
                }
            });

            if (inLayer) {
                this.modifyFeatures.selectFeature(this._clickedFeature);
            }
        },

        /*
         * This sets the viewport to either the user's current location or to
         * the a view of the features added to the map.
         */
        setViewport: function() {
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
                              false
                          );
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
            var updateEl = this.params.map.raw_update;
            if (updateEl !== null) {
                var text = this.getWktForSave();
                text = text.replace(/\|/g, "|\n");
                console.log(updateEl);
                updateEl.val(text);
            }
        },

        endEditWithoutSave: function(id, immediate) {
            // Before OpenLayers axes the toolbar controls, clone the div so
            // that it can be faded down in unison with the buttons.
            var toolbarClone = $('.' + this.options.markup.toolbar_class).clone();

            // Remove controls.
            this.modifyFeatures.unselectFeature(this._clickedFeature);
            this.map.removeControl(this.modifyFeatures);
            this.map.removeControl(this.editToolbar);

            // If the form is immediately switching to another form, do not do
            // the fade down, as as to avoid a little opacity dip in the buttons
            // when the form switches.
            if (!immediate) {
                this.element.editgeometry('hideButtons');

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

        getWktForSave: function() {
            var wkts = [];

            if (this._clickedFeature !== null) {
                this.modifyFeatures.unselectFeature(this._clickedFeature);
            }

            // Push the wkt's onto the array.
            $.each(this._currentEditLayer.features, function(i, feature) {
                wkts.push(feature.geometry.toString());
            });

            if (this._clickedFeature !== null) {
                this.modifyFeatures.selectFeature(this._clickedFeature);
            }

            return wkts.join(this.options.wkt_delimiter);
        },

        getExtentForSave: function() {
            return this.map.getExtent().toString();
        },

        getZoomForSave: function() {
            return this.map.getZoom();
        },

        zoomToItemVectors: function(id) {
            var layer = this.idToLayer[id];

            if (layer !== null && layer.features.length > 0) {
                this.map.zoomToExtent(layer.getDataExtent());
            }
        },

        _getStyleMap: function(fillColor) {
            return new OpenLayers.StyleMap({
                'default': new OpenLayers.Style({
                    fillColor: fillColor,
                    fillOpacity: this.options.styles.default_opacity,
                    strokeColor: fillColor,
                    pointRadius: this.options.styles.select_point_radius,
                    strokeWidth: 1
                }),
                'select': new OpenLayers.Style({
                    fillColor: fillColor,
                    fillOpacity: this.options.styles.default_opacity,
                    strokeColor: this.options.styles.select_stroke_color,
                    pointRadius: this.options.styles.select_point_radius,
                    strokeWidth: 2
                })
            });
        },

        setItemColor: function(color) {
            // Rebuild the style map.
            this._currentEditLayer.styleMap = this._getStyleMap(color);

            // Rerender the layer to manifest the change.
            this._currentEditLayer.redraw();
        },

        /*
         * These are some query functions to call during testing.
         */

        getCenterLonLat: function() {
            var wsg  = new OpenLayers.Projection('EPSG:4326'),
                proj = this.map.getProjectionObject();
            return this.map.getCenter().transform(proj, wsg);
        },

        hasPoint: function() {
            return this.hasFeature('OpenLayers.Geometry.Point');
        },

        hasLine: function() {
            return this.hasFeature('OpenLayers.Geometry.LineString');
        },

        hasPolygon: function() {
            return this.hasFeature('OpenLayers.Geometry.Polygon');
        },

        hasFeature: function(className) {
            result = false;

            $.each(this._currentVectorLayers, function(i, layer) {
                $.each(layer.features, function(j, feature) {
                    result = result || (feature.geometry.CLASS_NAME == className);
                });
            });

            return result;
        }
    });
})( jQuery );
