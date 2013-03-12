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
<script type='text/javascript'>
// console.log('in script');
(function($, undefined) {
    // console.log('in scope');
    $(function() {
        // console.log('in ready');
        var options = {
            mode: <?php echo json_encode($mode) ?>,
            labels: {
                html : <?php echo json_encode(__('Use HTML')) ?>,
                map  : <?php echo json_encode(__('Use Map')) ?>
                },
            map_options: {
                styles: {
                    default_opacity: 0.4,
                    select_point_radius: 20,
                    point_graphic: {
                        normal  : '<?php echo img('pushpin-1.png'); ?>',
                        selected: '<?php echo img('pushpin-2.png'); ?>'
                        }
                    }
                },
            values: <?php echo json_encode($features) ?>
            };
        var existing = $("#element-38.nlfeatures-on").data('featurewidget');
        if (typeof existing !== 'undefined') {
            existing.destroy();
        }
        $("#<?php echo $parent_id ?>").featurewidget(options);
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
