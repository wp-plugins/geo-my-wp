<?php 
/////// POSTS LOOP - REAL ESTATE /////////
foreach ($wppl['posts_within'] as $single_result) {
	$post = get_post($single_result->post_id); 
	$single_result->post_permalink = get_permalink($post->ID); 
	$single_result->post_thumbnail = get_the_post_thumbnail($post->ID); 
	?>
	<div class="wppl-single-result-wrapper">
		<div class="wppl-single-result" <?php echo 'style="width:'.$single_result_width.'px; height:'.$single_result_height.'px"'; ?>>
		
			<?php /* /////////////////////////// START EDITING RESULTS ////////////////////////////////////// */  ?>
			
			<?php wppl_featured_posts($wppl, $post); ?>
			
			<?php wppl_title($wppl, $post, $single_result); ?>
			
			<div class="radius-wrapper"><?php echo '('; wppl_by_radius($wppl, $single_result, $returned_address); echo ')'; ?></div>
			
			<?php wppl_thumbnail($wppl, $post); ?>
			
    		<?php wppl_address($post); ?>
    	
    		<?php wppl_taxonomies($wppl, $post); ?>
    		
    		<?php wppl_additional_info($wppl, $post); ?>
    		
    		<div class="result-footer">
    			<div class="popup-additional-info">
    				<a href="<?php the_permalink(); ?>/?address=<?php echo $org_address; ?>" class="wppl-excerpt-link">Aditional Info
    					<?php wppl_excerpt($wppl, $post); ?>
    				</a>
    			</div>
    		
    			<?php wppl_directions($wppl, $single_result); ?>
    		</div>
    		
    		<?php /* /////////////////////////// NO MORE EDITING ////////////////////////////////////// */  ?>
    		
    	</div> <!-- single result -->
    </div> <!--  results wrapper -->      
<?php	$pc++; $mc++; } ?>

