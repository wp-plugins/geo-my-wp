<?php
/*
Plugin Name: WP Places Locator
Plugin URI: http://www.wpplaceslocator.com 
Description:  Advence search Post types by address within a certain radius.
Author: Eyal Fitoussi
Version: 1.3
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
include_once 'registered.php';
include_once 'metaboxes.php';

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
  `country` varchar(255) NOT NULL ,
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

// REGISTER STYLESHEET AND JAVASCRIPTS IN THE FRONT END//
function wppl_register_scripts() {
	$options = get_option('wppl_fields');
	wp_enqueue_style( 'wppl-style', plugins_url('css/style.css', __FILE__) );
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css');
	wp_enqueue_script( 'google-api-key', 'http://maps.googleapis.com/maps/api/js?key='.$options['google_api'].'&sensor=false&region='.$options['country_code'],array(),false); 
    wp_register_script( 'wppl-map',  plugins_url('js/map.js', __FILE__),array(),false,true );
    wp_enqueue_script( 'wppl-locate',  plugins_url('js/locate.js', __FILE__),array(),false,true );
    wp_enqueue_script('thickbox', null,  array('jquery'));    	
	if ($options['front_autocomplete'] == 1 ) {
		wp_enqueue_script( 'jquery-172', plugins_url('js/jquery-1.7.2.min.js', __FILE__),array(),false);
   	 	wp_enqueue_script( 'jquery-187', plugins_url('js/jquery-ui-1.8.7.min.js', __FILE__),array(),false);
   	 	wp_enqueue_script( 'wppl-autocomplete',  plugins_url('js/autocomplete-front.js', __FILE__),array(),true );
   	 }
}
add_action( 'wp_enqueue_scripts', 'wppl_register_scripts' );

//// Include javascripts, jquery and styles only in choosen post types admin area ////
function wppl_admin_page() {
	$options = get_option('wppl_fields');
    global $post_type;
    if( (in_array($post_type, $options['address_fields'])) ) {
    	wp_enqueue_style( 'admin-style', plugins_url('css/style-admin.css', __FILE__),array(),false,false);
    	wp_enqueue_script( 'google-api-key', 'http://maps.googleapis.com/maps/api/js?key='.$options['google_api'].'&sensor=false&region='.$options['country_code'],array(),false); 
    	wp_enqueue_script('my-custom-script',plugins_url('js/locate-admin.js', __FILE__),array(),false,true);
    	if ($options['java_conflict'] == 1) {
    		wp_enqueue_script( 'jquery', plugins_url('js/jquery-1.7.2.min.js', __FILE__),array(),false,true);
   		} else {
   			wp_enqueue_script( 'jquery-172', plugins_url('js/jquery-1.7.2.min.js', __FILE__),array(),false,true);
    	}
    	wp_enqueue_script( 'jquery-187', plugins_url('js/jquery-ui-1.8.7.min.js', __FILE__),array(),false,true);
    	wp_enqueue_script( 'jquery-address-picker', plugins_url('js/jquery.ui.addresspicker.js', __FILE__),array(),false,true);	
	}
}
add_action( 'admin_print_scripts-post-new.php', 'wppl_admin_page', 11 );
add_action( 'admin_print_scripts-post.php', 'wppl_admin_page', 11 );


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
	global $wpdb, $post;
	$wpdb->query(
		$wpdb->prepare( 
			"DELETE FROM " . $wpdb->prefix . "places_locator WHERE post_id=%d",$post->ID
		)
	);
}
add_action('before_delete_post', 'delete_address_map_rows');

?>