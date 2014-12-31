<?php
global $options;
global $posts_within;
global $org_address;
global $directions;
global $from_page;
global $get_directions;
$pc = $from_page +1; /* post count, to display number of post */
foreach ($posts_within as $single_result) {
	$post = get_post($single_result->post_id); ?>
	
	<div class="wppl-single-result<?php if ($options['different_style'] == 1) {echo "-" . get_post_type($post->ID);} ?>">
		<h3 class="wppl-h3">
			<a href="<?php the_permalink(); ?>"><?php echo $pc . ") ";  the_title(); ?></a>
		</h3>
		<div class="wppl-thumb">
			<?php the_post_thumbnail(); ?>
		</div>
    	<div class="wppl-excerpt">
			<?php the_excerpt(); ?>
		</div> 
    	<div class="clear"></div>
    	<div class="wppl-info">
    		<div class="wppl-address"><span>Address: </span><?php echo get_post_meta($post->ID,'_wppl_address',true); ?></div>
    		<!-- DISPLAY DISTANCE -->
    		<div class="wppl-radius-dis">distance to this location is <?php echo $single_result->distance . " " . $mikm_n; ?></div>
    		<!-- display driving distance and direction link -->
    		<?php	if ($by_driving == 1) { distance_between($single_result->lat, $single_result->long); ?>
    			<div class="wppl-drive-dis">the actual driving distance to this location is <?php echo $distance_between; ?></div>
    		<?php	} ?>
    		<?php	if ($get_directions == 1) { get_directions($single_result->lat, $single_result->long,$single_result->address); ?>
    			<div class="wppl-get-directions"><?php echo	$directions; ?></div>
    		<?php	} ?>
    	</div> <!-- info -->
    </div> <!--  single- wrapper -->
    <div class="clear"></div>
      
<?php	$pc++; } ?>
	
