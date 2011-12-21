#
# Component widget that controls the map. Instantiated by the parent Neatline
# widget.
#
# @package     omeka
# @subpackage  neatline
# @author      Scholars' Lab <>
# @author      Bethany Nowviskie <bethany@virginia.edu>
# @author      Adam Soroka <ajs6f@virginia.edu>
# @author      David McClure <david.mcclure@virginia.edu>
# @copyright   2011 The Board and Visitors of the University of Virginia
# @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
#

(($) ->
  $.widget('neatline.simpletab',
    options: {
      nav_list: 'ul:first'
    }

    _create: ->
      tab = this
      this.anchors = this.element.find("#{this.options.nav_list} li a")
      this.anchors.each (index) ->
        jel = $ this
        target = $(jel.attr('href'))
        jel.data('neatline.simpletab.target', target)
        target.hide()

        jel.click (event) ->
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
        target = jel.data('neatline.simpletab.target')
        target.show()
        tab.current = target

      if this.options.tabchange?
        this.element.bind('tabchange', this.options.tabchange)

      this

    destroy: ->
      this.anchors.each ->
        $(this).unbind('click')

    _setOption: (key, value) ->
      $.Widget.prototype._setOption.apply(this, arguments)
  )
)(jQuery)
