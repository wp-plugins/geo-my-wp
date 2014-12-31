<?php
/*
Plugin Name: WP Places Locator
Plugin URI: http://www.wpplaceslocator.com 
Description:  Advence search Post types by address within a certain radius.
Author: Eyal Fitoussi
Version: 1.2.7
Author URI: http://www.wpplaceslocator.com 

*/

/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

include('admin-settings.php'); 
include_once 'get-places.php';

add_action( 'wp_enqueue_scripts', 'wppl_stylesheet' );

function wppl_stylesheet() {
    // REGISTER STYLESHEET //
    wp_register_style( 'wppl-style', plugins_url('css/style.css', __FILE__) );
    wp_enqueue_style( 'wppl-style' );
}

//// CREATE DATABASE'S TABLE ////

global $places_locator_db_version;
$places_locator_db_version = "1.0";

function places_locator_install() {
   global $wpdb;
   global $places_locator_db_version;

   $table_name = $wpdb->prefix . "places_locator";
      
   $sql = "CREATE TABLE $table_name (
  `post_id` bigint(30) NOT NULL,
  `post_status` varchar(255) NOT NULL ,
  `post_type` varchar(255) NOT NULL , 
  `lat` varchar(255) NOT NULL ,
  `long` varchar(255) NOT NULL ,
  `post_title` varchar(255) NOT NULL ,
  `city` varchar(255) NOT NULL ,
  `state` varchar(255) NOT NULL ,
  `zipcode` varchar(255) NOT NULL ,
  `address` varchar(255) NOT NULL ,
  `phone` varchar(255) NOT NULL ,
  `fax` varchar(255) NOT NULL ,
  `email` varchar(255) NOT NULL ,
  `website` varchar(255) NOT NULL ,
  UNIQUE KEY id (post_id)
    );";
    
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
   add_option("places_locator_db_version", $places_locator_db_version);
}

register_activation_hook(__FILE__,'places_locator_install');

// when post status changes - change it in our table as well //
function filter_transition_post_status( $new_status, $old_status, $post ) { 
    global $wpdb;
    $wpdb->query( 
        $wpdb->prepare( 
          "UPDATE " . $wpdb->prefix . "places_locator SET post_status=%s WHERE post_id=%d", 
           $new_status,$post->ID
         ) 
     );
}
add_action('transition_post_status', 'filter_transition_post_status', 10, 3);

// delete info from our database after post was deleted //
function delete_address_map_rows()	{
	global $wpdb;
	global $post;
	$wpdb->query(
		$wpdb->prepare( 
			"DELETE FROM " . $wpdb->prefix . "places_locator WHERE post_id=%d",$post->ID
		)
	);
}
add_action('before_delete_post', 'delete_address_map_rows');

////// SHORTCODE FOR SINGLE LOCATION MAP///////
function wppl_shortcode_single($single) {
	extract(shortcode_atts(array(
		'map_height' => '250',
		'map_width' => '250',
		'map_type' => 'ROADMAP',
		'zoom_level' => 13,
		
	),$single));
	global $post, $wpdb;		
    $get_loc = array(get_post_meta($post->ID,'_wppl_lat',true),get_post_meta($post->ID,'_wppl_long',true));
 	
 	echo	'<div class="wppl-single-wrapper">';
	echo		'<div class="wppl-single-map-wrapper">';
	echo			'<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>';
	echo			'<div id="map_single" style="width:' . $map_width .'px; height:' . $map_height . 'px;"></div>';
	echo 			'<script type="text/javascript">'; 
	echo 				'singleLocation= '.json_encode($get_loc),';'; 
	echo 				'single="1";'; 
	echo 				'mapType= '.json_encode($map_type),';'; 
	echo 				'var zoomLevel= '.json_encode($zoom_level),';';
	echo  				'</script>';
	echo 			'<script src="' . plugins_url('js/map.js', __FILE__) . '" type="text/javascript"></script>';
	echo		'</div>';// map wrapper //
	echo	'</div>';	
}
add_shortcode( 'swppl' , 'wppl_shortcode_single' );

//// Register Search Form widget ////
class wppl_widget extends WP_Widget {
	// Constructor //
		function wppl_widget() {
			$widget_ops = array( 'classname' => 'wppl_widget', 'description' => 'Displays Places Locator Search form in your sidebar' ); // Widget Settings
			$control_ops = array( 'id_base' => 'wppl_widget' ); // Widget Control Settings
			$this->WP_Widget( 'wppl_widget', 'WPPL Search Form', $widget_ops, $control_ops ); // Create the widget
		}
	// Extract Args //
		function widget($args, $instance) {
			extract( $args );
			$title 		= apply_filters('widget_title', $instance['title']); // the widget title
			$short_code	= $instance['short_code'];

			echo $before_widget;

			if ( $title ) { echo $before_title . $title . $after_title; }

        	echo do_shortcode('[wppl form="'.$short_code.'" form_only="1"]');

			echo $after_widget;
		}

	// Update Settings //
		function update($new_instance, $old_instance) {
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['short_code'] = $new_instance['short_code'];
			$instance['pages'] = $new_instance['pages'];
			return $instance;
		}

	// Widget Control Panel //
		function form($instance) {
			$w_posts = get_post_types();
			$defaults = array( 'title' => 'Search Places');
			$instance = wp_parse_args( (array) $instance, $defaults ); 
			$shortcodes = get_option('wppl_shortcode');
			?>
  			<p style="margin-bottom:10px; float:left;">
		    	<lable><?php echo  esc_attr( __( 'Title:' ) ); ?></lable>
				<input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" width="25" style="float: left;width: 100%;"/>
			</p>
			<p>
			<lable><?php echo esc_attr( __( 'Choose shortcode to use in the sidebar:' ) ); ?></lable>
				<select name="<?php echo $this->get_field_name('short_code'); ?>" style="float: left;width: 100%;">
				<?php foreach ($shortcodes as $shortcode) {
					echo '<option value="' . $shortcode[form_id] . '"'; echo ($instance['short_code'] == $shortcode[form_id] ) ?'selected="selected"' : ""; echo '>wppl form="' . $shortcode[form_id] . '"</options>';
						} ?>
				</select>
			</p>
	<?php } 
	 }
add_action('widgets_init', create_function('', 'return register_widget("wppl_widget");'));


$options = get_option('wppl_fields');
//'locations= '.json_encode($posts_within),';';
//echo 'googleApi= '.json_encode($options['google_api']),';';
/*function insert_google_api() {
	echo	'<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key='. $options['google_api'] . '&sensor=true"></script>';
}
add_action('wp_head', 'insert_google_api');
*/
//   Create address & info meta Boxes    //
$prefix = '_wppl_';
$wppl_meta_box = array(
    'id' => 'wppl-meta-box',
    'title' => 'Address',
    'pages' => $options['address_fields'],
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(
        array(
            'name' => 'Street',
            'desc' => '',
            'id' => $prefix . 'street',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'Apt/Suite',
            'desc' => '',
            'id' => $prefix . 'apt',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'City',
            'desc' => '',
            'id' => $prefix . 'city',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'State',
            'desc' => '',
            'id' => $prefix . 'state',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'Zip Code',
            'desc' => '',
            'id' => $prefix . 'zipcode',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'Country',
            'desc' => '',
            'id' => $prefix . 'country',
            'type' => 'text',
            'std' => ''
        ),
        
        array(
            'name' => 'Phone Number',
            'desc' => '',
            'id' => $prefix . 'phone',
            'type' => 'text',
            'std' => ''
        ),
        
        array(
            'name' => 'Fax Number',
            'desc' => '',
            'id' => $prefix . 'fax',
            'type' => 'text',
            'std' => ''
        ),
        
        array(
            'name' => 'Email Address',
            'desc' => '',
            'id' => $prefix . 'email',
            'type' => 'text',
            'std' => ''
        ),
        
        array(
            'name' => 'Website Address',
            'desc' => 'Ex: www.website.com',
            'id' => $prefix . 'website',
            'type' => 'text',
            'std' => ''
        ),
        
    )
);

///// Create lat/long meta boxes ////

$wppl_meta_box_lat_long = array(
    'id' => 'wppl-meta-box-lat-long',
    'title' => 'latitude / Longitude (you must enter both latitude and longitude of a location)',
    'pages' => $options['address_fields'],
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array( 
         array(
            'name' => 'Latitude',
            'desc' => '',
            'id' => $prefix . 'enter_lat',
            'type' => 'text',
            'std' => ''
        ),
        
         array(
            'name' => 'Longitude',
            'desc' => '',
            'id' => $prefix . 'enter_long',
            'type' => 'text',
            'std' => ''
        ),
    )
);


// Add meta box //
function wppl_add_box() {
	$options = get_option('wppl_fields');
    global $wppl_meta_box;
    	if ($options['address_fields']) {
 		foreach ($wppl_meta_box['pages'] as $page) {
        	add_meta_box($wppl_meta_box['id'], $wppl_meta_box['title'], 'wppl_show_box', $page, $wppl_meta_box['context'], $wppl_meta_box['priority']);
   		}
    }
}
add_action('admin_menu', 'wppl_add_box');

// Callback function to show fields in meta box //
function wppl_show_box() {
    global $wppl_meta_box, $post;
 
    // Use nonce for verification //
    echo '<input type="hidden" name="wppl_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
 
    echo '<table class="form-table">';
 
    foreach ($wppl_meta_box['fields'] as $field) {
        // get current post meta data //
        $meta = get_post_meta($post->ID, $field['id'], true);
 
        echo '<tr>',
                '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                '<td>';
        switch ($field['type']) {
            case 'text':
                echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
                break;
            case 'textarea':
                echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
                break;
            case 'select':
                echo '<select name="', $field['id'], '" id="', $field['id'], '">';
                foreach ($field['options'] as $option) {
                    echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                }
                echo '</select>';
                break;
            case 'radio':
                foreach ($field['options'] as $option) {
                    echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                }
                break;
            case 'checkbox':
                echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
                break;
        }
        echo     '<td>',
            '</tr>';
    }
    	echo '<tr>';
 	 		echo '<th>latitude</th><td>' . get_post_meta($post->ID, '_wppl_lat', TRUE) . '</td></th>';
        echo '</tr>';
        
        echo '<tr>';
 	 		echo '<th>Longitude</th><td>' . get_post_meta($post->ID, '_wppl_long', TRUE) . '</td></th>';
        echo '</tr>';
        
		echo '<tr>';
 	 		echo '<th>Full Address</th><td>' . get_post_meta($post->ID, '_wppl_address', TRUE) . '</td></th>';
        echo '</tr>';
        
    echo '</table>';
}

// Add lat/long meta boxes //
function wppl_add_box_lat_long() {
	$options = get_option('wppl_fields');
    global $wppl_meta_box_lat_long;
    	if ($options['address_fields']) {
 		foreach ($wppl_meta_box_lat_long['pages'] as $page) {
        	add_meta_box($wppl_meta_box_lat_long['id'], $wppl_meta_box_lat_long['title'], 'wppl_show_box_lat_long', $page, $wppl_meta_box_lat_long['context'], $wppl_meta_box_lat_long['priority']);
   		}
    }
}
add_action('admin_menu', 'wppl_add_box_lat_long');
 
// Callback function to show fields in meta box //
function wppl_show_box_lat_long() {
    global $wppl_meta_box_lat_long, $post;
 
    // Use nonce for verification //
    echo '<input type="hidden" name="wppl_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
 
    echo '<table class="form-table">';
 
    foreach ($wppl_meta_box_lat_long['fields'] as $field) {
        // get current post meta data //
        $meta = get_post_meta($post->ID, $field['id'], true);
 
        echo '<tr>',
                '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                '<td>';
        switch ($field['type']) {
            case 'text':
                echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
                break;
            case 'textarea':
                echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
                break;
            case 'select':
                echo '<select name="', $field['id'], '" id="', $field['id'], '">';
                foreach ($field['options'] as $option) {
                    echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                }
                echo '</select>';
                break;
            case 'radio':
                foreach ($field['options'] as $option) {
                    echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                }
                break;
            case 'checkbox':
                echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
                break;
        }
        echo     '<td>',
            '</tr>';
    }
        
    echo '</table>';
}

//// convert lat/long to address ////
function ConvertToAddress($c_lat,$c_long) {	
	
	$ck = curl_init();	

    //// GET THE XML FILE WITH RESUALTS
    curl_setopt($ck, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ck, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ck, CURLOPT_USERAGENT, "Mozilla/2.0 (compatible; MSIE 3.02; Update a; AK; Windows 95)");
	curl_setopt($ck, CURLOPT_HTTPGET, true);
	curl_setopt($ck, CURLOPT_URL, 'http://maps.googleapis.com/maps/api/geocode/xml?latlng='.$c_lat.','.$c_long.'&sensor=false');
	
	curl_setopt($ck, CURLOPT_CONNECTTIMEOUT, 60);
	$got_xml = curl_exec($ck);  

    //// PARSE THE XML FILE //////////////
	$xml = new SimpleXMLElement($got_xml);	
	//print_r($xml);
	//// GET THE LATITUDE/LONGITUDE FROM THE XML FILE ///////////////
	global $c_street, $c_city ,$c_state, $c_zip, $c_country, $c_address;
	list($c_street,$c_city,$state_zip,$c_country) = explode(",", $xml->result->formatted_address);	
	$state_zip = explode(' ', $state_zip);
	$c_state = $state_zip[1];
	$c_zip = $state_zip[2];
	$c_address = $c_street." ".$c_city." ".$c_state." ".$c_zip." ".$c_country;	
}	

//////////////  EVERY NEW POST OR WHEN POST IS BEING UPDATED ////////////////////
//// CREATE MAP, LATITUDE, LONGITUDE AND SAVE DATA INTO OUR LOCATIONS TABLE ////
///  DATA SAVED - POST ID, POST TYPE, POST STATUS , POST TITLE , LATITUDE, LONGITUDE AND ADDRESS     ////
//////////////////////////////////////////////////////////////////////////////

add_action( 'save_post' , 'wppl_save_data'); 

function wppl_save_data($post_id) {
    global $wppl_meta_box, $wpdb, $wppl_meta_box_lat_long;
    global $post;
    $options = get_option('wppl_fields'); 
   
    // verify nonce //
    if (!wp_verify_nonce($_POST['wppl_meta_box_nonce'], basename(__FILE__))) {
        return;
    }
 
    // check autosave //
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
      
    // Check permissions //
  	if (in_array($_POST['post_type'], $options['address_fields'])) {
        if (!current_user_can('edit_page', $post->ID)) {
            return;
        }
    } else { 
    	if (!current_user_can('edit_post', $post->ID)) {
        return;
    	}
 	}
 	
 	if (!in_array($_POST['post_type'], $options['address_fields']))
	return;
	
	$wpdb->update( $wpdb->prefix . 'places_locator', array('post_title' => $_POST['post_title'], 'phone' => $_POST[$wppl_meta_box['fields'][6]['id']], 'fax' => $_POST[$wppl_meta_box['fields'][7]['id']], 'email' => $_POST[$wppl_meta_box['fields'][8]['id']], 'website' => $_POST[$wppl_meta_box['fields'][9]['id']]), array('post_id' => $_POST[ID]));
	
    foreach ($wppl_meta_box['fields'] as $field) {	
        $old = get_post_meta($post->ID, $field['id'], true);
        $new = $_POST[$field['id']];
 
        if ($new && $new != $old) {
            update_post_meta($post->ID, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post->ID, $field['id'], $old);
        }
    }

    $address_arg = $_POST[$wppl_meta_box['fields'][0]['id']] . " " . $_POST[$wppl_meta_box['fields'][1]['id']] . " " . $_POST[$wppl_meta_box['fields'][2]['id']] . " " . $_POST[$wppl_meta_box['fields'][3]['id']] . " " . $_POST[$wppl_meta_box['fields'][4]['id']] . " " . $_POST[$wppl_meta_box['fields'][5]['id']];    
    $old_address_arg = get_post_meta($post->ID, '_wppl_address' , true); 
	
	$c_lat = str_replace(" ","", $_POST[$wppl_meta_box_lat_long['fields'][0]['id']]);
	$c_long = str_replace(" ","", $_POST[$wppl_meta_box_lat_long['fields'][1]['id']]);
	
	// check if address has changed - for existing post or if empty for a new post //
	if ( ($old_address_arg != $address_arg) || ( (!empty($c_lat)) && (!empty($c_long)) )) {
		
		if (!empty($c_lat) && !empty($c_long)) {
			
			$got_address = ConvertToAddress($c_lat,$c_long);
		
			global $c_zip, $c_address, $c_state, $c_city, $c_country, $c_street;
		
			if(!empty($c_zip) && ($c_state) && ($c_city)) {
				update_post_meta($post->ID, '_wppl_lat', $c_lat);
				update_post_meta($post->ID, '_wppl_long' , $c_long);
				delete_post_meta($post->ID, '_wppl_enter_lat');
				delete_post_meta($post->ID, '_wppl_enter_long');
				update_post_meta($post->ID, '_wppl_street', $c_street);
				update_post_meta($post->ID, '_wppl_city', $c_city);
				update_post_meta($post->ID, '_wppl_state', $c_state);
				update_post_meta($post->ID, '_wppl_zipcode', $c_zip);
				update_post_meta($post->ID, '_wppl_country', $c_country);
				delete_post_meta($post->ID, '_wppl_apt');	
				update_post_meta($post->ID, '_wppl_address' , $c_address);
				$wpdb->replace( $wpdb->prefix . 'places_locator', array( 'post_id' => $post->ID, 'post_type' => $_POST['post_type'] ,'lat' => $c_lat , 'long' => $c_long, 'post_title' => $_POST['post_title'], 'post_status' => $_POST['post_status'], "city" => $c_city, "state" => $c_state, "zipcode" => $c_zip, "address" => $c_address, 'phone' => $_POST[$wppl_meta_box['fields'][6]['id']], 'fax' => $_POST[$wppl_meta_box['fields'][7]['id']], 'email' => $_POST[$wppl_meta_box['fields'][8]['id']], 'website' => $_POST[$wppl_meta_box['fields'][9]['id']]));
		
			} else {
				empty_results($options);
			}
		} else {
		
    		$got_coords = ConvertToCoords($address_arg);		
			global $wpdb, $lat, $long;	
		
			// check if lat/long was returned from google - if they are empty means the address is not exist //
			if(!empty($lat) || ($long)) {
	
				// enter new lat/long into custom fields of post //
				update_post_meta($post->ID, '_wppl_lat', $lat);
				update_post_meta($post->ID, '_wppl_long' , $long);
		
				// update new address in custom field //
				update_post_meta($post->ID, '_wppl_address' , $address_arg);
		
				$wpdb->replace( $wpdb->prefix . 'places_locator', array( 'post_id' => $post->ID, 'post_type' => $_POST['post_type'] ,'lat' => $lat , 'long' => $long, 'post_title' => $_POST['post_title'], 'post_status' => $_POST['post_status'], "city" => $_POST[$wppl_meta_box['fields'][2]['id']], "state" => $_POST[$wppl_meta_box['fields'][3]['id']], "zipcode" => $_POST[$wppl_meta_box['fields'][4]['id']], "address" => $address_arg, 'phone' => $_POST[$wppl_meta_box['fields'][6]['id']], 'fax' => $_POST[$wppl_meta_box['fields'][7]['id']], 'email' => $_POST[$wppl_meta_box['fields'][8]['id']], 'website' => $_POST[$wppl_meta_box['fields'][9]['id']]));
			} else {
				empty_results($options);
			}
		}
	}
}

function empty_results($options) {
	global $wpdb, $post;
	if ($options['mandatory_address'] == 1) {
		remove_action('save_post', 'wppl_save_data');
   	 	// call wp_update_post update, which calls save_post //
    	wp_update_post(array('ID' => $post->ID, 'post_status' => 'draft'));
   		 // re-hook this function //
		add_action('save_post', 'wppl_save_data');
	}
				
	delete_post_meta($post->ID, '_wppl_lat');
	delete_post_meta($post->ID, '_wppl_long');
	delete_post_meta($post->ID, '_wppl_address');
	delete_post_meta($post->ID, '_wppl_street');
	delete_post_meta($post->ID, '_wppl_apt');
	delete_post_meta($post->ID, '_wppl_city');
	delete_post_meta($post->ID, '_wppl_state');
	delete_post_meta($post->ID, '_wppl_zipcode');
	delete_post_meta($post->ID, '_wppl_country');
	
	//$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "places_locator WHERE post_id=%d",$post_id));
	$wpdb->update( $wpdb->prefix . 'places_locator', array('lat' => '' , 'long' => '', 'address' => '', 'city' => '', 'state' => ''),array('post_id' => $_POST[ID]));
	add_filter('redirect_post_location', 'wppl_redirect_post_location_filter', 99);
}

function wppl_redirect_post_location_filter($location) {
  remove_filter('redirect_post_location', __FUNCTION__, 99);
  $location = add_query_arg('message', 99, $location);
  return $location;
}

add_filter('post_updated_messages', 'wppl_post_updated_messages_filter');
function wppl_post_updated_messages_filter($messages) {
	global $post;
		$options = get_option('wppl_fields'); 
		if ($options['mandatory_address'] == 1) {
  			$messages['post'][99] = '<div id="error-message" style="color: #E33434;font-size: 14px;">Publish not allowed - the address you entered does not exist or some of the address fields are empty. please check the address fields again.</div>';
  		} else {
  			$messages['post'][99] = '<div id="error-message" style="color: #E33434;font-size: 14px;">The address you entered does not exist or some of the address fields are empty. please check the address fields again.</div>';
  		}
  		return $messages;
}

?>