(function() {

  window.NLFeatures = {
    viewCoverageMap: function(mapEl, wkt, options) {
      var el, item, m;
      if (options == null) options = {};
      el = jQuery(mapEl);
      m = el.nlfeatures(options).data('nlfeatures');
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
