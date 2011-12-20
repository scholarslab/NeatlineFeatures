<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package     omeka
 * @subpackage  neatline
 * @author      Scholars' Lab <>
 * @author      Eric Rochester <erochest@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?><?php

require_once 'NeatlineFeatures_Test.php';
require_once 'lib/NeatlineFeatures_Functions.php';

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

}

