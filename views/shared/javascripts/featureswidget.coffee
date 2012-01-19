
##  FeaturesWidget
#
# This is a controller for the Features map. It sits a level above the map
# itself, and therefore, it can coordinate the map with the rest of the editing
# aparatus for the admin interface.
#
# This assumes that it is initialized with an existing HTML structure like
# below. `idPrefix` is inferred from the parent element used to construct the
# widget, and the input are assumed to have the IDs below based on that.
#
#
# <div id="{idPrefix}widget" class='nlfeatures nlfeatures-edit'>
#   <div>
#     <!-- hidden field input {idPrefix}text  -->
#     <!-- free-form textarea {idPrefix}free  -->
#     <!-- Use HTML checkbox  {idPrefix}html  -->
#     <!-- Use Map checkbox   {idPrefix}mapon -->
#   </div>
#   <div>
#     <div id="{idPrefix}map"></div>
#     <div class='nlfeatures-map-tools'></div>
#   </div>
# </div>

(($) ->
  $.widget('nlfeatures.featurewidget',
    options: {
      id_prefix   : null
      text        : null
      free        : null
      html        : null
      mapon       : null
      map         : null

      map_options : {}

      value       : null
      formats     :
        is_wkt    : false
        is_html   : false
    }

    _create: ->
      id = @element.attr 'id'
      @options.id_prefix ?= '#' + id.substring(0, id.length - 'widget'.length)

      @options.text  ?= "#{@options.id_prefix}text"
      @options.free  ?= "#{@options.id_prefix}free"
      @options.html  ?= "#{@options.id_prefix}html"
      @options.mapon ?= "#{@options.id_prefix}mapon"
      @options.map   ?= "#{@options.id_prefix}map"

      this._initMap()
      this._recaptureEditor()

      # console.log this

    destroy: ->
      $.Widget.prototype.destroy.call this

    _setOptions: (key, value) ->
      $.Widget.prototype._setOption.apply this, arguments

    # This initializes a map for editing with the value passed in. It returns
    # the nlfeatures data object.
    _initMap: ->
      map = $ @options.map
      item =
        title : 'Coverage'
        name  : 'Coverage'
        id    : @element.attr 'id'
        wkt   : @options.value
      local_options =
        map:
          raw_update: $ @options.text
        edit_json: item

      all_options = $.extend true, {}, @options.map_options, local_options
      $(@options.map)
        .nlfeatures(all_options)
        .hide()
        .data('nlfeatures')

    # If "Use HTML" isn't checked, this polls until the TinyMCE controls have
    # initialized, and then it turns off the TEXTAREA specified.
    #
    # This is a sledgehammer, but the response is proportional. Basically, if
    # there are any checked checkboxes in a field, Omeka turns on TinyMCE for
    # all textareas in the field.  In this case, it's picking up an OpenLayers
    # checkbox and setting the raw textarea up incorrectly.
    #
    # Also, because of the way TinyMCE is handled, we have to poll to make sure
    # it gets set back *after* it's incorrectly enabled. Double ugh.
    #
    # Finally, any checkboxes get bound to turn on TinyMCE. So I have to remove
    # that binding on "Use Map" before adding my own.
    #
    # TODO: Bring this up on #omeka and file a bug report.
    # admin/themes/default/javascripts/items.js, around line 410, should be
    # more specific.
    _recaptureEditor: ->
      html = $ @options.html
      this._poll(
        -> $('.mceEditor').length > 0,
        =>
          if not html.checked
            free = @options.free.substr 1
            tinyMCE.execCommand 'mceRemoveControl', false, free
          $(@options.mapon).unbind 'click'
          # TODO: add my click event here
      )

    # This polls until either the predicate returns true or until the maximum
    # number of polls is reached. When it's done polling, then it calls
    # callback.
    _poll: (predicate, callback, maxPoll=null, timeout=100) ->
      n = 0
      pred = if maxPoll? && maxPoll != 0
        -> (predicate() || n >= maxPoll)
      else
        predicate

      _poll = ->
        if pred()
          callback()
        else
          n++
          setTimeout _poll, timeout

      setTimeout _poll, timeout

  ))(jQuery)

