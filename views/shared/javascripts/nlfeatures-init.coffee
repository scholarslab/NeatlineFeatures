
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

