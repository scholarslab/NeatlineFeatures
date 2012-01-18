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
    initEditMap: function(widget, map, text, value, options) {
      var all_options, el, item, m;
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
      m = el.nlfeatures(all_options).data('nlfeatures');
      jQuery(widget).bind('tabchange', function(event, data) {
        if (data.index === 0) {
          item = {
            id: el.attr('id'),
            title: 'Coverage',
            name: 'Coverage',
            wkt: jQuery(text).val()
          };
          return m.editJsonItem(item);
        }
      });
      return m;
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
    switchToTab: function(tabs, n) {
      return jQuery(tabs.element.find('li a')[n]).trigger('click');
    },
    editCoverageMap: function(parent, tabs, widgets, value, formats, options) {
      var m, tabWidget;
      if (options == null) options = {};
      tabWidget = NLFeatures.initTabs(parent);
      m = NLFeatures.initEditMap(parent, widgets.map, widgets.text, value, options);
      if (!formats.is_html) NLFeatures.destroyTinyMCE(widgets.text, widgets.html);
      if (!(value === '' || formats.is_wkt)) tabWidget.switchToTab(1);
      return m;
    }
  };

}).call(this);
