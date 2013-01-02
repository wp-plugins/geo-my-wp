<?php
/*
Plugin Name: GEO my WP
Plugin URI: http://www.geomywp.com 
Description: Add location to any post types, pages or members (using Buddypress) and create an advance proximity search.
Author: Eyal Fitoussi
Version: 1.7
Author URI: http://www.geomywp.com 

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

$gmw_current_version = '1.7';
global $gmw_bp_comp;
$gmw_bp_comp = get_option( 'bp-active-components' );

define('GMW_URL', plugins_url() . '/geo-my-wp');
define('GMW_PATH', plugin_dir_path(dirname(__FILE__)) . 'geo-my-wp');
define('GMW_AJAX' ,get_bloginfo('wpurl') .'/wp-admin/admin-ajax.php');

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

$wppl_on = get_option('wppl_plugins');
$wppl_options = get_option('wppl_fields');
if( is_multisite() ) $wppl_site_options = get_site_option('wppl_site_options');

// REGISTER STYLESHEET AND JAVASCRIPTS IN THE FRONT END//
function wppl_register_scripts($wppl_options) {	
	wp_enqueue_style( 'wppl-style', plugins_url('css/style.css', __FILE__) );
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css');
	wp_enqueue_script( 'google-api-key', 'http://maps.googleapis.com/maps/api/js?key='.$wppl_options['google_api'].'&sensor=false&libraries=places&region='.$wppl_options['country_code'],array(),false);
    wp_register_script( 'wppl-maxmind', 'http://j.maxmind.com/app/geoip.js',array(),false); 
    wp_enqueue_script( 'wppl-locate',  plugins_url('js/locate.js', __FILE__),array(),false,true );
    wp_enqueue_script('thickbox', null,  array('jquery'));  
}
add_action( 'wp_enqueue_scripts', 'wppl_register_scripts' );

include_once GMW_PATH . '/functions.php';

if ( !is_admin() ) {
	include_once GMW_PATH . '/shortcodes.php';
}
include_once GMW_PATH . '/widgets.php';

if (is_admin() ) {
	include_once GMW_PATH . '/admin/admin-settings.php';
	//include_once 'admin/categories.php';
}

// include add-ons
include_once GMW_PATH .'/plugins/post-types/connect.php';
include_once GMW_PATH .'/plugins/wppl-friends-locator/connect.php';

function wppl_theme_color($wppl_options) {
    if($wppl_options['use_theme_color']) {
    $color = $wppl_options['theme_color'];
   		echo "<style> #wppl-output-wrapper .wppl-pagination-wrapper h2.wppl-h2 { color: $color; } </style>"; 
    	echo "<style> .wppl-single-result .wppl-h3 a:link, .wppl-single-result .wppl-h3 a:visited { color: $color; } </style>";
    	echo "<style> .wppl-single-result .wppl-address { color: $color; } </style>";
    	echo "<style> .wppl-single-result  .wppl-get-directions a:link, .wppl-single-result .wppl-get-directions a:visited { color: $color; } </style>";
    	echo "<style>  #wppl-output-wrapper .wppl-website a:link, #wppl-output-wrapper .wppl-website a:visited  { color: $color; } </style>";
    	echo "<style> .near-title-holder a:link, .near-title-holder a:visited  { color: $color; } </style>";
    	echo "<style> #wppl-output-wrapper .wppl-pagination a:link, #wppl-output-wrapper .wppl-pagination a:visited { color: $color; } </style>";
    	echo "<style> .wppl-excerpt a:link, .wppl-excerpt a:visited { color: $color; } </style>";
    	echo "<style> .wppl-single-wrapper div span { color: $color; } </style>";
    	echo "<style> .wppl-extra-info-wrapper a:link, .wppl-extra-info-wrapper a:visited { color: $color; } </style>";
    	echo "<style> .popup-additional-info a:link, .popup-additional-info a:visited { color: $color; } </style>";
    	echo "<style> .wppl-slider-single-result .wppl-title-holder a:link, .wppl-slider-single-result .wppl-title-holder a:visited { color: $color; } </style>";
    	echo "<style> .wppl-featured-single-result .wppl-title-holder a:link, .wppl-featured-single-result .wppl-title-holder a:visited { color: $color; } </style>";
		}
		echo '<script> autoLocate= '.json_encode($wppl_options['auto_locate']),'; </script>';
		echo '<script> locateMessage= '.json_encode($wppl_options['locate_message']),'; </script>';
	}
add_action( 'wp_head', 'wppl_theme_color' );

?>