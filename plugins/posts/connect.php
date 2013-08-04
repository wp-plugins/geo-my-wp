<?php

define('GMW_PT_URL', GMW_URL . '/plugins/posts/');
define('GMW_PT_PATH', GMW_PATH . '/plugins/posts/');

if ( is_admin() ) include_once GMW_PT_PATH . 'admin/admin-addons.php';

if (!isset($wppl_on['post_types']) ) return;

if ( is_admin() ) {
	
	include_once GMW_PT_PATH . 'admin/admin-settings.php';
	function gmw_register_pt_taxonomies_script() {
		wp_register_script( 'gmw-pt-shortcodes-categories', GMW_PT_URL . 'admin/js/admin-settings.js', array(),false,true );
	}
	add_action( 'admin_enqueue_scripts', 'gmw_register_pt_taxonomies_script' );
	
	function gmw_pt_shortcode_page($shortcode_page, $wppl_on, $options_r, $wppl_options, $posts, $pages_s) {
		if ( isset($_GET['form_type']) && $_GET['form_type'] == 'posts' ) :
			$shortcode_page = include_once GMW_PT_PATH . 'admin/edit-shortcode.php';
			return $shortcode_page;
		endif;
	}
	
	add_filter('gmw_edit_shortcode_page', 'gmw_pt_shortcode_page', 10, 6 );
	
	//// Include javascripts, jquery and styles only in choosen post types admin area ////
	function gmw_register_admin_location_scripts() {
		global $wppl_options, $post_type, $wppl_on;
		
		if( isset( $wppl_options['address_fields'] ) && !empty( $wppl_options['address_fields'] ) && ( in_array( $post_type, $wppl_options['address_fields'] ) ) ) {
			wp_register_style( 'admin-style', GMW_PT_URL . 'admin/css/style-admin.css', array(),false,false);
			wp_enqueue_style('admin-style');
			wp_register_script( 'google-api-key', 'http://maps.googleapis.com/maps/api/js?key='.$wppl_options['google_api'].'&sensor=false&region='.$wppl_options['country_code'],array(),false); 
			wp_enqueue_script('google-api-key');
			wp_enqueue_script( 'jquery-ui-autocomplete');
			wp_register_script( 'wppl-address-picker', GMW_PT_URL .'admin/js/jquery.ui.addresspicker.js',array(),false,true);
			wp_register_script('wppl-locate-admin', GMW_PT_URL .'admin/js/locate-admin.js',array(),false,true);
			wp_enqueue_script('wppl-locate-admin');
			wp_enqueue_script( 'wppl-address-picker');
			if ( isset( $wppl_options['mandatory_address'] ) )
				$mandatoryFields = true;
			else 
				$mandatoryFields = false;
			wp_localize_script('wppl-locate-admin', 'mandatoryAddress', $mandatoryFields);
		}
	}
	add_action( 'admin_print_scripts-post-new.php', 'gmw_register_admin_location_scripts', 11 );
	add_action( 'admin_print_scripts-post.php', 'gmw_register_admin_location_scripts', 11 );
	
	/* UPLOAD METABOXES ONLY ON NECESSARY PAGES */
	if (in_array( basename($_SERVER['PHP_SELF']), array( 'post-new.php', 'post.php','page.php','page-new' ) ) ) {
		include_once GMW_PT_PATH . 'admin/metaboxes.php'; 
	}
} else {	 
		
	function gmw_pt_results_shortcode_start($gmw, $gmw_options) {
		include_once GMW_PT_PATH. 'includes/search-functions.php';
	}
	add_action('gmw_posts_results_shortcode_start', 'gmw_pt_results_shortcode_start', 10 , 2);
	add_action('gmw_posts_main_shortcode_start', 'gmw_pt_results_shortcode_start', 10 , 2);
	//add_action('gmw_posts_before_custom_search_form', 'gmw_pt_results_shortcode_start', 10 , 2);
		
	include_once GMW_PT_PATH . 'includes/functions.php';
	/*
	 * Query all posts and save in transient. later will be used when need to show all or random locations
	*/
	if( false === get_transient('gmwAllLocations') ) {
		add_action('wp','gmw_all_posts_transient');
	}
	function wppl_register_pt_scripts() {
		wp_register_script( 'gmw-pt-map', GMW_PT_URL . 'js/map.js', array(),false,true );
		wp_register_script( 'wppl-sl-map', GMW_PT_URL . 'js/single-location-map.js' ,array(),false,true );
	}
	add_action( 'gmw_register_cssjs_front_end', 'wppl_register_pt_scripts', 10 );
	
}
	

	