(function() {
  window.NLFeatures = {
    viewCoverageMap: function(mapEl, wkt) {
      var el, item, m;
      el = jQuery(mapEl);
      m = el.nlfeatures().data('nlfeatures');
      item = {
        id: el.attr('id'),
        title: 'Coverage',
        name: 'Coverage',
        wkt: wkt
      };
      m.loadLocalData([item]);
      return m.setViewport();
    }
  };
}).call(this);
