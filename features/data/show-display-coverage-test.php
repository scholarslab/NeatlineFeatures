<?php head(array('title' => item('Dublin Core', 'Title'), 'bodyid'=>'items','bodyclass' => 'show')); ?>

<div id="primary">

	<h1><?php echo item('Dublin Core', 'Title'); ?></h1>

<?php
$coverages = item('Dublin Core', 'Coverage', array('all' => true));
?>
    <ul id='item-coverage'><?foreach ($coverages as $coverage) { ?>
        <li><? if (NeatlineFeatures_Functions::isWkt($coverage)) { echo "true"; } else { echo "false"; } ?></li><? } ?>
    </ul>

</div><!-- end primary -->

<?php foot(); ?>
