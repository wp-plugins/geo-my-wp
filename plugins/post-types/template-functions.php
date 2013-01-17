<?php
////// TITLE //////
function wppl_title($wppl, $post, $single_result) {
	global $pc;
	?>
	<div class="wppl-title-holder">
		<h3 class="wppl-h3">
			<a href="<?php echo $single_result->post_permalink . '?address='. $wppl['org_address']; ?>"><?php echo $post->post_title; ?></a>
		</h3>
	</div>
<?php }
	
///////// THUMBNAIL ///////////////
function wppl_thumbnail($wppl, $post) {
	if( isset($wppl['show_thumb']) ) {
		echo '<div class="wppl-thumb">';
			$thumbnail_id=get_the_post_thumbnail($post->ID);
			preg_match ('/src="(.*)" class/',$thumbnail_id,$link);
			echo '<a href="'.$link[1] .'" class="thickbox">' . get_the_post_thumbnail($post->ID,array($wppl['thumb_width'],$wppl['thumb_height'])) . '</a>';	
		echo '</div>';
	}	
}

////// DAYS AND HOURS ///////////////	
function wppl_days_hours($post) {
	echo '<div class="wppl-days-hours-wrapper">';
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
		echo '<span class="single-day">Sorry, days & Hours not avaiable</span>'; 
	} 				
	echo '</div>';
}

////////// BY RADIUS ///////////
function wppl_by_radius($wppl, $single_result, $returned_address ) {
	$units_name = (isset($wppl['units_array']['name'])) ? $wppl['units_array']['name'] : '';
	if ( isset($returned_address['lat']) && !empty($returned_address['lat']) ) echo '<span class="radius-dis">'. $single_result->distance . " " . $units_name . '</span>';
}

///////// TAXES WRAPPER //////////// 
function wppl_taxonomies($wppl, $post) {
	if ( isset($wppl['custom_taxes']) ) {
		echo '<div class="wppl-taxes-wrapper">';
    		if( isset($wppl['taxonomies']) ) {
    			foreach ($wppl['taxonomies'] as $tax) {
    				$terms = get_the_terms($post->ID, $tax);
    		
    				if ( $terms && ! is_wp_error( $terms ) ) :
						$terms_o = array();

						foreach ( $terms as $term ) {
							$terms_o[] = $term->name;
						}
						
						$terms_o = join( ", ", $terms_o );
						$the_tax = get_taxonomy($tax);
						echo '<div class="wppl-taxes"><span>'.$the_tax->labels->singular_name . ':</span> ' . $terms_o.'</div>';
					endif;	 		
				}
			}
		echo '</div>';
	}
}
		
//////// ADDITIONAL INFORMAION  //////////
function wppl_additional_info($wppl, $post) { ?>
	<?php if( isset($wppl['additional_info']) ); { ?>
		<div class="wppl-additional-info">	
   			<?php if( isset($wppl['additional_info']['phone']) ) { ?><div class="wppl-phone"><span>Phone: </span><?php echo (get_post_meta($post->ID,'_wppl_phone',true)) ? get_post_meta($post->ID,'_wppl_phone',true) : 'N/A'; ?></div><?php } ?>
    		<?php if( isset($wppl['additional_info']['fax']) ) { ?><div class="wppl-fax"><span>Fax: </span><?php echo (get_post_meta($post->ID,'_wppl_fax',true)) ? get_post_meta($post->ID,'_wppl_fax',true) : 'N/A'; ?></div><?php } ?>
    		<?php if( isset($wppl['additional_info']['email'])) { ?><div class="wppl-email"><span>Email: </span><?php echo (get_post_meta($post->ID,'_wppl_email',true)) ? get_post_meta($post->ID,'_wppl_email',true) : 'N/A'; ?></div><?php } ?>
    		<?php if( isset($wppl['additional_info']['website']) ) { ?><div class="wppl-website"><span>Website: </span><?php if (get_post_meta($post->ID,'_wppl_website',true)) { ?><a href="http://<?php echo get_post_meta($post->ID,_wppl_website,true); ?>" target="_blank"><?php echo get_post_meta($post->ID,'_wppl_website',true); } else { echo "N/A"; }?></a></div><?php } ?>
		</div> <!-- additional info -->
	<?php } ?>
<?php } ?>
<?php
///////// EXCERPT //////////////
function wppl_excerpt($wppl, $post) {
	if ( isset($wppl['show_excerpt']) ) { 
    	echo '<div class="wppl-excerpt">';
			echo wp_trim_words( $post->post_content,$wppl['words_excerpt']);
		echo '</div>';
	}
}

//////// GET DIRECTIONS ////////////
function wppl_directions($wppl, $single_result) {
	$single_address = (isset($single_result->address)) ? $single_result->address : '';
	$org_address = (isset($wppl['org_address'])) ? $wppl['org_address'] : '';
	$map_units = (isset($wppl['units_array']['map_units'])) ? $wppl['units_array']['map_units'] : '';
	
	if ($wppl['get_directions']) { 			 	
    	echo '<div class="wppl-get-directions">'; 
    	echo 	'<span><a href="http://maps.google.com/maps?f=d&hl=en&doflg=' . $map_units . '&geocode=&saddr=' . $org_address . '&daddr=' . str_replace(" ", "+", $single_address) . '&ie=UTF8&z=12" target="_blank">Get Directions</a></span>';
		echo '</div>';
	}
}

/////// ADDRESS ///////////
function wppl_address($post) {
	echo '<div class="wppl-address">';
 	echo 	get_post_meta($post->ID,'_wppl_address',true); 
	echo '</div>';
	} 
	
//////// DRIVING DISTANCE ///////////

/////// CALCULATE DRIVING DISTANCE ////////
/* function distance_between($des_lat,$des_long) {  			 	
  	global $distance_between, $lat, $long, $directions, $org_address, $unit, $wppl_units;
  	
   $ci = curl_init();
   	curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/2.0 (compatible; MSIE 3.02; Update a; AK; Windows 95)");
	curl_setopt($ci, CURLOPT_HTTPGET, true);
	curl_setopt($ci, CURLOPT_URL, 'http://maps.googleapis.com/maps/api/directions/xml?origin=' . $lat . "," . $long . '&destination=' .  $des_lat . "," . $des_long .  '&units=' . $wppl_units . '&sensor=true'  );
	curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60);
	$got_xml = curl_exec($ci);		
   	$xml_distance = new SimpleXMLElement($got_xml);
   	list($distance_between) = explode(",", $xml_distance->route->leg->distance->text);		
}
function driving_distance($by_driving, $single_result) {
	if ($by_driving == 1) { 
		distance_between($single_result->lat, $single_result->long); ?>
    					<span class="drive-dis">Driving: <?php echo $distance_between; ?></span> 
    				<?php	} ?>
    				
*/
	
function wppl_driving_distance($wppl, $single_result, $returned_address) {
	global $pc;
	if ( isset($wppl['by_driving']) && $wppl['by_driving'] == 1 ) { 
	echo '<script>';
	echo		'latitude= '.json_encode($returned_address['lat']),';'; 
    echo		'longitude= '.json_encode($returned_address['long']),';'; 
    echo		'des_lat= '.json_encode($single_result->lat),';'; 
    echo		'des_long= '.json_encode($single_result->long),';';
    echo		'unit= '.json_encode($wppl['units_array']['name']),';';
    echo		'pc= '.json_encode($pc),';'; 
    echo '</script>';
	?>
	<script>    	
    	var directionsDisplay;
		var directionsService = new google.maps.DirectionsService();	
        var directionsDisplay = new google.maps.DirectionsRenderer();	
  		var start = new google.maps.LatLng(latitude,longitude);
  		var end = new google.maps.LatLng(des_lat,des_long);
  		var request = {
    		origin:start,
    		destination:end,
    		travelMode: google.maps.TravelMode.DRIVING
 		};
 		
  		directionsService.route(request, function(result, status) {
    		if (status == google.maps.DirectionsStatus.OK) {
      			directionsDisplay.setDirections(result);
      			//alert((result.routes[0].legs[0].distance.value * 0.001));
      			if (unit == 'Mi') {
      				totalDistance = (Math.round(result.routes[0].legs[0].distance.value * 0.000621371192 * 10) / 10)+' Mi'
      			} else { 
      				totalDistance = (Math.round(result.routes[0].legs[0].distance.value * 0.01) / 10)+' Km'
      			}	
      			jQuery('#wppl-driving-distance-<?php echo $pc; ?>').text('Driving: ' + totalDistance)
      			//directionsDisplay.setPanel(document.getElementById("wppl-directions-panel-<?php echo $pc; ?>"));	
    		}
 		 });	 
 		 //directionsDisplay.setMap(map);
 		 //directionsDisplay.setPanel(document.getElementById("wppl-directions-panel-" + (i + 1) ));    
	</script>
	<?php
	//echo 	'<div id="wppl-directions-panel-'. $pc .'"></div>';
	echo 	'<div class="wppl-driving-distance" id="wppl-driving-distance-' .$pc . '"></div>';
	}
}

   
			
    