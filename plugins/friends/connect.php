<?php

/*
 * Define Globals
 */
define ( 'GMW_FL_URL', GMW_URL . '/plugins/friends/');
define ( 'GMW_FL_PATH', GMW_PATH . '/plugins/friends/');
define ( 'GMW_LOCATION_SLUG', 'location' );
define ( 'GMW_LOCATION_PLUGIN_NAME', 'friends' );
define ( 'GMW_LOCATION_PLUGIN_DIR', GMW_PATH . '/plugins/friends' );
define ( 'GMW_LOCATION_PLUGIN_JS', GMW_LOCATION_PLUGIN_DIR . '/includes/js' );
define ( 'GMW_LOCATION_PLUGIN_CSS', GMW_LOCATION_PLUGIN_DIR . '/includes/css' );

require( GMW_LOCATION_PLUGIN_DIR . '/includes/admin/admin-addons.php' );

if (!isset($wppl_on['friends']) || $wppl_on['friends'] != 1 ) return;

function gmw_location_init() {
	global $bp;
	
	/*
	 * include search functions when search is being performed 
	 */
	function gmw_fl_results_shortcode_start($gmw, $gmw_options) {
		include_once GMW_FL_PATH. 'includes/gmw-location-search-functions.php';
	}
	add_action('gmw_friends_results_shortcode_start', 'gmw_fl_results_shortcode_start', 10 , 2);
	add_action('gmw_friends_main_shortcode_search_form', 'gmw_fl_results_shortcode_start', 10 , 2);
	
	require( GMW_LOCATION_PLUGIN_DIR . '/includes/gmw-location-component.php' );
	
	if ( is_admin() ) :
	
		include_once GMW_LOCATION_PLUGIN_DIR . '/includes/admin/admin-settings.php';
		include_once GMW_LOCATION_PLUGIN_DIR . '/includes/admin/admin-functions.php';
		
		/*
		 * include shortcode page when editing a shortcode
		*/
		function gmw_fl_shortcode_page($shortcode_page, $wppl_on, $options_r, $wppl_options, $posts, $pages_s) {
			
			if ( isset($_GET['form_type']) && $_GET['form_type'] == 'friends' ) :
			$shortcode_page = require( GMW_LOCATION_PLUGIN_DIR . '/includes/admin/edit-shortcode.php' );
			return $shortcode_page;
			endif;
			
		}
		
		add_filter('gmw_edit_shortcode_page', 'gmw_fl_shortcode_page', 10, 6 );
		
	endif;

}
add_action('bp_include', 'gmw_location_init');




	