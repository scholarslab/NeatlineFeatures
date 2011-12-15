(function() {
  (function($) {
    return $.widget('neatline.simpletab', {
      options: {
        nav_list: 'ul:first'
      },
      _create: function() {
        var tab;
        tab = this;
        this.anchors = this.element.find("" + this.options.nav_list + " li a");
        this.anchors.each(function(index) {
          var jel, target;
          jel = $(this);
          target = $(jel.attr('href'));
          jel.data('neatline.simpletab.target', target);
          target.hide();
          return jel.click(function(event) {
            if (tab.current != null) {
              tab.current.hide();
            }
            tab.current = target;
            target.show();
            jel.trigger('tabchange', [
              {
                a: jel,
                tab: tab,
                index: index,
                target: target
              }
            ]);
            return event.preventDefault();
          });
        });
        this.anchors.first().each(function() {
          var jel, target;
          jel = $(this);
          target = jel.data('neatline.simpletab.target');
          target.show();
          return tab.current = target;
        });
        if (this.options.tabchange != null) {
          this.element.bind('tabchange', this.options.tabchange);
        }
        return this;
      },
      destroy: function() {
        return this.anchors.each(function() {
          return $(this).unbind('click');
        });
      },
      _setOption: function(key, value) {
        return $.Widget.prototype._setOption.apply(this, arguments);
      }
    });
  })(jQuery);
}).call(this);
