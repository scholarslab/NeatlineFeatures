
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
  ## Some utility functions

  # Dereferences ID attributes (i.e., it removes the '#' at the start)
  derefid = (id) ->
    if id[0] == '#' then id[1...id.length] else id

  # This convert values to string, or null or undefined to an empty string.
  to_s = (value) ->
    if value? then value.toString() else ''

  # This polls until either the predicate returns true or until the maximum
  # number of polls is reached. When it's done polling, then it calls
  # callback.
  poll = (predicate, callback, maxPoll=null, timeout=100) ->
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

  # This strips the first line off the input text. That line acts as the hash
  # to make sure that each coverage has unique data so we can search for it
  # later.
  stripFirstLine = (text) ->
    if text? then text.substr(text.indexOf("\n") + 1) else ''


  # These classes encapsulate the parts of the widget that change depending on
  # the mode.

  class BaseWidget
    constructor: (@widget) ->

    initMap: ->
      map = @fields.map
      input = @widget.options.values
      item =
        title  : 'Coverage'
        name   : 'Coverage'
        id     : @widget.element.attr 'id'
        wkt    : input.wkt
      local_options =
        mode   : @widget.options.mode
        json   : item
        markup :
          id_prefix: @widget.options.id_prefix
      local_options.zoom   = input.zoom   if input.zoom?
      local_options.center = input.center if input.center?

      all_options = $.extend true, {}, @widget.options.map_options, local_options
      @nlfeatures = map
        .nlfeatures(all_options)
        .data('nlfeatures')

      @nlfeatures


  class ViewWidget extends BaseWidget
    init: ->
      this.build()
      this.initMap()
      this.populate()

    build: ->
      el        = $ @widget.element
      id_prefix = derefid @widget.options.id_prefix

      map  = $ "<div id='#{id_prefix}map' class='map map-container'></div>"
      free = $ "<div id='#{id_prefix}free' class='freetext'></div>"

      el.addClass('nlfeatures')
        .append(map)
        .append(free)

      @fields =
        map  : $ "##{id_prefix}map"
        free : $ "##{id_prefix}free"

      el

    populate: ->
      free = @widget.options.values.text
      @fields.free.html stripFirstLine(free)


  class EditWidget extends BaseWidget
    init: ->
      this.build()
      this.initMap()
      this.captureEditor()
      this.populate()
      this.wire()

    build: ->
      el          = $ @widget.element
      id_prefix   = derefid @widget.options.id_prefix
      name_prefix = @widget.options.name_prefix

      map_container = $ """
        <div class="nlfeatures map-container">
          <div id="#{id_prefix}map"></div>
          <div class='nlfeatures-map-tools'></div>
        </div>
        """
      text_container = $ """
        <div class="nlfeatures text-container">
          <input type="hidden" id="#{id_prefix}wkt" name="#{name_prefix}[wkt]" value="" />
          <input type="hidden" id="#{id_prefix}zoom" name="#{name_prefix}[zoom]" value="" />
          <input type="hidden" id="#{id_prefix}center_lon" name="#{name_prefix}[center_lon]" value="" />
          <input type="hidden" id="#{id_prefix}center_lat" name="#{name_prefix}[center_lat]" value="" />
          <input type="hidden" id="#{id_prefix}text" name="#{name_prefix}[text]" value="" />
          <textarea id="#{id_prefix}free" name="#{name_prefix}[free]" class="textinput" rows="5" cols="50"></textarea>
          <label class="use-html">Use HTML
            <input type="hidden" name="#{name_prefix}[html] value="0" />
            <input type="checkbox" name="#{name_prefix}[html]" id="#{id_prefix}html" value="1" />
          </label>
          <label class="use-mapon">Use Map
            <input type="hidden" name="#{name_prefix}[mapon]" value="0" />
            <input type="checkbox" name="#{name_prefix}[mapon]" id="#{id_prefix}mapon" value="1" />
          </label>
        </div>
        """

      el.addClass('nlfeatures')
        .addClass('nlfeatures-edit')
        .append(map_container)
        .append(text_container)

      @fields =
        map_container  : el.find ".map-container"
        text_container : el.find ".text-container"
        map            : $ "##{id_prefix}map"
        map_tools      : el.find ".nlfeatures-map-tools"
        mapon          : $ "##{id_prefix}mapon"
        text           : $ "##{id_prefix}text"
        free           : $ "##{id_prefix}free"
        html           : $ "##{id_prefix}html"
        # Hidden fields that need to be maintained.
        wkt            : $ "##{id_prefix}wkt"
        zoom           : $ "##{id_prefix}zoom"
        center_lon     : $ "##{id_prefix}center_lon"
        center_lat     : $ "##{id_prefix}center_lat"

      el

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
    captureEditor: ->
      poll(
        -> $('.mceEditor').length > 0,
        =>
          if not this.usesHtml()
            free = @fields.free.attr 'id'
            tinyMCE.execCommand 'mceRemoveControl', false, free
          @fields.mapon
            .unbind('click')
            .change => this._onUseMap()
          @fields.html
            .change => this._updateTinyEvents()
      )

    populate: (values=@widget.options.values) ->
      @fields.mapon.attr 'checked', values.is_map
      @fields.wkt.val to_s(values.wkt)
      @fields.zoom.val to_s(values.zoom)
      @fields.center_lon.val to_s(values.center?.lon)
      @fields.center_lat.val to_s(values.center?.lat)
      @fields.text.val to_s(values.text)
      @fields.free.val stripFirstLine(values.text)

    wire: ->
      updateFields = => this.updateFields()
      @fields.free.change updateFields
      @nlfeatures.element
        .bind('featureadded.nlfeatures', updateFields)
        .bind('update.nlfeatures'      , updateFields)
        .bind('delete.nlfeatures'      , updateFields)
        .bind('saveview.nlfeatures'    , =>
          @nlfeatures.saveViewport()
          this.updateFields()
        )

    # Tests for the content types active. These look at the states of the
    # checkboxes.
    usesHtml: -> @fields.html.is  ':checked'
    usesMap : -> @fields.mapon.is ':checked'

    showMap : -> @fields.map_container.show()
    hideMap : -> @fields.map_container.hide()

    # This handles when the Use Map checkbox is clicked.
    _onUseMap : ->
      if this.usesMap()
        this.showMap()
      else
        this.hideMap()
      this.updateFields()

    # This adds a change event to the TinyMCE editor to update the text field.
    _updateTinyEvents: ->
      if this.usesHtml()
        freeId = @fields.free.attr 'id'
        poll(
          -> tinyMCE.get(freeId)?,
          =>
            @fields.free.unbind 'change'
            tinyMCE.get(freeId).onChange.add =>
              this.updateFields()
          )
      else
        @fields.free.change => this.updateFields()

    # This handles passing the content from the visible inputs (the map) to the
    # hidden field that Omeka actually uses.
    updateFields: ->
      wkt = @nlfeatures.getWktForSave()
      @fields.wkt.val wkt

      zoom = @nlfeatures.getSavedZoom()
      @fields.zoom.val zoom if zoom?

      center = @nlfeatures.getSavedCenter()
      if center?
        @fields.center_lon.val center.lon
        @fields.center_lat.val center.lat

      free = @fields.free.val()
      @fields.text.val "#{wkt}/#{zoom}/#{center?.lon}/#{center?.lat}\n#{free}"


  # And here's the widget itself.
  $.widget('nlfeatures.featurewidget',
    options: {
      mode        : 'view'

      id_prefix   : null
      name_prefix : null

      map_options : {}

      values:
        wkt     : null
        zoom    : null
        center  : null  # center is an object with the lon and lat properties.
        text    : null
        is_html : null
        is_map  : null
    }

    _create: ->
      id = @element.attr 'id'
      @options.id_prefix   ?= '#' + id.substring(0, id.length - 'widget'.length)
      @options.name_prefix ?= this._idPrefixToNamePrefix()

      @mode = if @options.mode == 'edit'
        new EditWidget this
      else
        new ViewWidget this

      @mode.init()
      @mode.hideMap() unless @options.values.is_map

    # This converts the ID prefix to a name prefix, which uses array-access.
    _idPrefixToNamePrefix: (id_prefix=@options.id_prefix) ->
      id_prefix = derefid id_prefix
      parts     = (p for p in id_prefix.split '-' when p.length > 0)
      base      = parts.shift()
      indices   = ("[#{p}]" for p in parts)
      "#{base}#{ indices.join('') }"

    destroy: ->
      $.Widget.prototype.destroy.call this

    _setOptions: (key, value) ->
      $.Widget.prototype._setOption.apply this, arguments

  ))(jQuery)

