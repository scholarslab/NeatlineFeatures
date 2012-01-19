(function() {

  (function($) {
    return $.widget('nlfeatures.featurewidget', {
      options: {
        id_prefix: null,
        text: null,
        free: null,
        html: null,
        mapon: null,
        map: null,
        map_options: {},
        value: null,
        formats: {
          is_wkt: false,
          is_html: false
        }
      },
      _create: function() {
        var id, _base, _base2, _base3, _base4, _base5, _base6;
        id = this.element.attr('id');
        if ((_base = this.options).id_prefix == null) {
          _base.id_prefix = '#' + id.substring(0, id.length - 'widget'.length);
        }
        if ((_base2 = this.options).text == null) {
          _base2.text = "" + this.options.id_prefix + "text";
        }
        if ((_base3 = this.options).free == null) {
          _base3.free = "" + this.options.id_prefix + "free";
        }
        if ((_base4 = this.options).html == null) {
          _base4.html = "" + this.options.id_prefix + "html";
        }
        if ((_base5 = this.options).mapon == null) {
          _base5.mapon = "" + this.options.id_prefix + "mapon";
        }
        if ((_base6 = this.options).map == null) {
          _base6.map = "" + this.options.id_prefix + "map";
        }
        this.map = this._initMap();
        this.hideMap();
        this._recaptureEditor();
        return this._addUpdateEvents();
      },
      destroy: function() {
        return $.Widget.prototype.destroy.call(this);
      },
      _setOptions: function(key, value) {
        return $.Widget.prototype._setOption.apply(this, arguments);
      },
      _initMap: function() {
        var all_options, item, local_options, map;
        map = $(this.options.map);
        item = {
          title: 'Coverage',
          name: 'Coverage',
          id: this.element.attr('id'),
          wkt: this.options.value
        };
        local_options = {
          edit_json: item
        };
        all_options = $.extend(true, {}, this.options.map_options, local_options);
        return $(this.options.map).nlfeatures(all_options).data('nlfeatures');
      },
      _recaptureEditor: function() {
        var _this = this;
        return this._poll(function() {
          return $('.mceEditor').length > 0;
        }, function() {
          var free;
          if (!_this.usesHtml()) {
            free = _this.options.free.substr(1);
            tinyMCE.execCommand('mceRemoveControl', false, free);
          }
          $(_this.options.mapon).unbind('click').change(function() {
            return _this._onUseMap();
          });
          return $(_this.options.html).change(function() {
            return _this._updateTinyEvents();
          });
        });
      },
      _poll: function(predicate, callback, maxPoll, timeout) {
        var n, pred, _poll;
        if (maxPoll == null) maxPoll = null;
        if (timeout == null) timeout = 100;
        n = 0;
        pred = (maxPoll != null) && maxPoll !== 0 ? function() {
          return predicate() || n >= maxPoll;
        } : predicate;
        _poll = function() {
          if (pred()) {
            return callback();
          } else {
            n++;
            return setTimeout(_poll, timeout);
          }
        };
        return setTimeout(_poll, timeout);
      },
      usesHtml: function() {
        return $(this.options.html).is(':checked');
      },
      usesMap: function() {
        return $(this.options.mapon).is(':checked');
      },
      _onUseMap: function() {
        if (this.usesMap()) {
          this.showMap();
        } else {
          this.hideMap();
        }
        return this.updateTextInput();
      },
      showMap: function() {
        return $(this.element).find('.map-container').show();
      },
      hideMap: function() {
        return $(this.element).find('.map-container').hide();
      },
      _updateTinyEvents: function() {
        var free,
          _this = this;
        if (this.usesHtml()) {
          free = this.options.free.substr(1);
          return this._poll(function() {
            return tinyMCE.get(free) != null;
          }, function() {
            $(_this.options.free).unbind('change');
            return tinyMCE.get(free).onChange.add(function() {
              return _this.updateTextInput();
            });
          });
        } else {
          return $(this.options.free).change(function() {
            return _this.updateTextInput();
          });
        }
      },
      _addUpdateEvents: function() {
        var handler,
          _this = this;
        handler = function() {
          return _this.updateTextInput();
        };
        $(this.options.free).change(handler);
        return $(this.map.element).bind('featureadded.nlfeatures', handler).bind('update.nlfeatures', handler).bind('delete.nlfeatures', handler);
      },
      updateTextInput: function() {
        var buffer;
        buffer = [];
        if (this.usesMap()) {
          buffer.push("WKT: " + (this.map.getWktForSave()) + "\n\n");
        }
        if (this.usesHtml()) {
          buffer.push(tinyMCE.get(this.options.free.substr(1)).getContent());
        } else {
          buffer.push($(this.options.free).val());
        }
        return $(this.options.text).val(buffer.join(''));
      }
    });
  })(jQuery);

}).call(this);
