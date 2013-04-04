
# NeatlineFeatures

NeatlineFeatures offers the ability to graphically encode and edit geospatial
shape metadata for Omeka items. This is stored in the coverage field alongside
the textual coverage information, allowing you to add rich, multi-faceted
geospatial metadata.

## Installation

* Upload the ‘NeatlineFeatures’ plugin directory to your Omeka
  installation’s `plugin` directory. See [Installing a
  Plugin][installing].
* Activate the plugin from the admin → Settings → Plugins page.

## Usage

Once installed, NeatlineFeatures adds an option to the Coverage field for the
Dublin Core Element Set named "Use Map."

### Add Coverage

Go to the Item Edit view for the item you want to associate a geospatial
feature with. Scroll to the Coverage field and select "Use Map"

<a href="http://neatline.org/wp-content/uploads/2013/04/features_1.png"><img class="alignnone size-full wp-image-430" title="features_1" src="http://neatline.org/wp-content/uploads/2013/04/features_1.png" alt="" width="565" height="185" /></a>

This will reveal a map with some basic editing tools. If your browser supports
this and if you give this page permission to know where you are, the map will
also center on your current location.

<a href="http://neatline.org/wp-content/uploads/2013/04/features_2.png"><img class="alignnone size-full wp-image-431" title="features_2" src="http://neatline.org/wp-content/uploads/2013/04/features_2.png" alt="" width="565" height="602" /></a>

Simply zoom to the location you want to draw a feature on, select the drawing
tool, and draw.

<a href="http://neatline.org/wp-content/uploads/2013/04/features_3.png"><img class="alignnone size-full wp-image-432" title="features_3" src="http://neatline.org/wp-content/uploads/2013/04/features_3.png" alt="" width="563" height="597" /></a>

There are three drawing tools, from left to right:

* **Polygon** is for drawing multi-sided, closed shapes;
* **Point** is for drawing points;
* **Line** is for drawing lines and open shapes; and
* **Selection** is for selecting and editing the shapes.

Along the bottom of the map are also a number of tools you can us to change
shapes you've selected:

* **Save View** bookmarks the zoom level and map center point, which will be
  used whenever anyone views that coverage map;
* **Delete** removes a feature from the map;
* **Drag** adds a point to the shape so you can drag it to another location;
* **Scale** adds a point to the lower right of the shape so you can scale it
  and flip it; and
* **Rotate** adds a point to the lower right of the shape so you can rotate it.

NeatlineFeatures also provides you a choice of base layers for your map. To
select a different base layer, click on the stack of papers in the upper-right
corner of the coverage map and select from the list.

<a href="http://neatline.org/wp-content/uploads/2013/04/features_4.png"><img class="alignnone size-full wp-image-474" title="NeatlineFeatures select base layer" src="http://neatline.org/wp-content/uploads/2013/04/features_4.png" alt="" width="561" height="599" /></a>

### Textual Information

The feature metadata is in addition to the standard coverage field. You can
still enter text and HTML into the coverage field.

### View Coverage

Once you have drawn a coverage on an item, an interactive map viewer will
appear on the item view with the coverage you drew.

<a href="http://neatline.org/wp-content/uploads/2013/04/features_5.png"><img class="alignnone size-full wp-image-433" title="features_4" src="http://neatline.org/wp-content/uploads/2013/04/features_5.png" alt="" width="705" height="604" /></a>

Any text entered in the coverage field will display under the map.

**Note**: If you are a theme developer, be sure to include the 
[`public_append_to_items_show()`][public_append_to_items_show] hook where you
want the map to appear in your theme.

## Support

We use an [issue tracker][issues] for feedback on issues and requested
improvements. If you have general questions, you may also post them to
the [Omeka Forums][forums]  or the [Omeka Developers Group][groups].

## Credits

### Translations

* Martin Liebeskind (German)
* Gillian Price (Spanish)
* Oguljan Reyimbaeva (Russian)
* Katina Rogers (French)

[installing]: http://omeka.org/codex/Installing_a_Plugin "Installing a Plugin"
[public_append_to_items_show]: http://omeka.org/codex/Hooks/public_append_to_items_show "public_append_to_items_show"
[issues]: https://github.com/scholarslab/NeatlineFeatures/issues/ "issue tracker"
[forums]: http://omeka.org/forums/
[groups]: https://groups.google.com/forum/?fromgroups#!forum/omeka-dev

