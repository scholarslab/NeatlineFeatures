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

require_once dirname(__FILE__) .
    '/../../../../../application/helpers/Functions.php';

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

    function __construct()
    {
    }

    /**
     * This sets the options necessary to create the edit view.
     *
     * @param string       $inputNameStem The stem of the input name.
     * @param string       $value         The initial value for the input.
     * @param array        $options       Additional options.
     * @param Omeka_Record $record        The element's record.
     * @param Element      $element       The Element.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setEditOptions($inputNameStem, $value, $options, $record,
        $element)
    {
        $this->_inputNameStem = $inputNameStem;
        $this->_value         = $value;
        $this->_options       = $options;
        $this->_record        = $record;
        $this->_element       = $element;
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
        $matches = array();
        $count   = preg_match(
            '/^Elements\[\d+\]\[(\d+)\]/',
            $this->_inputNameStem,
            $matches
        );
        return ($count != 0 ? $matches[1] : NULL);
    }

    /**
     * This constructs the TEXTAREA for the free-form coverage data and returns 
     * it as a string.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getFreeField()
    {
        return __v()->formTextarea(
            $this->_inputNameStem . '[free]',
            $this->_value,
            array('class'=>'textinput', 'rows'=>5, 'cols'=>50)
        );
    }

    /**
     * This constructs a hidden field for the WKT and free-form coverage data 
     * and returns it as a string.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getTextField()
    {
        return __v()->formHidden(
            $this->_inputNameStem . '[text]',
            $this->_value
        );
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
        $eid  = (string)$this->getElementId();
        if (array_key_exists('Elements', $_POST) &&
            array_key_exists($eid, $_POST['Elements'])) {
            $post = $_POST['Elements'][$eid];
        }
        return $post;
    }

    /**
     * This returns the value of the 'html' field from the POST request.
     *
     * @return string|NULL
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getHtmlValue()
    {
        $edata = $_POST['Elements'][$this->_element->id];
        $value = $edata[$this->getIndex()]['html'];

        return $value;
    }

    /**
     * This returns the ElementText for the current element and index or NULL.
     *
     * @return ElementText|NULL
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getElementText()
    {
        $index = $this->getIndex();
        $texts = $this->_record->getTextsByElement($this->_element);
        $text  = NULL;

        if ($index !== NULL) {
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
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function isHtml()
    {
        $isHtml = FALSE;

        if ($this->isPosted()) {
            try {
                $isHtml = (bool)$_POST['Elements'][$this->getElementId()]
                    [$this->getIndex()]['html'];
            } catch (Exception $e) {
                $isHtml = FALSE;
            }
        } else {
            $etext = $this->getElementText();
            if (isset($etext)) {
                $isHtml = (bool)$etext->html;
            }
        }

        return $isHtml;
    }

    /**
     * This predicate tests whether this element currently is marked to have 
     * map data.
     *
     * @return bool
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function isMap()
    {
        $isMap = FALSE;

        if ($this->isPosted()) {
            try {
                $isMap = (bool)$_POST['Elements'][$this->getElementId()]
                    [$this->getIndex()]['mapon'];
            } catch (Exception $e) {
                $isMap = FALSE;
            }
        } else {
            $etext = $this->getElementText();
            if (isset($etext)) {
                $db      = get_db();
                $record  = $db
                    ->getTable('Item')
                    ->find($etext->record_id);
                $feature = $db
                    ->getTable('NeatlineFeature')
                    ->getRecordByItemAndElementText($record, $etext);

                if (isset($feature)) {
                    $isMap = (bool)$feature->is_map;
                }
            }

            $db  = get_db();
            $sql = $db
                ->select()
                ->from("{$db->prefix}neatline_features")
                ->where('item_id=?');
            $stmt = $db->query($sql, array($this->_record->id));
            $stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
            $result = $stmt->fetchAll();

            $isMap = (count($result) == 1) ? (bool)$result[0]['is_map'] : FALSE;
        }

        return $isMap;
    }

    /**
     * This returns the string for a "Use X" widget.
     *
     * @param $key   string The key for the widget's name.
     * @param $label string The label (X above).
     * @param $value bool   Is it checked or not?
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    private function _getUseWidget($key, $label, $value)
    {
        $use = '';

        $use .= "<label class='use-$key'>Use $label ";
        $use .= __v()->formCheckbox(
            "{$this->_inputNameStem}[$key]",
            1,
            array( 'checked' => $value )
        );
        $use .= '</label>';

        return $use;
    }

    /**
     * This returns the HTML for the "Use HTML" widget.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getUseHtml()
    {
        return $this->_getUseWidget('html', 'HTML', $this->isHtml());
    }

    /**
     * This returns the HTML for the "Use Map" widget.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getUseMap()
    {
        return $this->_getUseWidget('mapon', 'Map', $this->isMap());
    }

    /**
     * This returns the HTML for the edit control.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getEditControl()
    {
        $inputNameStem = $this->_inputNameStem;
        $value         = $this->_value;
        $options       = $this->_options;
        $record        = $this->_record;
        $element       = $this->_element;

        $idPrefix  = preg_replace('/\W+/', '-', $inputNameStem);
        $freeField = $this->getFreeField();
        $textField = $this->getTextField();
        $isHtml    = $this->isHtml();
        $useHtml   = $this->getUseHtml();
        $useMap    = $this->getUseMap();

        ob_start();
        include NEATLINE_FEATURES_PLUGIN_DIR . '/views/admin/coverage.php';
        return ob_get_clean();
    }

    /**
     * This returns the HTML to view a coverage features map.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getView()
    {
        $text        = strip_tags($this->_text);
        $record      = $this->_record;
        $elementText = $this->_elementText;
        $idPrefix    = uniqid("nlfeatures-") . '-';

        ob_start();
        include NEATLINE_FEATURES_PLUGIN_DIR . '/views/shared/coverage.php';
        return ob_get_clean();
    }
}

