<?php
if ( !defined( 'ABSPATH' ) ) exit;

function gmw_site_installation() {
	global $wpdb;
	$gmw_sql = array();
	
	$gmw_sql[] = "CREATE TABLE wppl_friends_locator (
		`member_id` 		bigint(30) NOT NULL,
		`lat` 				varchar(255) NOT NULL ,
		`long` 				varchar(255) NOT NULL ,
		`street` 			varchar(255) NOT NULL ,
		`apt` 				varchar(255) NOT NULL ,
		`city` 				varchar(255) NOT NULL ,
		`state` 			varchar(255) NOT NULL ,
		`state_long` 		varchar(255) NOT NULL ,
		`zipcode` 			varchar(255) NOT NULL ,
		`country` 			varchar(255) NOT NULL ,
		`country_long` 		varchar(255) NOT NULL ,
		`address` 			varchar(255) NOT NULL ,
		`formatted_address` varchar(255) NOT NULL ,
		`map_icon` 			varchar(255) NOT NULL ,
		UNIQUE KEY id (member_id)
		
	)	DEFAULT CHARSET=utf8;";
    
   	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   	
   	dbDelta($gmw_sql);
   	
	
	$old_table = $wpdb->prefix . 'wppl_friends_locator';
   	$check_table_rows = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $old_table );
   	
   	if($check_table_rows > 0) {
		$wpdb->get_results('INSERT INTO wppl_friends_locator SELECT * FROM ' . $old_table);
		$wpdb->get_results('RENAME table '  . $old_table. ' to old_' . $old_table);  
	} elseif ($check_table_rows == 0) {
		$wpdb->get_results('RENAME table '  . $old_table. ' to old_' . $old_table); 
	}	
}
register_activation_hook(__FILE__,'gmw_site_installation');

function gmw_installation() {
	global $wpdb;
	
   	$gmw_sql = "CREATE TABLE {$wpdb->prefix}places_locator (
  		`post_id` 			bigint(30) NOT NULL,
  		`feature`			tinyint NOT NULL default '0',
  		`post_status` 		varchar(20) NOT NULL ,
  		`post_type` 		varchar(20) default 'post',
  		`post_title` 		TEXT,
  		`lat` 				varchar(255) NOT NULL ,
  		`long` 				varchar(255) NOT NULL ,
  		`street` 			varchar(255) NOT NULL ,
  		`apt` 				varchar(255) NOT NULL ,
  		`city` 				varchar(255) NOT NULL ,
  		`state` 			varchar(255) NOT NULL ,
  		`state_long` 		varchar(255) NOT NULL ,
  		`zipcode` 			varchar(255) NOT NULL ,
  		`country` 			varchar(255) NOT NULL ,
  		`country_long` 		varchar(255) NOT NULL ,
  		`address` 			varchar(255) NOT NULL ,
  		`formatted_address` varchar(255) NOT NULL ,
  		`phone` 			varchar(255) NOT NULL ,
  		`fax` 				varchar(255) NOT NULL ,
  		`email` 			varchar(255) NOT NULL ,
  		`website` 			varchar(255) NOT NULL ,
  		`map_icon`			varchar(255) NOT NULL ,
  		UNIQUE KEY id (post_id)
  		
	)	DEFAULT CHARSET=utf8;";
	
   	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   	
   	dbDelta($gmw_sql);
   		
}
register_activation_hook(__FILE__,'gmw_installation');