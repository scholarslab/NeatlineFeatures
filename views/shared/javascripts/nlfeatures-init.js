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
    },
    initTabs: function(widget) {
      var w;
      w = jQuery(widget);
      w.simpletab({
        nav_list: '.nlfeatures-edit-nav-menu ul',
        tabchange: function(event, data) {
          var a, _i, _len, _ref;
          _ref = data.tab.anchors;
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            a = _ref[_i];
            jQuery(a.parentNode).removeClass('selected');
          }
          data.a.parent().addClass('selected');
          return event.preventDefault;
        }
      });
      return w.data('simpletab');
    },
    initEditMap: function(widget, map, text, value) {
      var el, item, m;
      el = jQuery(map);
      m = el.nlfeatures({
        map: {
          raw_update: $(text)
        }
      }).data('nlfeatures');
      item = {
        id: el.attr('id'),
        title: 'Coverage',
        name: 'Coverage',
        wkt: value
      };
      m.loadLocalData([item]);
      m.setViewport();
      m.editJson(item, true);
      jQuery(widget).bind('tabchange', function(event, data) {
        if (data.index === 0) {
          item = {
            id: el.attr('id'),
            title: 'Coverage',
            name: 'Coverage',
            wkt: jQuery(text).val()
          };
          m.loadLocalData([item]);
          m.setViewport();
          return m.editJson(item, true);
        }
      });
      return m;
    },
    destroyTinyMCE: function(rawTab, text, html) {
      var cb, poll;
      cb = jQuery(html);
      poll = function() {
        var eds, raw;
        raw = $(rawTab);
        eds = document.getElementsByClassName('mceEditor');
        if (eds.length === 0) {
          return setTimeout(poll, 100);
        } else {
          return tinyMCE.execCommand('mceRemoveControl', false, text);
        }
      };
      if (!cb.checked) {
        return setTimeout(poll, 100);
      }
    },
    switchToTab: function(tabs, n) {
      return jQuery(tabs.element.find('li a')[n]).trigger('click');
    },
    editCoverageMap: function(widget, tabs, map, text, html, value, formats) {
      var m;
      NLFeatures.initTabs(widget);
      m = NLFeatures.initEditMap(widget, map, text, value);
      if (!formats.is_html) {
        NLFeatures.destroyTinyMCE(tabs.raw, text, html);
      }
      if (!(value === '' || formats.is_wkt)) {
        NLFeatures.switchToTab(tabs, 1);
      }
      return m;
    }
  };
}).call(this);
