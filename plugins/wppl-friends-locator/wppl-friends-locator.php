<?php
// function save's user location //
function addLocation(){
	global $wpdb, $bp;
	if( is_multisite() ) $wppl_options = get_site_option('wppl_site_options'); else $wppl_options = get_option('wppl_fields');
	$mem_id = $bp->loggedin_user->id;
	
	$address = $_POST['wppl_street'] .' ' . $_POST['wppl_city'] .' ' .$_POST['wppl_state'] . ' ' . $_POST['wppl_zipcode'] . ' ' . $_POST['wppl_country'];
	$address_apt = $_POST['wppl_street'] .' ' . $_POST['wppl_apt'] .' ' . $_POST['wppl_city'] .' ' .$_POST['wppl_state'] . ' ' . $_POST['wppl_zipcode'] . ' ' . $_POST['wppl_country'];
	// get address details from google
	$returned_address = convertToCoords($address);
	
	$map_icon = ($_POST['map_icon']) ? $_POST['map_icon'] : '_default.png';
	
	if ( !isset($returned_address['lat']) || empty($returned_address['lat'] ) ) {
		$wpdb->query(
			$wpdb->prepare( 
				"DELETE FROM wppl_friends_locator WHERE member_id=%d",$mem_id
			)
		);
		
		if( isset($wppl_options['xprofile_address']['useit']) && $wppl_options['xprofile_address']['useit'] == 1 ) {
			xprofile_set_field_data($wppl_options['xprofile_address']['street'], $mem_id, '');
			xprofile_set_field_data($wppl_options['xprofile_address']['apt'], $mem_id, '');
			xprofile_set_field_data($wppl_options['xprofile_address']['city'], $mem_id, '');
			xprofile_set_field_data($wppl_options['xprofile_address']['state'], $mem_id, '');
			xprofile_set_field_data($wppl_options['xprofile_address']['zipcode'], $mem_id, '');
			xprofile_set_field_data($wppl_options['xprofile_address']['country'], $$mem_id, ''); 
			xprofile_set_field_data($wppl_options['xprofile_address']['address'], $mem_id, ''); 
		}
	
	} elseif ($wpdb->replace( 'wppl_friends_locator', array( 
		'member_id'			=> $mem_id,	
		'street' 			=> $returned_address['street'],
		'apt' 				=> $returned_address['apt'],
		'city' 				=> $returned_address['city'],
		'state' 			=> $returned_address['state_short'], 
		'state_long' 		=> $returned_address['state_long'], 
		'zipcode'			=> $returned_address['zipcode'],
		'country' 			=> $returned_address['country_short'],
		'country_long' 		=> $returned_address['country_long'],
		'address'			=> $address_apt,
		'formatted_address' => $returned_address['formatted_address'],
		'lat'				=> $returned_address['lat'],
		'long'				=> $returned_address['long'],
		'map_icon'			=> $map_icon	
	))===FALSE) { 
		
		echo "Error";
 	
	} else {	
		echo "Data successfully saved!";
		if( isset($wppl_options['xprofile_address']['useit']) && $wppl_options['xprofile_address']['useit'] == 1 ) {
			xprofile_set_field_data($wppl_options['xprofile_address']['street'], $mem_id, $returned_address['street']);
			xprofile_set_field_data($wppl_options['xprofile_address']['apt'], $mem_id, $_POST['wppl_apt']);
			xprofile_set_field_data($wppl_options['xprofile_address']['city'], $mem_id, $returned_address['city']);
			xprofile_set_field_data($wppl_options['xprofile_address']['state'], $mem_id, $returned_address['state_short']);
			xprofile_set_field_data($wppl_options['xprofile_address']['zipcode'], $mem_id, $returned_address['zipcode']);
			xprofile_set_field_data($wppl_options['xprofile_address']['country'], $mem_id, $returned_address['country_short']); 
			xprofile_set_field_data($wppl_options['xprofile_address']['address'], $mem_id, $returned_address['formatted_address']); 
		}
	}
	die();
}
add_action('wp_ajax_addLocation', 'addLocation');

// location tabs
function add_bp_location_tab () {
	global $bp;
	$wppl_options = get_option('wppl_fields');
	
	// display "my Location" tab when setting set to show it.
	if ( bp_is_home() ) {
		if (is_multisite() ) $wppl_site_options = get_site_option('wppl_site_options');
		if ( 
			( isset($wppl_options['user_location_tab']) && $wppl_options['user_location_tab'] == 1 ) ||
			( isset($wppl_site_options['friends']['location_tab'] ) && $wppl_site_options['friends']['location_tab'] == 1 )
		) return;  
		else $func = 'my_location_page';
	} else $func = 'user_location_page';
	
	bp_core_new_nav_item(
   	array(
        'name' => __('Location', 'buddypress'),
        'slug' => 'wppl-location',
        'position' => 50,
        'show_for_displayed_user' => true,
        'screen_function' => $func,
        'item_css_id' => 'wppl-my-location'
    ));
}
    	 
function my_location_page () {
	add_action( 'bp_template_title', 'my_location_page_title' );
	add_action( 'bp_template_content', 'my_location_page_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function user_location_page () {
	add_action( 'bp_template_title', 'user_location_page_title' );
	add_action( 'bp_template_content', 'user_location_page_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function my_location_page_title() {
	global $bp;
	echo 'Your location';
}
// "location" tab for looged in user
function my_location_page_content() { 
	global $bp, $wpdb;
	$wppl_options = get_option('wppl_fields');

	$member_loc_tab = $wpdb->get_results(
					  	$wpdb->prepare(
							"SELECT * FROM wppl_friends_locator 
							WHERE member_id = %s", 
							array($bp->displayed_user->id)
						), ARRAY_A 
					  );
	
	$mem_loc = array(
		'savedLat' 	=> $member_loc_tab[0]['lat'], 
		'savedLong' => $member_loc_tab[0]['long']
	);
	
	include_once GMW_FL_PATH . 'my-location-tab.php';
	wp_enqueue_script( 'jquery-ui-autocomplete');
	wp_enqueue_script('wppl-fl'); 
	wp_localize_script('wppl-fl', 'adminUrl', GMW_AJAX);
	wp_localize_script('wppl-fl','memLoc', $mem_loc);
}
		
function user_location_page_title() {
	global $bp;
	echo $bp->displayed_user->fullname . ' Location';
}
// displayed user "location" tab
function user_location_page_content() { 
	include 'user-location-tab.php';
}
add_action('bp_init', 'add_bp_location_tab');
?>