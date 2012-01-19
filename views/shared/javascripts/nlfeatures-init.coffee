
# This is a module/static class containing utility functions for setting up the
# Neatline Features controls.

window.NLFeatures =

  # This creates a Feature map for viewing.
  #
  # + `mapEl` is a jQuery selector for the element to turn into a map.
  # + `wkt` is the WKT data to display on it.
  viewCoverageMap: (mapEl, wkt, options={}) ->
    el = jQuery(mapEl)
    m = el.nlfeatures(options).data('nlfeatures')
    item = 
      id: el.attr('id')
      title: 'Coverage'
      name: 'Coverage'
      wkt: wkt
    m.loadLocalData([item])
    m.setViewport()

  # This initializes an nlfeatures map for editing.
  #
  # This initializes the nlfeatures map, hooks it to the raw TEXTAREA,
  # populates it with the WKT data, turns on editing, and creates a listener
  # for tabchange to update the map again whenever the tab is changed back to
  # the map tab.
  #
  # + `map` is the jQuery selector for the DIV to turn into the nlfeatures map;
  # + `text` is the jQuery selector for the TEXTAREA to update with the raw
  # feature data; and
  # + `value` is the initial WKT (or not) value for the Coverage field.
  # + `options` are the options to the nlfeatures widget.
  #
  # This returns the nlfeatures data instance.
  initEditMap: (map, text, value, options) ->
    el = jQuery(map)
    item =
      id: el.attr('id')
      title: 'Coverage'
      name: 'Coverage'
      wkt: value

    all_options = jQuery.extend(
      true,
      {},
      options, 
      {
        map:
          raw_update: jQuery(text)
        edit_json: item
      }
    )

    el.nlfeatures(all_options)
      .hide()
      .data('nlfeatures')

  # If "Use HTML" isn't checked, this polls until the TinyMCE controls have
  # initialized, and then it turns off the TEXTAREA specified.
  #
  # This is a sledgehammer, but the response is proportional. Basically, if
  # there are any checked checkboxes in a field, Omeka turns on TinyMCE for all
  # textareas in the field.  In this case, it's picking up an OpenLayers
  # checkbox and setting the raw textarea up incorrectly.
  #
  # Also, because of the way TinyMCE is handled, we have to poll to make sure
  # it gets set back *after* it's incorrectly enabled. Double ugh.
  #
  # TODO: Bring this up on #omeka and file a bug report.
  # admin/themes/default/javascripts/items.js, around line 410, should be more
  # specific.
  #
  # + `text` is the jQuery selector for the TEXTAREA to remove TinyMCE from.
  # + `html` is the jQuery selector for the "Use HTML" control.
  destroyTinyMCE: (text, html) ->
    cb = jQuery(html)
    text = text.substr(1) if text.charAt(0) == '#'
    poll = ->
      eds = document.getElementsByClassName('mceEditor')
      if eds.length is 0
        setTimeout(poll, 100)
      else
        tinyMCE.execCommand('mceRemoveControl', false, text)
    unless cb.checked
      setTimeout(poll, 100)

  # This sets up a control for editing a coverage field.
  #
  # It returns the nlfeatures data instance.
  #
  # + `parent` is the jQuery selector for the parent DIV on the control.
  # + `widgets` is an object containing information about the input widgets:
  #   - `map` is the jQuery selector for the OpenLayers/nlfeatures map DIV;
  #   - `text` is the jQuery selector for the hidden input for the raw data;
  #   - `free` is the jQuery selector for the TEXTAREA for the free-form text;
  #   - `html` is the jQuery selector for the "Use HTML" checkbox.
  # + `value` is the current value of coverage field as a string.
  # + `formats` is an object containing predicates about data formats are
  # enabled for the data.
  #   - `is_html` is true if the data in the raw field is HTML, so TinyMCE
  #   should be enabled;
  #   - `is_wkt` is true if the data appears to be WKT, so the OpenLayers map
  #   should be enabled.
  # + `options` are options to pass to the nlfeatures widget.
  editCoverageMap: (parent, widgets, value, formats, options={}) ->
    m = NLFeatures.initEditMap(widgets.map, widgets.text, value, options)
    NLFeatures.destroyTinyMCE(widgets.free, widgets.html) unless formats.is_html
    m

