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

    /**
     * This returns the features associated with an item.
     *
     * @param $item Omeka_Record The Omeka item associated with the features.
     *
     * @return array of NeatlineFeature
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getItemFeatures($item)
    {
        return $this->fetchObjects(
            $this->getSelect()->where('item_id=?', $item->id)
        );
    }

    /**
     * This clears out all records for the given item.
     *
     * @param $item Omeka_Record The item to delete features from.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function removeItemFeatures($item)
    {
        if (!is_null($item->id)) {
            $where = $this->getAdapter()->quoteInto('item_id=?', $item->id);
            $this->delete($this->getTableName(), $where);
        }
    }

    /**
     * This populates features for an item from an associative array, such as 
     * might be found in $_POST.
     *
     * @param $item   Omeka_Record The item to populate items for.
     * @param $params array        The array to pull data from. This will 
     * ususally be $_POST['Elements'][$id].
     *
     * @return array of NeatlineFeature The features created.
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function createFeatures($item, $params)
    {
        $name     = $this->getTableName();
        $item_id  = $item->id;
        $db       = $this->getDb();
        $coverage = $db
            ->getTable('Element')
            ->findByElementSetNameAndElementName('Dublin Core', 'Coverage');
        $cid      = $coverage->id;

        $sql     = $db->prepare(
            "INSERT INTO {$db->prefix}neatline_features
                (added, item_id, element_text_id, is_map)
                SELECT NOW(), ?, et.id, ?
                FROM {$db->prefix}element_texts et
                WHERE et.record_id=? AND et.text=? AND et.element_id=?;
            "
        );

        foreach ($params as $field) {
            $isMap = FALSE;
            try {
                $isMap = (bool)$field['mapon'];
            } catch (Exception $e) {
            }

            $data = array(
                $item_id, (int)$isMap, $item_id, $field['text'], $cid
            );
            $sql->execute($data);
        }

        return $this->getItemFeatures($item);
    }

    /**
     * This removes the current features for an item and re-creates them from the parameters.
     *
     * @param $item   Omeka_Record The item to populate items for.
     * @param $params array        The array to pull data from. This will 
     * ususally be $_POST['Elements'][$id].
     *
     * @return array of NeatlineFeature The features created.
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function updateFeatures($item, $params)
    {
        $this->removeItemFeatures($item);
        return $this->createFeatures($item, $params);
    }

}

