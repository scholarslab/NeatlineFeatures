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
    
    function __construct($inputNameStem, $value, $options, $record, $element)
    {
        $this->inputNameStem = $inputNameStem;
        $this->value = $value;
        $this->options = $options;
        $this->record = $record;
        $this->element = $element;
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
}
