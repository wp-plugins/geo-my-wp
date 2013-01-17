<?php 
/////// POSTS LOOP - RESTAURANTS /////////
foreach ($wppl['posts_within'] as $single_result) {
	$post = get_post($single_result->post_id); 
	$single_result->post_permalink = get_permalink($post->ID); 
	$single_result->post_thumbnail = get_the_post_thumbnail($post->ID);
	?>
	
	<div class="wppl-single-result" <?php echo 'style="width:'.$single_result_width.'px; height:'.$single_result_height.'px"'; ?>>	
		
		<?php /* /////////////////////////// START EDITING RESULTS ////////////////////////////////////// */  ?>
		
		<?php wppl_featured_posts($wppl, $post); ?>
		
		<div class="single-map-holder">
			<?php wppl_single_map($wppl); ?>
			    		
    		<?php wppl_directions($wppl, $single_result); ?>
    		
    		<?php wppl_driving_distance($wppl, $single_result, $returned_address); ?>
		
		</div><!-- single map holder -->
		
		<div class="right-info-wrapper">
						
			<?php wppl_title($wppl, $post, $single_result); ?>
			
			<div class="radius-wrapper"><?php echo '('; wppl_by_radius($wppl, $single_result, $returned_address); echo ')'; ?></div>
			
			<div class="clear"></div>
			
			<?php wppl_thumbnail($wppl, $post); ?>
			
			<?php wppl_days_hours($post); ?>
			
			<?php wppl_address($post); ?>
			
    		<?php wppl_additional_info($wppl, $post); ?>
    		
    		<?php /* if($extra_info) { ?>
    			<div class="wppl-extra-info-wrapper">
    				<div class="wppl-extra-info" id="hide-extra-info-<?php echo $pc;?>" style="display:none">
						<!-- 
							ANY INFORMATION CAN GO IN HERE 
						-->
					</div>
					<a href="#" class="show-extra-info" id="extra-info-btn-<?php echo $pc;?>">Extra Information</a>
    			</div><!-- extra info wrapper -->
    			<script> jQuery('#extra-info-btn-<?php echo $pc;?>').click(function(event){ event.preventDefault(); jQuery("#hide-extra-info-<?php echo $pc;?>").slideToggle(); }); </script>
    		<?php } */ ?>
    	
    	</div><!-- right info wrapper -->
    	
    	<?php /* /////////////////////////// NO MORE EDITING ////////////////////////////////////// */  ?>
    </div> <!--  single- wrapper -->
    <div class="clear"></div>     
<?php $pc++; $mc++; } ?>

