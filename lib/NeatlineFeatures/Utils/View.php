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
 * This contructs the view and holds a bunch of utility methods.
 *
 * TODO: A bunch of this was just copied-and-pasted from the Omeka code-base.
 * Surely there must be a better way.  Bring this up on #omeka.
 **/
class NeatlineFeatures_Utils_View
{
    /**
     * This is the name stem to use in constructing the form.
     *
     * @var string
     **/
    private $_inputNameStem;

    /**
     * This is the value of the Coverage element.
     *
     * @var string
     **/
    private $_value;

    /**
     * These are options to use in creating the element.
     *
     * @var array
     **/
    private $_options;

    /**
     * The record being displayed.
     *
     * @var Omeka_Record
     **/
    private $_record;

    /**
     * The element object of the Coverage field.
     *
     * @var Element
     **/
    private $_element;

    /**
     * The text of a field value.
     *
     * @var string
     **/
    private $_text;

    /**
     * The ElementText representing that the text was taken from.
     *
     * @var ElementText
     **/
    private $_elementText;

    /**
     * A cached copy of the current NeatlineFeature
     *
     * @var NeatlineFeature
     **/
    private $_feature;

    /**
     * The index of the element in the form.
     *
     * @var int
     **/
    private $_index;

    public $debug;

    function __construct()
    {
        $this->debug = false;
    }

    /**
     * This sets the options necessary to create the edit view.
     *
     * @param Omeka_Record $record    The element's record.
     * @param Element      $element   The Element.
     * @param string       $value     The value of the element.
     * @param string       $name_stem The @id name stem for the input element.
     * @param int          $index     The index of the element in the list of
     *                                coverage fields for the item.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setEditOptions($record, $element, $value, $name_stem, $index)
    {
        $this->_record        = $record;
        $this->_element       = $element;
        $this->_value         = $value;
        $this->_inputNameStem = $name_stem;
        $this->_index         = $index;
    }

    /**
     * This finds the element text from the value of the element, record, and
     * value.
     *
     * @param $record  Omeka_Record The record associated with the ElementText.
     * @param $element Element      The element to look for.
     * @param $value   string       The string value of the ElementText.
     *
     * @return ElementText|null
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function findElementText($record, $element, $value)
    {
        $etext = null;

        if (!is_null($record) && !is_null($record->id) && !is_null($element)) {
            $table = get_db()
                ->getTable('ElementText');
            $search = $table
                ->getSelect()
                ->where('record_id=?',   $record->id)
                ->where('element_id=?',  $element->id)
                ->where('text=?',        is_null($value) ? '' : $value);
            if (!is_null($element->data_type_id)) {
                $search = $search
                    ->where('record_type=?', $element->data_type_id);
            }
            $etext = $table->fetchObject($search);
        }

        return $etext;
    }

    /**
     * This sets the options necessary to create the view.
     *
     * @param string           $text        The original text for the element.
     * @param Omeka_Record     $record      The record that this text applies
     * to.
     * @param ElementText|NULL $elementText The ElementText record that stores
     * this text. (This is optional and defaults to NULL.)
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setViewOptions($text, $record, $elementText=NULL)
    {
        $this->_text        = $text;
        $this->_record      = $record;
        $this->_elementText = $elementText;
        if (!is_null($elementText) && $elementText != NULL) {
            $this->_value   = $elementText->text;
        }
        $this->createInputNameStem();
    }

    /**
     * This sets the input name stem from the element text, if it's available.
     * Otherwise, it sets a random number.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function createInputNameStem()
    {
        if (isset($this->_inputNameStem)) {
            return $this->_inputNameStem;
        }

        $stem = NULL;

        if (is_null($this->_elementText)) {
            $stem = uniqid("nlfeatures") . '_';
        } else {
            $feature = $this->getNeatlineFeature();
            if (is_null($feature) || !isset($feature->id) || is_null($feature->id)) {
                $stem = uniqid("nlfeatures");
            } else {
                $stem = "nlfeatures{$feature->id}_";
            }
        }

        $this->_inputNameStem = $stem;
        return $stem;
    }

    /**
     * This returns either the valid nlfeature object for this ElementText or
     * its own.
     *
     * @return NeatlineFeature
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getNeatlineFeature()
    {
        if (isset($this->_feature)) {
            return $this->_feature;
        }
        $feature = null;
        $etext = $this->getElementText();

        if (is_null($etext)) {
            if (isset($this->_value) && !is_null($this->_value)) {
                $feature = get_db()
                    ->getTable('NeatlineFeature')
                    ->getRecordByText($this->_value);
            } else {
                $feature = new NeatlineFeature();
            }
        } else {
            $feature = get_db()
                ->getTable('NeatlineFeature')
                ->getRecordByElementText($etext);
        }
        $this->_feature = $feature;
        return $feature;
    }

    /**
     * This sets the element to the dc:coverage field.
     *
     * @return Element The Element for the dc:coverage field.
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setCoverageElement()
    {
        $coverage = get_db()
            ->getTable('Element')
            ->findByElementSetNameAndElementName('Dublin Core', 'Coverage');
        $this->setElement($coverage);
        return $coverage;
    }

    /**
     * This sets the current record.
     *
     * @param $record Omeka_Record The current item.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setRecord($record)
    {
        $this->_record = $record;
    }

    /**
     * This sets the Element that this will use.
     *
     * @param $element Element The element to use.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setElement($element)
    {
        $this->_element = $element;
    }

    /**
     * This returns the element ID.
     *
     * @return int
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getElementId()
    {
        return $this->_element->id;
    }

    /**
     * This returns the current element.
     *
     * @return Element
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * This returns the element data's index as parsed from $inputNameStem.
     *
     * @return int|NULL
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getIndex()
    {
        if (!isset($this->_index)) {
            $matches = array();
            $count   = preg_match(
                '/^Elements\[\d+\]\[(\d+)\]/',
                $this->_inputNameStem,
                $matches
            );
            $this->_index = ($count != 0 ? $matches[1] : NULL);
        }

        return $this->_index;
    }

    /**
     * This predicate tests whether data for the element is in the POST
     * request.
     *
     * @return bool
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function isPosted()
    {
        return (! is_null($this->getPost()));
    }

    /**
     * This returns the POST data for this, if it's available.
     *
     * @return array|null
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getPost()
    {
        $post = null;
        if (count($_POST) > 0) {
            $eid  = (string)$this->getElementId();
            if (array_key_exists('Elements', $_POST) &&
                array_key_exists($eid, $_POST['Elements'])) {
                    $post = $_POST['Elements'][$eid];
                }
        }
        return $post;
    }

    /**
     * This returns the value of the 'html' field from the POST request.
     *
     * @param $index int The index of the value to return.
     *
     * @return string|NULL
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getHtmlValue($index=null)
    {
        $edata = $_POST['Elements'][$this->_element->id];
        $index = is_null($index) ? $this->getIndex() : $index;
        $value = $edata[$index]['html'];
        return $value;
    }

    /**
     * This returns the ElementText instances for the current element or NULL.
     *
     * @return array of ElementText|NULL
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getElementText()
    {
        $text = NULL;

        if (isset($this->_elementText) && ! is_null($this->_elementText)) {
            $text = $this->_elementText;
        } else {
            $index     = $this->getIndex();
            $textTable = get_db()->getTable('ElementText');
            $select    = $textTable
                ->getSelectForRecord($this->_record->id, get_class($this->_record))
                ->where('element_texts.element_id=?', (int)$this->_element->id);
            $texts     = $textTable->fetchObjects($select);
            if (array_key_exists($index, $texts)) {
                $text = $texts[$index];
            }
        }

        return $text;
    }

    /**
     * This predicate tests whether this element currently is marked to have 
     * HTML data.
     *
     * @return bool
     * @author Eric Rochester
     **/
    public function isHtml()
    {
        $isHtml = 0;

        if ($this->isPosted()) {
            try {
                $container = $_POST['Elements'][$this->getElementId()];
                $i         = $this->getIndex();
                if (array_key_exists($i, $container)) {
                    $isHtml = (bool)$container[$i]['html'];
                }
            } catch (Exception $e) {
                $isHtml = 0;
            }
        } else {
            $etext = $this->getElementText();
            if (isset($etext) && $etext != NULL && !is_null($etext)) {
                $isHtml = (bool)$etext->html;
            }
        }

        return $isHtml;
    }

    /**
     * This attempts to find the index in _POST, based on the current element 
     * text.
     *
     * This also caches any matching NeatlineFeature objects as 
     * $this->_feature.
     *
     * @return $index int|null
     * @author Eric Rochester
     **/
    private function _findIndex()
    {
        $i     = 0;
        $j     = null;
        $etext = $this->getElementText();
        $els   = $_POST['Elements'][$this->getElementId()];

        if (isset($this->_inputNameStem) &&
            strpos($this->_inputNameStem, "Elements") === 0) {
            $j = intval(substr(
                $this->_inputNameStem,
                13,
                strlen($this->_inputNameStem) - 14
            ));
        } else if (is_null($etext)) {
            $j = count($els) - 1;
        } else {
            while (array_key_exists($i, $els)) {
                $text    = $els[$i]['text'];
                if ($etext->text == $els[$i]['text']) {
                    $j            = $i;
                    $this->_index = $j;
                    break;
                }

                $i++;
            }
        }

        return $j;
    }

    /**
     * This predicate tests whether this element currently is marked to have
     * map data.
     *
     * @param $index integer|null This is the index of the element in the
     * output/input.
     *
     * @return bool
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function isMap($index=null)
    {
        $isMap = 0;

        if ($this->isPosted()) {
            $index = is_null($index) ? $this->_findIndex() : $index;
            $el    = $_POST['Elements'][$this->getElementId()];
            if (array_key_exists($index, $el)) {
                try {
                    $isMap = (bool)$el[$index]['mapon'];
                } catch (Exception $e) {
                }
            }
        } else {
            $etext = $this->getElementText();
            if (isset($etext) && !is_null($etext)) {
                $feature = $this->getNeatlineFeature();
                if (! is_null($feature)) {
                    $isMap = (bool)$feature->is_map;
                }
            }
        }

        return $isMap;
    }

    /**
     * This returns the HTML for the edit control.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getEditControl()
    {
        return $this->_getHtmlView($this->isHtml(), $this->isMap(), 'edit');
    }

    /**
     * This returns the HTML to view a coverage features map.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getView()
    {
        $isHtml = $this->isHtml();
        $isMap  = $this->isMap();
        $etext  = $this->_elementText;

        // Pull a fresh $value, if we can.
        $value = (is_null($etext) || $etext == NULL)
               ? $this->_text : $etext->getText();

        if (! is_null($etext) && $etext != NULL && is_null($etext->record_id)) {
            // There's no data for this.
            $view = '';
        } else if ((bool)$isMap) {
            $view = $this->getViewMap($isHtml);
        } else {
            if (($i = strpos($value, "\n")) != FALSE) {
                $view = substr($value, $i + 1);
            } else {
                $view = $value;
            }
        }

        return $view;
    }

    /**
     * This returns the HTML string for the Map widget.
     *
     * @param $isHtml bool Does the text value contain HTML?
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getViewMap($isHtml)
    {
        return $this->_getHtmlView($isHtml, 1, 'view');
    }

    /**
     * @param $feature     NeatlineFeature
     * @param $elementText ElementText
     * @return array
     *
     **/
    private function _elementTextToFeature($feature, $elementText) {
        return array(
            'is_map'     => $feature->is_map,
            'geo'        => $feature->geo,
            'zoom'       => $feature->zoom,
            'center'     => array(
                'lon'    => $feature->center_lon,
                'lat'    => $feature->center_lat
            ),
            'base_layer' => $feature->base_layer,
            'is_html'    => $elementText->html,
            'text'       => $elementText->text
        );
    }

    /**
     * This actually handles setting up the environment and passing execution
     * off to a PHP-HTML file.
     *
     * @param $isHtml bool   Does the text value contain HTML?
     * @param $isMap  bool   Does the features table have map coverage data?
     * @param $view   string The mode to put the widget in.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    private function _getHtmlView($isHtml, $isMap, $mode)
    {
        $inputNameStem = $this->_inputNameStem;
        $idPrefix      = preg_replace('/\W+/', '-', $inputNameStem);
        $options       = $this->_options;
        $record        = $this->_record;
        $element       = $this->_element;
        $post          = $this->getPost();
        $features      = array();
        $view_id       = '';

        if (isset($this->_value) && !is_null($this->_value)) {
            $value = $this->_value;
        } else {
            $value = $this->_text;
        }

        if (is_null($post)) {
            $feature    = $this->getNeatlineFeature();
            if (!is_null($feature)) {
                $geo        = $feature->geo;
                $zoom       = $feature->zoom;
                $center_lon = $feature->center_lon;
                $center_lat = $feature->center_lat;
                $base_layer = $feature->base_layer;
            }
        } else {
            $i          = $this->getIndex();
            $p          = array_key_exists($i, $post) ? $post[$i] : array();
            $geo        = isset($p['geo'])        ? $p['geo']        : null;
            $zoom       = isset($p['zoom'])       ? $p['zoom']       : null;
            $center_lon = isset($p['center_lon']) ? $p['center_lon'] : null;
            $center_lat = isset($p['center_lat']) ? $p['center_lat'] : null;
            $base_layer = isset($p['base_layer']) ? $p['base_layer'] : null;
        }

        ob_start();
        include NEATLINE_FEATURES_PLUGIN_DIR . "/views/shared/coverage.php";
        return ob_get_clean();
    }

}

