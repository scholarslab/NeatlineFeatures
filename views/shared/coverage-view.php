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
<div id="<?php echo $view_id; ?>-view"></div>
<script type='text/javascript'>
(function($, undefined) {
    $(function() {
        var options = {
            mode: <?php echo json_encode($mode) ?>,
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
        $("#<?php echo $view_id; ?>-view").featurewidget(options);
    });

})(jQuery);
</script>
