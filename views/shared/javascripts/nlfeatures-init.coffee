
# This is a module/static class containing utility functions for setting up the
# Neatline Features controls.

window.NLFeatures =

  # This creates a Feature map for viewing.
  #
  # + `mapEl` is a jQuery selector for the element to turn into a map.
  # + `wkt` is the WKT data to display on it.
  viewCoverageMap: (mapEl, wkt) ->
    el = jQuery(mapEl)
    m = el.nlfeatures().data('nlfeatures')
    item = 
      id: el.attr('id')
      title: 'Coverage'
      name: 'Coverage'
      wkt: wkt
    m.loadLocalData([item])
    m.setViewport()

  # This initializes the tabs for the editing interface.
  #
  # + `widget` is a jQuery selector for the parent element for the entire
  # control. This will be turned into a simpletab, which assumes that there the
  # tab list is at '.nlfeatures-edit-nav-menu ul'.
  #
  # This returns the simpletabs data instance.
  initTabs: (widget) ->
    w = jQuery(widget)
    w.simpletab(
      nav_list: '.nlfeatures-edit-nav-menu ul'
      tabchange: (event, data) ->
        jQuery(a.parentNode).removeClass('selected') for a in data.tab.anchors
        data.a.parent().addClass('selected')
        event.preventDefault
    )
    w.data('simpletab')

  # This initializes an nlfeatures map for editing.
  #
  # This initializes the nlfeatures map, hooks it to the raw TEXTAREA,
  # populates it with the WKT data, turns on editing, and creates a listener
  # for tabchange to update the map again whenever the tab is changed back to
  # the map tab.
  #
  # + `widget` is the jQuery selector for the parent control/simpletabs;
  # + `map` is the jQuery selector for the DIV to turn into the nlfeatures map;
  # + `text` is the jQuery selector for the TEXTAREA to update with the raw
  # feature data; and
  # + `value` is the initial WKT (or not) value for the Coverage field.
  #
  # This returns the nlfeatures data instance.
  initEditMap: (widget, map, text, value) ->
    el = jQuery(map)
    m = el.nlfeatures(
      map:
        raw_update: jQuery(text)
    )
      .data('nlfeatures')
    item =
      id: el.attr('id')
      title: 'Coverage'
      name: 'Coverage'
      wkt: value

    m.loadLocalData([item])
    m.setViewport()
    m.editJson(item, true)

    jQuery(widget).bind('tabchange', (event, data) ->
      if (data.index == 0)
        item =
          id: el.attr('id')
          title: 'Coverage'
          name: 'Coverage'
          wkt: jQuery(text).val()
        m.loadLocalData([item])
        m.setViewport()
        m.editJson(item, true)
    )

    m

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

  # This is a utility function to facilitate switching to a given tab by
  # issuing a click event on the appropriate A tab.
  switchToTab: (tabs, n) ->
    jQuery(tabs.element.find('li a')[n]).trigger('click')

  # This sets up a control for editing a coverage field.
  #
  # It returns the nlfeatures data instance.
  #
  # + `parent` is the jQuery selector for the parent DIV on the control.
  # + `tabs` is an object containing the information about the tabs:
  #   - `raw` is the jQuery selector for the raw tab;
  #   - `map` is the jQuery selector for the map tab.
  # + `widgets` is an object containing information about the input widgets:
  #   - `map` is the jQuery selector for the OpenLayers/nlfeatures map DIV;
  #   - `text` is the jQuery selector for the TEXTAREA for the raw text;
  #   - `html` is the jQuery selector for the "Use HTML" checkbox.
  # + `value` is the current value of coverage field as a string.
  # + `formats` is an object containing predicates about data formats are
  # enabled for the data.
  #   - `is_html` is true if the data in the raw field is HTML, so TinyMCE
  #   should be enabled;
  #   - `is_wkt` is true if the data appears to be WKT, so the OpenLayers map
  #   should be enabled.
  editCoverageMap: (parent, tabs, widgets, value, formats) ->
    tabWidget = NLFeatures.initTabs(parent)
    m = NLFeatures.initEditMap(parent, widgets.map, widgets.text, value)

    NLFeatures.destroyTinyMCE(widgets.text, widgets.html) unless formats.is_html
    tabWidget.switchToTab(1) unless value == '' or formats.is_wkt

    m

