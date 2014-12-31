<?php

///// SHORTCODE TO DISPLAY RESULTS  ////
function wppl_get_results($get_results) {
	if(!empty( $_GET['action'] ) &&  $_GET['action'] == "wppl_post") {	
 	global $org_address, $mikm, $mikm_n, $unit, $ptc, $taxonomies, $show_map, $by_driving, $map_height, $map_width, $per_page, $options, $result_style, $get_directions, $map_type, $show_excerpt, $show_thumb, $thumb_width, $thumb_height, $zoom_level, $auto_zoom;
		
		$ptc = count(explode( " ",$_GET['wppl_post']));
		$options_r = get_option('wppl_shortcode'); 
		
		// shortcode attributed //	
   		extract(shortcode_atts($options_r[$_GET['wppl_form']], $get_results)); 
    
    	(!empty($per_page)) ? $per_page = $per_page : $per_page = '5' ;
 		
 		// distance units //
		if ($_GET['wppl_units'] == "imperial") { $mikm = 3959; $mikm_n = "Mi"; $unit = "ptm"; } else {  $mikm = 6371; $mikm_n = "Km"; $unit = "ptk";}
	
    	get_results_within();
    	}    
	}
add_shortcode( 'wpplresults' , 'wppl_get_results' );

//// CREATE SHORTCODE TO DISPLAY FORM AND RESULTS ////
function wppl_shortcode($params) {	
	
	$post_types = array();
		
	// get options from admin settings //
	$options = get_option('wppl_fields'); 
	$options_r = get_option('wppl_shortcode'); 
	$miles = explode(",", $options['distance_values']);
	
	// shortcode attributed //	
    extract(shortcode_atts($options_r[$params[form]], $params)); 
    
    ob_start();
    
    (!empty($per_page)) ? $per_page = $per_page : $per_page = '5' ;
    
    // post counter //
   	$ptc = count($post_types);
    $pti = implode( " ",$post_types); 
	
	($_GET["wppl_post"] == $pti ) ? $pti_all = 'selected="selected"' : $pti_all = "";
	
	//$faction =  get_bloginfo('wpurl');
	if($params[form_only] == 1) { 
		$widget = "widget-";
		$faction = get_bloginfo('wpurl') . "/" . $options['results_page'];
	} elseif ($params[form_only] == "y") { 
		$faction = get_bloginfo('wpurl') . "/" . $options['results_page'];
		$widget = "";
	} elseif ( (empty($params[form_only])) || ($params[form_only] == "y") ){ 
		$faction = "";
		$widget = "";
	} 
	
	if ($_GET["wppl_units"] == "metric" ) {
		$unit_m = 'selected="selected"';
		$unit_i = "";	
	} else {
		$unit_i = 'selected="selected"';
		$unit_m = "";
	}

	//// DISPLAY SEARCH FORM ////	
	echo	'<div class="wppl-' .$widget .'form-wrapper">';
	echo 	'<form class="wppl-' .$widget .'form" name="wppl_form" id="wppl-form" action="'. $faction .'" method="get">';
	echo		'<input type="hidden" name="pagen" value="0" />';
				// only if more then one post type entered display them in dropdown menu otherwise no dropdown needed //
				if($ptc>1) {
	echo			'<div class="wppl-post-type-wrapper">';
	echo				'<p>';
	echo    				'<label for="wppl-post-type">What are you looking for: </label>';				
							/* post types dropdown */
	echo 					'<select name="wppl_post" id="wppl-post-type">';
	echo 						'<option value="' . $pti . '"' . $pti_all . '> -- Search Site -- </option>';
								foreach ($post_types as $post_type) {
								if( $_GET["wppl_post"] == $post_type ) {$pti_post = 'selected="selected"';} else {$pti_post = "";}
	echo 							'<option value="'.$post_type .'"' .$pti_post .'>'. get_post_type_object($post_type)->labels->name . '</option>';			
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

	echo	'<div class="wppl-category-wrapper">';			
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
	echo		'<p>';
	echo        	'<label for="wppl-address">Enter zip code, city or a full address: </label>';
	echo        	'<input type="text" name="wppl_address" class="wppl-address" value="'. $_GET['wppl_address'] . '" size="35" tabindex="1" />';
	echo		'</p>';
	echo	'</div>'; // end address wrapper//
	
			// distance values dropdown	//
	echo	'<div class="wppl-distance-units-wrapper">';
	echo    	'<label for="wppl-distance">Within: </label>';
	echo		'<div class="wppl-distance-wrapper">';
	echo    		'<p>';
	echo 				'<select name="wppl_distance" class="wppl-distance">';
							foreach ($miles as $mile) {	
								if( $_GET["wppl_distance"] == $mile ) {$mile_s = 'selected="selected"';} else {$mile_s = "";}
	echo 						'<option value="'. $mile .'"' .$mile_s. '>'. $mile . '</option>';
							}
	echo 				'</select>'; 
	echo			'</p>';
	echo		'</div>';
	echo		'<div class="wppl-units-wrapper">';
	echo			'<p>';			
						// units dropdown //
	echo 				'<select name="wppl_units" class="wppl-units">';
	echo 					'<option value="imperial"'.$unit_i. '>Miles</option>';
	echo					'<option value="metric"'.$unit_m. '>Kilometers</option>';
	echo 				'</select>';
	echo			'</p>';
	echo		'</div>';	
	echo	'</div>'; // distance wrapper //
	
	echo	'<div class="wppl-submit">';
	echo		'<p>';
	echo    		'<input name="submit" type="submit" id="submit" value="Submit" />';
	echo			'<input type="hidden" name="wppl_form" value="' .$params[form]. '" />';
	echo			'<input type="hidden" name="action" value="wppl_post" />';
	echo 		'</p>';
	echo	'</div>';	
	echo    '</form>';
	echo	'</div>'; // form wrapper //
	
		
	if (empty($params[form_only])) {
    		wppl_get_results($get_results);
    }
    $output_string=ob_get_contents();
	ob_end_clean();

	return $output_string;
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
function get_results_within() {	
	global $wpdb, $post, $distance_between, $lat, $long, $per_page, $from_page, $taxonomies, $post_types, $ptc, $mikm, $mikm_n, $units, $org_address; 
	
	// original address entered //       
 	//$org_address = ;      
    
    // prepare address for google //
   	$org_address = str_replace(" ", "+", $_GET['wppl_address']);

    // radius value //
 	$radius = $_GET['wppl_distance'];
	
	//$taxterms = array();
	
	$from_page = $_GET['pagen'] * $per_page;
		
	/////// CONVERT ORIGINAL ADDRESS TO LATITUDE/LONGITUDE ////
	$do_it = ConvertToCoords($org_address);
	// green marker for "your location" //
	(!empty($_GET['wppl_address'])) ? $your_loc = array("Your Location", $lat,$long) : $your_loc = "0";
	
		
	//// CREATE TAXONOMIES VARIATIONS TO EXECUTE WITH SQL QUERY ////
	if ($ptc<2 && (isset($taxonomies))) {
		// count that we have at leaset one category choosen //
		//otherwise no need to run the first wp_query//
		$rr = 0; 
		$args = array('relation' => 'AND');
		foreach ($taxonomies as $tax) { 
			if ($_GET[$tax] != 0) {
				$rr++;
				$args[] = array(
					'taxonomy' => $tax,
					'field' => 'id',
					'terms' => array($_GET[$tax])
				);
			}		
		}
		
		if ($rr > 0) {	
			// run first query for taxonomies - return posts id //
			$query = new WP_Query( array('post_type' => $_GET['wppl_post'],'post_status' => array('publish','inherit'), 'tax_query' => $args));
			
			foreach ($query->posts as $tt) { 
				$posts_id[] = $tt->ID;
			}		
			if($posts_id) { 
				$posts_id = implode(',',$posts_id);
			}			
			$ands = "AND wposts.post_id IN ($posts_id)";
		}
	}
	
	$order_by = "wposts.post_title";
	
	if (!empty($_GET['wppl_address'])) {
		$having = "HAVING distance <= $radius OR distance IS NULL";
		$order_by = "distance";
		$calculate = ", ROUND(" . $mikm . "* acos( cos( radians( $lat ) ) * cos( radians( wposts.lat ) ) * cos( radians( wposts.long ) - radians( $long ) ) + sin( radians( $lat ) ) * sin( radians( wposts.lat) ) ),1 )  AS distance";
		$showing = 'within ' . $radius . ' ' . $mikm_n . ' from ' . $_GET['wppl_address'];
	}
		
	// IF WE HAVE AT LEAST ONE TERM FROM ANY TAXONOMY, CREATE "JOINS" AND "ANDS" FOR OUR SQL QUERY "
	$sql ="
		SELECT SQL_CALC_FOUND_ROWS wposts.post_id, wposts.lat, wposts.long, wposts.address, wposts.post_title, wposts.phone, wposts.fax, wposts.email, wposts.website {$calculate}
			FROM " . $wpdb->prefix . "places_locator  wposts	
    		WHERE 
    		wposts.post_type IN ('" . str_replace(" ", "','", $_GET['wppl_post']) . "')
    		AND wposts.post_status IN ('publish','inherit')
    		{$ands}
            GROUP BY wposts.post_id, wposts.lat, wposts.long		
			{$having} ORDER BY {$order_by} LIMIT " . $from_page . "," . $per_page  ;
	
	//print_r($sql);
	$sql = $wpdb->prepare($sql,$values);	
	global $posts_within, $map_data, $plugin_map, $total_rows, $by_driving, $mikm_n, $posts_within, $map_type, $result_style, $auto_zoom;
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
	$page_n = $_GET['pagen'];
	$pages = $total_rows[0]/$per_page; //get number of pages //
		if ($pages > 1) {
			echo '<div class="wppl-pagination-holder">';
			if ($page_n > 0) {		
					echo '<div class="prev-btn"><a href="' .add_query_arg('pagen', ($page_n - 1)) .'" onclick="document.wppl_form.submit();">&#171;</a></div>';
				} 
				echo '<div class="wppl-pagination">';
					for($i=0; $i< $pages; $i++): 
						$num = $i + 1;
    					echo '<div '; if( $page_n+1 == $num ) :; echo 'class="current"'; endif; echo ' id="page-' . $num . '"><a href="' .add_query_arg('pagen',$i) .'" onclick="document.wppl_form.submit();">' . $num .  '</a></div>';
					endfor; 
				echo '</div>';
				if ($page_n < ($pages -1)) {
					echo '<div class="next-btn"><a href="' .add_query_arg('pagen', ($page_n + 1)) .'" onclick="document.wppl_form.submit();">&#187;</a></div>';
				 }			
			echo '</div>';
		}
	}	
	
	//////// DISPLAY OUR POSTS LOOP - YAAAAAAYYY  /////////	
	global $show_map, $which_map, $map_height, $map_width, $thumb_size, $directions, $zoom_level;
	
	echo	'<div id="wppl-output-wrapper-' . $result_style .'">';
	
	echo	'<div class="wppl-pagination-wrapper">';
	echo		'<h2 class="wppl-h2"> Showing ' . count($posts_within) . ' out of ' . $total_rows[0] . ' results ' . $showing . '</h2>';
				pagination_links();
	echo	'</div>'; // pagination wrapper//
	
	///// DISPLAY MAP  /////		
					if($show_map == "1") {
	echo			'<div class="wppl-map-wrapper-' . $result_style .'">';
	echo				'<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>';
	echo				'<div id="map" style="width:'; echo (!empty($map_width)) ? $map_width : 500 ; echo 'px; height:'; echo (!empty($map_height)) ? $map_height : 500; echo 'px;"></div>';
	echo 				'<script type="text/javascript">'; 
	echo 					'locations= '.json_encode($posts_within),';'; 
	echo 					'your_location= '.json_encode($your_loc),';'; 
	echo					'page= '.json_encode($from_page),';'; 
	echo 					'mapType= '.json_encode($map_type),';';
	echo					'zoomLevel= '. $zoom_level,';';
	echo					'autoZoom= '.json_encode($auto_zoom),';';
	echo 					'single="0";'; 
	echo                '</script>';
	echo 				'<script src="' . plugins_url('js/map.js', __FILE__) . '" type="text/javascript"></script>';
	echo			'</div>';// map wrapper //
					};	
	echo	'<div class="clear"></div>';
	
	//////  DISPLAY RESULTS ///////
	
	echo		'<div class="wppl-results-wrapper-' . $result_style .'">';
		if( file_exists( STYLESHEETPATH. '/wppl_results.php' ) ) {
    		include(STYLESHEETPATH . '/wppl_results.php');
		} else {	
			include('wppl_results.php');	
		}
	echo		'</div>'; // results wrapper //
	
	echo	'<div class="wppl-pagination-wrapper">';
	echo		'<h2 class="wppl-h2"> Showing ' . count($posts_within) . ' out of ' . $total_rows[0] . ' results ' . $showing . '</h2>';
				pagination_links();
	echo	'</div>'; // pagination wrapper//
	
	echo	'</div>'; // output wrapper //
    // DONE - GOOD JOB //
    
    // IF NO RESULTS //
    } else {
    	echo '<h2 class="wppl-h2">No results were found.</h2>';
    }    	
} // end function //

?>
