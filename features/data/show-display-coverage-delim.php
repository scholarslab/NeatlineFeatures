<?php echo head(array('title' => item('Dublin Core', 'Title'), 'bodyid'=>'items','bodyclass' => 'show')); ?>

<div id="primary">
    
	<h1><?php echo item('Dublin Core', 'Title'); ?></h1>

    <div id='dublin-core-coverage'>
      <? echo item('Dublin Core', 'Coverage', array( 'delimiter' => '; ' )); ?>
    </div>
    
	<ul class="item-pagination navigation">
	    <li id="previous-item" class="previous"><?php echo link_to_previous_item_show('Previous Item'); ?></li>
	    <li id="next-item" class="next"><?php echo link_to_next_item_show('Next Item'); ?></li>
	</ul>
	
</div><!-- end primary -->

<?php echo foot(); ?>
