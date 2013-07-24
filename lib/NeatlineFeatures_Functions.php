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

require_once NEATLINE_FEATURES_PLUGIN_DIR .
    '/lib/NeatlineFeatures/Utils/View.php';

/**
 * This is a container class for a bunch of static method.
 **/
class NeatlineFeatures_Functions
{

    /**
     * This makes a best guess at whether a string contains WKT data.
     *
     * This test is pretty lame. Currently, it just looks for some feature 
     * types.
     *
     * @param string $maybeWkt This is the string to test.
     *
     * @return bool
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public static function isWkt($maybeWkt)
    {
        $isWkt       = 0;
        $wktFeatures = array(
            'POINT',
            'LINESTRING',
            'POLYGON',
            'MULTIPOINT',
            'MULTILINESTRING',
            'MULTIPOLYGON'
        );

        foreach ($wktFeatures as $feature) {
            $isWkt = $isWkt || (preg_match("/\\b$feature\\b/", $maybeWkt) > 0);
            if ($isWkt) {
                break;
            }
        }

        return $isWkt;
    }

    /**
     * This returns true if the input string is KML.
     *
     * @param string $coverage The text data from the coverage field.
     *
     * @return bool $isKml Whether the string is KML.
     * @author Eric Rochester
     **/
    public static function isKml($coverage)
    {
        $isKml  = false;

        $kmlNs = 'http://earth.google.com/kml/2.0';
        $names = array(
            'Point',
            'Polygon',
            'LineString'
        );

        try {
            $doc = new DOMDocument();
            @$doc->loadXML($coverage);

            $nodes = $doc->getElementsByTagNameNS($kmlNs, 'kml');
            if ($nodes->length === 1) {
                foreach ($names as $name) {
                    $isKml = $isKml ||
                        ($doc->getElementsByTagNameNS($kmlNs, $name)->length > 0);
                }
            }
        } catch (Exception $e) {
        }

        return $isKml;
    }

    /**
     * This attempts to pull the KML data from the rendered coverage field and 
     * checks that it is, in fact, KML.
     *
     * @param string $coverage The rendered coverage HTML.
     *
     * @return bool $isKml Does the coverage contain KML?
     * @author Eric Rochester
     **/
    public static function isKmlCoverage($coverage)
    {
        return (substr_compare($coverage, '<kml ', 0, 5) === 0);
    }

    /**
     * This returns the string to display a coverage field, whether a map or 
     * not.
     *
     * @param string           $text        The original text for the element.
     * @param Omeka_Record     $record      The record that this text applies 
     * to.
     * @param ElementText|NULL $elementText The ElementText record that stores 
     * this text. (This is optional and defaults to NULL.)
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public static function displayCoverage($text, $record, $elementText=NULL)
    {
        $util = new NeatlineFeatures_Utils_View();
        $util->setCoverageElement();
        $util->setViewOptions($text, $record, $elementText);

        $output = $util->getView();

        return $output;
    }

    public static function fclear($filename)
    {
        $f = fopen($filename, 'w');
        fclose($f);
    }

    public static function flog($filename, $msg)
    {
        $now = date(DATE_ISO8601);
        $f   = fopen($filename, 'a');
        fwrite($f, "[$now] $msg\n");
        fclose($f);
    }

    public static function fdump($filename, $name, $obj)
    {
        if (is_string($obj)) {
            $cname = 'string';
        } else if (is_array($obj)) {
            $cname = 'array';
        } else {
            $cname = '';
        }

        if (is_null($obj)) {
            $repr = "NULL";
        } else {
            $repr = print_r($obj, true);
        }

        NeatlineFeatures_Functions::flog($filename, "($cname) $name => $repr");
    }

    public static function fstack($filename, $name, $backtrace=null)
    {
        if (is_null($backtrace)) {
            $backtrace = debug_backtrace();
        }
        $buffer = '';
        $i = 0;
        foreach ($backtrace as $node) {
            $buffer .= "$i. ";
            if (array_key_exists('file', $node)) {
                $buffer .= basename($node['file']);
            }
            $buffer .= ":" . $node['function'];
            if (array_key_exists('line', $node)) {
                $buffer .=  " ({$node['line']})";
            }
            $buffer .= "\n";
            $i++;
        }
        NeatlineFeatures_Functions::flog($filename, "$name => $buffer");
    }

    public static function clog($msg)
    {
        echo '<script>console.log("' . str_replace('"', '\"', $msg) . '");</script>';
    }

    public static function cdump($obj)
    {
        NeatlineFeatures_Functions::clog(is_null($obj) ? "NULL" : print_r($obj, true));
    }

}
