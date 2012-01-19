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
    poll: function(predicate, callback, maxPoll, timeout) {
      var n, _poll;
      if (maxPoll == null) maxPoll = null;
      if (timeout == null) timeout = 100;
      n = 0;
      _poll = function() {
        if (predicate() || ((maxPoll != null) && maxPoll !== 0 && n >= maxPoll)) {
          return callback();
        } else {
          n++;
          return setTimeout(_poll, timeout);
        }
      };
      return setTimeout(_poll, timeout);
    },
    editCoverageMap: function(parent, widgets, value, formats, options) {
      var m;
      if (options == null) options = {};
      m = NLFeatures.initEditMap(widgets.map, widgets.text, value, options);
      NLFeatures.poll(function() {
        return document.getElementsByClassName('mceEditor').length > 0;
      }, function() {
        var free;
        if (!jQuery(widgets.html).checked) {
          free = widgets.free.charAt(0) === '#' ? widgets.free.substr(1) : widgets.free;
          tinyMCE.execCommand('mceRemoveControl', false, free);
        }
        return jQuery(widgets.mapon).unbind('click');
      });
      NLFeatures.destroyTinyMCE(widgets.free, widgets.html, widgets.mapon);
      return m;
    }
  };

}).call(this);
