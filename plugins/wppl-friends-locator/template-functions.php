<?php

/////// TITLE ///////////
function wppl_bp_title($single_user) {
	echo '<div class="wppl-item-title">';
	echo	'<a href="' . $single_user->user_permalink . '">'. $single_user->full_name . '</a>';
	echo '</div>';
	}
	
///////// THUMBNAIL ///////////////
function wppl_bp_thumbnail($wppl, $single_user) {
	if( !isset($wppl['show_thumb']) || $wppl['show_thumb'] != 1 ) return;
	echo '<div class="wppl-item-avatar">';
	echo "<style> .wppl-item-avatar img { width:$wppl[thumb_width]px !important; height:$wppl[thumb_height]px !important; } </style>";
		echo '<a href="' . $single_user->user_permalink . '">'. $single_user->avatar . '</a>';
	echo '</div>';
}

if ( !function_exists('wppl_bp_address') ) {
	function wppl_bp_address($wppl, $single_user) {
		echo '<div class="wppl-bp-address">';
		echo 	'<span>Address: </span>';
		echo $single_user->address . ' ';
		echo '</div>';
	}
}

if ( !function_exists('wppl_bp_user_profile_fields') ) {
	function wppl_bp_user_profile_fields() {
		return;
	}
}

////////// ACTION BUTTONS //////
function wppl_bp_action_btn($single_user) {
	global $bp;
	$logged_in = ( isset($bp->loggedin_user->id) ) ? $bp->loggedin_user->id : '';
	$member_id = ( isset($single_user->member_id) ) ? $single_user->member_id : '';
	
	if( isset($bp->active_components['friends']) &&  $bp->active_components['friends'] == 1) {
		echo '<div class="wppl-action">';
			$friend_status = friends_check_friendship_status( $logged_in , $member_id); 
			echo bp_get_add_friend_button( $member_id, $friend_status ); 
		echo '</div>';
		}
   	}
   	
////////// BY RADIUS ///////////
function wppl_bp_by_radius ($returned_address, $single_user, $wppl) {
		if ($returned_address['lat'] && $returned_address['long']) echo '<div class="bp-radius-wrapper">' . $single_user->distance .' ' . $wppl['units_array']['name'] . '</div>'; 
	}
		
//////// GET DIRECTIONS ////////////

if ( function_exists('wppl_bp_directions') ) {
	return;
} else {
	function wppl_bp_directions($wppl, $single_user) {
		if ( isset($wppl['get_directions']) && $wppl['get_directions'] == 1 ) { 	
			echo '<div class="bp-get-directions">'; 
			echo 	'<span><a href="http://maps.google.com/maps?f=d&hl=en&doflg=' . $wppl['units_array']['map_units'] . '&geocode=&saddr=' .$wppl['org_address'] . '&daddr=' . str_replace(" ", "+", $single_user->address) . '&ie=UTF8&z=12" target="_blank">Get Directions</a></span>';
			echo '</div>';
		}
	}
}
	
function wppl_bp_last_active($user_data) {
	echo '<div class="wppl-item-meta">';
		echo '<span class="activity">';
			echo $user_data->last_active; 
		echo '</span>';
	echo '</div>';
	}

//////// DRIVING DISTANCE ///////////
function bp_distance_between($wppl, $single_user, $returned_address, $pc) {
	if ( !isset($wppl['by_driving']) || $wppl['by_driving'] != 1 ) return;
	echo '<script>';
	echo		'latitude= '.json_encode($returned_address['lat']),';'; 
    echo		'longitude= '.json_encode($returned_address['long']),';'; 
    echo		'des_lat= '.json_encode($single_user->lat),';'; 
    echo		'des_long= '.json_encode($single_user->long),';';
    echo		'unit= '.json_encode($wppl['units_array']['name']),';';
    echo		'pc= '.json_encode($pc),';'; 
    echo '</script>';
	?>
	<script>    	
    	var directionsDisplay;
		var directionsService = new google.maps.DirectionsService();	
		var directionsDisplay;
        directionsDisplay = new google.maps.DirectionsRenderer();	
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
      			jQuery('#bp-driving-distance-<?php echo $pc; ?>').text('Driving: ' + totalDistance)
      			//directionsDisplay.setPanel(document.getElementById("wppl-directions-panel-<?php echo $pc; ?>"));	
    		}
 		 });	 
 		 //directionsDisplay.setMap(map);
 		 //directionsDisplay.setPanel(document.getElementById("wppl-directions-panel-" + (i + 1) ));    
	</script>
	<?php
	//echo 	'<div id="wppl-directions-panel-'. $pc .'"></div>';
	echo 	'<div class="bp-driving-distance" id="bp-driving-distance-' .$pc . '"></div>';
}
