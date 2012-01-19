
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

  # This polls until either the predicate returns true or until the maximum
  # number of polls is reached. When it's done polling, then it calls the
  # callback.
  #
  # + `predicate` is the function to call to see if it needs to keep polling.
  # + `callback` is the function to call once it's done polling.
  # + 'maxPoll` is the maximum number of times to poll. This is optional and
  #   defaults to polling forever (0, null, or undefined).
  # + `timeout` is the timeout period. This defaults to 100.
  poll: (predicate, callback, maxPoll=null, timeout=100) ->
    n = 0
    _poll = ->
      if predicate() || (maxPoll? && maxPoll != 0 && n >= maxPoll)
        callback()
      else
        n++
        setTimeout(_poll, timeout)
    setTimeout(_poll, timeout)

  # This sets up a control for editing a coverage field.
  #
  # It returns the nlfeatures data instance.
  #
  # + `parent` is the jQuery selector for the parent DIV on the control.
  # + `widgets` is an object containing information about the input widgets:
  #   - `map` is the jQuery selector for the OpenLayers/nlfeatures map DIV;
  #   - `text` is the jQuery selector for the hidden input for the raw data;
  #   - `free` is the jQuery selector for the TEXTAREA for the free-form text;
  #   - `html` is the jQuery selector for the "Use HTML" checkbox;
  #   - `mapon` is the jQuery selector for the "Use Map" checkbox.
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

    # If "Use HTML" isn't checked, this polls until the TinyMCE controls have
    # initialized, and then it turns off the TEXTAREA specified.
    #
    # This is a sledgehammer, but the response is proportional. Basically, if
    # there are any checked checkboxes in a field, Omeka turns on TinyMCE for
    # all textareas in the field.  In this case, it's picking up an OpenLayers
    # checkbox and setting the raw textarea up incorrectly.
    #
    # Also, because of the way TinyMCE is handled, we have to poll to make sure
    # it gets set back *after* it's incorrectly enabled. Double ugh.
    #
    # Finally, any checkboxes get bound to turn on TinyMCE. So I have to remove
    # that binding on "Use Map" before adding my own.
    #
    # TODO: Bring this up on #omeka and file a bug report.
    # admin/themes/default/javascripts/items.js, around line 410, should be
    # more specific.
    NLFeatures.poll(
      -> document.getElementsByClassName('mceEditor').length > 0,
      ->
        if not jQuery(widgets.html).checked
          free = if widgets.free.charAt(0) == '#'
            widgets.free.substr(1)
          else
            widgets.free
          tinyMCE.execCommand('mceRemoveControl', false, free)
        jQuery(widgets.mapon).unbind('click')
    )

    NLFeatures.destroyTinyMCE(widgets.free, widgets.html, widgets.mapon)
    m

