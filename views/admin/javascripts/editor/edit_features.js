/*
 * # Geometry Editor
 *
 * Geometry editor widget that appears at the top right corner of the
 * map during an item edit in the Neatline editor.
 *
 * This creates these buttons:
 *
 * + Scale;
 * + Rotate;
 * + Drag; and
 * + Delete.
 *
 * ## Toggle Buttons
 *
 * Scale, Rotate, and Drag are all toggle buttons. When toggled on, these
 * properties hold:
 *
 * + the data key 'activated' is set to true; and
 * + CSS class '.primary' is added to the list of classes.
 *
 * Of course, when toggled off, the inverse of these properties is true.
 *
 * When clicked, these buttons trigger an 'update.nlfeatures' event. The first
 * argument is an object with the keys `drag`, `rotate`, and `scale`, whose
 * values are the value of the activated flags for those buttons.
 *
 * ## Delete Button
 *
 * The Delete button is not a toggle button. When clicked, it triggers a
 * 'delete.nlfeatures' event.
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
    $.widget('nlfeatures.editfeatures', {
        options: {
            // Markup hooks.
            markup: {
                geo_edit_class: 'geo-edit',
                id_prefix: 'nlf-'
            },

            // Animation constants.
            animation: {
                fade_duration: 500
            }
        },

        /*
         * This creates an editing button.
         *
         * o prefix: The prefix to use for the ID. This is combined with the next parameter;
         * o className: The class name to use for this button; and
         * o text: The text of the button
         *
         * Returns a jQuery selector containing the button.
         */
        _createEditButton: function(prefix, className, text) {
            var firstClass = className.split(' ', 1)[0];
            return $('<button id="' + prefix + firstClass + '" ' +
                     'type="button" class="btn edit-geometry-small geo-edit ' + className + '">' +
                     text + '</button>');
        },

        /*
         * Create the buttons to edit features, hook up events, etc.
         */
        _create: function() {
            var self   = this;
            var prefix = this.options.markup.id_prefix;

            if (prefix.charAt(0) == '#') prefix = prefix.substr(1);

            // Build the buttons, insert, and gloss.
            this.scaleButton    = this._createEditButton(prefix, 'scale-button radio-button sel-button', 'Scale');
            this.rotateButton   = this._createEditButton(prefix, 'rotate-button radio-button sel-button', 'Rotate');
            this.dragButton     = this._createEditButton(prefix, 'drag-button radio-button sel-button', 'Drag');
            this.deleteButton   = this._createEditButton(prefix, 'delete-button sel-button', 'Delete');
            this.viewportButton = this._createEditButton(prefix, 'viewport-button', 'Save View');

            // Insert the buttons.
            this.element.append(this.dragButton);
            this.element.append(this.rotateButton);
            this.element.append(this.scaleButton);
            this.element.append(this.deleteButton);
            this.element.append(this.viewportButton);

            // Sets of buttons for group operations later.
            this.radioButtons     = this.element.children('button.radio-button');
            this.selectionButtons = this.element.children('button.sel-button');

            // Store starting status data trackers.
            this.radioButtons.data('activated', false);
            this.disableAll();

            // Enable only if a feature is selected.
            this.element.bind({
                'select.nlfeatures'  : function() {
                    self.enableAll();
                },
                'deselect.nlfeatures': function() {
                    self.disableAll();
                }
            });

            // Gloss the drag button.
            this.dragButton.bind({
                'mousedown': function() {
                    self.toggleButton(self.dragButton);
                    self.triggerUpdateEvent();
                },

                // Suppress the default submit behavior on the button.
                'click': function(e) {
                    e.preventDefault();
                }

            });

            // Gloss the scale button.
            this.scaleButton.bind({
                'mousedown': function() {
                    self.toggleButton(self.scaleButton);
                    self.triggerUpdateEvent();
                },

                // Suppress the default submit behavior on the button.
                'click': function(e) {
                    e.preventDefault();
                }

            });

            // Gloss the rotate button.
            this.rotateButton.bind({
                'mousedown': function() {
                    self.toggleButton(self.rotateButton);
                    self.triggerUpdateEvent();
                },

                // Suppress the default submit behavior on the button.
                'click': function(e) {
                    e.preventDefault();
                }

            });

            // Gloss the delete button.
            this.deleteButton.bind({
                // Fire out the delete event.
                'mousedown': function() {
                    self.element.trigger('delete.nlfeatures');
                },

                // Suppress the default submit behavior on the button.
                'click': function(e) {
                    e.preventDefault();
                }

            });

            this.viewportButton.bind({
                'mousedown': function() {
                    self.element.trigger('saveview.nlfeatures');
                },
                'click': function(e) {
                    e.preventDefault();
                }
            });
        },

        /*
         * Display the buttons.
         */
        showButtons: function() {
            // Display:block the buttons.
            this.element.children('button').css({
                'display': 'block !important',
                'opacity': 0
            }).stop().animate({ 'opacity': 1}, this.options.animation.fade_duration);

            // By default, deactivate all buttons.
            this.deactivateAllButtons();
        },

        /*
         * Hide the buttons.
         */
        hideButtons: function() {
            // Get the buttons.
            var buttons = this.element.children('button');

            // Fade down.
            buttons.stop().animate({
                'opacity': 0
            }, this.options.markup.fade_duration, function() {
                buttons.css('display', 'none !important');
            });
        },

        /*
         * This deactivates all three toggle buttons in one action.
         *
         * This does *not* trigger the 'update.nlfeatures' event.
         */
        deactivateAllButtons: function() {
            this.radioButtons
                .removeClass('primary')
                .data('activated', false);
        },

        /*
         * This disables all buttons that operate on a selected feature.
         */
        disableAll: function() {
            this.selectionButtons
                .removeClass('primary')
                .addClass('disabled');
            this.selectionButtons.each(function() {
                this.disabled = true;
            });
        },

        /*
         * This enables all buttons that operate on a selected feature.
         */
        enableAll: function() {
            this.selectionButtons.removeClass('disabled');
            this.selectionButtons.each(function() {
                this.disabled = false;
            });
        },

        /*
         * This activates a button.
         */
        activateButton: function(button) {
            this.deactivateAllButtons();
            button.addClass('primary')
                  .data('activated', true);
            this.element.trigger('lockfocus.nlfeatures');
        },

        /*
         * This deactivates a button.
         */
        deactivateButton: function(button) {
            button.removeClass('primary')
                  .data('activated', false);
            this.element.trigger('unlockfocus.nlfeatures');
        },

        /*
         * This toggles button activation.
         */
        toggleButton: function(button) {
            if (button.data('activated')) {
                this.deactivateButton(button);
            } else {
                this.activateButton(button);
            }
        },

        /*
         * This triggers the update.nlfeatures event.
         */
        triggerUpdateEvent: function() {
            this.element.trigger('update.nlfeatures', [{
                drag   : this.dragButton.data('activated'),
                rotate : this.rotateButton.data('activated'),
                scale  : this.scaleButton.data('activated')
            }]);
        }

    });
})( jQuery );

