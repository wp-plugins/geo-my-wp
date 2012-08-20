<?php

/*
 Plugin Name: buddypress-locations-addons
 Plugin URI: http://www.salsalo.com/
 Description: buddypress schools , instructors addons
 Version: 1.0
 Author: Eyal Fitoussi
 Author URI: http://www.salsalo.com/
 */

$plugins_options = get_option('wppl_plugins');

if ($plugins_options['friends_locator_on'] == 1) {

function addLocation(){
	global $wpdb, $bp;
	
	$map_icon = ($_POST['map_icon']) ? $_POST['map_icon'] : '_default.png';
	
	if ( empty($_POST['wppl_address']) &&  empty($_POST['wppl_lat']) &&  empty($_POST['wppl_long']) ) {
		$wpdb->query(
			$wpdb->prepare( 
				"DELETE FROM " . $wpdb->prefix . "wppl_friends_locator WHERE member_id=%d",$bp->loggedin_user->id
				)
			);
	
	} elseif ($wpdb->replace( $wpdb->prefix . 'wppl_friends_locator', array( 
				'member_id'		=> $bp->loggedin_user->id,	
				'address'		=> $_POST['wppl_address'],
				'zipcode'		=> $_POST['wppl_zipcode'],
				'city' 			=> $_POST['wppl_city'],
				'state' 		=> $_POST['wppl_state'], 
				'country' 		=> $_POST['wppl_country'],
				'lat'			=> $_POST['wppl_lat'],
				'long'			=> $_POST['wppl_long'],
				'map_icon'		=> $map_icon,		
			))===FALSE) {
			 
	echo "Error";
 
	}
	else {	
		echo "Data successfully saved!"; 
	}
	die();
}

add_action('wp_ajax_addLocation', 'addLocation');
add_action('bp_init', 'add_bp_location_tab');

function add_bp_location_tab () {
	global $bp;
	if (bp_is_home()) {$func = 'my_location_page'; } else {$func = 'user_location_page';}

	bp_core_new_nav_item(
   	array(
        'name' => __('Location', 'buddypress'),
        'slug' => 'wppl-location',
        'position' => 50,
        'show_for_displayed_user' => true,
        'screen_function' => $func,
        //'default_subnav_slug' => $who,
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
			
    function my_location_page_content() { 
   
	global $post, $bp, $wpdb;
	$options = get_option('wppl_fields');

	$sql ="
		SELECT * 
			FROM " . $wpdb->prefix . "wppl_friends_locator  	
    		WHERE 
    		(1 = 1)
    		AND member_id =". $bp->loggedin_user->id. "
			ORDER BY member_id" ;
    
    $member_info = $wpdb->get_results($sql, ARRAY_A);
    echo '<script type="text/javascript">'; 
	echo 	'savedLat= '.json_encode($member_info[0]['lat']),';'; 
	echo 	'savedLong= '.json_encode($member_info[0]['long']),';'; 
	echo '</script>';
	
    include 'user-location-tab.php';
    }
        	
    function user_location_page_title() {
    	global $bp;
        echo $bp->displayed_user->fullname . '&#39;s Location';
    }
    
    function user_location_page_content() { 
    	wp_enqueue_style('wppl-bp-style');
    	global $bp, $wpdb;
    	$sql ="
		SELECT * 
			FROM " . $wpdb->prefix . "wppl_friends_locator  	
    		WHERE 
    		(1 = 1)
    		AND member_id =". $bp->displayed_user->id. "
			ORDER BY member_id" ;
			
		$member_info = $wpdb->get_results($sql, ARRAY_A);
	
		if ($member_info) {
   			$get_loc = array(
    			$member_info[0]['lat'],
    			$member_info[0]['long'],
    			$member_info[0]['address']
    		);
    
    		echo '<script type="text/javascript">'; 
			echo 	'singleLocation= '.json_encode($get_loc),';'; 
			echo  '</script>';
	
			wp_enqueue_script( 'wppl-member-map', true); ?>
			<script>
			jQuery(function() {
    			jQuery('.show-directions').click(function(event){
    				event.preventDefault();
    				jQuery(".wppl-single-member-direction").slideToggle(); 
    			}); 
   	 		});
			</script>
    		<?php	
    		$member_map .='';	
    		$member_map .=	'<div class="wppl-single-member-wrapper">';
    		$member_map .=		'<div class="wppl-single-member-address"><span>Address: </span>' .$member_info[0]['address'].'</div>';
			$member_map .=		'<div class="wppl-single-member-map-wrapper" style="position:relative">';
			$member_map .=			'<div id="member_map" style="width:350px; height:350px;"></div>';
			$member_map .= 			'<div class="wppl-single-member-direction-wrapper">';
    		$member_map .= 				'<div class="wppl-single-member-direction" style="display:none;">';
			$member_map .=					'<form action="http://maps.google.com/maps" method="get" target="_blank">';
			$member_map .= 						'<input type="text" size="35" name="saddr" />';
			$member_map .= 						'<input type="hidden" name="daddr" value="'. $member_info[0]['address'].'" /><br />';
			$member_map .= 						'<input type="submit" class="button" value="GO" />';
			$member_map .= 					'</form>'; 
    		$member_map .= 				'</div>';
    		$member_map .= 				'<a href="#" class="show-directions">Get Directions</a>';
    		$member_map .= 			'</div>';
    		$member_map .=		'</div>';// map wrapper //
    		$member_map .=	'</div>';// map wrapper //
    
    		echo $member_map;
		} else {
			echo $bp->displayed_user->fullname . ' has not added a location yet.';
		}
	}
} // friends locator on

?>