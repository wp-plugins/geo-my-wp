<?php

//// CREATE SHORTCODE TO DISPLAY FORM AND RESULTS ////

function wppl_shortcode($params) {	
	global $post_types;
	global $taxonomies;	
	global $show_map;
	global $by_driving;
	global $which_map;
	global $map_height;
	global $map_width;
	global $per_page;
	global $options;
	global $ptc; // post type counter //
	global $pti; // post type/types to execute //
	global $get_directions;
	$post_types = array();
		
	// get options from admin settings //
	$options = get_option('wppl_fields'); 
	$options_r = get_option('wppl_shortcode'); 
	$miles = explode(",", $options['distance_values']);
	$shortcode_arg = get_option('wppl_shortcode');
	
	// shortcode attributed //	
    extract(shortcode_atts($options_r[$params[form]], $params)); 
    
    (!empty($per_page)) ? $per_page = $per_page : $per_page = '5' ;
    
    

    // post counter //
    $ptc = count($post_types);
    $pti = implode( " ",$post_types); 
    
	//// DISPLAY SEARCH FORM ////	
	echo 	'<form class="wppl-form" name="wppl_form" id="wppl-form" action="" method="get">';
	echo		'<input type="hidden" name="page" value="0" />';
				// only if more then one post type entered display them in dropdown menu otherwise no dropdown needed //
				if($ptc>1) {
	echo			'<div class="wppl-post-type-wrapper">';
	echo				'<p>';
	echo    				'<label for="wppl-post-type">What are you looking for: </label>';				
							/* post types dropdown */
	echo 					'<select name="wppl_post" id="wppl-post-type">';
	echo 						'<option value="' . $pti . '"' ; if( $_GET["wppl_post"] == $pti ) :; echo 'selected="selected"'; endif; echo '> -- Search Site -- </option>';
								foreach ($post_types as $post_type) {
	echo 							'<option value="'.$post_type .'"'; if( $_GET["wppl_post"] == $post_type ) :; echo 'selected="selected"'; endif; echo '>'. get_post_type_object($post_type)->labels->name . '</option>';			
								}
	echo 					'</select>';
	echo				'</p>';
	echo			'</div>'; // post type wrapper //
				} else {
					$post_types = implode(' ',$post_types);
	echo			'<input type="hidden" name="wppl_post" value="' . $post_types. '" />';
				}// post category //
								
			// disply categories only when one post type chooses //
			// we cant switch categories between the post types if more then one // 
			if ($ptc<2 && (isset($taxonomies))) {

	echo		'<input type="hidden" name="wppl_post" value="' . $post_types. '" />';
	echo		'<div class="wppl-category-wrapper" >';
			
			// output dropdown for each taxonomy //
				foreach ($taxonomies as $tax) {
	echo	 		'<div id="' . $tax . '_cat">';
	echo	       		'<label for="wppl-category-id">' . get_taxonomy($tax)->labels->singular_name . ': </label>';
	        			custom_taxonomy_dropdown($tax);				
	echo 			'</div>';    
				} // end foreach //			
	echo	'</div>'; // category-wrapper //
			} // end taxonomies dropdown	//
			
					// address //
	echo	'<div class="wppl-address-wrapper">';        		
	echo	'<p>';
	echo        '<label for="wppl-address">Enter zip code, city or a full address: </label>';
	echo        '<input type="text" name="wppl_address" class="wppl-address" value="'. $_GET['wppl_address'] . '" size="45" tabindex="1" />';
	echo	'</p>';
	echo	'</div>'; // end address wrapper//
	
			// distance values dropdown	//
	echo	'<div class="wppl-distance-wrapper">';
	echo		'<div class="wppl-num-value">';
	echo    		'<p>';
	echo    			'<label for="wppl-distance">Within: </label>';
	echo 				'<select name="wppl_distance" class="wppl-distance">';
							foreach ($miles as $mile) {	
	echo 						'<option value="'. $mile .'"'; if( $_GET["wppl_distance"] == $mile ) :; echo 'selected="selected"'; endif; echo '>'. $mile . '</option>';
							}
	echo 				'</select>'; 
	echo			'</p>';
	echo		'</div>';
	echo		'<div class="wppl-units-wrapper">';
	echo			'<p>';			
						// units dropdown //
	echo 				'<select name="wppl_units" id="wppl-units">';
	echo 					'<option value="imperial"' ; if( $_GET["wppl_units"] == "imperial" ) :; echo 'selected="selected"'; endif; echo '>Miles</option>';
	echo					'<option value="metric"' ; if( $_GET["wppl_units"] == "metric" ) :; echo 'selected="selected"'; endif; echo '>Kilometers</option>';
	echo 				'</select>';
	echo			'</p>';
	echo		'</div>';	
	echo	'</div>'; // distance wrapper //
	
	echo	'<div class="wppl-submit">';
	echo		'<p>';
	echo    		'<input name="submit" type="submit" id="submit" value="Submit" /></p>';
	echo 			'<input type="hidden" name="wppl_action" value="search" />';
	echo 		'</p>';
	echo	'</div>';	
	echo    '</form>';
	
if(!empty($_GET['wppl_address'])) {	
 
 	global $org_address;
 	global $mikm;
 	global $mikm_n;
 	global $unit;
 		
   	// original address entered //       
 	$org_address = $_GET['wppl_address'];      
    
    // prepare address for google //
    $org_address = str_replace(" ", "+", $org_address);

    // radius value //
 	$the_distance = $_GET['wppl_distance'];

	// distance units //
	if ($_GET['wppl_units'] == "imperial") { $mikm = 3959; $mikm_n = "Mi"; $unit = "ptm"; } else {  $mikm = 6371; $mikm_n = "Km"; $unit = "ptk";}
	
	///// RUN THE FUNCTION TO CALCULATE DISTANCE AND DISPLAY POSTS /////
    $listings = get_results_within( $org_address, $the_distance );
    }    
} //end of shortcode
add_shortcode( 'wppl' , 'wppl_shortcode' );

///// CONVERT ADDRESS TO COORDINATES //////
function ConvertToCoords($address) {	

	$ch = curl_init();	
    $rip_it = array( " " => "+", "," => "", "?" => "", "&" => "", "=" => "" , "#" => "");
    
    //// MAKE SURE ADDRES DOENST HAVE ANY CHARACTERS THAT GOOGLE CANNOT READ //
    $address = str_replace(array_keys($rip_it), array_values($rip_it), $address);
    
    //// GET THE XML FILE WITH RESUALTS
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/2.0 (compatible; MSIE 3.02; Update a; AK; Windows 95)");
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	curl_setopt($ch, CURLOPT_URL, 'http://maps.google.com/maps/geo?output=xml&q=' . $address  );
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	$got_xml = curl_exec($ch);    
    //// PARSE THE XML FILE //////////////
	$xml = new SimpleXMLElement($got_xml);	
	//// GET THE LATITUDE/LONGITUDE FROM THE XML FILE ///////////////
	list($longitude, $latitude) = explode(",", $xml->Response->Placemark->Point->coordinates);	
	global $lat;
	global $long;
	$lat = "$latitude";
	$long = "$longitude";		
}	

/////// CALCULATE DRIVING DISTANCE ////////
function distance_between($des_lat,$des_long) {  			 	
  	global $distance_between, $lat, $long, $directions, $org_address, $unit;		
  
   $ci = curl_init();
   	curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ci, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/2.0 (compatible; MSIE 3.02; Update a; AK; Windows 95)");
	curl_setopt($ci, CURLOPT_HTTPGET, true);
	curl_setopt($ci, CURLOPT_URL, 'http://maps.googleapis.com/maps/api/directions/xml?origin=' . $lat . "," . $long . '&destination=' .  $des_lat . "," . $des_long .  '&units=' . $_GET['wppl_units'] . '&sensor=true'  );
	curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60);
	$got_xml = curl_exec($ci);		
   	$xml_distance = new SimpleXMLElement($got_xml);
   	list($distance_between) = explode(",", $xml_distance->route->leg->distance->text);		
}
/////// GET DIRECTIONS LINK ////////
function get_directions($des_lat,$des_long,$address) {  			 	
  	global $lat, $long, $directions, $org_address, $unit;				
	$directions = '<a href="http://maps.google.com/maps?f=d&hl=en&doflg=' . $unit . '&geocode=&saddr=' .$org_address . '&daddr=' . str_replace(" ", "+", $address) . '&sll=' . $lat . "," . $long . '&sspn=' .  $des_lat . "," . $des_long . '&ie=UTF8&z=12" target="_blank">Get Directions</a>';
}

// when post status changes - change it in our table as well //
function filter_transition_post_status( $new_status, $old_status, $post ) { 
    global $wpdb;
    $wpdb->query( 
        $wpdb->prepare( 
          "UPDATE wp_places_locator SET post_status=%s WHERE post_id=%d", 
           $new_status,$post->ID
         ) 
     );
}
add_action('transition_post_status', 'filter_transition_post_status', 10, 3);

// delete map and info from our database after post was deleted //
function delete_address_map_rows()	{
	global $wpdb;
	global $post;
	$wpdb->query(
		$wpdb->prepare( 
			"DELETE FROM wp_places_locator WHERE post_id=%d",$post->ID
		)
	);
}
add_action('before_delete_post', 'delete_address_map_rows');
 
//// DROPDOWN TAXONOMIES FOR THE SEARCH FORM /////
function custom_taxonomy_dropdown($tax_name) {
$args = array(
		'taxonomy' 			=> $tax_name,
		'hide_empty' 		=> 1,
		'depth' 			=> 10,
		'hierarchical' 		=>	1,
		'show_count'        => 1,
		'id' 				=>	$pts . '_id',
		'name' 				=> $tax_name,
		'selected' 			=> $_GET[$tax_name],
		'show_option_all'   => ' - All - ',
		);		
wp_dropdown_categories($args); 
} 
   			
////////// MAIN CALCULATION FUNCTION ///////////////////
function get_results_within( $org_address, $radius ) {
	
	global $wpdb, $post, $distance_between, $lat, $long, $per_page, $from_page, $taxonomies, $post_types, $ptc, $mikm; 	
	$taxterms = array();
	
	$from_page = $_GET['page'] * $per_page;
		
	/////// CONVERT ORIGINAL ADDRESS TO LATITUDE/LONGITUDE ////
	$do_it = ConvertToCoords($org_address);
	// green marker for "your location" //
	$your_loc = array("Your Location", $lat,$long); 
		
	//// CREATE TAXONOMIES VARIATIONS TO EXECUTE WITH SQL QUERY ////
	if ($ptc<2 && (isset($taxonomies))) {
		$tt = 0;
		$terms = array();
		$terms_c = array(); // terms counter ceates %d and %s arrays for SQL //
		$count = 0; // count the terms chooses //
		
		// run function for each taxonomy //
		foreach ($taxonomies as $tax) { 
			if ($_GET[$tax] != 0) {
				$count++;
				// create taxonomy against terms for the sql query // 
				$taxterms[$tt] = $tax . '/' . $_GET[$tax];
				array_push($terms, $_GET[$tax]);
				array_push($terms_c,'%d');
				// if this is a parent categories, get its children IDs we are going to display them as well. //
				$children = get_term_children($_GET[$tax], $tax);
				$tt++;
				// insert each child into the array //
				if ($children) {
					foreach ($children as $child) {
      					$taxterms[$tt] = $tax . '/' . $child;
      					array_push($terms, $child);
      					array_push($terms_c,'%d');
      					$tt++;
      				}
      			} 
      		}
      	}
	}
	
	// IF WE HAVE AT LEAST ONE TERM FROM ANY TAXONOMY, CREATE "JOINS" AND "ANDS" FOR OUR SQL QUERY "
	if ($count!=0) {
		$terms_c =  implode(",",$terms_c);
		$terms = array_keys(array_flip($terms));
		$taxterms = array_keys(array_flip($taxterms));	
    	$values = array_merge($terms,$taxterms);  // For %d and $s //
		$having = "count(*) = {$count} AND";
		$joins = "INNER JOIN $wpdb->term_relationships ON (wposts.post_id = $wpdb->term_relationships.object_id)
			INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
			INNER JOIN $wpdb->terms ON($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)";
		$ands = "AND $wpdb->term_taxonomy.term_id IN ({$terms_c})
        	AND CONCAT($wpdb->term_taxonomy.taxonomy,'/',$wpdb->term_taxonomy.term_id) IN (" . str_replace('%d','%s',$terms_c) . ")";
    } 
    
	$sql ="
		SELECT SQL_CALC_FOUND_ROWS wposts.post_id, wposts.lat, wposts.long, wposts.address, wposts.post_title, ROUND(" . $mikm . "* acos( cos( radians( $lat ) ) * cos( radians( wposts.lat ) ) * cos( radians( wposts.long ) - radians( $long ) ) + sin( radians( $lat ) ) * sin( radians( wposts.lat) ) ),1 )  AS distance 
			FROM wp_places_locator  wposts	
    		{$joins}
    		WHERE 
    		wposts.post_type IN ('" . str_replace(" ", "','", $_GET['wppl_post']) . "')
    		AND wposts.post_status IN ('publish','inherit')
    		{$ands}
            GROUP BY wposts.post_id, wposts.lat, wposts.long		
			HAVING {$having} distance <= $radius OR distance IS NULL ORDER BY distance LIMIT " . $from_page . "," . $per_page  ;
			
	$sql = $wpdb->prepare($sql,$values);
	
	global $posts_within, $map_data, $plugin_map, $total_rows, $by_driving, $mikm_n, $posts_within;
	$posts_within = $wpdb->get_results($sql);
	//print_r($sql);
	// get total results before "limit" to get the number of pages //
	$total_rows = $wpdb->get_col('SELECT FOUND_ROWS()',0); 	
  	
	//// CKECK IF WE GOT AT LEAST ONE RESULT TO DISPLAY   ////
	//// OTHERWISE NO NEED TO RUN THE FUNCTION BELOW ////
	if (!empty($posts_within)) { 
	
	// pagination function //	
	function pagination_links() {
	global $per_page, $total_rows, $mikm_n;
	$page_n = get_query_var('page');
	$pages = $total_rows[0]/$per_page; //get number of pages //
		if ($pages > 1) {
			echo '<div class="wppl-pagination-holder">';
			if ($page_n > 0) {		
					echo '<div class="prev-btn"><a href="' .add_query_arg('page', ($page_n - 1)) .'" onclick="document.wppl_form.submit();">&lt;&lt; Prev</a></div>';
				} 
				echo '<div class="wppl-pagination">';
					for($i=0; $i< $pages; $i++): 
						$num = $i + 1;
    					echo '<div '; if( $page_n+1 == $num ) :; echo 'class="current"'; endif; echo ' id="page-' . $num . '"><a href="' .add_query_arg('page',$i) .'" onclick="document.wppl_form.submit();">' . $num .  '</a></div>';
					endfor; 
				echo '</div>';
				if ($page_n < ($pages -1)) {
					echo '<div class="next-btn" id="fg"><a href="' .add_query_arg('page', ($page_n + 1)) .'" onclick="document.wppl_form.submit();">Next &gt;&gt;</a></div>';
				 }			
			echo '</div>';
		}
	}	
	
	//////// DISPLAY OUR POSTS LOOP - YAAAAAAYYY  /////////	
	global $show_map, $which_map, $map_height, $map_width, $thumb_size;
	
	echo	'<div id="wppl-output-wrapper">';
	echo		'<h2 class="wppl-h2"> Showing ' . count($posts_within) . ' out of ' . $total_rows[0] . ' results within ' . $radius . ' miles from ' . $_GET['wppl_address'] . '</h2>';
					pagination_links();
	///// DISPLAY MAP  /////		
					if($show_map == "1") {
	echo			'<div class="wppl-map-wrapper">';
	echo				'<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>';
	echo				'<div id="map" style="width:'; echo (!empty($map_width)) ? $map_width : 500 ; echo 'px; height:'; echo (!empty($map_height)) ? $map_height : 500; echo 'px;"></div>';
	echo 				'<script type="text/javascript">'; echo 'locations= '.json_encode($posts_within),';'; echo 'your_location= '.json_encode($your_loc),';'; echo 'page= '.json_encode($from_page),';'; echo '</script>';
	echo 				'<script src="' . plugins_url('js/map.js', __FILE__) . '" type="text/javascript"></script>';
	echo			'</div>';// map wrapper //
					};	
	echo	'<div class="clear"></div>';	
	//////  DISPLAY RESULTS ///////
	echo		'<div class="wppl-results-wrapper">';
		if( file_exists( STYLESHEETPATH. '/wppl_results.php' ) ) {
    		include(STYLESHEETPATH . '/wppl_results.php');
		} else {	
			include('wppl_results.php');	
		}
	echo		'</div>'; // results wrapper //
	
	pagination_links();
	
	echo	'</div>'; // output wrapper //
    // DONE - GOOD JOB //
    
    // IF NO RESULTS //
    } else {
    	echo '<h2 class="wppl-h2">No results were found.</h2>';
    }    	
} // end function //

?>
