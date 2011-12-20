
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

