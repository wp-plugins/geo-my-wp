<?php
//////////////////////////////////////////////////////////////////////////////////////////
/// SOME OF THE CODE IN THIS PAGE WAS INSPIRED BY THE CODE WRITTEN BY ANDREA TARANTI ////
/// 				THE CREATOR OF BP PROFILE SEARCH -- THANK YOU 					////
///////////////////////////////////////////////////////////////////////////////////////

///// MEMBER'S LOCATION MAP SHORTCODE ////////
function wppl_member_location($member) {
	$member_address = array();
	$show_address = array();
			
	extract(shortcode_atts(array(
		'directions' 	=> '1',
		'map_height' 	=> '250',
		'map_width' 	=> '250',
		'map_type' 		=> 'ROADMAP',
		'zoom_level' 	=> 13,
		'address' 		=> 1,
		'no_location' 	=> 0	
	),$member));
	
    global $bp, $wpdb, $mmc, $get_loc;
    if(!isset($mmc)) $mmc = 0;
    $mmc++;
   
    $member_info = $wpdb->get_results( 
    					$wpdb->prepare(
    						"SELECT * FROM wppl_friends_locator 
    						WHERE member_id = %s", $bp->displayed_user->id
    		 			), 
    		 	   ARRAY_A );
    		
	if ( isset($member_info) && $member_info !=0) {
		// get the address to be displayed
		if ( is_multisite() ) {
			$wppl_site_options = get_site_option('wppl_site_options'); 
			$member_address = $wppl_site_options['friends']['single_location_address_fields'];
		} else {
			$wppl_options = get_option('wppl_fields'); 
			$member_address = $wppl_options['friends']['single_location_address_fields'];
		}
		
		if ( isset($member_address) && !empty($member_address) ) {
			foreach ($member_address as $field) {
				$show_address[] = $member_info[0][$field];
			}
			$show_address = implode(' ' , $show_address);
		} else {
			$show_address = $member_info[0]['address'];
		}
		
   		$get_loc[$mmc] = array(
    		'map_icon'		=> $member_info[0]['map_icon'],
    		'map_type'		=> $map_type,
    		'zoom_level' 	=> $zoom_level,
    		'map_height' 	=> $map_height,
    		'map_width'		=> $map_width,
    		'address'		=> $show_address,
    	);
    	
    	$single_member_args = array(
			'singleMember'	=> $get_loc,
			'memberMapId'	=> $mmc
		);
		
    	$member_map .='';	
    	$member_map .=	'<div class="wppl-single-member-wrapper">';
    	if($address == 1) {
    		$member_map .=		'<div class="wppl-single-member-address"><span>Address: </span>' .$get_loc[$mmc]['address'].'</div>';
		}
		$member_map .=		'<div class="wppl-single-member-map-wrapper" style="position:relative">';
		$member_map .=			'<div id="member-map-'.$mmc.'" style="width:'.$get_loc[$mmc]['map_width'].'px; height:'.$get_loc[$mmc]['map_height'].'px;"></div>';
		$member_map .=			'<img class="map-loader" src="'.GMW_URL. '/images/map-loader.gif" style="position:absolute;top:45%;left:25%;width:50%"/>';
		$member_map .= 			'<div class="wppl-single-member-direction-wrapper" style="width:'.($get_loc[$mmc]['map_width'] - 10).'px">';
    	$member_map .= 				'<div class="wppl-single-member-direction" style="display:none;">';
		$member_map .=					'<form action="http://maps.google.com/maps" method="get" target="_blank">';
		$member_map .= 						'<input type="text" name="saddr" />';
		$member_map .= 						'<input type="hidden" name="daddr" value="'. $get_loc[$mmc]['address'].'" /><br />';
		$member_map .= 						'<input type="submit" class="button" value="GO" />';
		$member_map .= 					'</form>'; 
		$member_map .= 				'</div>';
		if ($directions == 1) {
    		$member_map .= 				'<span><a href="#" class="show-directions">Get Directions</a></span>';
    	}
    	$member_map .= 			'</div>';
    	$member_map .=		'</div>';// map wrapper //
    	$member_map .=	'</div>';// map wrapper //
    
    	echo $member_map;
    	wp_enqueue_script('wppl-member-map', true);
    	wp_localize_script('wppl-member-map', 'sMArgs', $single_member_args);
	} else {
		if (isset($no_location) && $noLocation == 1) echo $bp->displayed_user->fullname . ' has not added a location yet.';
	}
}
add_shortcode( 'wppl_member_location' , 'wppl_member_location' );

//// paginations for buddypress  /////////////
function bp_pagination_links($pages, $per_page) {
	$page_n = ( isset($_GET['pagen']) ) ? $_GET['pagen'] : false ;
	if ($pages > 1) {
		if ($page_n > 0) {		
			echo '<a href="' .add_query_arg('pagen', ($page_n - 1)) .'" class="prev page-numbers" onclick="document.wppl_form.submit();">&#171;</a>';
		} 			
		for($i=0; $i< $pages; $i++): 
			$num = $i + 1;
    		echo '<a href="' .add_query_arg('pagen',$i) .'" '; echo ( $page_n+1 == $num ) ? 'class="page-numbers current"' : 'class="page-numbers"'; echo ' onclick="document.wppl_form.submit();">' . $num .  '</a>';
		endfor; 
		if ($page_n < ($pages -1)) {
			echo '<a href="' .add_query_arg('pagen', ($page_n + 1)) .'" class="next page-numbers" onclick="document.wppl_form.submit();">&#187;</a>';
		}			
	}
}	

///// fields dropdown for search form /////////
function bp_fields_dropdown($wppl) {
	 //// check buddypress fields ////
    $total_fields = ( isset($wppl['profile_fields']) ) ? $wppl['profile_fields'] : array();
	if($wppl['profile_fields_date'] ) array_unshift($total_fields, $wppl['profile_fields_date']);
  	
	echo '<div class="wppl-category-wrapper">';
		
	foreach ($total_fields as $field_id) {
		
		$get_field = (isset($_GET[$field_id])) ? $_GET[$field_id] : '';
		$get_field_to = (isset($_GET[$field_id. '_to'])) ? $_GET[$field_id. '_to'] : '';
		
		$field_data = new BP_XProfile_Field ($field_id);
		$children = $field_data->get_children ();
	
		switch ($field_data->type) {
			case 'datebox':
				echo "<div class='date'>";
					echo '<span class="label">Age Range (min - max)</span>';
					echo '<input size="3" type="text" name="'. $field_id.'" value="' . $get_field . '" placeholder="Min" />';
					echo '&nbsp;-&nbsp;';
					echo '<input size="3" type="text" name="' . $field_id . '_to" value="' . $get_field_to . '" placeholder="Max" />';
				echo '</div>';
			break;
		
			case 'multiselectbox':
			case 'selectbox':
			case 'radio':
			case 'checkbox': 
				echo '<div class="checkbox">';
					echo '<span class="label">' . $field_data->name. '</span>';
					$tt = array();
					if($get_field) { 
						$tt = $get_field;
					}
					//echo '<div class="wppl-checkboxes-wrapper">';
					foreach ($children as $child) {	
						$child->name = trim ($child->name);
						$checked =  (in_array ($child->name, $tt ) )? "checked='checked'": ""; 
						echo '<label><input ' .$checked . ' type="checkbox" name="'. $field_id. '[]" value="'.$child->name.'" />'.$child->name.'</label>';
					}
					//echo '</div>';
				echo '</div>';
			break;
		} // switch
		
	}
	echo '</div>';
}

///////// query profile fields //////////
function wppl_bp_query_fields($total_fields) {
		global $bp, $wpdb;
		$empty_fields = array();
		$userids = false;
		
		foreach ($total_fields as $field_id) {
			
			$value = (isset($_GET[$field_id])) ? $_GET[$field_id] : '';
			$to = (isset($_GET[$field_id. '_to'])) ?  $_GET[$field_id. '_to'] : '';
			
			if ($value) array_push($empty_fields, $_GET[$field_id]);
			
			if($value || $to) {
			
				$field_data = new BP_XProfile_Field ($field_id);
				
				switch ($field_data->type) {
					
					case 'selectbox':
					case 'multiselectbox':
					case 'checkbox':
					case 'radio':
						$sql = "SELECT user_id from {$bp->profile->table_name_data}";
						$sql .= " WHERE field_id = $field_id ";
						$like = array ();
						foreach ($value as $curvalue)
							$like[] = "value = '$curvalue' OR value LIKE '%\"$curvalue\"%' ";
						$sql .= ' AND ('. implode (' OR ', $like). ')';	
					break;

					case 'datebox':
					
						$value = (!$value) ? '1': $value;
						$to = (!$to) ? '200' : $to;
						if ($to < $value)  $to = $value;
		
						$time = time ();
						$day = date ("j", $time);
						$month = date ("n", $time);
						$year = date ("Y", $time);
						$ymin = $year - $to - 1;
						$ymax = $year - $value;

						$sql = "SELECT user_id from {$bp->profile->table_name_data}";
						$sql .= " WHERE field_id = $field_id AND value > '$ymin-$month-$day' AND value <= '$ymax-$month-$day'";
					break;
				}//switch //	
		
				$results = $wpdb->get_col ($sql, 0);
				
				if (!is_array($userids)) $userids = $results; else $userids = array_intersect($userids, $results); 
							
				echo '<br />';
			} // if value //
		} // for eaech //
	/* build SQL filter from profile fields results - member ids array */
	if (isset($userids) && !empty($userids) ) return $userids;
	/* if no results and profile fields are not empty - buba is going to stop the function */ 
	else if (!empty($empty_fields)) return "buba";	
} 

/////// query buddypress results /////////
function wppl_bp_query_results($params) {
	global $wppl, $bp;
	$wppl_options = get_option('wppl_fields');
	$get_results = false;
	$calculate = false;
	$showing = false;
	$having = false;
	$order_by = false;
	
	/* get all existing wp users. in the next sql query we will make sure ///
	//// the user exists before displaying results. to prevent not active users from being displayed */
	global $total_wp_users, $wpdb;
	$mem_query = $all_wp_users = $total_wp_users = $wpdb->get_col("SELECT $wpdb->users.ID FROM $wpdb->users");
	
	/* if the search form submitted */
	if(!empty( $_GET['action'] ) &&  $_GET['action'] == "wppl_post") {
	
	/* check if session exists and url is the same */
	if(!isset($_SESSION['wppl_search_members']) || !isset($_SESSION['wppl_url_members']) || $_SESSION['wppl_url_members'] != remove_query_arg('pagen') ) $session_on = 0; else $session_on = 1;	
		
		/* if the search form is a widget or different page */
		if (empty($params['form'])) {
			$options_r = get_option('wppl_shortcode'); 
			$wppl = $options_r[$_GET['wppl_form']];
			ob_start();	
  	 	}
   	
   		extract(shortcode_atts($wppl, $get_results));
   		
   		/* when we need to display profile fields in the search form */
   		$total_fields = ( isset($profile_fields) ) ? $profile_fields : array();
   		if( isset($profile_fields_date) && !empty($profile_fields_date) ) array_unshift($total_fields, $profile_fields_date);
   		$userids = wppl_bp_query_fields($total_fields);
   		
   		/* when we have date field to display in search form - shift it to the beginning of the array */
		if($profile_fields_date ) array_unshift($total_fields, $profile_fields_date);
		
		/* run the profile fields query. if not results returned, stop the function and display no results message */
   		if (isset($total_fields) && !empty($total_fields) ) if ( $userids == "buba") {
   			wppl_bp_no_members();
   			if (empty($params['form'])) {
   				$output_results=ob_get_contents();
				ob_end_clean();
				return $output_results;
			} else return;
   		}
   		
   		/* when we need to display profile fields in results */
		if ($results_profile_fields || $results_profile_fields_date ) {
			$total_results_fields = ($results_profile_fields ) ? $results_profile_fields : array(); 		
			if($results_profile_fields_date ) array_unshift($total_results_fields, $results_profile_fields_date);
		}
   	   		
		echo '<link rel="stylesheet" type="text/css" media="all" href="' . plugins_url('themes/'.$results_template.'/css/style.css', (__FILE__))  .'">'; 
   		 
   		 /* array contains units params */ 			
		if ($_GET['wppl_units'] == "imperial") 
			$wppl['units_array'] = array('radius' => 3959, 'name' => "Mi", 'map_units' => "ptm", 'units'	=> 'imperial');
		else  
			$wppl['units_array'] = array('radius' => 6371, 'name' => "Km", 'map_units' => 'ptk', 'units' => "metric");
		
 		$radius = $_GET['wppl_distance'];	
		$from_page = $_GET['pagen'];
	
		$wppl['org_address'] = str_replace(" ", "+", $_GET['wppl_address']);

		/////// CONVERT ORIGINAL ADDRESS TO LATITUDE/LONGITUDE ////	
		$returned_address = ConvertToCoords($wppl['org_address']);
		
		$lat  = $returned_address['lat'];
		$long = $returned_address['long'];
			
		/* set the "your location" icon */
		(!empty($wppl['org_address'])) ? $your_loc = array("Your Location", $lat,$long) : $your_loc = "0";
	
		$order_by = "members.member_id";
		
		/* if address field is not empty */ 
		if (!empty($wppl['org_address'])) {
			$calculate 	= ", ROUND(".$wppl['units_array']['radius']. " * acos( cos( radians( '%s' ) ) * cos( radians( members.lat ) ) * cos( radians( members.long ) - radians( '%s'  ) ) + sin( radians( '%s' ) ) * sin( radians( members.lat) ) ),1 )  AS distance";
			$having 	= "HAVING distance <= '%d' OR distance IS NULL";
			$order_by	= "distance";
			$showing 	= 'within ' . $radius . ' ' . $wppl['units_array']['name'] . ' from ' . $_GET['wppl_address'];
		}	
		$get_them = 1;	
	/* when user first visit a search page we look for lat/long and display results based on that */
	} elseif ( ($_COOKIE['wppl_lat']) && ($_COOKIE['wppl_long']) && ($params['form']) && ($wppl_options['auto_search'] == 1) ) {
	
	/* check if session exists and url is the same */
		$session_on =0;
		
		global $wppl;
		$get_results = false;
		
		extract(shortcode_atts($wppl, $get_results));
		
		$wppl['org_address'] = urldecode($_COOKIE['wppl_city']) . ' ' .  urldecode($_COOKIE['wppl_state']) . ' ' .  urldecode($_COOKIE['wppl_zipcode']) . ' ' .  urldecode($_COOKIE['wppl_country']);
   		
   		if ($results_profile_fields || $results_profile_fields_date) {
			$total_results_fields = ($results_profile_fields ) ? $results_profile_fields : array(); 		
			if($results_profile_fields_date ) array_unshift($total_results_fields, $results_profile_fields_date);
		} 		
   		
   		echo '<link rel="stylesheet" type="text/css" media="all" href="' . plugins_url('themes/'.$results_template.'/css/style.css', (__FILE__))  .'">'; 
    	
		if ($wppl_options['auto_units'] == "imperial") 
			$wppl['units_array'] = array('radius' => 3959, 'name' => "Mi", 'map_units' => "ptm", 'units'	=> 'imperial');
		else 
			$wppl['units_array'] = array('radius' => 6371, 'name' => "Km", 'map_units' => 'ptk', 'units' => "metric");
		
		$radius 	= $wppl_options['auto_radius'];
		$from_page  = (isset($_GET['pagen'])) ? $_GET['pagen'] : 0;
		$lat 		= urldecode($_COOKIE['wppl_lat']);
		$long 		= urldecode($_COOKIE['wppl_long']);
		$your_loc 	= array("Your Location", $lat,$long);
		$having 	= "HAVING distance <= '%d' OR distance IS NULL";
		$order_by 	= "distance";
		$calculate 	= ", ROUND(" . $wppl['units_array']['radius'] . "* acos( cos( radians( '%s' ) ) * cos( radians( members.lat ) ) * cos( radians( members.long ) - radians( '%s' ) ) + sin( radians( '%s' ) ) * sin( radians( members.lat) ) ),1 )  AS distance";
		$showing 	= 'within ' . $radius . ' ' . $wppl['units_array']['name'] . ' Near your location';

		$get_them = 1;			
	}
	/* query and get the results */
	
	if( isset($get_them) && !empty($get_them) ) {
		include_once GMW_FL_PATH . 'template-functions.php';
		
		/* check for results in session */
		if($session_on == 0) {
			
			//add users from profile fields query results
			if( isset($userids) && !empty($userids) ) $mem_query = $all_wp_users = array_intersect($userids, $all_wp_users);
			
			if (isset($lat) && !empty($lat) ) $mem_query = array_merge(array($lat, $long, $lat), $all_wp_users);
			array_push($mem_query,$radius);
			
			$total_ids = $wpdb->get_results( 
				$wpdb->prepare("SELECT members.* {$calculate} FROM wppl_friends_locator members 
					WHERE (1=1)
					{$and_users}
					AND members.member_id IN (".str_repeat("%d,", count($all_wp_users)-1) . "%d) 
					{$having} ORDER BY {$order_by}",$mem_query
				)); 
    						
			/* enter results into session var */
			$_SESSION['wppl_search_members'] = $total_ids;
			/* enter url into session - need to check if user changed the search query */
			$_SESSION['wppl_url_members'] = remove_query_arg('pagen');
			//echo "0";	
	
		} elseif ($session_on == 1) {
			//echo "1";
			$total_ids = $_SESSION['wppl_search_members'];
		}
		
		if ( isset($total_ids) && !empty($total_ids) ) { 
			/* divide results into pages */
			$user_ids = array_chunk($total_ids,$per_page);
			/// count number of pages ///
			$pages = count($user_ids);
			/// users to display ///
			$user_ids = $user_ids[$from_page];
		
			if( ($results_type == "both") || ($results_type == "map") ) $map_yes = 1; ?>
	
			<div id="wppl-output-wrapper" style="width:<?php echo ($main_wrapper_width) ? $main_wrapper_width.'px' : '100%'; ?>">
			
				<div id="pag-top" class="pagination">
					<?php if ( isset($map_yes) && $map_yes == 1) echo '<div id="show-hide-btn-wrapper"><a href="#" class="map-show-hide-btn"><img src="'.GMW_URL .'/images/show-map-icon.png" /></a></div>'; ?>
					<div class="pag-count" id="member-dir-count-top">
						Viewing member <?php echo (($from_page * $per_page) +1) ; ?> to <?php echo ($from_page * $per_page) + count($user_ids); ?> (of <?php echo count($total_ids); ?> active members) <?php echo $showing; ?>
					</div>	
					<div class="pagination-links" id="member-dir-pag-bottom">
						<?php bp_pagination_links($pages, $per_page); ?>
					</div>
				</div>
				
				<?php 
		
				///// DISPLAY MAP  /////		
				if ($map_yes == 1) {
					echo '<div id="wppl-hide-map" style="float:left;">';
					echo	'<div class="wppl-map-wrapper" style="position:relative">';
					echo		'<div id="wppl-members-map" style="width:'; echo (!empty($map_width)) ? $map_width : 500 ; echo 'px; height:'; echo (!empty($map_height)) ? $map_height : 500; echo 'px;"></div>';
					echo		'<img class="map-loader" src="'.plugins_url('geo-my-wp/images/map-loader.gif', basename(__file__) ).'" style="position:absolute;top:45%;left:33%;"/>';
					echo	'</div>';// map wrapper //
					echo '</div>'; // show hide map //				
				};	
				echo	'<div class="clear"></div>';	
				
				/* display results */
				if( ($results_type == "both") || ($results_type == "posts") ) {
					$pc = ($from_page * $per_page) + 1;	
					/* include the results.php and themplate-functions pages */
					include_once GMW_FL_PATH . 'themes/default/results.php'; ?>
				
					<div id="pag-bottom" class="pagination">
						<div class="pag-count" id="member-dir-count-bottom"> 
							Viewing member <?php echo (($from_page * $per_page) +1) ; ?> to <?php echo ($from_page * $per_page) + count($user_ids); ?> (of <?php echo count($total_ids); ?> active members) <?php echo $showing; ?>
						</div>	
						<div class="pagination-links" id="member-dir-pag-bottom">
							<?php bp_pagination_links($pages, $per_page); ?>
						</div>
					</div>
				
				<?php } ?>
				<div class="clear"></div>
				
				<?php 
				/* pass results to javascript to use to display the map and markers */		
				if ( isset($map_yes) && $map_yes == 1) {
					$members_map_args = array(
						'avatar'			=> $avatars,
						'locations'			=> $user_ids,
						'your_location'		=> $your_loc,
						'page'				=> $from_page * $per_page,
						'mapType'			=> $map_type,
						'additionalInfo'	=> $additional_info,
						'zoomLevel'			=> $zoom_level,
						'autoZoom'			=> $auto_zoom,
						'friendsSearch'		=> 1,
						'perMemberIcon'		=> $per_member_icon,
						'mainIconsFolder'	=> GMW_URL . '/map-icons/',
						'bpIconsFolder'		=> GMW_FL_URL . 'map-icons/',
						'mainIcon'			=> $friends_map_icon,
						'ylIcon'			=> $your_location_icon,
						'units'				=> $wppl['units_array'],
						'mapControls'		=> $map_controls
					);
					wp_enqueue_script( 'wppl-friends-map', true);
					wp_localize_script( 'wppl-friends-map', 'fMapArgs', $members_map_args);
				};	
			echo '</div>';
		} else {
			echo '<div id="message" class="info" style="float:left; width:100%;">';
				echo '<p>'; echo _e( "Sorry, no members were found.", 'buddypress' );echo ($wppl_options['wider_search']) ? '  <a href="'. add_query_arg('wppl_distance', $wppl_options['wider_search_value']). '" onclick="document.wppl_form.submit();">click here</a> to search within a greater range or <a href="'. add_query_arg('wppl_address', ''). '" onclick="document.wppl_form.submit();">click here</a> to see all results. </p>' : ''; echo '';
			echo '</div>';
		}
		/* end the function */
		if (empty($params['form'])) {
   			$output_results=ob_get_contents();
			ob_end_clean();
			return $output_results;
		}
	}
} 
add_shortcode( 'wppl_friends_results' , 'wppl_bp_query_results' );

/* no results message */
function wppl_bp_no_members() {
	echo '<div id="message" class="info" style="float:left; width:100%;">';
		echo '<p>'; echo _e( "Sorry, no members were found.", 'buddypress' );
	echo '</div>';
}

?>