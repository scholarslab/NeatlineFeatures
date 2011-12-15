(function() {
  describe('simpletab', function() {
    var counter, el, tab;
    counter = 0;
    el = null;
    tab = null;
    beforeEach(function() {
      counter++;
      $('body').append("<div id=\"simpletab-eg-" + counter + "\">\n  <div class='tabnav'>\n    <ul>\n      <li><a href=\"#simpletab-tab-" + counter + "-0\">Tab 0</a></li>\n      <li><a href=\"#simpletab-tab-" + counter + "-1\">Tab 1</a></li>\n    </ul>\n  </div>\n  <div id=\"simpletab-tab-" + counter + "-0\"><p>Content in Tab 0</p></div>\n  <div id=\"simpletab-tab-" + counter + "-1\"><p>Content in Tab 1</p></div>\n</div>");
      el = $("#simpletab-eg-" + counter);
      el.simpletab({
        nav_list: '.tabnav ul'
      });
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
      $("#simpletab-eg-" + counter + " li:nth-child(2) a").click();
      expect($("#simpletab-tab-" + counter + "-0").is(':visible')).toBeFalsy();
      return expect($("#simpletab-tab-" + counter + "-1").is(':visible')).toBeTruthy();
    });
    it('should show the first tab when you click the second item, then the first item', function() {
      $("#simpletab-eg-" + counter + " li:nth-child(2) a").click();
      $("#simpletab-eg-" + counter + " li:first a").click();
      expect($("#simpletab-tab-" + counter + "-0").is(':visible')).toBeTruthy();
      return expect($("#simpletab-tab-" + counter + "-1").is(':visible')).toBeFalsy();
    });
    return it('should fire a tabchange event when a tab is clicked on', function() {
      var clicked;
      clicked = 0;
      el.bind('tabchange', function(event) {
        return clicked++;
      });
      $("#simpletab-eg-" + counter + " li:nth-child(2) a").click();
      $("#simpletab-eg-" + counter + " li:first a").click();
      return expect(clicked).toBe(2);
    });
  });
}).call(this);
