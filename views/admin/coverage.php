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
<div id="<?php echo $idPrefix ?>widget" class='nlfeatures nlfeatures-edit'>
  <div>
    <?php echo $textField ?>
    <?php echo $freeField ?>
    <?php echo $useHtml   ?>
  </div>
  <div>
    <div id="<?php echo $idPrefix ?>map"></div>
    <div class='nlfeatures-map-tools'></div>
  </div>
</div>
<script type='text/javascript'>
(function($) {

    $(function() {
        var formats = {
            is_html: <?php echo json_encode($isHtml) ?>,
            is_wkt: <?php
echo json_encode(NeatlineFeatures_Functions::isWkt($value))
                ?>
        };
        var widgets = {
            map  : '#<?php echo $idPrefix ?>map',
            text : '#<?php echo $idPrefix ?>text',
            free : '#<?php echo $idPrefix ?>free',
            html : '#<?php echo $idPrefix ?>html'
        };
        var options = {
            styles: {
                point_graphic: '<?php echo img('pushpin-1.png'); ?>'
            }
        };
        NLFeatures.editCoverageMap(
            '#<?php echo $idPrefix ?>widget',
            widgets,
            <?php echo json_encode(is_null($value) ? '' : $value) ?>,
            formats,
            options
        );
    });

})(jQuery);
</script>
