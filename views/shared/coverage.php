<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

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
<div id="<?php echo $idPrefix ?>nlf" class='nlfeatures'>
  <div id="<?php echo $idPrefix ?>map" class='map map-container'>
  </div>
  <div id="<?php echo $idPrefix ?>free" class='freetext'>
  </div>
</div>
<script type='text/javascript'>
(function($) {
    $(function() {
        var options = {
            mode: 'view',
            id_prefix: <?php echo json_encode('#' . $idPrefix) ?>,
            map_options: {
                styles: {
                    point_graphic: '<?php echo img('pushpin-1.png'); ?>'
                    }
                },
            value: <?php echo json_encode(is_null($value) ? '' : $value) ?>,
            formats: {
                is_html: <?php echo json_encode($isHtml) ?>,
                is_map : <?php echo json_encode($isMap) ?>
                }
            };
        $("#<?php echo $idPrefix ?>nlf").featurewidget(options);
    });
})(jQuery);
</script>
