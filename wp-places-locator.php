<?php
/*
Plugin Name: WP Places Locator
Plugin URI: http://www.wpplaceslocator.com 
Description:  Advence search Post types by address within a certain radius.
Author: Eyal Fitoussi
Version: 1.1.2 
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
  `address` varchar(255) NOT NULL ,
  UNIQUE KEY id (post_id)
    );";
    
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
   add_option("places_locator_db_version", $places_locator_db_version);
}

register_activation_hook(__FILE__,'places_locator_install');

//////////////////////////////
include_once 'get-places.php';

$prefix = '_wppl_';
$options = get_option('wppl_fields');

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
      /*  
        array(
            'name' => 'Phone Number',
            'desc' => '',
            'id' => $prefix . 'phone',
            'type' => 'text',
            'std' => ''
        ),
        */
    )
);

add_action('admin_menu', 'wppl_add_box');
 
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
        
      /*  echo '<tr>';
 	 		echo '<th>Map ID</th><td>' . get_post_meta($post->ID, 'agm_map_created', TRUE) . '</td></th>';
        echo '</tr>'; */
        
    echo '</table>';
}

//////////////  EVERY NEW POST OR WHEN POST IS BEING UPDATED ////////////////////
//// CREATE MAP, LATITUDE, LONGITUDE AND SAVE DATA INTO OUR LOCATIONS TABLE ////
///  DATA SAVED - POST ID, POST TYPE, POST STATUS , POST TITLE , LATITUDE, LONGITUDE AND ADDRESS     ////
//////////////////////////////////////////////////////////////////////////////

add_action( 'save_post' , 'wppl_save_data'); 

function wppl_save_data($post_id) {
    global $wppl_meta_box, $wpdb;
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
	
	$wpdb->update( 'wp_places_locator', array('post_title' => $_POST['post_title']), array('post_id' => $_POST[ID]));
	
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
	
	//echo $address_arg;
	// check if address has changed - for existing post or if empty for a new post //
	if ($old_address_arg != $address_arg) {
		
    	$got_coords = ConvertToCoords($address_arg);		
		global $wpdb, $lat, $long;	
		
		// check if lat/long was returned from google - if they are empty means the address is not exist //
		if(!empty($lat) || ($long)) {
	
		// enter new lat/long into custom fields of post //
		update_post_meta($post->ID, '_wppl_lat', $lat);
		update_post_meta($post->ID, '_wppl_long' , $long);
		
		// update new address in custom field //
		update_post_meta($post->ID, '_wppl_address' , $address_arg);
		
		$wpdb->replace( 'wp_places_locator', array( 'post_id' => $post->ID, 'post_type' => $_POST['post_type'] ,'lat' => $lat , 'long' => $long, 'post_title' => $_POST['post_title'], 'post_status' => $_POST['post_status'], "address" => $address_arg));
		} else {
		
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
		$wpdb->query( $wpdb->prepare( "DELETE FROM wp_places_locator WHERE post_id=%d",$post_id));	
		add_filter('redirect_post_location', 'wppl_redirect_post_location_filter', 99);
		}
	}
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