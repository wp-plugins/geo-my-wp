<?php
/**
 * PT Search form function - Posts, Pages post types dropdown.
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_form_post_types_dropdown($gmw, $title, $id , $class, $all ) {
	
	if ( isset( $gmw['post_types'] ) && count( $gmw['post_types'] ) > 1 ) :
	
		if ( !empty( $title ) ) echo '<label for="dropdown">'.$title.'</label>';
		echo '<select name="wppl_post" id="'.$id.'" class="'.$class.'">';
			echo '<option value="'. implode(' ', $gmw['post_types']).'">'.$all.'</option>';
			foreach ($gmw['post_types'] as $post_type) :
				if ( $_GET["wppl_post"] == $post_type ) $pti_post = 'selected="selected"'; else $pti_post = "";
					echo '<option value="' .$post_type .'" '.$pti_post.'>' . get_post_type_object($post_type)->labels->name.'</option>';
			endforeach;
		echo '</select>';
	
	endif;
}
   
/**
 * PT search form function - Display taxonomies/categories
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_form_taxonomies($gmw) {
	
	if ( count( $gmw['post_types'] ) < 2 && ( isset( $gmw['taxonomies'] ) ) ) :
		
		/* 
		 * output dropdown for each taxonomy 
		 */
		foreach ($gmw['taxonomies'] as $tax => $values) :
			
			$ta = ( !empty( $values['exclude'] ) ) ? explode(',', $values['exclude']) : '';
			
			if ( isset( $values['style']) && $values['style'] == 'drop') :
				$get_tax = false;
				
				echo '<div id="'.$tax . '_cat">';
					echo '<label for="' .get_taxonomy($tax)->rewrite['slug'].'">'.get_taxonomy($tax)->labels->singular_name .': </label>';
				
					if (isset($_GET['tax_'.$tax])) $get_tax = $_GET['tax_'.$tax];
					$args = array(
							'taxonomy' 			=> $tax,
							'hide_empty' 		=> 1,
							'depth' 			=> 10,
							'hierarchical' 		=> 1,
							'show_count'        => 0,
							'id' 				=> $tax . '_id',
							'name' 				=> 'tax_'.$tax,
							'selected' 			=> $get_tax,
							'exclude'			=> $ta,
							'show_option_all'   => __(' - All - ','GMW'),
					);
					wp_dropdown_categories($args);
				echo '</div>';
			endif;
		
		endforeach;
		
	endif;
}

/**
 * GMW Search results function - Per page dropdown
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_per_page_dropdown($gmw, $class) {
	
	$perPage = explode(",", $gmw['per_page']);
	$lastpage = ceil($gmw['total_results']/$gmw['get_per_page']);
	
	$gmwLat = ( isset($gmw['your_lat']) && $gmw['your_lat'] != false ) ? $gmw['your_lat'] : 'false';
	$gmwLong = ( isset($gmw['your_long']) && $gmw['your_long'] != false ) ? $gmw['your_long'] : 'false';
	
	if ( count( $perPage) > 1 ) :
		
		echo '<select name="gmw_per_page" class="gmw-pt-per-page-dropdown '.$class.'">';
		
			foreach ( $perPage as $pp ) :
				if ( isset($_GET['wppl_per_page']) && $_GET['wppl_per_page'] == $pp ) $pp_s = 'selected="selected"'; else $pp_s = "";
				echo '<option value="'. $pp .'" '.$pp_s.'>'.$pp . ' ' . __('per page','GMW') . '</option>';
			endforeach;

		echo '</select>';

	endif;

	?>
	<script>
		jQuery(document).ready(function($) {
		   	$(".gmw-pt-per-page-dropdown").change(function() {
			   	var totalResults = <?php echo $gmw['total_results']; ?>;
		
			   	var lastPage = Math.ceil(totalResults/$(this).val());
			   	var newPaged = ( <?php echo $gmw['paged']; ?> > lastPage ) ? lastPage : false;

		   		window.location.href = jQuery.query.set("wppl_per_page", $(this).val()).set('paged', newPaged).set('gmw_lat', <?php echo $gmwLat; ?>).set('gmw_long', <?php echo $gmwLong; ?>);
		   	
		    });
		});
    </script>
<?php 
}

/**
 * PT Search results function - display map
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_results_map($gmw) {
	
	if ( ( $gmw['results_type'] != 'posts' ) )  :
		if ( empty($id) ) $id = 'gmw-pt-map-wrapper-'. $gmw['form_id'];
		if ( empty($class) ) $class = 'gmw-pt-map-wrapper';
		$width = ( !empty($gmw['map_width']['value'] ) ) ? $gmw['map_width']['value'].$gmw['map_width']['units'] : '500px';
		$height = ( !empty($gmw['map_height']['value'] ) ) ? $gmw['map_height']['value'].$gmw['map_height']['units'] : '500px';
		echo 	'<div id="gmw-pt-map-'.$gmw['form_id'].'" class="gmw-map" style="width:'.$width.'; height:'.$height.';"></div>';
	endif;
}

/**
 * GMW PT results function - Paginations
 * @version 1.0
 * @author Eyal Fitoussi & inspierd by http://www.awcore.com/dev/1/3/Create-Awesome-PHPMYSQL-Pagination_en
 */

function gmw_pt_paginations($gmw) {
	
	$adjacents = "2";
	
	$lastpage = ceil($gmw['total_results']/$gmw['get_per_page']);
	
	if ( $gmw['paged'] == 0 ) 
		$paged = 1;
	elseif ( $gmw['paged'] > $lastpage ) 
		$paged = $lastpage;
	else 
		$paged = $gmw['paged'];
		
	$start = ($paged - 1) * $gmw['get_per_page'];

	$firstPage = 1;
	if (isset($page) ) $prev = ($page == 1)? 1:$page - 1;

	$prev = $paged - 1;
	$next = $paged + 1;
	$lpm1 = $lastpage - 1;

	$gmw_pagi = "";

	if($lastpage > 1) {
		$gmw_pagi .= '<ul id="gmw-pt-prem-pagination-'.$gmw['form_id'].'" class="gmw-prem-pagination gmw-prem-pagination-'.$gmw['form_id'].'">';

		$gmw_pagi .= "<li class='details'> ". __('Page','GMW') . " " . $paged . " " . __('of','GMW') . " " .  $lastpage . "</li>";

		if ($paged == 1) {
			$gmw_pagi .= "<li><a class='current'>" .__('First','GMW') . "</a></li>";
			$gmw_pagi .= "<li><a class='current'>".__('Prev','GMW') ."</a></li>";
		} else {
			$gmw_pagi .= "<li><a href='".add_query_arg( array('paged' => $firstPage, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long'] ) )."'>" . __('First','GMW') . "</a></li>";
			$gmw_pagi .= "<li><a href='".add_query_arg( array('paged' => $prev, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>" . __('Prev','GMW') . "</a></li>";
		}

		if ($lastpage < 7 + ($adjacents * 2)) {

			for ($counter = 1; $counter <= $lastpage; $counter++) {
				if ($counter == $paged)
					$gmw_pagi.= "<li><a class='current'>$counter</a></li>";
				else
					$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => $counter, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long'] ) )."' onclick='document.wppl_form.submit();'>$counter</a></li>";
			}
		} elseif ($lastpage > 5 + ($adjacents * 2)) {
			if($paged < 1 + ($adjacents * 2)) {
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
					if ($counter == $paged)
						$gmw_pagi.= "<li><a class='current'>$counter</a></li>";
					else
						$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => $counter, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>$counter</a></li>";
				}
				$gmw_pagi.= "<li class='dot'>...</li>";
				$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => $lpm1, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>$lpm1</a></li>";
				$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => $lastpage, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>$lastpage</a></li>";

			} elseif ($lastpage - ($adjacents * 2) > $paged && $paged > ($adjacents * 2)) {
				$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => 1, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>1</a></li>";
				$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => 2, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>2</a></li>";
				$gmw_pagi.= "<li class='dot'>...</li>";

				for ($counter = $paged - $adjacents; $counter <= $paged + $adjacents; $counter++) {
					if ($counter == $paged)
						$gmw_pagi.= "<li><a class='current'>$counter</a></li>";
					else
						$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => $counter, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>$counter</a></li>";
				}
				$gmw_pagi.= "<li class='dot'>..</li>";
				$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => $lpm1, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>$lpm1</a></li>";
				$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => $lastpage, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>$lastpage</a></li>";
			} else {
				$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => 1, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>1</a></li>";
				$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => 2, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>2</a></li>";
				$gmw_pagi.= "<li class='dot'>..</li>";

				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
					if ($counter == $paged)
						$gmw_pagi.= "<li><a class='current'>$counter</a></li>";
					else
						$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => $counter, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>$counter</a></li>";
				}
			}
		}

		if ($paged < $counter - 1) {
			$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => $next, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>".__('Next','GMW') ."</a></li>";
			$gmw_pagi.= "<li><a href='".add_query_arg( array('paged' => $lastpage, 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) )."'>".__('Last','GMW') ."</a></li>";
		}else{
			$gmw_pagi.= "<li><a class='current'>".__('Next','GMW') ."</a></li>";
			$gmw_pagi.= "<li><a class='current'>".__('Last','GMW') ."</a></li>";
		}
		$gmw_pagi.= "</ul>\n";
	}

	echo $gmw_pagi;
}

/**
 * PT Results function - Query taxonomies/categories dropdown
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_query_taxonomies($taxonomies) {
	
	$rr = 0;
	$get_tax = false;
	$args = array('relation' => 'AND');
	
	foreach ($taxonomies as $tax => $values) :
		if ( isset($values['style']) && $values['style'] == 'drop') :
			$get_tax = false;
			if( isset( $_GET['tax_'.$tax] ) ) $get_tax = $_GET['tax_'.$tax];
			
			if ($get_tax != 0) :
				$rr++;
				$args[] = array(
						'taxonomy' => $tax,
						'field' => 'id',
						'terms' => array($get_tax)
				);
			endif;
			
		endif;
	endforeach;
	
	if($rr == 0) $args = array();
	return $args;
}

/**
 * PT Results function - 'Within' message.
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_within( $gmw,$sm, $om, $rm ,$wm, $fm, $nm ) {
	if ( isset( $_GET['action'] ) && $_GET['action'] == 'wppl_post' ) $nm = $gmw['org_address'];
	echo $sm .' ' . $gmw['results_count'] .' ' . $om . ' ' .$gmw['total_results']. ' ' . $rm;
	if ( !empty($gmw['org_address']) ) echo ' ' .$wm . ' ' . $gmw['radius'] . ' ' . $gmw['units_array']['name'] . ' ' . $fm . ' ' .$nm;
}

/**
 * PT results function - thumbnail
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_thumbnail($gmw) {
	if( !isset( $gmw['show_thumb'] ) )
		return;
	the_post_thumbnail( array( $gmw['thumb_width'],$gmw['thumb_height'] ) );
}

/**
 * PT results function - Display radius distance.
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_by_radius($gmw, $post) {
	if ( isset( $gmw['your_lat'] ) && !empty( $gmw['your_lat'] ) ) echo $post->distance . " " . $gmw['units_array']['name'];
}

/**
 * PT results function - Display taxonomies per result.
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_taxonomies($gmw, $post) {
	
	if ( !isset( $gmw['custom_taxes'] ) ) 
		return;
	
	if( isset($gmw['taxonomies']) ) :
		foreach ($gmw['taxonomies'] as $tax => $style) :
			$terms = get_the_terms($post->ID, $tax);

			if ( $terms && ! is_wp_error( $terms ) ) :
			$terms_o = array();

			foreach ( $terms as $term ) {
				$terms_o[] = $term->name;
			}

			$terms_o = join( ", ", $terms_o );
			$the_tax = get_taxonomy($tax);
			echo '<div class="wppl-taxes '.$the_tax->rewrite['slug'].'"><span>'.$the_tax->labels->singular_name . ':</span> ' . $terms_o.'</div>';
			endif;
		endforeach;
	endif;
}

/**
 * PT results function - Day & Hours.
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_days_hours($post, $nr_message) {
	$days_hours = get_post_meta($post->ID, '_wppl_days_hours', true);
	$dc = 0;
	if ($days_hours) {
		foreach ($days_hours as $day) {
			if(!in_array('', $day)){
				$dc++;
				echo '<div class="single-days-hours"><span class="single-day">' . $day['days'] . ':</span><span class="single-hour">' . $day['hours'] . '</span></div>';
			}
		}
	}
	if ( (!$days_hours) || ($dc == 0) ) {
		echo '<span class="single-day">'.$nr_message.'</span>'; 
	} 				
}

/**
 * PT results function - Additional information.
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_additional_info($gmw, $post) { 
	 if( !isset($gmw['additional_info']) ) 
	 	return; ?>
   	<?php if( isset($gmw['additional_info']['phone']) ) { ?><div class="gmw-phone"><span><?php _e('Phone: ','GMW'); ?></span><?php echo ( $post->phone ) ? $post->phone : _e('N/A','GMW'); ?></div><?php } ?>
    <?php if( isset($gmw['additional_info']['fax']) ) { ?><div class="gmw-fax"><span><?php _e('Fax: ','GMW'); ?></span><?php echo ( $post->fax ) ? $post->fax : _e('N/A','GMW'); ?></div><?php } ?>
    <?php if( isset($gmw['additional_info']['email'])) { ?><div class="gmw-email"><span><?php _e('Email: ','GMW'); ?></span><?php echo ( $post->email ) ? $post->email : _e('N/A','GMW'); ?></div><?php } ?>
    <?php if( isset($gmw['additional_info']['website']) ) { ?><div class="gmw-website"><span><?php _e('Website: ','GMW'); ?> </span><?php if ( $post->website ) { ?><a href="http://<?php echo $post->website; ?>" target="_blank"><?php echo $post->website; } else { _e('N/A','GMW'); }?></a></div><?php } ?>
<?php 
} 

/**
 * PT results function - Excerpt from content.
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_excerpt($gmw, $post) {
	if ( !isset( $gmw['show_excerpt'] ) )
		return;
	echo wp_trim_words( $post->post_content,$gmw['words_excerpt']);
}

/**
 * PT results function - Get directions.
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_directions($gmw, $post, $title) {
	if ( !isset( $gmw['get_directions'] ) ) 
		return;
    echo 	'<span><a href="http://maps.google.com/maps?f=d&hl=en&doflg=' . $gmw['units_array']['map_units'] . '&geocode=&saddr=' . $gmw['org_address'] . '&daddr=' . str_replace(" ", "+", $post->address) . '&ie=UTF8&z=12" target="_blank">'.$title.'</a></span>';
}

/**
 * PT results function - Driving distance.
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_pt_driving_distance($gmw, $post, $class) {
	if ( !isset($gmw['by_driving']) || $gmw['units_array'] == false ) 
		return;
	
	echo 	'<div id="gmw-driving-distance-'.$post->ID . '" class="'.$class.'"></div>';
	?>
	<script>   
    	var directionsDisplay;
		var directionsService = new google.maps.DirectionsService();	
        var directionsDisplay = new google.maps.DirectionsRenderer();	
        
  		var start = new google.maps.LatLng('<?php echo $gmw['your_lat']; ?>','<?php echo $gmw['your_long']; ?>');
  		var end = new google.maps.LatLng('<?php echo $post->lat; ?>','<?php echo $post->long; ?>');
  		var request = {
    		origin:start,
    		destination:end,
    		travelMode: google.maps.TravelMode.DRIVING
 		};
 		
  		directionsService.route(request, function(result, status) {
    		if (status == google.maps.DirectionsStatus.OK) {
      			directionsDisplay.setDirections(result);
      			if ( '<?php echo $gmw['units_array']['name']; ?>' == 'Mi') {
      				totalDistance = (Math.round(result.routes[0].legs[0].distance.value * 0.000621371192 * 10) / 10)+' Mi';
      			} else { 
      				totalDistance = (Math.round(result.routes[0].legs[0].distance.value * 0.01) / 10)+' Km';
      			}	
      			
      			jQuery('#<?php echo 'gmw-driving-distance-'. $post->ID; ?>').text('Driving: ' + totalDistance);	
    		}
 		 });	 
 		 
	</script>
	<?php	
}

/**
 * GMW PT function - include search form
 * @param $gmw
 * @param $gmw_options
 */
function gmw_pt_main_shortcode_search_form($gmw, $gmw_options) {

	do_action('gmw_pt_before_search_form', $gmw, $gmw_options);
	
	wp_enqueue_style( 'gmw-'.$gmw['form_id'].'-'.$gmw['form_template'].'-form-style', GMW_PT_URL. 'search-forms/'.$gmw['form_template'].'/css/style.css',99 );
	include GMW_PT_PATH .'search-forms/'.$gmw['form_template'].'/search-form.php';
	
	do_action('gmw_pt_after_search_form', $gmw, $gmw_options);

}
add_action('gmw_posts_main_shortcode_search_form', 'gmw_pt_main_shortcode_search_form', 15 , 2);

/**
 * GMW PT function - no results
 * @param $gmw
 * @param $gmw_options
 */
function gmw_pt_no_results($gmw, $gmw_options) {
	
	do_action('gmw_pt_before_no_results', $gmw, $gmw_options);
	
	$no_results = '<div class="gmw-pt-no-results-wrapper"><h3>No results found</h3></div>';
	echo apply_filters('gmw_pt_no_results_message', $no_results, $gmw);
	
	do_action('gmw_pt_after_no_results', $gmw, $gmw_options);
}
add_action('gmw_posts_form_submitted_address_geocoded_not', 'gmw_pt_no_results', 10, 2);

/**
 * GMW Display results for post, post type and pages
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_get_posts_results($gmw, $gmw_options) {
	
	$gmw['get_per_page'] = ( isset($_GET['wppl_per_page']) ) ? $_GET['wppl_per_page'] : current(explode(",", $gmw['per_page']));
	
	$gmw = apply_filters('gmw_pt_settings_before_search_results', $gmw, $gmw_options);
	
	do_action('gmw_pt_before_search_results', $gmw, $gmw_options);
	
	$tax_args = false;
	$meta_args = false;

	$ptc = ( isset($_GET['wppl_post'] ) ) ? count( explode( " ",$_GET['wppl_post']) ) : count($gmw['post_types']);
	
	/* 
	 * CREATE TAXONOMIES VARIATIONS TO EXECUTE WITH SQL QUERY 
	 */
	if ( isset( $ptc ) && $ptc < 2 && ( isset($gmw['taxonomies']) ) ) :
		$tax_args = gmw_pt_query_taxonomies($gmw['taxonomies']);
		$tax_args = apply_filters('gmw_pt_query_tax_args', $tax_args, $gmw);
	endif;
	
	global $gmwQuery;
	$gmwQuery = $gmw;
		
	/*
	 * midify main query to search based on our shortcode
	 */
	function gmw_pt_locations_query($clauses) {
		
		global $wpdb, $gmwQuery;
			/*
			 * join the location table into the query
			 */
			$clauses['join'] .= "INNER JOIN " . $wpdb->prefix . "places_locator gmwlocations ON $wpdb->posts.ID = gmwlocations.post_id ";	
			
			/*
			 * add the radius calculation and add the locations fields into the results
			*/
			if ( !empty( $gmwQuery['org_address'] ) ) :
				$clauses['fields'] .= $wpdb->prepare(", gmwlocations.* , ROUND( %d * acos( cos( radians( %s ) ) * cos( radians( gmwlocations.lat ) ) * cos( radians( gmwlocations.long ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( gmwlocations.lat) ) ),1 ) AS distance",  array( $gmwQuery['units_array']['radius'], $gmwQuery['your_lat'], $gmwQuery['your_long'], $gmwQuery['your_lat']) ); 
				$clauses['groupby'] = $wpdb->prepare(" $wpdb->posts.ID HAVING distance <= %d OR distance IS NULL", $gmwQuery['radius']) ;
				$clauses['orderby'] = 'distance';
				
			else :
				$clauses['fields'] .= ", gmwlocations.*";
			endif;
			
		return apply_filters('gmw_pt_location_query_clauses', $clauses, $gmwQuery);
	}
		
	/* 
	 * get current page number 
	 */
	$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	
	/*
	 *  add filters to wp_query to do radius calculation and get  locations detail into results
	*/
	add_filter('posts_clauses', 'gmw_pt_locations_query');
	
	/* 
	 * locations Query 
	 */
	global $gmw_query;
	$meta_args = false;
	$gmw_post = ( isset( $_GET['wppl_post'] ) && !empty( $_GET['wppl_post']) )  ? explode(' ' , $_GET['wppl_post']) : $gmw['post_types'];
	
	$gmw_query = new WP_Query( array('post_type' => $gmw_post, 'post_status' => array('publish'), 'tax_query' => $tax_args, 'posts_per_page'=> $gmw['get_per_page'], 'paged' => $paged, 'meta_query' => apply_filters('gmw_pt_query_meta_args', $meta_args, $gmw) ) );
	
	remove_filter( 'posts_clauses', 'gmw_pt_locations_query' );
	
	$gmw = apply_filters('gmw_pt_settings_before_posts_loop',$gmw, $gmw_query);
	$gmw_query = apply_filters('gmw_pt_posts_before_posts_loop',$gmw_query, $gmw);
		
	//check if we got results and if so display it
	if ( $gmw_query->have_posts() ):
		global $post;
		$gmw['results_count'] = count($gmw_query->posts);
		$gmw['total_results'] = $gmw_query->found_posts;
		$gmw['max_pages'] 	  = $gmw_query->max_num_pages;
		$gmw['paged']		  = $paged;
			
		if ( $paged == 1 ) $pc = 1; else $pc = ($gmw['get_per_page'] * ($paged-1)) + 1;
			
		do_action('gmw_pt_have_posts_start', $gmw, $gmw_options, $gmw_query);
		
		/* 
		 * let you modify each post within the loop
		 */
		function gmw_pt_modify_post($post) {
			global $gmw_query, $gmwQuery;
			
			if ( $gmw_query->post != $post )
				return;
		
			/*
			 * add permalink and thumbnail into each post in the loop
			 * we are doing it here to be able to display it in the info window of the map
			*/
			if ( ( isset( $gmwQuery['results_type'] ) && $gmwQuery['results_type'] != 'posts') || isset( $gmwQuery['single_map']) ) {
			
				$post->post_permalink = get_permalink($post->ID);
				$post->post_thumbnail = get_the_post_thumbnail( $post->ID);
				
			}
			
			do_action('gmw_pt_modify_post_within_loop', $post);
		}
		add_action('the_post', 'gmw_pt_modify_post',1,1);
			
		/*
		 * get custom stylesheet and results template from child/theme
		*/
		if( strpos($gmw['results_template'], 'custom_') !== false ) :
			wp_enqueue_style( 'gmw-current-style', get_stylesheet_directory_uri(). '/geo-my-wp/posts/search-results/'.str_replace('custom_','',$gmw['results_template']).'/css/style.css' );		
			include(STYLESHEETPATH. '/geo-my-wp/posts/search-results/'.str_replace('custom_','',$gmw['results_template']).'/results.php');
		/*
		 * get stylesheet and results template from plugin's folder
		*/
		else :
			wp_enqueue_style( 'gmw-current-style', GMW_PT_URL. 'search-results/'.$gmw['results_template'].'/css/style.css' );
			include_once GMW_PT_PATH . 'search-results/'.$gmw['results_template'] .'/results.php';
		endif;
		
		/* 
		 *  if need to display map the function below will pass the locations information to the javascript function that displays the map 
		 */
		if ( $gmw['results_type'] != 'posts' || isset( $gmw['single_map']) ) :
			
			/* send args to javascript that display the map */
			if ( !isset( $gmw['map_controls'] ) ) $gmw['map_controls'] = false;
			
			$gmw = apply_filters('gmw_pt_settings_before_map', $gmw, $gmw_query, $gmw_options);
			$gmw_query = apply_filters('gmw_pt_posts_loop_before_map',$gmw_query, $gmw, $gmw_options);
			
			do_action('gmw_pt_have_posts_before_map_displayed', $gmw, $gmw_options, $gmw_query);
			
			if ( $gmw['results_type'] != 'posts' ) :
				wp_enqueue_script( 'gmw-pt-map', true);	
				wp_localize_script( 'gmw-pt-map', 'ptMapArgs', $gmw );
				wp_localize_script( 'gmw-pt-map', 'ptLocations', $gmw_query->posts );
			endif;
					
		endif;
    		
		do_action('gmw_pt_after_search_results', $gmw, $gmw_options);
		
		wp_reset_query();
	else :
		gmw_pt_no_results($gmw, $gmw_options);
	endif;
}
add_action('gmw_posts_form_submitted_latlong', 'gmw_get_posts_results', 10, 2);
add_action('gmw_posts_form_submitted_address_geocoded', 'gmw_get_posts_results', 10, 2);
add_action('gmw_posts_form_submitted_address_not', 'gmw_get_posts_results', 10, 2);
add_action('gmw_posts_results_auto_results', 'gmw_get_posts_results', 10, 2);