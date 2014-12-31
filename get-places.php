<?php

//// DROPDOWN TAXONOMIES FOR THE SEARCH FORM /////
function custom_taxonomy_dropdown($tax_name) {
$args = array(
		'taxonomy' 			=> $tax_name,
		'hide_empty' 		=> 1,
		'depth' 			=> 10,
		'hierarchical' 		=>	1,
		'show_count'        => 0,
		'id' 				=>	$pts . '_id',
		'name' 				=> $tax_name,
		'selected' 			=> $_GET[$tax_name],
		'show_option_all'   => ' - All - ',
		);		
wp_dropdown_categories($args); 
} 

/////// GET DIRECTIONS LINK ////////
function get_directions($des_lat,$des_long,$address) {  			 	
  	global $lat, $long, $directions, $org_address, $unit;				
	$directions = '<a href="http://maps.google.com/maps?f=d&hl=en&doflg=' . $unit . '&geocode=&saddr=' .$org_address . '&daddr=' . str_replace(" ", "+", $address) . '&sll=' . $lat . "," . $long . '&sspn=' .  $des_lat . "," . $des_long . '&ie=UTF8&z=12" target="_blank">Get Directions</a>';
}

///// CONVERT ADDRESS TO COORDINATES //////
function ConvertToCoords($address) {	

	$ch = curl_init();	
    $rip_it = array( " " => "+", "," => "", "?" => "", "&" => "", "=" => "" , "#" => "");
    
    //// MAKE SURE ADDRES DOENST HAVE ANY CHARACTERS THAT GOOGLE CANNOT READ //
    $address = str_replace(array_keys($rip_it), array_values($rip_it), $address);
    
    //// GET THE XML FILE WITH RESUALTS
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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
	curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/2.0 (compatible; MSIE 3.02; Update a; AK; Windows 95)");
	curl_setopt($ci, CURLOPT_HTTPGET, true);
	curl_setopt($ci, CURLOPT_URL, 'http://maps.googleapis.com/maps/api/directions/xml?origin=' . $lat . "," . $long . '&destination=' .  $des_lat . "," . $des_long .  '&units=' . $_GET['wppl_units'] . '&sensor=true'  );
	curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60);
	$got_xml = curl_exec($ci);		
   	$xml_distance = new SimpleXMLElement($got_xml);
   	list($distance_between) = explode(",", $xml_distance->route->leg->distance->text);		
}

   

//// CREATE SHORTCODE TO DISPLAY FORM AND RESULTS ////
function wppl_shortcode($params) {
	global $post_types;
	$post_types = array();
	// get options from admin settings //
	$options = get_option('wppl_fields'); 
	$options_r = get_option('wppl_shortcode'); 
	echo $options_r['distance_values'];


	// shortcode attributed //	
    extract(shortcode_atts($options_r[$params[form]], $params)); 
    
    ob_start(); 
    (!empty($per_page)) ? $per_page = $per_page : $per_page = '5' ;
    
    // post counter //
   	$ptc = count($post_types);
    $pti = implode( " ",$post_types);
    $miles = explode(",", $distance_values);
    
	($_GET["wppl_post"] == $pti ) ? $pti_all = 'selected="selected"' : $pti_all = "";

	if($params[form_only] == 1) { 
		$widget = "widget-";
		$faction = get_bloginfo('wpurl') . "/" . $options['results_page'];
	} elseif ($params[form_only] == "y") { 
		$faction = get_bloginfo('wpurl') . "/" . $options['results_page'];
		$widget = "";
	} elseif ( (empty($params[form_only])) ){ 
		$faction = "";
		$widget = "";
	} 
	

	//// DISPLAY SEARCH FORM ////	
	echo	'<div class="wppl-' .$widget .'form-wrapper">';
	echo 	'<form class="wppl-' .$widget .'form" name="wppl_form" action="'. $faction .'" method="get">';
	echo		'<input type="hidden" name="pagen" value="0" />';
				// only if more then one post type entered display them in dropdown menu otherwise no dropdown needed //
				if($ptc>1) {
	echo			'<div class="wppl-post-type-wrapper">'; 
	echo    				'<label for="wppl-post-type">What are you looking for: </label>';				
							/* post types dropdown */
	echo 					'<select name="wppl_post" id="wppl-post-type">';
	echo 						'<option value="0"' . $pti_all . '> -- Search Site -- </option>';
								foreach ($post_types as $post_type) {
								if( $_GET["wppl_post"] == $post_type ) {$pti_post = 'selected="selected"';} else {$pti_post = "";}
	echo 							'<option value="'.$post_type .'"' .$pti_post .'>'. get_post_type_object($post_type)->labels->name . '</option>';			
								}
	echo 					'</select>';
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
	if($address_title != 1) {
	echo        	'<label for="wppl-address">Enter zip code, city or a full address: </label>';
	}
	echo        	'<input type="text" name="wppl_address" class="wppl-address" value="'. $_GET['wppl_address'] . '" size="35"';echo($address_title ==1) ? 'placeholder="Zipcode, city & state or full address..."' : ""; echo  '/>';
	echo		'<button type="button" value="'.$params[form].'" onClick="getLocation();formId=this.value;" class="wppl-locate-me-btn"><img src="'. plugins_url('images/locate-me-'.$options['locator_icon'].'.png', __FILE__) . '"></button> ';
	echo	'</div>'; // end address wrapper//
		
			// distance values dropdown	//
	echo	'<div class="wppl-distance-units-wrapper">';
	if ($display_radius == 1) {
	echo    	'<label for="wppl-distance">Within: </label>';
	echo		'<div class="wppl-distance-wrapper">';
	echo 				'<select name="wppl_distance" class="wppl-distance" id="wppl-distance">';
							foreach ($miles as $mile) {	
								if( $_GET["wppl_distance"] == $mile ) {$mile_s = 'selected="selected"';} else {$mile_s = "";}
	echo 						'<option value="'. $mile .'"' .$mile_s. '>'. $mile . '</option>';
							}
	echo 				'</select>'; 
						if($display_units != 1) {
	echo					'<span style="font-size: 14px;font-family: arial,sans-serif;">'; echo ($units_name == "imperial") ? " Mi" : " Km"; echo '</span>';
						}
	} else {
	echo		'<input type="hidden" name="wppl_distance" value="'. end($miles) . '" />';
	}
	echo		'</div>';
	
	if ($display_units == 1) {
	echo		'<div class="wppl-units-wrapper">';			
						// units dropdown //
						($_GET['wppl_units'] == 'metric') ? $unit_m = 'selected' : $unit_i = 'selected';
	echo 				'<select name="wppl_units" class="wppl-units">';
	echo 					'<option value="imperial"'.$unit_i. '>Miles</option>';
	echo					'<option value="metric"'.$unit_m. '>Kilometers</option>';
	echo 				'</select>';
	echo		'</div>';	
	} else {
	echo	'<input type="hidden" name="wppl_units" value="'. $units_name . '" />';
	}
	echo	'</div>'; // distance wrapper //
	
	echo	'<div class="wppl-submit">';
	echo    		'<input name="submit" type="submit" id="wppl-submit-'.$params[form].'" value="Submit" />';
	echo			'<input type="hidden" name="wppl_form" value="' .$params[form]. '" />';
	echo			'<input type="hidden" name="action" value="wppl_post" />';
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

///// SHORTCODE TO DISPLAY RESULTS  ////
function wppl_get_results($get_results) {
	if(!empty( $_GET['action'] ) &&  $_GET['action'] == "wppl_post") {		
 		
 		global $wpdb, $post, $distance_between,$post_types, $lat , $long, $org_address, $unit, $options,$per_page, $mikm_n, $posts_within, $total_rows, $from_page; 
 		
		$ptc = count(explode( " ",$_GET['wppl_post']));
		$options_r = get_option('wppl_shortcode'); 
		
		// shortcode attributed //	
   		extract(shortcode_atts($options_r[$_GET['wppl_form']], $get_results)); 
    	
    	(!empty($per_page)) ? $per_page = $per_page : $per_page = '5' ;
 		
 		// distance units //
		if ($_GET['wppl_units'] == "imperial") { 
			$mikm = 3959; 
			$mikm_n = "Mi"; 
			$unit = "ptm"; 
			
		} else {  
			$mikm = 6371; 
			$mikm_n = "Km"; 
			$unit = "ptk";
		}
	
    // prepare address for google //
   	$org_address = str_replace(" ", "+", $_GET['wppl_address']);
    // radius value //
 	$radius = $_GET['wppl_distance'];	
	$from_page = $_GET['pagen'] * $per_page;
		
	/////// CONVERT ORIGINAL ADDRESS TO LATITUDE/LONGITUDE ////	
	$do_it = ConvertToCoords($org_address);
	
	// green marker for "your location" //
	(!empty($org_address)) ? $your_loc = array("Your Location", $lat,$long) : $your_loc = "0";
		
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
		if($rr == 0) {$args = '';}	
	}
		
	$query = new WP_Query( array('post_type' => ($_GET['wppl_post'] != "0") ? $_GET['wppl_post'] : $post_types, 'post_status' => array('publish'), 'tax_query' => $args, 'fields' => 'ids', 'posts_per_page'=>-1 ));		
	$order_by = "wposts.post_title";
	
	if (!empty($org_address)) {
		$having = "HAVING distance <= $radius OR distance IS NULL";
		$order_by = "distance";
		$calculate = ", ROUND(" . $mikm . "* acos( cos( radians( $lat ) ) * cos( radians( wposts.lat ) ) * cos( radians( wposts.long ) - radians( $long ) ) + sin( radians( $lat ) ) * sin( radians( wposts.lat) ) ),1 )  AS distance";
		$showing = 'within ' . $radius . ' ' . $mikm_n . ' from ' . $_GET['wppl_address'];
	}
		
	$sql ="
		SELECT SQL_CALC_FOUND_ROWS wposts.post_id, wposts.lat, wposts.long, wposts.address, wposts.post_title, wposts.phone, wposts.fax, wposts.email, wposts.website {$calculate}
			FROM " . $wpdb->prefix . "places_locator  wposts	
    		WHERE 
    		(1 = 1)
    		AND wposts.post_id IN (" . implode( ',' , $query->posts) . ")	
			{$having} ORDER BY {$order_by} LIMIT " . $from_page . "," . $per_page  ;
	
	//print_r($sql);
	$sql = $wpdb->prepare($sql,$values);	

	$posts_within = $wpdb->get_results($sql);
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
	echo	'<div id="wppl-output-wrapper-' . $result_style .'">';
	
	echo	'<div class="wppl-pagination-wrapper">';
	echo		'<h2 class="wppl-h2"> Showing ' . count($posts_within) . ' out of ' . $total_rows[0] . ' results ' . $showing . '</h2>';
				pagination_links();
	echo	'</div>'; // pagination wrapper//
	
	///// DISPLAY MAP  /////		
					if( ($results_type == "both") || ($results_type == "map") ) {
	echo			'<div class="wppl-map-wrapper-' . $result_style .'">';
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
	echo			'</div>';// map wrapper //
	wp_enqueue_script( 'wppl-map', true);
					};	
	echo	'<div class="clear"></div>';
	
	//////  DISPLAY RESULTS ///////
	
	if( ($results_type == "both") || ($results_type == "posts") ) {
	echo	'<div class="wppl-results-wrapper-' . $result_style .'">';
			if( file_exists( STYLESHEETPATH. '/wppl_results.php' ) ) {
    			include(STYLESHEETPATH . '/wppl_results.php');
			} else {	
				include_once 'wppl_results.php';	
			}
	echo	'</div>'; // results wrapper //
	
	echo	'<div class="wppl-pagination-wrapper">';
	echo		'<h2 class="wppl-h2"> Showing ' . count($posts_within) . ' out of ' . $total_rows[0] . ' results ' . $showing . '</h2>';
				pagination_links();
	echo	'</div>'; // pagination wrapper//
	
	echo	'</div>'; // output wrapper //
    // DONE - GOOD JOB //
    }
    // IF NO RESULTS //
    } else {
    	echo '<h2 class="wppl-h2">'.$options['no_results'].'</h2>';
   	} // end function //
   	
    	}    
	}
add_shortcode( 'wpplresults' , 'wppl_get_results' );
 			
?>
