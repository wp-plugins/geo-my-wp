<?php 
/////// POSTS LOOP - DEFAULT /////////
foreach ($wppl['posts_within'] as $single_result) {
	$post = get_post($single_result->post_id); 
	$single_result->post_permalink = get_permalink($post->ID); 
	$single_result->post_thumbnail = get_the_post_thumbnail($post->ID); ?>
	
	<div class="wppl-single-result" <?php echo 'style="width:'.$single_result_width.'px; height:'.$single_result_height.'px"'; ?>>
	
		<?php wppl_title($wppl, $post, $single_result); ?>
		
		<?php wppl_by_radius($wppl, $single_result, $returned_address); ?>
		
		<?php wppl_thumbnail($wppl, $post); ?>
		
		<?php wppl_excerpt($wppl, $post); ?>
		
		<?php wppl_taxonomies($wppl, $post); ?>
		
    	<div class="clear"></div>
    	<div class="wppl-info">
    			<?php wppl_additional_info($wppl, $post); ?>	
    		
    			<?php wppl_address($post); ?>
    		
    			<?php wppl_driving_distance($wppl, $single_result, $returned_address); ?>
    			
    			<?php wppl_directions($wppl, $single_result); ?>
    	</div> <!-- info -->
    </div> <!--  single- wrapper -->
    <div class="clear"></div>     
<?php $mc++; $pc++; } ?>
	
