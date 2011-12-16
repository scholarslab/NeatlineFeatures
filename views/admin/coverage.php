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
<div id="<? echo $id_prefix ?>widget" class='nlfeatures nlfeatures-edit'>
  <div class='nlfeatures-edit-nav'>
    <div class='nlfeatures-edit-nav-menu'>
      <ul>
        <li class='selected'><a href="#<? echo $id_prefix ?>maptab">NL Features</a></li>
        <li><a href="#<?echo $id_prefix ?>rawtab">Raw</a></li>
      </ul>
    </div>
    <div class='nlfeatures-edit-nav-tools'>
    </div>
  </div>
  <div class='nlfeatures-edit-body'>
    <div id="<? echo $id_prefix ?>rawtab" class='nlfeatures-edit-raw'>
      <!-- TODO: remove the next line -->
      <div><em><? echo $inputNameStem ?></em></div>
      <? echo $raw_field ?>
      <? echo $use_html ?>
      &nbsp;
    </div>
    <div id="<? echo $id_prefix ?>maptab" class='nlfeatures-edit-map'>
      <div id="<? echo $id_prefix ?>map"></div>
      <div class='nlfeatures-map-tools'></div>
    </div>
  </div>
</div>
<script type='text/javascript'>
(function($) {

    function initTabs() {
        var w, t;
        w = $("#<? echo $id_prefix ?>widget");
        t = w.simpletab({
            nav_list: ".nlfeatures-edit-nav-menu ul",
            tabchange: function(event, data) {
                data.tab.anchors.each(function() {
                    $(this.parentNode).removeClass('selected');
                });
                data.a.parent().addClass('selected');
                event.preventDefault();
            }
        });
    }

    function initOpenLayerMap() {
        var el, m, item;
        el = $(document.getElementById('<? echo $id_prefix ?>map'));
        m = el.nlfeatures({
            map: {
                raw_update: $('#<? echo $id_prefix ?>text')
            }
        })
            .data('nlfeatures');
        item = {
            id: el.attr('id'),
            title: 'Coverage',
            name: 'Coverage',
            wkt: <? echo json_encode(is_null($value) ? '' : $value) ?>
        };
        m.loadLocalData([item]);
        m.setViewport();
        m.editJson(item, true);
        // TODO: Delete this line.
        window._nlfeatureMap = m;
    }

    // This is a sledgehammer, but the response is proportional. Basically, if
    // there are any checked checkboxes in a field, Omeka turns on TinyMCE for 
    // all textareas in the field.  In this case, it's picking up
    // an OpenLayers checkbox and setting the raw textarea up incorrectly.
    //
    // Also, because of the way TinyMCE is handled, we have to use setTimeout 
    // to make sure it gets set back *after* it's incorrectly enabled. Double 
    // ugh.
    //
    // TODO: Bring this up on #omeka and file a bug report.  
    // admin/themes/default/javascripts/items.js, around line 410, should be 
    // more specific.
    $(function() {
        initTabs();
        initOpenLayerMap();
<? if (!$is_html) { ?>
        // For some reason, $() isn't working for this.
        var cb = $(document.getElementById('<? echo $id_prefix ?>html'));
        if (!cb.checked) {
            var pollTinyMCE = function() {
                var rawtab = document.getElementById('<? echo $id_prefix ?>rawtab');
                var eds = document.getElementsByClassName('mceEditor');
                if (eds.length == 0) {
                    setTimeout(function() { pollTinyMCE(); }, 100);
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false,
                        '<? echo $id_prefix ?>text');
                }
            }
            setTimeout(function() { pollTinyMCE(); }, 100);
        }
<? } ?>
    });

})(jQuery);
</script>
