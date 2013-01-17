<?php

define('GMW_PT_URL', GMW_URL . '/plugins/post-types/');
define('GMW_PT_PATH', GMW_PATH . '/plugins/post-types/');

if ( is_admin() ) include_once GMW_PT_PATH . 'admin/admin-addons.php';

if (!isset($wppl_on['post_types']) || $wppl_on['post_types'] != 1 ) return;

if ( is_admin() ) {
	include_once GMW_PT_PATH . 'admin/admin-settings.php';
	include_once GMW_PT_PATH . 'admin/help-shortcodes.php';
	
	//// Include javascripts, jquery and styles only in choosen post types admin area ////
	function wppl_admin_page() {
		global $wppl_options, $post_type, $wppl_on;
		
		if( isset( $wppl_options['address_fields'] ) && (in_array($post_type, $wppl_options['address_fields']) ) ) {
			wp_enqueue_style( 'admin-style', GMW_PT_URL . 'admin/css/style-admin.css', array(),false,false);
			wp_enqueue_script( 'google-api-key', 'http://maps.googleapis.com/maps/api/js?key='.$wppl_options['google_api'].'&sensor=false&region='.$wppl_options['country_code'],array(),false); 
			wp_enqueue_script('wppl-locate-admin', GMW_PT_URL .'admin/js/locate-admin.js',array(),false,true);
			wp_enqueue_script( 'jquery-ui-autocomplete');
			wp_enqueue_script( 'wppl-address-picker', GMW_PT_URL .'admin/js/jquery.ui.addresspicker.js',array(),false,true);
		}
	}
	add_action( 'admin_print_scripts-post-new.php', 'wppl_admin_page', 11 );
	add_action( 'admin_print_scripts-post.php', 'wppl_admin_page', 11 );
	
	//// UPLOAD METABOXES ONLY ON NECESSARY PAGES ////
	if (in_array( basename($_SERVER['PHP_SELF']), array( 'post-new.php', 'post.php','page.php','page-new' ) ) ) {
		include_once GMW_PT_PATH . 'admin/metaboxes.php'; 
	}
} else {	 
	include_once GMW_PT_PATH . 'functions.php';
	include_once GMW_PT_PATH . 'shortcodes.php';
}
	
function wppl_register_pt_scripts() {	
	wp_register_script( 'wppl-map', GMW_PT_URL . 'js/map.js', array(),false,true );
	wp_register_script( 'wppl-small-maps', GMW_PT_URL . 'js/single-maps.js', array(),false,true );
	wp_register_script( 'wppl-infobox', GMW_PT_URL . 'js/infobox.js' ,array(),false,true );
	wp_register_script( 'wppl-sl-map', GMW_PT_URL . 'js/single-location-map.js' ,array(),false,true );
}
add_action( 'wp_enqueue_scripts', 'wppl_register_pt_scripts' );
	