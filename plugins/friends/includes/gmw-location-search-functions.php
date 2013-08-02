<?php 
/**
 * GMW FL search form function - Display xprofile fields
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_fl_fields_dropdown($gmw, $id, $class) {

	$total_fields = ( isset($gmw['profile_fields']) ) ? $gmw['profile_fields'] : array();
	if( isset($gmw['profile_fields_date']) ) array_unshift($total_fields, $gmw['profile_fields_date']);

	echo '<div id="'.$id.'" class="'.$class.'">';

	foreach ( $total_fields as $field_id ) {

		$field_data = new BP_XProfile_Field ($field_id);
		$fieldName = explode(' ', $field_data->name);

		$get_field = (isset($_GET[$fieldName[0] . '_' . $field_id])) ? $_GET[$fieldName[0] . '_' . $field_id] : '';
		$get_field_to = (isset($_GET[$fieldName[0] . '_' . $field_id. '_to'])) ? $_GET[$fieldName[0] . '_' . $field_id. '_to'] : '';

		$children = $field_data->get_children ();

		switch ($field_data->type) {
			case 'datebox':
				echo '<div class="editfield field_'.$field_id.' datebox">';
				echo '<span class="label">Age Range (min - max)</span>';
				echo '<input size="3" type="text" name="'. $fieldName[0] . '_' . $field_id.'" value="' . $get_field . '" placeholder="Min" style="width:10%" />';
				echo '&nbsp;-&nbsp;';
				echo '<input size="3" type="text" name="'. $fieldName[0] . '_' . $field_id . '_to" value="' . $get_field_to . '" placeholder="Max" style="width:10%" />';
				echo '</div>';
				break;

			case 'multiselectbox':
			case 'selectbox':
			case 'radio':
			case 'checkbox':
				echo '<div class="editfield field_'.$field_id.' checkbox">';
				echo '<span class="label">' . $field_data->name. '</span>';
				$tt = array();
				if($get_field) {
					$tt = $get_field;
				}
				//echo '<div class="wppl-checkboxes-wrapper">';
				foreach ($children as $child) {
					$child->name = trim ($child->name);
					$checked =  (in_array ($child->name, $tt ) )? "checked='checked'": "";
					echo '<label><input ' .$checked . ' type="checkbox" name="'. $fieldName[0] . '_' . $field_id. '[]" value="'.$child->name.'" />'.$child->name.'</label>';
				}
				//echo '</div>';
				echo '</div>';
				break;
		} // switch

	}
	echo '</div>';
}

/**
 * GMW FL Search results function - display map
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_fl_results_map($gmw) {
	if ( ( $gmw['results_type'] == "both" ) || ( $gmw['results_type'] == "map") )  :
		$width = ( !empty($gmw['map_width']['value'] ) ) ? $gmw['map_width']['value'].$gmw['map_width']['units'] : '500px';
		$height = ( !empty($gmw['map_height']['value'] ) ) ? $gmw['map_height']['value'].$gmw['map_height']['units'] : '500px';
		echo '<div id="gmw-fl-map-'.$gmw['form_id'].'" class="gmw-map" style="width:'.$width.'; height:'.$height.';"></div>';
	endif;
}

/**
 * GMW FL Search results function - adds elements to be display on the map
 * @version 1.0
 * @author Eyal Fitoussi
 */

function gmw_fl_map_elements($gmw, $members_template) {
	if ( $gmw['results_type'] == "posts" ) return;
	$members_template->member->permalink = bp_get_member_permalink();
	$members_template->member->avatar = bp_get_member_avatar( $args = 'type=thumb' );
}

/**
 * GMW FL Search results function - Display user's full address
 * @version 1.0
 * @author Eyal Fitoussi
 */

function gmw_fl_user_address($gmw, $members_template) {
	$address = $members_template->member->address;
	echo apply_filters('gmw_fl_has_members_loop_memebr_address', $address, $gmw, $members_template);
}

/**
 * GMW FL Search results function - Display Radius distance
 * @version 1.0
 * @author Eyal Fitoussi
 */

function gmw_fl_by_radius($gmw, $members_template) {
	if ( isset( $gmw['your_lat'] ) && !empty( $gmw['your_lat'] ) ) echo '<div class="gmw-fl-radius-wrapper">' . $members_template->member->distance .' ' . $gmw['units_array']['name'] . '</div>';
}

/**
 * GMW FL search results function - Query xprofile fields
 * @version 1.0
 * @author Eyal Fitoussi
 * @author Some of the code in this function was inspired by the code written by Andrea Taranti the creator of BP Profile Search - Thank you
 */

function gmw_fl_query_fields($gmw) {
	global $bp, $wpdb;
	$total_fields = false;

	$total_fields = ( isset( $gmw['profile_fields'] ) ) ? $gmw['profile_fields'] : array();
	if( isset( $gmw['profile_fields_date'] ) && !empty( $gmw['profile_fields_date'] ) ) array_unshift( $total_fields, $gmw['profile_fields_date'] );

	if ( !isset($total_fields) || empty($total_fields) ) return;

	$empty_fields = array();
	$userids = false;

	foreach ($total_fields as $field_id) {

		$field_data = new BP_XProfile_Field ($field_id);
		$fieldName = explode(' ', $field_data->name);

		$value = ( isset($_GET[$fieldName[0] . '_'. $field_id]) ) ? $_GET[$fieldName[0] . '_'. $field_id] : '';
		$to = ( isset($_GET[$fieldName[0] . '_' . $field_id. '_to']) ) ?  $_GET[$fieldName[0] . '_' . $field_id. '_to'] : '';
			
		if ($value) array_push($empty_fields, $value);
			
		if( $value || $to ) {

			switch ($field_data->type) {
					
				case 'selectbox':
				case 'multiselectbox':
				case 'checkbox':
				case 'radio':
					$sql = "SELECT user_id from {$bp->profile->table_name_data}";
					$sql .= " WHERE field_id = $field_id ";
					$like = array();

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

			$results = $wpdb->get_col($sql, 0);

			if ( !is_array($userids) ) $userids = $results; else $userids = array_intersect($userids, $results);

		} // if value //
	} // for eaech //

	/* build SQL filter from profile fields results - member ids array */
	if ( isset($userids) && !empty($userids) ) return $userids;
	/* if no results and profile fields are not empty - buba is going to stop the function */
	else if ( !empty($empty_fields) ) return "buba";
}

/**
 * GMW FL search results function - "Get directions" link
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_fl_get_directions($gmw, $members_template) {
	if ( isset( $gmw['get_directions'] ) && $gmw['get_directions'] == 1 ) {
		if ( !isset($gmw['org_address']) ) $gmw['org_address'] = '';
		echo 	'<a href="http://maps.google.com/maps?f=d&hl=en&doflg=' . $gmw['units_array']['map_units'] . '&geocode=&saddr=' .$gmw['org_address'] . '&daddr=' . str_replace(" ", "+", $members_template->member->address) . '&ie=UTF8&z=12" target="_blank">Get Directions</a>';
	}
}

/**
 * GMW FL search results function - display within distance message
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_fl_wihtin_message($gmw) {
	if ( !isset( $gmw['org_address'] ) || empty( $gmw['org_address'] ) ) return;
	echo ' <span>within '. $gmw['radius'] . ' ' . $gmw['units_array']['name'] .' from ' . $gmw['org_address'] .'</span>';
}

/**
 * GMW FL search results function - calculate driving distance
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_fl_driving_distance($gmw, $members_template, $class) {
	if ( !isset($gmw['by_driving']) || $gmw['units_array']['name'] == false )
		return;

	echo 	'<div id="gmw-fl-driving-distance-'.$members_template->member->ID . '" class="'.$class.'"></div>';
	?>
	<script>   
    	var directionsDisplay;
		var directionsService = new google.maps.DirectionsService();	
        var directionsDisplay = new google.maps.DirectionsRenderer();	
        
  		var start = new google.maps.LatLng('<?php echo $gmw['your_lat']; ?>','<?php echo $gmw['your_long']; ?>');
  		var end = new google.maps.LatLng('<?php echo $members_template->member->lat; ?>','<?php echo $members_template->member->long; ?>');
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
      			
      			jQuery('#<?php echo 'gmw-fl-driving-distance-'. $members_template->member->ID; ?>').text('Driving: ' + totalDistance)
    		}
 		 });	 
	</script>
	<?php	
}

/**
 * GMW FL Search results function - Per page dropdown
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_fl_per_page_dropdown($gmw, $class) {
	global $members_template;
	$perPage = explode(",", $gmw['per_page']);
	$lastpage = ceil($members_template->total_member_count/$gmw['get_per_page']);
	
	$gmwLat = ( isset($gmw['your_lat']) && $gmw['your_lat'] != false ) ? $gmw['your_lat'] : 'false';
	$gmwLong = ( isset($gmw['your_long']) && $gmw['your_long'] != false ) ? $gmw['your_long'] : 'false';
	
	if ( count( $perPage) > 1 ) :
		
		echo '<select name="wppl_per_page" class="gmw-fl-per-page-dropdown '.$class.'">';
		
			foreach ( $perPage as $pp ) :
				if ( isset($_GET['wppl_per_page']) && $_GET['wppl_per_page'] == $pp ) $pp_s = 'selected="selected"'; else $pp_s = "";
				echo '<option value="'. $pp .'" '.$pp_s.'>'.$pp.' per page</option>';
			endforeach;

		echo '</select>';

	endif;

	?>
	<script>
		jQuery(document).ready(function($) {
		   	$(".gmw-fl-per-page-dropdown").change(function() {
			   	var totalResults = <?php echo $members_template->total_member_count; ?>;
			   	var lastPage = Math.ceil(totalResults/$(this).val());
			   	var newPaged = ( <?php echo $members_template->pag_num; ?> > lastPage ) ? lastPage : false;
		   		//var seperator = (window.location.href.indexOf("?")===-1)?"?":"&";
		   		window.location.href = jQuery.query.set("wppl_per_page", $(this).val()).set('upage', newPaged).set('gmw_lat', <?php echo $gmwLat; ?>).set('gmw_long', <?php echo $gmwLong; ?>);
		    });
		});
    </script>
<?php 
}

/**
 * GMW FL function - Create filters for buddypress members query
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmwUserQueries( $gmw ) {
	global $wpdb;
	
	$gmwUserQueries['wp_user_query'] = false;
	$gmwUserQueries['wp_user_query']['query_fields'] = false;
	$gmwUserQueries['wp_user_query']['query_from'] = false;
		
	/*
	 * prepare the filter of bp_query_user. the filter will modify the SQL function and will check the distance
	* of each user from the address entered and will results in user ID's of the users that within
	* the radius entered. The user IDs will then pass to the next wp_query_user below.
	*/
			
	if ( !empty($gmw['org_address']) ) :
		/*
		 * if address entered:
		* prepare the filter of the select clause of the SQL function. the function join Buddypress's members table with
		* wppl_friends_locator table, will calculate the distance and will get only the members that
		* within the radius was chosen
		*/
		$gmwUserQueries['bp_user_query']['select'] = $wpdb->prepare( " SELECT gmwlocations.member_id, u.ID as id, 			
														ROUND( %d * acos( cos( radians( %s ) ) * cos( radians( gmwlocations.lat ) ) * cos( radians( gmwlocations.long ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( gmwlocations.lat) ) ),1 ) AS distance ", 
														$gmw['units_array']['radius'], $gmw['your_lat'], $gmw['your_long'], $gmw['your_lat'] );
			
		$gmwUserQueries['bp_user_query']['from'] = " FROM wppl_friends_locator gmwlocations INNER JOIN ".$wpdb->prefix."users u ON gmwlocations.member_id = u.ID ";
		$gmwUserQueries['bp_user_query']['group_by'] = $wpdb->prepare(' GROUP BY u.ID HAVING distance <= %d OR distance IS NULL ', $gmw['radius']);
		$gmwUserQueries['bp_user_query']['order_by'] = 'ORDER BY distance';
			
	else:
	
		/*
		 * if no address entered choose all members that in members table and wppl_friends_locator table
		* check agains the useids (returned from xprofile fields query) if exist and results in ids
		*/
		$gmwUserQueries['bp_user_query']['select'] = " SELECT u.ID as id, u.display_name as dName FROM wppl_friends_locator gmwlocations INNER JOIN ".$wpdb->prefix."users u ON gmwlocations.member_id = u.ID ";
		$gmwUserQueries['bp_user_query']['from'] = "";
		$gmwUserQueries['bp_user_query']['order_by'] = '';
	endif;
		
	/*
	 * prepare the filter of the wp_query_user which is within bp_query_user.
	* the filter will modify the SQL function and will calculate the distance of each user
	* in the array of user IDs that was returned from the function above.
	* the filter will also add the members information from wppl_friends_locator table into the results
	* as well as the distance.
	*/
	if ( !empty( $gmw['org_address'] ) ) :
		$gmwUserQueries['wp_user_query']['query_fields'] .= $wpdb->prepare(" , gmwlocations.* , 
																ROUND( %d * acos( cos( radians( %s ) ) * cos( radians( gmwlocations.lat ) ) * cos( radians( gmwlocations.long ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( gmwlocations.lat) ) ),1 ) AS distance",
																$gmw['units_array']['radius'], $gmw['your_lat'], $gmw['your_long'], $gmw['your_lat'] );
	else :
	
		$gmwUserQueries['wp_user_query']['query_fields'] .= " , gmwlocations.* ";
	
	endif;

	$gmwUserQueries['wp_user_query']['query_from'] 	.= " INNER JOIN wppl_friends_locator gmwlocations ON ID = gmwlocations.member_id ";
			
	return apply_filters('gmw_fl_after_query_clauses',$gmwUserQueries, $gmw);
}

/**
 * GMW FL function - Add filter to BP_user_query
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmwBpQuery( $gmwBpQuery ) {
	global $gmwUserQueries;
		
	/* modify the function to to calculate the total rows(members). */
	$gmwBpQuery->query_vars['count_total'] = 'sql_calc_found_rows';
		
	$gmwBpQuery->uid_clauses['select'] = implode(' ',$gmwUserQueries['bp_user_query']);
	
	//if ( isset($gmwUserQueries['near_members'] ) ) :
	//	$gmwBpQuery->uid_clauses['where'] = '';
	//	$gmwBpQuery->uid_clauses['limit'] = 'LIMIT 0,'.$gmwUserQueries['per_page'];
	//endif;
	
	return $gmwBpQuery;
}

/**
 * GMW FL function - Add filter to WP_user_query
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmwWpQuery( $gmwWpQuery ) {
	global $gmwUserQueries;
		
	$gmwWpQuery->query_fields .= $gmwUserQueries['wp_user_query']['query_fields'];
	$gmwWpQuery->query_from .= $gmwUserQueries['wp_user_query']['query_from'];
		
	return $gmwWpQuery;
}

/**
 * GMW FL results function - no members found
*/
function gmw_fl_no_members($gmw, $gmw_options) {
	
	do_action('gmw_fl_before_no_members', $gmw, $gmw_options);
	
	$no_members = '<div id="message" class="info"><h3>No members found</h3></div>';
	echo apply_filters('gmw_fl_no_members_message', $no_members, $gmw);
	
	do_action( 'bp_after_members_loop' );
	do_action('gmw_fl_before_no_members', $gmw, $gmw_options);
}
add_action('gmw_friends_form_submitted_address_geocoded_not', 'gmw_fl_no_members', 10, 2);

/**
 * GMW PT function - include search form
 * @param $gmw
 * @param $gmw_options
 */
function gmw_fl_main_shortcode_search_form($gmw, $gmw_options) {

	do_action('gmw_fl_before_search_form', $gmw, $gmw_options);
	
	wp_enqueue_style( 'gmw-'.$gmw['form_id'].'-'.$gmw['form_template'].'-form-style', GMW_FL_URL. 'search-forms/'.$gmw['form_template'].'/css/style.css',99 );
	include GMW_FL_PATH .'search-forms/'.$gmw['form_template'].'/search-form.php';
	
	do_action('gmw_fl_before_search_form', $gmw, $gmw_options);

}
add_action('gmw_friends_main_shortcode_search_form', 'gmw_fl_main_shortcode_search_form', 15 , 2);

/**
 * GMW FL function - add xprofile fields query to results 
 * @param $gmwUserQueries
 * @param $gmw
 * 
 */
function gmw_fl_add_xprofile_query_to_results($gmwUserQueries, $gmw) {
	global $wpdb;
   	$userids = gmw_fl_query_fields($gmw);
   			   	
   	if ( isset( $userids ) && !empty( $userids ) ) $gmwUserQueries['bp_user_query']['from'] .= $wpdb->prepare(" AND gmwlocations.member_id IN (".str_repeat("%d,", count( $userids ) -1) . "%d)", $userids );
   												   
   	return $gmwUserQueries;

}
add_filter('gmw_fl_after_query_clauses', 'gmw_fl_add_xprofile_query_to_results', 5, 2 );

/**
 * GMW FL function - Display results for buddypress members
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_get_friends_results($gmw, $gmw_options) {
		global $gmwUserQueries;
		
		$gmw['get_per_page'] = ( isset($_GET['wppl_per_page']) ) ? $_GET['wppl_per_page'] : current(explode(",", $gmw['per_page']));
		
		$gmw = apply_filters('gmw_fl_settings_before_search_results', $gmw, $gmw_options);
		
		do_action('gmw_fl_before_search_results', $gmw, $gmw_options);
		
		$gmwUserQueries = gmwUserQueries($gmw);
		add_action( 'bp_pre_user_query', 'gmwBpQuery' );
		add_action( 'pre_user_query', 'gmwWpQuery' );
		
		/* 
		 * start buddypress's members query
		 */
		do_action( 'bp_before_directory_members_page' ); 
		
		do_action( 'bp_before_members_loop' ); 
		
		if ( bp_has_members( array('type' => '',  'per_page' => $gmw['get_per_page'] ) ) ) :
			global $members_template;
		
			$gmw = apply_filters('gmw_fl_settings_has_members_start', $gmw, $gmw_options);
			do_action('gmw_fl_has_members_start', $gmw, $gmw_options, $members_template);
			/*
			 * add lat/long values to url via pagination links
			 */
			if ( (int) $members_template->total_member_count && (int) $members_template->pag_num ) {
				$members_template->pag_links = paginate_links( array(
					'base'      => add_query_arg( array('upage' => '%#%', 'gmw_lat' => $gmw['your_lat'], 'gmw_long' => $gmw['your_long']) ),
					'format'    => '',
					'total'     => ceil( (int) $members_template->total_member_count / (int) $members_template->pag_num ),
					'current'   => (int) $members_template->pag_page,
					'prev_text' => _x( '&larr;', 'Member pagination previous text', 'buddypress' ),
					'next_text' => _x( '&rarr;', 'Member pagination next text', 'buddypress' ),
					'mid_size'  => 1
				) );
			}
			
			/* 
			 * include custom results and stylesheet pages from child/theme
			 */
			if( strpos($gmw['results_template'], 'custom_') !== false ) :
				
				$sResults = str_replace('custom_','',$gmw['results_template']);
				wp_register_style( 'gmw-current-style', get_stylesheet_directory_uri() . '/geo-my-wp/friends/search-results/'.$sResults.'/css/style.css' );
				wp_enqueue_style( 'gmw-current-style');
						
				include(STYLESHEETPATH. '/geo-my-wp/friends/search-results/'.$sResults.'/results.php');
			/*
			 * include results and stylesheet pages from plugin's folder
			*/
			else :
				wp_register_style( 'gmw-current-style', GMW_FL_URL. 'search-results/'.$gmw['results_template'].'/css/style.css' );
				wp_enqueue_style( 'gmw-current-style');
				include_once GMW_FL_PATH . 'search-results/'.$gmw['results_template'].'/results.php';
			endif;
		
			if ( ($gmw['results_type'] == "map") ) echo '<style>ul#members-list { display:none; }</style>';
			
			/*
			 * When we want to display the map
			*/
			if( $gmw['results_type'] != "posts" ) :
				
				/* 
				 * pass results to javascript to use to display the map and markers 
				*/							
				if ( !isset($gmw['map_controls']) || empty($gmw['map_controls']) ) $gmw['map_controls'] = false;
				
				/* get page number */
				$gmw['page'] = $members_template->pag_page;
				$gmw['main_icons_folder'] = GMW_URL . '/map-icons/';
				$gmw['bp_icons_folder'] = GMW_FL_URL . 'map-icons/';
				
				$gmw = apply_filters('gmw_fl_settings_before_map', $gmw, $members_template);
				$members_template = apply_filters('gmw_fl_posts_loop_before_map', $members_template, $gmw);
					
				do_action('gmw_fl_has_members_before_map_displayed', $gmw, $gmw_options, $members_template);
				
				wp_enqueue_script( 'gmw-fl-map', true);
				wp_localize_script( 'gmw-fl-map', 'flMapArgs', $gmw);
				wp_localize_script( 'gmw-fl-map', 'flMembers', $members_template->members);
				
			endif;
			
			do_action( 'bp_after_members_loop' );
		/* 
		 * if no results 
		*/
		else :
			gmw_fl_no_members($gmw, $gmw_options);
		endif;
}
add_action('gmw_friends_form_submitted_latlong', 'gmw_get_friends_results', 10, 2);
add_action('gmw_friends_form_submitted_address_geocoded', 'gmw_get_friends_results', 10, 2);
add_action('gmw_friends_form_submitted_address_not', 'gmw_get_friends_results', 10, 2);
add_action('gmw_friends_results_auto_results', 'gmw_get_friends_results', 10, 2);