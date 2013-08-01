<?php
/*
Plugin Name: GEO my WP
Plugin URI: http://www.geomywp.com 
Description: Add location to any post types, pages or members (using Buddypress) and create an advance proximity search.
Version: 2.0
Author: Eyal Fitoussi
Author URI: http://www.geomywp.com
License: GPLv2
Text Domain: GMW
Domain Path: /languages/

*/

$gmw_current_version = '2.0';

$wppl_on = get_option('wppl_plugins');
$wppl_options = get_option('wppl_fields');

define(	'GMW_URL', plugins_url() . '/geo-my-wp');
define(	'GMW_PATH', plugin_dir_path(dirname(__FILE__)) . 'geo-my-wp');
define(	'GMW_AJAX' ,get_bloginfo('wpurl') .'/wp-admin/admin-ajax.php');
define( 'GMW_REMOTE_SITE_URL', 'http://geomywp.wpengine.com' ); 
define( 'GMW_PREMIUM_PLUGIN_NAME', 'GEO my WP Premium' ); 

do_action('gmw_define_name_constant');

/*
 * Load language
 */
function gmw_load_text_domain() {
	load_plugin_textdomain('GMW', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
}
add_action('init', 'gmw_load_text_domain');

if (is_admin() ) {
	if ( get_option( "gmw_version" ) == '' || get_option( "gmw_version" ) != $gmw_current_version ) {
		include_once GMW_PATH . '/admin/db.php';
		update_option( "gmw_version", $gmw_current_version );
		global $blog_id;
		if($blog_id == 1) {
			gmw_site_installation();
			gmw_installation();
		} else gmw_installation();
		
	}
}

function wppl_check_bp_blog() {	
	if (is_admin() ) {	
		global $gmw_bp_comp, $blog_id, $switched;
		if ($blog_id != BP_ROOT_BLOG) {
    		switch_to_blog(BP_ROOT_BLOG);
    		$gmw_bp_comp = get_option( 'bp-active-components' );
    		restore_current_blog();
    	} else {
    		$gmw_bp_comp = get_option( 'bp-active-components' );
    	}
	}
}
add_action('bp_init','wppl_check_bp_blog');

//if( is_multisite() ) $wppl_site_options = get_site_option('wppl_site_options');

// REGISTER STYLESHEET AND JAVASCRIPTS IN THE FRONT END//
function gmw_register_cssjs_front_end() {
	$wppl_options = get_option('wppl_fields');
	$wppl_on = get_option('wppl_plugins');
	
	wp_register_style( 'wppl-style', plugins_url('css/style.css', __FILE__) );
	wp_enqueue_style( 'wppl-style' );
	
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css');
	
	//if ( isset($wppl_options['google_api']) ) $google_api = $wppl_options['google_api']; else $google_api = false;
	wp_register_script( 'google-maps', 'http://maps.googleapis.com/maps/api/js?key='.$wppl_options['google_api'].'&sensor=false&libraries=places&region='.$wppl_options['country_code'],array(),false);
	wp_enqueue_script( 'google-maps');
    wp_register_script( 'gmw-locate',  plugins_url('js/locate.js', __FILE__),array(),false,true );
    wp_enqueue_script( 'gmw-locate' );
    
    if ( isset($wppl_options['auto_locate']) ) {
    	wp_localize_script( 'gmw-locate', 'autoLocate', $wppl_options['auto_locate']);
	}else {
    	wp_localize_script( 'gmw-locate', 'autoLocate', 'false');
    }
    wp_enqueue_script('thickbox', null,  array('jquery')); 
    wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    wp_enqueue_script('jquery-ui-datepicker');
    
    do_action('gmw_register_cssjs_front_end', $wppl_options, $wppl_on);
}
add_action( 'wp_enqueue_scripts', 'gmw_register_cssjs_front_end' );

include_once GMW_PATH . '/includes/functions.php';
include_once GMW_PATH . '/includes/widgets.php';

if (is_admin() ) {
	include_once GMW_PATH . '/admin/admin-settings.php';
	include_once GMW_PATH  . '/admin/gmw-premium-updater.php';
}

// include add-ons
include_once GMW_PATH .'/plugins/posts/connect.php';
include_once GMW_PATH .'/plugins/friends/connect.php';
 
?>