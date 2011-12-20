
window.NLFeatures =
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

  initEditMap: (widget, map, text, value) ->
    el = jQuery(map)
    m = el.nlfeatures(
      map:
        raw_update: $(text)
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

  # This is a sledgehammer, but the response is proportional. Basically, if
  # there are any checked checkboxes in a field, Omeka turns on TinyMCE for all
  # textareas in the field.  In this case, it's picking up an OpenLayers
  # checkbox and setting the raw textarea up incorrectly.
  #
  # Also, because of the way TinyMCE is handled, we have to use setTimeout to
  # make sure it gets set back *after* it's incorrectly enabled. Double ugh.
  #
  # TODO: Bring this up on #omeka and file a bug report.
  # admin/themes/default/javascripts/items.js, around line 410, should be more
  # specific.
  destroyTinyMCE: (rawTab, text, html) ->
    cb = jQuery(html)
    raw = $(rawTab)
    text = text.substr(1) if text.charAt(0) == '#'
    poll = ->
      eds = document.getElementsByClassName('mceEditor')
      if eds.length is 0
        setTimeout(poll, 100)
      else
        tinyMCE.execCommand('mceRemoveControl', false, text)
    unless cb.checked
      setTimeout(poll, 100)

  switchToTab: (tabs, n) ->
    jQuery(tabs.element.find('li a')[n]).trigger('click')

  editCoverageMap: (parent, tabs, widgets, value, formats) ->
    tabWidget = NLFeatures.initTabs(parent)
    m = NLFeatures.initEditMap(parent, widgets.map, widgets.text, value)

    NLFeatures.destroyTinyMCE(tabs.raw, widgets.text, widgets.html) unless formats.is_html
    NLFeatures.switchToTab(tabWidget, 1) unless value == '' or formats.is_wkt

    m

