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
 * @author      Bethany Nowviskie <bethany@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @author      Eric Rochester <erochest@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?>
<div id="<?php echo $idPrefix ?>widget" class='nlfeatures nlfeatures-edit'>
  <div class='nlfeatures-edit-nav'>
    <div class='nlfeatures-edit-nav-menu'>
      <ul>
        <li class='selected'>
            <a href="#<?php echo $idPrefix ?>maptab">NL Features</a>
        </li>
        <li><a href="#<?php echo $idPrefix ?>rawtab">Raw</a></li>
      </ul>
    </div>
    <div class='nlfeatures-edit-nav-tools'>
    </div>
  </div>
  <div class='nlfeatures-edit-body'>
    <div id="<?php echo $idPrefix ?>rawtab" class='nlfeatures-edit-raw'>
      <!-- TODO: remove the next line -->
      <div><em><?php echo $inputNameStem ?></em></div>
      <?php echo $rawField ?>
      <?php echo $useHtml ?>
      &nbsp;
    </div>
    <div id="<?php echo $idPrefix ?>maptab" class='nlfeatures-edit-map'>
      <div id="<?php echo $idPrefix ?>map"></div>
      <div class='nlfeatures-map-tools'></div>
    </div>
  </div>
</div>
<script type='text/javascript'>
(function($) {

    $(function() {
        var tabs = {
            raw: '#<?php echo $idPrefix ?>rawtab',
            map: '#<?php echo $idPrefix ?>maptab'
        };
        var formats = {
            is_html: <?php echo json_encode($isHtml) ?>,
                is_wkt: <?php
echo json_encode(NeatlineFeatures_Functions::isWkt($value))
                ?>
        };
        var widgets = {
            map: '#<?php echo $idPrefix ?>map',
            text: '#<?php echo $idPrefix ?>text',
            html: '#<?php echo $idPrefix ?>html'
        };
        NLFeatures.editCoverageMap(
            '#<?php echo $idPrefix ?>widget',
            tabs,
            widgets,
            <?php echo json_encode(is_null($value) ? '' : $value) ?>,
            formats
        );
    });

})(jQuery);
</script>
