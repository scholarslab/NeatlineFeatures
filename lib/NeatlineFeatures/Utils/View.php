<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * PHP version 5
 *
 * @package     omeka
 * @subpackage  neatline
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
     * The $elementText isn't currently used, and null can be passed in in its 
     * place.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setViewOptions($text, $record, $elementText)
    {
        $this->_text        = $text;
        $this->_record      = $record;
        $this->_elementText = $elementText;
    }

    /**
     * This returns the element ID as parsed from $inputNameStem.
     *
     * @return int
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getElementId()
    {
        return $this->_element->id;
    }

    /**
     * This returns the element data's index as parsed from $inputNameStem.
     *
     * @return int|null
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getIndex()
    {
        $matches = array();
        $count = preg_match(
            '/^Elements\[\d+\]\[(\d+)\]/',
            $this->_inputNameStem,
            $matches
        );
        return ($count != 0 ? $matches[1] : null);
    }

    /**
     * This constructs the TEXTAREA for the raw coverage data and returns it as 
     * a string.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getRawField()
    {
        return __v()->formTextarea(
            $this->_inputNameStem . '[text]', 
            $this->_value, 
            array('class'=>'textinput', 'rows'=>5, 'cols'=>50)
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
        $posted = FALSE;
        if (array_key_exists('Elements', $_POST)) {
            $posted = !empty($_POST['Elements'][$this->_element->id]);
        }    
        return $posted;
    }

    /**
     * This returns the value of the 'html' field from the POST request.
     *
     * @return string|null
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getHtmlValue()
    {
        $edata = $_POST['Elements'][$this->_element->id];
        $value = $edata[$this->getIndex()]['html'];

        return $value;
    }

    /**
     * This returns the ElementText for the current element and index or null.
     *
     * @return ElementText|null
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getElementText()
    {
        $index = $this->getIndex();
        $texts = $this->_record->getTextsByElement($this->_element);
        $text  = null;

        if ($index !== null) {
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
     * This returns the HTML for the "Use HTML" widget.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getUseHtml()
    {
        $useHtml  = '';

        $useHtml .= '<label class="use-html">Use HTML ';
        $useHtml .= __v()->formCheckbox(
            $this->_inputNameStem . '[html]', 1,
            array('checked'=>$this->isHtml())
        );
        $useHtml .= '</label>';

        return $useHtml;
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

        $idPrefix = preg_replace('/\W+/', '-', $inputNameStem);
        $rawField = $this->getRawField();
        $isHtml   = $this->isHtml();
        $useHtml  = $this->getUseHtml();

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

