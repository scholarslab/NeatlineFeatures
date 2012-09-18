<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * PHP version 5
 *
 * @package     omeka
 * @subpackage  nlfeatures
 * @author      Scholars' Lab <>
 * @author      Eric Rochester <erochest@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?><?php

require_once NEATLINE_FEATURES_PLUGIN_DIR . '/tests/NeatlineFeatures_Test.php';
require_once NEATLINE_FEATURES_PLUGIN_DIR . '/lib/NeatlineFeatures_Functions.php';

/**
 * This tests the various utility functions in NeatlineFeatures_Functions.php.
 **/
class NeatlineFeatures_Functions_Test extends NeatlineFeatures_Test
{

    /**
     * This tests that is_wkt correctly finds points.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsWktPoint()
    {
        $this->assertTrue(NeatlineFeatures_Functions::isWkt("SOMETHING|POINT (1 2)|ELSE"));
    }

    /**
     * This tests that is_wkt correctly finds lines.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsWktLineString()
    {
        $this->assertTrue(NeatlineFeatures_Functions::isWkt("AAA|LINESTRING (1 2,3 4)|BBB"));
    }

    /**
     * This tests that is_wkt correctly finds polygons.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsWktPolygon()
    {
        $this->assertTrue(
            NeatlineFeatures_Functions::isWkt("111|POLYGON ((1 2,3 4),(5 6,7 8))|222")
        );
    }

    /**
     * This tests that is_wkt correctly finds multi-points.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsWktMultPoint()
    {
        $this->assertTrue(
            NeatlineFeatures_Functions::isWkt("AD|MULTIPOINT (1 2,3 4)|HOC")
        );
    }

    /**
     * This tests that is_wkt correctly finds multi-lines.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsWktMultiLineString()
    {
        $this->assertTrue(
            NeatlineFeatures_Functions::isWkt("ET|MULTILINESTRING (1 2,3 4,5 6)|CETERA")
        );
    }

    /**
     * This tests that is_wkt correctly finds multi-polygons.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsWktMultiPolygon()
    {
        $this->assertTrue(
            NeatlineFeatures_Functions::isWkt("BEGIN|MULTIPOLYGON (1 2,3 4,5 6)|END")
        );
    }

    /**
     * This tests that is_wkt correctly misses trash of various kinds.
     *
     * This test is bogus. Really, the who function is a little lame, until I 
     * can implement a full WKT parser. Which won't happen, so don't hold your 
     * breath.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsWktTrash()
    {
        $this->assertFalse(
            NeatlineFeatures_Functions::isWkt("ABCDEFGHIJKLMNOPQRSTUVWXYZ")
        );
    }

    /**
     * This tests whether a string is KML.
     *
     * @return void
     * @author Eric Rochester
     **/
    public function testIsKml()
    {
        $this->assertTrue(
            NeatlineFeatures_Functions::isKml(
                "<?xml version='1.0'?>\n" .
                "<kml xmlns='http://earth.google.com/kml/2.0'>\n" .
                "<Placemark>" .
                "<Point>" .
                "<coordinates>0, 0</coordinates>" .
                "</Point>" .
                "</Placemark>" .
                "</kml>"
            )
        );
        $this->assertTrue(
            NeatlineFeatures_Functions::isKml(
                "<?xml version='1.0'?>\n" .
                "<kml xmlns='http://earth.google.com/kml/2.0'>\n" .
                "<Placemark>" .
                "<LineString>" .
                "<coordinates>0, 0</coordinates>" .
                "</LineString>" .
                "</Placemark>" .
                "</kml>"
            )
        );
        $this->assertTrue(
            NeatlineFeatures_Functions::isKml(
                "<?xml version='1.0'?>\n" .
                "<kml xmlns='http://earth.google.com/kml/2.0'>\n" .
                "<Placemark>" .
                "<Polygon>" .
                "<coordinates>0, 0</coordinates>" .
                "</Polygon>" .
                "</Placemark>" .
                "</kml>"
            )
        );
        $this->assertTrue(
            NeatlineFeatures_Functions::isKml(
                "<kml:kml xmlns:kml='http://earth.google.com/kml/2.0'>\n" .
                "<kml:Placemark>" .
                "<kml:Polygon>" .
                "<kml:coordinates>0, 0</kml:coordinates>" .
                "</kml:Polygon>" .
                "</kml:Placemark>" .
                "</kml:kml>"
            )
        );

        $this->assertFalse(
            NeatlineFeatures_Functions::isKml(
                "<kml:kml xmlns:kml='http://earth.google.com/kml/2.0'>\n" .
                "</kml:kml>"
            )
        );
        $this->assertFalse(
            NeatlineFeatures_Functions::isKml(
                "<kml>\n" .
                "</kml>"
            )
        );
        $this->assertFalse(
            NeatlineFeatures_Functions::isKml(
                "<?xml version='1.0'?>\n" .
                "<html>\n" .
                "</html>"
            )
        );
        $this->assertFalse(
            NeatlineFeatures_Functions::isKml(
                "<?xml version='1.0'?>\n" .
                "<html>\n" .
                "</html>"
            )
        );
        $this->assertFalse(
            NeatlineFeatures_Functions::isKml("MULTIPOLYGON (1 2,3 4,5 6)|END")
        );
    }

}

