<?php
global $options, $posts_within, $org_address, $directions, $from_page, $get_directions, $result_style, $show_excerpt, $show_thumb, $thumb_width, $thumb_height;

$pc = $from_page +1; /* post count, to display number of post */
foreach ($posts_within as $single_result) {
	$post = get_post($single_result->post_id); ?>
	<div class="wppl-single-result-<?php  echo $result_style; ?>">
		<div class="wppl-title-holder">
			<div class="result-number"><?php echo $pc; ?>)</div>
			<h3 class="wppl-h3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<?php if (!empty($_GET['wppl_address'])) { ?><div class="wppl-radius-dis"> - <?php echo $single_result->distance . " " . $mikm_n; ?></div> <?php } ?>
		</div><!-- title holder -->
		<?php if($show_thumb == 1) { ?>
		<div class="wppl-thumb">
			<?php the_post_thumbnail(array($thumb_width,$thumb_height)); ?>
		</div><!-- thumb -->
		<?php } ?>
		<?php if ($show_excerpt == 1) { ?>
    	<div class="wppl-excerpt">
			<?php echo wp_trim_words( $post->post_content,15 ); ?>
		</div> <!-- excerpt -->
		<?php } ?>
		
    	<div class="clear"></div>
    	<div class="wppl-info">
    		<div class="wppl-info-left">
    			<div class="wppl-phone"><span>Phone: </span><?php echo get_post_meta($post->ID,'_wppl_phone',true); ?></div>
    			<div class="wppl-fax"><span>Fax: </span><?php echo get_post_meta($post->ID,'_wppl_fax',true); ?></div>
    			<div class="wppl-email"><span>Email: </span><?php echo get_post_meta($post->ID,'_wppl_email',true); ?></div>
    			<div class="wppl-website"><span>Website: </span><a href="http://<?php echo get_post_meta($post->ID,'_wppl_website',true); ?>" target="_blank"><?php echo get_post_meta($post->ID,'_wppl_website',true); ?></a></div>
    		</div><!-- info left -->
    		<div class="wppl-info-right">
    			<div class="wppl-address"><span>Address: </span><?php echo get_post_meta($post->ID,'_wppl_address',true); ?></div>
    		<!-- DISPLAY DISTANCE -->
    		<!-- display driving distance and direction link -->
    		<?php	if ($by_driving == 1) { distance_between($single_result->lat, $single_result->long); ?>
    			<div class="wppl-drive-dis"><span>Driving distance:</span> <?php echo $distance_between; ?></div>
    		<?php	} ?>
    		<?php	if ($get_directions == 1) { get_directions($single_result->lat, $single_result->long,$single_result->address); ?>
    			<div class="wppl-get-directions"><?php echo	$directions; ?></div>
    		<?php	} ?>
    		</div><!-- info right -->
    	</div> <!-- info -->
    </div> <!--  single- wrapper -->
    <div class="clear"></div>
      
<?php	$pc++; } ?>
	
