#
# # SimpleTab

#
# Component widget that implements a simple tabbing control. It doesn't handle
# styling, but it does handle visibility and change event notifications.
#
# Generally, you can just create the markup, instantiate this widget and,
# optionally, bind the `tabchange` event.
#
# For example, given this markup:
#
# ```
# <div id='navtab'>
#   <div class='navmenu'>
#
#     <!--
#       You have to specify the path from #navtab to the list containing the
#       navigation elements.
#     -->
#     <ul>
#
#       <!--
#         Each tab needs an anchor pointing to the div associated with it
#         below.
#       -->
#       <li><a href="#nav1">Tab 1</a></li>
#       <li><a href="#nav2">Tab 2</a></li>
#     </ul>
#   </div>
#   <div class='navbody'>
#
#     <!--
#       The body of each tab should be wrapped in an element with the @id that
#       is the target of one of the tabs.
#     -->
#     <div id='nav1'>
#       <p>This is the body of tab 1.</p>
#     </div>
#     <div id='nav2'>
#       <p>This is the body of tab 2.</p>
#     </div>
#   </div>
# </div>
# ```
#
# Instantiate the simpletab with this. This also binds the tabchange event to
# change the @class on the tab LI.
#
# ```
# $('div#navtab')
#   .simpletab(
#     nav_list: '.navmenu ul'
#     tabchange: (event, data) ->
#       jQuery(a.parentNode).removeClass('selected') for a in data.tab.anchors
#       data.a.parent().addClass('selected')
#       event.preventDefault
#     )
#
# **`tabchange`**
#
# The `tabchange` event also passes in a data object with these properties:
#
# * `a`: The `A` tag that was clicked on. This is inside the `LI`;
# * `tab`: The SimpleTab instance;
# * `index`: The index of the tab in the list.
# * `target`: The target `DIV`.
#
#
# @package     omeka
# @subpackage  nlfeatures
# @author      Scholars' Lab <>
# @author      Bethany Nowviskie <bethany@virginia.edu>
# @author      Adam Soroka <ajs6f@virginia.edu>
# @author      David McClure <david.mcclure@virginia.edu>
# @copyright   2011 The Board and Visitors of the University of Virginia
# @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
#

(($) ->
  $.widget('nlfeatures.simpletab',
    options: {
      # This is the jQuery selector to the list containing the tabs.
      nav_list: 'ul:first'

      # tabchange is also a valid option, which will be an event handler for
      # that event.
    }

    # This creates the simpletab by setting up the event handlers on the tabs
    # and hiding all but the first tab.
    _create: ->
      tab = this
      this.anchors = this.element.find("#{this.options.nav_list} li a")
      this.anchors.each (index) ->
        jel = $ this
        target = $(jel.attr('href'))
        jel.data('nlfeatures.simpletab.target', target)
        target.hide()

        jel.parent().click (event) ->
          tab.current.hide() if tab.current?
          tab.current = target
          target.show()

          jel.trigger('tabchange', [{
            a      : jel
            tab    : tab
            index  : index
            target : target
          }])

          event.preventDefault()

      this.anchors.first().each ->
        jel = $ this
        target = jel.data('nlfeatures.simpletab.target')
        target.show()
        tab.current = target

      if this.options.tabchange?
        this.element.bind('tabchange', this.options.tabchange)

      this

    # Destory the widget by removing the event handlers from the tabs.
    destroy: ->
      this.anchors.each ->
        $(this).unbind('click')

    # Set an option. This just passes it on up to the parents.
    _setOption: (key, value) ->
      $.Widget.prototype._setOption.apply(this, arguments)

    # This sets the tab. The tab parameter is the 0-indexed tab number.
    switchToTab: (tab) ->
      $(this.anchors[tab]).click()
  )
)(jQuery)
