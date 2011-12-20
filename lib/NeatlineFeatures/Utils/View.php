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

require_once dirname(__FILE__) . '/../../../../../application/helpers/Functions.php';

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
    private $inputNameStem;

    /**
     * This is the value of the Coverage element.
     *
     * @var string
     **/
    private $value;

    /**
     * These are options to use in creating the element.
     *
     * @var array
     **/
    private $options;

    /**
     * The record being displayed.
     *
     * @var Omeka_Record
     **/
    private $record;

    /**
     * The element object of the Coverage field.
     *
     * @var Element
     **/
    private $element;

    /**
     * The text of a field value.
     *
     * @var string
     **/
    private $text;

    /**
     * The ElementText representing that the text was taken from.
     *
     * @var ElementText
     **/
    private $elementText;
    
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
        $this->inputNameStem = $inputNameStem;
        $this->value         = $value;
        $this->options       = $options;
        $this->record        = $record;
        $this->element       = $element;
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
        $this->text        = $text;
        $this->record      = $record;
        $this->elementText = $elementText;
    }

    /**
     * This returns the element ID as parsed from $inputNameStem.
     *
     * @return int
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getElementId()
    {
        return $this->element->id;
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
        $count = preg_match('/^Elements\[\d+\]\[(\d+)\]/',
                            $this->inputNameStem, $matches);
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
                    $this->inputNameStem . '[text]', 
                    $this->value, 
                    array('class'=>'textinput', 'rows'=>5, 'cols'=>50));
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
            $posted = !empty($_POST['Elements'][$this->element->id]);
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
        $edata = $_POST['Elements'][$this->element->id];
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
        $texts = $this->record->getTextsByElement($this->element);
        $text = null;

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
        $is_html = FALSE;

        if ($this->isPosted()) {
            try {
                $is_html = (bool)$_POST['Elements'][$this->getElementId()]
                    [$this->getIndex()]['html'];
            } catch (Exception $e) {
                $is_html = FALSE;
            }
        } else {
            $etext = $this->getElementText();
            if (isset($etext)) {
                $is_html = (bool)$etext->html;
            }
        }

        return $is_html;
    }

    /**
     * This returns the HTML for the "Use HTML" widget.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getUseHtml()
    {
        $use_html  = '';

        $use_html .= '<label class="use-html">Use HTML ';
        $use_html .= __v()->formCheckbox(
            $this->inputNameStem . '[html]', 1,
            array('checked'=>$this->isHtml())
        );
        $use_html .= '</label>';

        return $use_html;
    }

    /**
     * This returns the HTML for the edit control.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function getEditControl()
    {
        $inputNameStem = $this->inputNameStem;
        $value         = $this->value;
        $options       = $this->options;
        $record        = $this->record;
        $element       = $this->element;

        $id_prefix = preg_replace('/\W+/', '-', $inputNameStem);
        $raw_field = $this->getRawField();
        $is_html   = $this->isHtml();
        $use_html  = $this->getUseHtml();

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
        $text        = strip_tags($this->text);
        $record      = $this->record;
        $elementText = $this->elementText;
        $id_prefix   = uniqid("nlfeatures-") . '-';

        ob_start();
        include NEATLINE_FEATURES_PLUGIN_DIR . '/views/shared/coverage.php';
        return ob_get_clean();
    }
}

