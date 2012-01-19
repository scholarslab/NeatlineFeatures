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
    },
    initEditMap: function(map, text, value, options) {
      var all_options, el, item;
      el = jQuery(map);
      item = {
        id: el.attr('id'),
        title: 'Coverage',
        name: 'Coverage',
        wkt: value
      };
      all_options = jQuery.extend(true, {}, options, {
        map: {
          raw_update: jQuery(text)
        },
        edit_json: item
      });
      return el.nlfeatures(all_options).hide().data('nlfeatures');
    },
    destroyTinyMCE: function(text, html) {
      var cb, poll;
      cb = jQuery(html);
      if (text.charAt(0) === '#') text = text.substr(1);
      poll = function() {
        var eds;
        eds = document.getElementsByClassName('mceEditor');
        if (eds.length === 0) {
          return setTimeout(poll, 100);
        } else {
          return tinyMCE.execCommand('mceRemoveControl', false, text);
        }
      };
      if (!cb.checked) return setTimeout(poll, 100);
    },
    editCoverageMap: function(parent, widgets, value, formats, options) {
      var m;
      if (options == null) options = {};
      m = NLFeatures.initEditMap(widgets.map, widgets.text, value, options);
      if (!formats.is_html) NLFeatures.destroyTinyMCE(widgets.free, widgets.html);
      return m;
    }
  };

}).call(this);
