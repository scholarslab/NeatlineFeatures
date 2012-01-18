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
  <div id="<?php echo $idPrefix ?>map">
  </div>
</div>
<script type='text/javascript'>
jQuery(function() {
    var options = {
        styles: {
            point_graphic: '<?php echo img('pushpin-1.png'); ?>'
        }
    };
    NLFeatures.viewCoverageMap('#<?php echo $idPrefix ?>map',
                               <?php echo json_encode($text) ?>,
                               options);
});
</script>
