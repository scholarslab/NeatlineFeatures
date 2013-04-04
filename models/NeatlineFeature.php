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

/**
 * This is a model class for the neatline_feature records.
 **/
class NeatlineFeature extends Omeka_Record_AbstractRecord
{

    //{{{ Properties

    /**
     * The timestamp that the feature was added.
     *
     * @var string
     **/
    public $added;

    /**
     * The ID of the item that this record is for.
     *
     * @var int
     **/
    public $item_id;

    /**
     * The element text that this record is for.
     *
     * @var int
     **/
    public $element_text_id;

    /**
     * Is the map enabled for this coverage field?
     *
     * @var int
     **/
    public $is_map;

    /**
     * This contains the KML describing the features on the map.
     *
     * @var string
     **/
    public $geo;

    /**
     * This is the zoom level for the saved view.
     *
     * @var int
     **/
    public $zoom;

    /**
     * This is the center longitude for the saved view.
     *
     * @var double
     **/
    public $center_lon;

    /**
     * This is the center latitude for the saved view.
     *
     * @var double
     **/
    public $center_lat;

    /**
     * A short code for the base layer. This is one of 'gphy' (Google 
     * Physical), 'gmap' (Google Streets), 'ghyb' (Google Hybrid), 'gsat' 
     * (Google Satellite), or 'osm' (Open Street Maps).
     *
     * @var string
     **/
    public $base_layer;

    //}}}

    //{{{ Constructors

    /**
     * This creates the NeatlineFeature object.
     *
     * @param $item         Omeka_Record The Omeka item associated with this 
     * feature.
     * @param $element_text ElementText The Omeka element text that this is 
     * associated with. If not given, it just takes the first element text for 
     * the Coverage field.
     *
     * @return NeatlineFeature $this
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function __construct($item=null, $element_text=null)
    {
        parent::__construct();

        if (!is_null($item)) {
            $this->item_id = $item->id;
        }
        if (!is_null($element_text)) {
            $this->element_text = $element_text->id;
        }

        // Default values.
        if (is_null($this->is_map)) {
            $this->is_map = false;
        }
        if (is_null($this->added)) {
            $this->added = date('c');
        }
    }

    //}}}

    //{{{ Methods

    /**
     * This gets the parent item record.
     *
     * @return Omeka_Record|null The parent item.
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getItem()
    {
        $item = null;

        if (!is_null($this->item_id)) {
            $item = $this->getTable('Item')->find($this->item_id);
        }

        return $item;
    }

    /**
     * This gets the associated element text record.
     *
     * @return ElementText|null The associated element text.
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getElementText()
    {
        $etext = null;

        if (!is_null($this->element_text_id)) {
            $etext = $this->getTable('ElementText')
                          ->find($this->element_text_id);
        }

        return $etext;
    }

    //}}}

}

