(function() {
  describe('simpletab', function() {
    var counter, el, tab;
    counter = 0;
    el = null;
    tab = null;
    beforeEach(function() {
      counter++;
      $('body').append("<div id=\"simpletab-eg-" + counter + "\">\n  <div>\n    <ul>\n      <li><a href=\"#simpletab-tab-" + counter + "-0\">Tab 0</a></li>\n      <li><a href=\"#simpletab-tab-" + counter + "-1\">Tab 1</a></li>\n    </ul>\n  </div>\n  <div id=\"simpletab-tab-" + counter + "-0\"><p>Content in Tab 0</p></div>\n  <div id=\"simpletab-tab-" + counter + "-1\"><p>Content in Tab 1</p></div>\n</div>");
      el = $("#simpletab-eg-" + counter);
      el.simpletab();
      return tab = el.data('simpletab');
    });
    afterEach(function() {
      el.remove();
      el = null;
      return tab = null;
    });
    it('should show only the first tab', function() {
      expect($("#simpletab-tab-" + counter + "-0").is(':visible')).toBeTruthy();
      return expect($("#simpletab-tab-" + counter + "-1").is(':visible')).toBeFalsy();
    });
    it('should show the second tab when you click the second list item', function() {
      $("#simpletab-tab-" + counter + " li:nth-child(2) a").trigger('click');
      expect($("#simpletab-tab-" + counter + "-0").is(':visible')).toBeFalsy();
      return expect($("#simpletab-tab-" + counter + "-1").is(':visible')).toBeTruthy();
    });
    return it('should show the first tab when you click the second item, then the first item', function() {
      $("#simpletab-tab-" + counter + " li:nth-child(2) a").trigger('click');
      $("#simpletab-tab-" + counter + " li:first a").trigger('click');
      expect($("#simpletab-tab-" + counter + "-0").is(':visible')).toBeTruthy();
      return expect($("#simpletab-tab-" + counter + "-1").is(':visible')).toBeFalsy();
    });
  });
}).call(this);
