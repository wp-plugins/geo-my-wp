<?php
/////// POSTS LOOP - DEFAULT /////////
foreach ($wppl['posts_within'] as $single_result) {
	$post = get_post($single_result->post_id); 
	$single_result->post_permalink = get_permalink($post->ID); 
	$single_result->post_thumbnail = get_the_post_thumbnail($post->ID); ?>
	
	<div class="wppl-single-result" <?php echo 'style="width:'.$single_result_width.'px; height:'.$single_result_height.'px"'; ?>>
	
		<div class="wppl-title-holder">
			<h3 class="wppl-h3">
				<a href="<?php echo $single_result->post_permalink; ?>/?address=<?php echo $wppl['org_address']; ?>"><?php echo $pc; ?>) <?php echo get_the_title($post->ID); ?></a>
				<?php if ( isset($lat) && !empty($lat) ) { ?><span class="radius-dis">(<?php echo wppl_by_radius($wppl, $single_result, $returned_address); ?>)</span><?php } ?>
			</h3>
		</div><!-- title holder -->
		
		<?php wppl_thumbnail($wppl, $post); ?>
		
		<?php wppl_excerpt($wppl, $post); ?>
		
		<?php wppl_taxonomies($wppl, $post); ?>
		
    	<div class="clear"></div>
    	<div class="wppl-info">
    		<div class="wppl-info-left">
    			<?php wppl_additional_info($wppl, $post); ?>
    		</div><!-- info left -->
    		<div class="wppl-info-right">
    			<?php wppl_address($post); ?>
    		
    			<?php wppl_driving_distance($wppl, $single_result, $returned_address); ?>
    			
    			<?php wppl_directions($wppl, $single_result); ?>
    		
    		</div><!-- info right -->
    	</div> <!-- info -->
    </div> <!--  single- wrapper -->
    <div class="clear"></div>     
<?php $mc++; $pc++; } ?>
	
