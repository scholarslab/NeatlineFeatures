<?php echo head(array('title' => item('Dublin Core', 'Title'), 'bodyid'=>'items','bodyclass' => 'show')); ?>

<div id="primary">
    
	<h1><?php echo item('Dublin Core', 'Title'); ?></h1>
    
    <?php echo custom_show_item_metadata(); ?>
	
	<!-- The following returns all of the files associated with an item. -->
	<div id="itemfiles" class="element">
	    <h3>Files</h3>
		<div class="element-text"><?php echo files_for_item(); ?></div>
	</div>
	
	<!-- If the item belongs to a collection, the following creates a link to that collection. -->
	<?php if (item_belongs_to_collection()): ?>
    <div id="collection" class="element">
        <h3>Collection</h3>
        <div class="element-text"><p><?php echo link_to_collection_for_item(); ?></p></div>
    </div>
    <?php endif; ?>

    <!-- The following prints a list of all tags associated with the item -->
	<?php if (metadata($item, 'has tags')): ?>
	<div id="item-tags" class="element">
		<h3>Tags</h3>
		<div class="element-text"><?php echo tag_string('item'); ?></div> 
	</div>
	<?php endif;?>
	
	<!-- The following prints a citation for this item. -->
	<div id="item-citation" class="element">
    	<h3>Citation</h3>
    	<div class="element-text"><?php echo metadata($item, 'citation'); ?></div>
	</div>
	
	<?php echo plugin_append_to_items_show(); ?>

	<ul class="item-pagination navigation">
	    <li id="previous-item" class="previous"><?php echo link_to_previous_item_show('Previous Item'); ?></li>
	    <li id="next-item" class="next"><?php echo link_to_next_item_show('Next Item'); ?></li>
	</ul>
	
</div><!-- end primary -->

<?php echo foot(); ?>
