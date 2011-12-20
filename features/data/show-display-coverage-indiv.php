<?php head(array('title' => item('Dublin Core', 'Title'), 'bodyid'=>'items','bodyclass' => 'show')); ?>

<div id="primary">
    
	<h1><?php echo item('Dublin Core', 'Title'); ?></h1>
    
<div id='dublin-core-coverage'>
<?php
$item = get_current_item();
$coverages = item('Dublin Core', 'Coverage', array('all' => true));
?>

    <ul id='item-coverage'><? foreach ($coverages as $coverage) { ?>
        <li><div><? echo NeatlineFeatures_Functions::displayCoverage($coverage, $item); ?></div></li><? } ?>
    </ul>
</div>

	<ul class="item-pagination navigation">
	    <li id="previous-item" class="previous"><?php echo link_to_previous_item('Previous Item'); ?></li>
	    <li id="next-item" class="next"><?php echo link_to_next_item('Next Item'); ?></li>
	</ul>
	
</div><!-- end primary -->

<?php foot(); ?>
