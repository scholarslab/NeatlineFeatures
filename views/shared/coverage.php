<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PHP version 5
 *
 * @package     omeka
 * @subpackage  nlfeatures
 * @author      Scholars' Lab <>
 * @author      Bethany Nowviskie <bethany@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @author      Eric Rochester <erochest@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?>
<div id="<?php echo $idPrefix ?>widget"></div>
<script type='text/javascript'>
(function($, undefined) {
    $(function() {
        var options = {
            mode: <?php echo json_encode($mode) ?>,
            id_prefix: <?php echo json_encode('#' . $idPrefix) ?>,
            name_prefix: <?php echo json_encode($inputNameStem) ?>,
            labels: {
                html : <?php echo json_encode(__('Use HTML')) ?>,
                map  : <?php echo json_encode(__('Use Map')) ?>
                },
            map_options: {
                styles: {
                    default_opacity: 0.4,
                    select_point_radius: 20
                    }
                },
            values: {
                geo: <?php echo json_encode(is_null($geo) ? '' : $geo) ?>,
                zoom: <?php echo json_encode(is_null($zoom) ? '' : $zoom) ?>,
                center: {
                    lon: <?php echo json_encode(is_null($center_lon) ? '' : $center_lon) ?>,
                    lat: <?php echo json_encode(is_null($center_lat) ? '' : $center_lat) ?>
                    },
                base_layer: <?php echo json_encode(is_null($base_layer) ? '' : $base_layer) ?>,
                text: <?php echo json_encode(is_null($value) ? '' : $value) ?>,
                is_html: <?php echo json_encode(is_null($isHtml) ? '' : $isHtml) ?>,
                is_map: <?php echo json_encode(is_null($isMap) ? '' : $isMap) ?>
                }
            };
        $("#<?php echo $idPrefix ?>widget").featurewidget(options);
    });

    // A nasty hack to clobber the current way that TinyMCE is set up for any 
    // element that has *any* checked checkbox in them.
    if (window.Omeka !== undefined && Omeka.Elements !== undefined) {
        Omeka.Elements.enableWysiwyg = function (element) {
            $(element).find('div.inputs label[class="use-html"] input[type="checkbox"]').each(function () {
                var textarea = $(this).parents('.input-block').find('textarea');
                if (textarea.length) {
                    var textareaId = textarea.attr('id');
                    var enableIfChecked = function () {
                        if (this.checked) {
                            tinyMCE.execCommand("mceAddControl", false, textareaId);
                        } else {
                            tinyMCE.execCommand("mceRemoveControl", false, textareaId);
                        }
                    };

                    enableIfChecked.call(this);

                    // Whenever the checkbox is toggled, toggle the WYSIWYG editor.
                    $(this).click(enableIfChecked);
                }
            });
        };
    }

})(jQuery);
</script>
