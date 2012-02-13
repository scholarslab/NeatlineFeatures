
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
      mode        : 'view'

      id_prefix   : null
      text        : null
      free        : null
      html        : null
      mapon       : null
      map         : null

      map_options : {}

      value       : null
      formats     :
        is_map    : false
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

      @map = this._initMap()
      if @options.mode == 'edit'
        this._recaptureEditor()
        this._updateFreeText()
        this._addUpdateEvents()
      else
        this._fillFreeView()
      this.hideMap() unless @options.formats.is_map

    destroy: ->
      $.Widget.prototype.destroy.call this

    _setOptions: (key, value) ->
      $.Widget.prototype._setOption.apply this, arguments

    # This initializes a map for editing with the value passed in. It returns
    # the nlfeatures data object.
    _initMap: ->
      input = this.parseTextInput(@options.value)
      map = $ @options.map
      item =
        title : 'Coverage'
        name  : 'Coverage'
        id    : @element.attr 'id'
        wkt   : input.wkt
      local_options =
        mode: @options.mode
        json: item
      item.zoom   = input.zoom   if input.zoom?
      item.center = input.center if input.center?

      all_options = $.extend true, {}, @options.map_options, local_options
      $(@options.map)
        .nlfeatures(all_options)
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
      this._poll(
        -> $('.mceEditor').length > 0,
        =>
          if not this.usesHtml()
            free = @options.free.substr 1
            tinyMCE.execCommand 'mceRemoveControl', false, free
          $(@options.mapon)
            .unbind('click')
            .change => this._onUseMap()
          $(@options.html)
            .change => this._updateTinyEvents()
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

    # Tests for the content types active. These look at the states of the
    # checkboxes.
    usesHtml: -> $(@options.html ).is ':checked'
    usesMap : -> $(@options.mapon).is ':checked'

    # This handles when the Use Map checkbox is clicked.
    _onUseMap: ->
      if this.usesMap()
        this.showMap()
      else
        this.hideMap()
      this.updateTextInput()

    showMap: -> $(@element).find('.map-container').show()
    hideMap: -> $(@element).find('.map-container').hide()

    _updateTinyEvents: ->
      if this.usesHtml()
        free = @options.free.substr 1
        this._poll(
          -> tinyMCE.get(free)?,
          =>
            $(@options.free).unbind('change')
            tinyMCE.get(free).onChange.add =>
              this.updateTextInput()
        )
      else
        $(@options.free).change => this.updateTextInput()

    _addUpdateEvents: ->
      handler = => this.updateTextInput()
      $(@options.free).change handler
      $(@map.element)
        .bind('featureadded.nlfeatures', handler)
        .bind('update.nlfeatures'      , handler)
        .bind('delete.nlfeatures'      , handler)

    # This handles passing the content from the visible inputs to the hidden
    # field that Omeka actually uses.
    updateTextInput: ->
      buffer = []

      if this.usesMap()
        buffer.push "WKT: #{@map.getWktForSave()}\n"

        zoom = @map.getSavedZoom()
        buffer.push "ZOOM: #{zoom}\n" if zoom?

        center = @map.getSavedCenter()
        buffer.push "CENTER: #{center.lat},#{center.lon}\n" if center?

        buffer.push "\n"

      if this.usesHtml()
        buffer.push tinyMCE.get(@options.free.substr 1).getContent()
      else
        buffer.push $(@options.free).val()

      $(@options.text).val(buffer.join '')

    # This breaks the value of the text input into 'wkt' and 'free' and returns
    # a JS object with those properties.
    parseTextInput: (input) ->
      input ?= if @options.mode == 'edit'
        $(@options.text).val()
      else
        @options.value
      output = wkt: '', free: ''

      if input.substr(0, 5) == 'WKT: '
        lines = input.split(/\r\n|\n|\r/)

        # This walks through the array to find the first blank line.
        splitAt = 0
        while (splitAt < lines.length && ! lines[splitAt].match(/^\s*$/))
          splitAt++

        if splitAt < lines.length
          prefixLines   = lines.slice(0, splitAt)
          prefix        = this._parseFeatureData lines.slice(0, splitAt)

          output.wkt    = prefix.wkt
          output.zoom   = prefix.zoom
          output.center = prefix.center
          output.free   = lines.slice(splitAt + 1).join("\n")
      else
        output.free = input

      output

    # This takes an array of lines and parses them for the WKT, ZOOM, and
    # CENTER fields.
    _parseFeatureData: (lines) ->
      data =
        wkt    : null
        zoom   : null
        center : null
      current = null

      for line in lines
        line = line.trim()

        if line.length == 0
          continue
        else if line.substr(0, 5) == 'WKT: '
          current  = 'wkt'
          data.wkt = []
          data.wkt.push line.substr(5)
        else if line.substr(0, 6) == 'ZOOM: '
          current   = 'zoom'
          data.zoom = parseInt line.substr(6)
        else if line.substr(0, 8) == 'CENTER: '
          current = 'center'
          [lon, lat] = line.substr(8).split ','
          data.center =
            lon: parseFloat lon
            lat: parseFloat lat
        else if current == 'wkt'
          data.wkt.push line

      if data.wkt?
        data.wkt = data.wkt.join("\n") 
      data

    # This updates the free-text field from the 
    _updateFreeText: ->
      output = this.parseTextInput()
      $(@options.free).val output.free

    # This populates the free-text DIV.
    _fillFreeView: (free) ->
      free ?= this.parseTextInput().free
      $(@options.free).html(free)

  ))(jQuery)

