<?php
///// SHORTCODE TO DISPLAY RESULTS  ////
function wppl_get_results($params) {
	$wppl_options = get_option('wppl_fields');
	/* when submitting a search form */
	if(!empty( $_GET['action'] ) &&  $_GET['action'] == "wppl_post") {		
 		
 		if(!isset($_SESSION['wppl_search_posts']) || !isset($_SESSION['wppl_url_posts']) || $_SESSION['wppl_url_posts'] != remove_query_arg('pagen') ) $posts_session_on = 0; else $posts_session_on = 1;	
 		global $wppl;
    	$wppl_post = array();
    		
   		if (empty($params['form'])) {	
   			$options_r = get_option('wppl_shortcode'); 
			$wppl = $options_r[$_GET['wppl_form']];
   			ob_start();	
   		}
   		// shortcode attributed //	
   		extract(shortcode_atts($wppl, $get_results)); 
   		
   		echo '<link rel="stylesheet" type="text/css" media="all" href="' . GMW_URL. '/plugins/post-types/themes/'.$results_template.'/css/style.css">';
   		   		
 		// distance units //
		if ($_GET['wppl_units'] == "imperial") $wppl['units_array'] = array('radius' => 3959, 'name' => "Mi", 'map_units' => "ptm", 'units'	=> 'imperial');
		else $wppl['units_array'] = array('radius' => 6371, 'name' => "Km", 'map_units' => 'ptk', 'units' => "metric");
		
   		$wppl['org_address'] = str_replace(" ", "+", $_GET['wppl_address']);
   			
    	// radius value //
 		$radius = $_GET['wppl_distance'];	
		$from_page = $_GET['pagen'];
		/////// CONVERT ORIGINAL ADDRESS TO LATITUDE/LONGITUDE ////	
		$returned_address = ConvertToCoords($wppl['org_address']);
		$lat  = $returned_address['lat'];
		$long = $returned_address['long'];
		
		// marker for "your location" //
		(!empty($wppl['org_address'])) ? $your_loc = array("Your Location", $lat,$long) : $your_loc = "0";
		if (isset($_GET['wppl_post']) && !empty($_GET['wppl_post']) )  $wppl_post = explode(' ' , $_GET['wppl_post']); 
		$order_by = "wposts.post_title";
		
		if (!empty($wppl['org_address'])) {
			$having = "HAVING distance <= $radius OR distance IS NULL";
			$order_by = "distance";
			$calculate = ", ROUND(" . $wppl['units_array']['radius'] . "* acos( cos( radians( '%s' ) ) * cos( radians( wposts.lat ) ) * cos( radians( wposts.long ) - radians( '%s' ) ) + sin( radians( '%s' ) ) * sin( radians( wposts.lat) ) ),1 )  AS distance";
			$showing = 'within ' . $radius . ' ' . $wppl['units_array']['name'] . ' from ' . $_GET['wppl_address'];
		} else {
			$showing = false;
		}
		
		$get_them = 1;
	/* when first visiting the page and user's location exists we will display automatically results */
	} elseif ( ($_COOKIE['wppl_lat']) && ($_COOKIE['wppl_long']) && ($params['form']) && ($wppl_options['auto_search'] == 1) ) {
		
		$posts_session_on = 0;
		
		global $wppl; 
		$options_r = get_option('wppl_shortcode'); 
		$wppl['org_address'] =  urldecode($_COOKIE['wppl_city']) . ' ' .  urldecode($_COOKIE['wppl_state']) . ' ' .  urldecode($_COOKIE['wppl_zipcode']) . ' ' .  urldecode($_COOKIE['wppl_country']);
		
   		extract(shortcode_atts($options_r[$params['form']], $get_results)); 		
   		
   		echo '<link rel="stylesheet" type="text/css" media="all" href="' . GMW_URL. '/plugins/post-types/themes/'.$results_template.'/css/style.css">';
		if ($wppl_options['auto_units'] == "imperial") 
			$wppl['units_array'] = array('radius' => 3959, 'name' => "Mi", 'map_units' => "ptm", 'units'	=> 'imperial');
		else 
			$wppl['units_array'] = array('radius' => 6371, 'name' => "Km", 'map_units' => 'ptk', 'units' => "metric");
		
		$radius 	= $wppl_options['auto_radius'];
		$from_page  = (isset($_GET['pagen'])) ? $_GET['pagen'] : 0;
		$lat 		= urldecode($_COOKIE['wppl_lat']);
		$long 		= urldecode($_COOKIE['wppl_long']);
		$wppl_post 	= $post_types;
		$your_loc 	= array("Your Location", $lat,$long);
		$having 	= "HAVING distance <= $radius OR distance IS NULL";
		$order_by 	= "distance";
		$calculate 	= ", ROUND(" . $wppl['units_array']['radius'] . "* acos( cos( radians( '%s' ) ) * cos( radians( wposts.lat ) ) * cos( radians( wposts.long ) - radians( '%s' ) ) + sin( radians( '%s' ) ) * sin( radians( wposts.lat) ) ),1 )  AS distance";
		$showing 	= 'within ' . $radius . ' ' . $wppl['units_array']['name'] . ' Near your location';
			
		$get_them = 1;			
	}
	
	if( isset($get_them) && $get_them == 1) {
		/* if no results exists in the session run new queries */
		if($posts_session_on == 0) {
			$ptc = count(explode( " ",$_GET['wppl_post']));
			//// CREATE TAXONOMIES VARIATIONS TO EXECUTE WITH SQL QUERY ////
			if (isset($ptc) && $ptc<2 && (isset($taxonomies))) {
				if (GMW_VERSION == 'premium' ) $tax_args = gmw_query_taxonomies_premium($taxonomies);
				else $tax_args = gmw_query_taxonomies($taxonomies);
			}
			
			global $wpdb;
			// check for keywords in the form and filter it into the wp_query
			if ( isset($_GET['wppl_keywords']) && !empty( $_GET['wppl_keywords'] ) ) {		
				function gmw_posts_where( $where = '') {
					$where .= ' AND post_title LIKE \'%' . esc_sql( like_escape($_GET['wppl_keywords']) ) . '%\'';
					return $where;
				}
				add_filter( 'posts_where', 'gmw_posts_where', 10, 2 );
			}
			
			// first post query to get all post ids
			$gmw_query = new WP_Query(array('post_type' => $wppl_post, 'post_status' => array('publish'), 'tax_query' => $tax_args, 'fields' => 'ids', 'posts_per_page'=>-1 ));
			remove_filter( 'posts_where', 'gmw_posts_where' );
			
			//if no results in first query no need to run second query
			if ( !isset($gmw_query->posts) || empty($gmw_query->posts) ) {
				$total_results  = array(); 
			} else {
				// run second query based on the results of the first query and distance
				$sql_array = (isset($calculate) && !empty($calculate) ) ? array_merge(array($lat,$long,$lat),$gmw_query->posts) : $gmw_query->posts;
				$total_results = $wpdb->get_results(
					$wpdb->prepare("SELECT wposts.* {$calculate}
					FROM " . $wpdb->prefix . "places_locator  wposts	
					WHERE wposts.post_id IN (".str_repeat("%d,", count($gmw_query->posts)-1) . "%d)
					{$having} ORDER BY {$order_by}", $sql_array)
				);
			}
		
			/* enter results into session */
			$_SESSION['wppl_search_posts'] = $total_results;
			$_SESSION['wppl_url_posts'] = remove_query_arg('pagen');
		
		/* if results exists in session than use them no need to run queries above again */
		} else {	
			$total_results = $_SESSION['wppl_search_posts'];
		}
		/// divide results to pages ///
		$wppl['posts_within'] = array_chunk($total_results,$per_page);
		/// count number of pages ///
		$pages = count($wppl['posts_within']);
		/// posts to display ///
		$wppl['posts_within'] = $wppl['posts_within'][$from_page];
		//// CKECK IF WE GOT AT LEAST ONE RESULT TO DISPLAY   ////
		//// OTHERWISE NO NEED TO RUN THE FUNCTION BELOW ////
		if (!empty($wppl['posts_within'])) { 
			global $pc, $mc;
			
			//////// DISPLAY OUR POSTS LOOP - YAAAAAAYYY  /////////	
			$mc = 1; /* single maps counter */ 
			$pc = ($from_page * $per_page) +1; /* post count, to display number of post */
		
			if( ($results_type == "both") || ($results_type == "map") ) $map_yes = 1;
			include_once GMW_PT_PATH .'template-functions.php';
			
			echo '<div class="wppl-output-wrapper-'. $wppl['form_id'] . '" id="wppl-output-wrapper" style="width:'; echo ($main_wrapper_width) ? $main_wrapper_width.'px' : '100%'; echo '">';
				if ($map_yes == 1)echo '<div id="show-hide-btn-wrapper"><a href="#" class="map-show-hide-btn"><img src="'.GMW_URL.'/images/show-map-icon.png" /></a></div>';
			
				echo '<div class="wppl-pagination-wrapper">';
					echo '<h2 class="wppl-h2"> Showing ' . count($wppl['posts_within']) . ' out of ' . count($total_results) . ' results ' . $showing . '</h2>';
					
					pagination_links($pages, $per_page);
				
				echo '</div>'; // pagination wrapper//
				
				///// DISPLAY MAP  /////		
				if ( isset($map_yes) && $map_yes == 1 ) {
					echo '<div id="wppl-hide-map" style="float:left;">';
					echo	'<div class="wppl-map-wrapper" style="position:relative">';
					echo		'<div id="wppl-pt-map" style="width:'; echo (!empty($map_width)) ? $map_width : 500 ; echo 'px; height:'; echo (!empty($map_height)) ? $map_height : 500; echo 'px;"></div>';
					echo		'<img class="map-loader" src="'.GMW_URL. '/images/map-loader.gif" style="position:absolute;top:45%;left:33%;"/>';
					echo	'</div>';// map wrapper //
					echo '</div>'; // show hide map //	
					
				};	
				echo	'<div class="clear"></div>';	
	
				if($random_featured_posts) {		
					$featured_posts_within = array_random_assoc($total_results, $random_featured_count);
					include_once GMW_PATH .'/plugins/featured-posts/results.php';
				}
	
				//////  DISPLAY POSTS ///////
				if( ($results_type == "both") || ($results_type == "posts") ) {
					echo '<div class="wppl-results-wrapper">';
						if( file_exists( STYLESHEETPATH.'/results.php' ) ) {
    						include(STYLESHEETPATH.'/results.php');
						} else {		
							include('themes/'.$results_template .'/results.php');	
						}
					echo '</div>'; // results wrapper //
			
					echo 	'<div id="wppl-go-top">Top</div>';
					echo	'<div class="wppl-pagination-wrapper">';
					echo		'<h2 class="wppl-h2"> Showing ' . count($wppl['posts_within']) . ' out of ' . count($total_results) . ' results ' . $showing . '</h2>';	
									pagination_links($pages, $per_page);
					echo	'</div>'; // pagination wrapper//
				}
				echo	'</div>'; // output wrapper //
				
				if ( ( isset($map_yes) && $map_yes == 1 ) || ( isset($single_map) && $single_map == 1) ) {
					if (!isset($map_controls) || empty($map_controls) ) $map_controls = false;
					$main_map_args = array(
						'sMapType' 		=> $single_map_type,
						'locations' 	=> $wppl['posts_within'],
						'yLocation' 	=> $your_loc,
						'page' 			=> $from_page * $per_page,
						'mapType' 		=> $map_type,
						'aInfo'	 		=> $additional_info,
						'zoomLevel' 	=> $zoom_level,
						'autoZoom' 		=> $auto_zoom,
						'mapIconUsage' 	=> $map_icon_usage,
						'mIFolder' 		=> GMW_URL. '/map-icons/',
						'mainIcon' 		=> $map_icon,
						'yLIcon' 		=> $your_location_icon,				
						'units' 		=> $wppl['units_array'],
						'postTypeIcons' => $wppl_options['post_types_icons'],
						'mapControls'	=> $map_controls,
						'gmwVersion'	=> GMW_VERSION
					);
					
					//wp_enqueue_script( 'wppl-infobox', true);
					if (isset($map_yes) && $map_yes == 1 ) {
						wp_enqueue_script( 'wppl-map', true);	
						wp_localize_script( 'wppl-map', 'mainMapArgs', $main_map_args );
					}
					if (isset($single_map) && $single_map == 1) {
						wp_enqueue_script( 'wppl-small-maps', true);
						wp_localize_script( 'wppl-small-maps', 'mainMapArgs', $main_map_args );
					}	
				}
    		// DONE - GOOD JOB //	
    	// IF NO RESULTS //
    	} else { 					
    		echo '<h2 class="wppl-h2">' . $wppl_options['no_results']. '</h2>'; echo ($wppl_options['wider_search']) ? ' <p>please, <a href="'. add_query_arg('wppl_distance', $wppl_options['wider_search_value']). '" onclick="document.wppl_form.submit();">click here</a> to search wihtin a greater range or <a href="'. add_query_arg('wppl_address', ''). '" onclick="document.wppl_form.submit();">click here</a> to see all results. </p>' : ''; echo '';
   		} // end function //
    }	 
    
    if (empty($params['form'])) {
   		$output_results=ob_get_contents();
		ob_end_clean();
		return $output_results;
	}
      
}
add_shortcode( 'wppl_results' , 'wppl_get_results' );


////// SHORTCODE FOR SINGLE LOCATION MAP///////
function wppl_single_location($single) {
	extract(shortcode_atts(array(
		'map_height' 	=> '250',
		'map_width' 	=> '250',
		'map_type' 		=> 'ROADMAP',
		'zoom_level' 	=> 13,
		'show_info' 	=> 1,
		'post_id'		=> 0,
		'directions'	=> 1
	),$single));
	
    global $wpdb, $mmc, $get_loc;
    if(!isset($mmc) || empty($mmc) ) $mmc = 0;
    $mmc++;
       	
   	if ( $post_id != 0 || is_single() ) {
   	
   		if( isset($post_id) ) $post = get_post( $post_id);
   	 
    	$post_info = $wpdb->get_results( 
    		$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "places_locator 
 	 	  		WHERE post_id = %s", 
 		   		array($post->ID
	    )), ARRAY_A );

		if ( isset($post_info) && $post_info !=0) {
   			$get_loc[$mmc] = array(
   				'post_id'		=> $post_info[0]['post_id'],
   				'post_title'	=> $post_info[0]['post_title'],
   				'map_height'	=> $map_height,
   				'map_width'		=> $map_width,
    			'lat' 			=> $post_info[0]['lat'],
    			'long'			=> $post_info[0]['long'],
    			'phone'			=> $post_info[0]['phone'],
    			'fax'			=> $post_info[0]['fax'],
    			'email'			=> $post_info[0]['email'],
    			'website'		=> $post_info[0]['website'],
    			'address'		=> $post_info[0]['address'],
   		 		'map_icon'		=> $post_info[0]['map_icon'],
   		 		'map_type' 	 	=> $map_type,
   		 		'zoom_level'	=> $zoom_level
    		);
    	
			$single_location_args = array (
				'singleLocation' => $get_loc,
				'mapId'			 => $mmc,
			);
			
			wp_enqueue_script( 'wppl-sl-map', true);
   		 	wp_enqueue_script( 'wppl-infobox', true);
   		 	wp_localize_script('wppl-sl-map', 'singleLM', $single_location_args);
		 	
    		$single_address = ( isset($get_loc[$mmc]['address']) && !empty($get_loc[$mmc]['address'])) ? $get_loc[$mmc]['address'] : 'N/A';
			$single_phone 	= ( isset($get_loc[$mmc]['phone']) && !empty($get_loc[$mmc]['phone']) ) ? $get_loc[$mmc]['phone']   : 'N/A';
			$single_fax 	= ( isset($get_loc[$mmc]['fax']) ) && !empty($get_loc[$mmc]['fax'])	   ? $get_loc[$mmc]['fax'] : 'N/A';
			$single_email 	= ( isset($get_loc[$mmc]['email']) && !empty($get_loc[$mmc]['email']))   ? $get_loc[$mmc]['email'] : 'N/A';
			$single_website = ( isset($get_loc[$mmc]['website']) && !empty($get_loc[$mmc]['website']) ) ? '<a href="http://' . $get_loc[$mmc]['website']. '" target="_blank">' .$get_loc[$mmc]['website']. '</a>' : "N/A";
		
 			$single_map .='';
 			$single_map .=	'<div class="wppl-single-wrapper">';
			$single_map .=		'<div class="wppl-single-map-wrapper wppl-map-wrapper" style="position:relative">';
			$single_map .=			'<div id="single-location-map-'.$mmc.'" style="width:' . $get_loc[$mmc]['map_width'] .'px; height:' . $get_loc[$mmc]['map_height'] . 'px;"></div>';
		
			$single_map .= 			'<div class="wppl-single-direction-wrapper" style="width:'.($get_loc[$mmc]['map_width'] - 10).'px">';
	   	 	$single_map .= 				'<div class="wppl-single-direction" style="display:none;">';
			$single_map .=					'<form action="http://maps.google.com/maps" method="get" target="_blank">';
			$single_map .= 						'<input type="text" size="35" name="saddr" value="'. $_GET['address'].'" />';
			$single_map .= 						'<input type="hidden" name="daddr" value="'. $get_loc[$mmc]['address'].'" />';
			$single_map .= 						'<input type="submit" class="button" value="GO" />';
			$single_map .= 					'</form>'; 
   	 		$single_map .= 				'</div>';
   	 		if ( isset($directions) && $directions == 1 ) {
    			$single_map .= 				'<a href="#" class="show-directions">Get Directions</a>';
    		}
    		$single_map .= 			'</div>';
    		$single_map .=		'</div>';// map wrapper //
		
			if ($show_info == 1) {
				$single_map .= '<div class="wppl-additional-info">';
				$single_map .=		'<div class="wppl-address"><span>Address: </span>' . $single_address . '</div>';
   		 		$single_map .=		'<div class="wppl-phone"><span>Phone: </span>' . $single_phone . '</div>';
    			$single_map .=		'<div class="wppl-fax"><span>Fax: </span>' . $single_fax . '</div>';
    			$single_map .=		'<div class="wppl-email"><span>Email: </span>' . $single_email . '</div>';
   	 			$single_map .=		'<div class="wppl-website"><span>Website: </span>' . $single_website . '</div>';
    			$single_map .= '</div>';
    		} 
	 
   		 	$single_map .= '</div>';
   	 	
   	 		return $single_map;
    	
    	}
	}
}
add_shortcode( 'wppl_single_location' , 'wppl_single_location' );

?>