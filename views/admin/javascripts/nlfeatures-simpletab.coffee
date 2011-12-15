#
# Component widget that controls the map. Instantiated by the parent Neatline
# widget.
#
# Licensed under the Apache License, Version 2.0 (the "License"); you may not
# use this file except in compliance with the License. You may obtain a copy of
# the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
# applicable law or agreed to in writing, software distributed under the
# License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
# OF ANY KIND, either express or implied. See the License for the specific
# language governing permissions and limitations under the License.
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

          jel.trigger('tabchange', {
            index : index
            tab   : target
          })

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
