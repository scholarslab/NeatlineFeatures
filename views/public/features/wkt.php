<?php if( count($wkts) > 1) { ?>
GEOMETRYCOLLECTION( <?php echo implode(',', $wkts)  ?> )
<?php } else { echo $wkts[0]; } ?>