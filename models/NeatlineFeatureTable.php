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

require_once NEATLINE_FEATURES_PLUGIN_DIR . '/models/NeatlineFeature.php';

/**
 * This is a model class for the neatline_features table.
 **/
class NeatlineFeatureTable extends Omeka_Db_Table
{

    /**
     * This looks for a record from the data table and returns it, or this 
     * creates it if it doesn't exist.
     *
     * @param $item         Omeka_Record The Omeka item associated with this 
     * feature.
     * @param $element_text ElementText The Omeka element text that this is 
     * associated with. If not given, it just takes the first element text for 
     * the Coverage field.
     *
     * @return NeatlineFeature
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function createOrGetRecord($item, $element_text)
    {
        $record = $this->getRecordByItemAndElementText($item, $element_text);

        if (is_null($record)) {
            return new NeatlineFeature($item, $element_text);
        }

        return $record;
    }

    /**
     * This looks in the database for a neatline features row for an item or 
     * element. If it cannot find one, it returns null.
     *
     * @param $item         Omeka_Record The Omeka item associated with this 
     * feature.
     * @param $element_text ElementText The Omeka element text that this is 
     * associated with. If not given, it just takes the first element text for 
     * the Coverage field.
     *
     * @return NeatlineFeature|null
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getRecordByItemAndElementText($item, $element_text)
    {
        return $this->fetchObject(
            $this->getSelect()
                ->where('item_id=?', $item->id)
                ->where('element_text_id=?', $element_text->id)
        );
    }

}

