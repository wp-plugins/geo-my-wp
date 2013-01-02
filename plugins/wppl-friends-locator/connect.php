<?php

define('GMW_FL_URL', GMW_URL . '/plugins/wppl-friends-locator/');
define('GMW_FL_PATH', GMW_PATH . '/plugins/wppl-friends-locator/');

if ( is_admin() )include_once GMW_FL_PATH . 'admin/admin-addons.php';

if (!isset($wppl_on['friends']) || $wppl_on['friends'] != 1 ) return;

if ( is_admin() ) {
	include_once GMW_FL_PATH . 'admin/admin-settings.php';
	include_once GMW_FL_PATH . 'admin/admin-functions.php';
	include_once GMW_FL_PATH . 'admin/help-shortcodes.php';
} else {
	include_once GMW_FL_PATH . 'functions.php';
}

include_once GMW_FL_PATH . 'widgets.php';
include_once GMW_FL_PATH . 'wppl-friends-locator.php';

function wppl_register_fl_scripts() {	
	wp_register_script( 'wppl-friends-map', GMW_FL_URL . 'js/main-map.js',array(),false,true );
	wp_register_script( 'wppl-fl', GMW_FL_URL . 'js/fl.js',array(),false,true );
	wp_register_script( 'wppl-member-map', GMW_FL_URL . 'js/single-member-map.js',array(),false,true );
}
add_action( 'wp_enqueue_scripts', 'wppl_register_fl_scripts' );
	