<?php
$wppl_options = get_option('wppl_fields');
if ( !isset( $wppl_options['address_fields'] ) || empty( $wppl_options['address_fields'] ) ) $wppl_options['address_fields'] = false;

$prefix = '_wppl_';
$wppl_meta_box = array(
    'id' => 'wppl-meta-box',
    'title' => __('GMW - Please enter the full address or the latitude / longitude of the location.','GMW'),
    'pages' => $wppl_options['address_fields'],
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(
        array(
            'name' => __('Street','GMW'),
            'desc' => '',
            'id' => $prefix . 'street',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => __('Apt/Suit','GMW'),
            'desc' => '',
            'id' => $prefix . 'apt',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => __('City','GMW'),
            'desc' => '',
            'id' => $prefix . 'city',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => __('State','GMW'),
            'desc' => '',
            'id' => $prefix . 'state',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => __('Zipcode','GMW'),
            'desc' => '',
            'id' => $prefix . 'zipcode',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => __('Country','GMW'),
            'desc' => '',
            'id' => $prefix . 'country',
            'type' => 'text',
            'std' => ''
        ),
        
        array(
            'name' => __('Phone Number','GMW'),
            'desc' => '',
            'id' => $prefix . 'phone',
            'type' => 'text',
            'std' => ''
        ),
        
        array(
            'name' => __('fax Number','GMW'),
            'desc' => '',
            'id' => $prefix . 'fax',
            'type' => 'text',
            'std' => ''
        ),
        
        array(
            'name' => __('Email Address','GMW'),
            'desc' => '',
            'id' => $prefix . 'email',
            'type' => 'text',
            'std' => ''
        ),
        
        array(
            'name' => __('Website','GMW'),
            'desc' => 'Ex: www.website.com',
            'id' => $prefix . 'website',
            'type' => 'text',
            'std' => ''
        ),
        
       
        array(
            'name' => __('Latitude','GMW'),
            'desc' => '',
            'id' => $prefix . 'enter_lat',
            'type' => 'text-right',
            'std' => ''
        ),
        
         array(
            'name' => __('Longitude','GMW'),
            'desc' => '',
            'id' => $prefix . 'enter_long',
            'type' => 'text-right',
            'std' => ''
        ),
         array(
            'name' => __('Latitude','GMW'),
            'desc' => '',
            'id' => $prefix . 'lat',
            'type' => 'text-disable',
            'std' => ''
        ),
         array(
            'name' => __('Longitude','GMW'),
            'desc' => '',
            'id' => $prefix . 'long',
            'type' => 'text-disable',
            'std' => ''
        ),
        array(
            'name' => __('Full Address','GMW'),
            'desc' => '',
            'id' => $prefix . 'address',
            'type' => 'text-disable',
            'std' => ''
        ),
         array(
            'name' => __('Days & Hours','GMW'),
            'desc' => '',
            'id' => $prefix . 'days_hours',
            'type' => 'text',
            'std' => ''
        )
    )
);

// Add meta box //
function wppl_add_box() {
    global $wppl_meta_box;
    $wppl_options = get_option('wppl_fields');
    	if ( isset($wppl_options['address_fields']) && !empty( $wppl_options['address_fields'] ) ) {
 		foreach ($wppl_meta_box['pages'] as $page) {
        	add_meta_box($wppl_meta_box['id'], $wppl_meta_box['title'], 'wppl_show_box', $page, $wppl_meta_box['context'], $wppl_meta_box['priority']);
   		}
    }
}
add_action('admin_menu', 'wppl_add_box');

// Callback function to show fields in meta box //
function wppl_show_box() {
    global $wppl_meta_box, $post;
    $wppl_options = get_option('wppl_fields');
    
    echo	'<script type="text/javascript">';
	echo		'addressMandatory= '.json_encode($wppl_options['mandatory_address']),';'; 
	echo	'</script>';
	
    echo 	'<input type="hidden" name="wppl_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    
    echo 	'<table class="form-table" style="width:100%;">';
    echo	'<tr><td>';
	
    echo	'<h4>'; _e('Get your current location','GMW'); echo ' <input type="button" id="gmw-admin-locator-btn" class="button-primary" value="'; _e('Locate Me','GMW'); echo'" /></h4>';
    
    echo 	'<h4 style="display: inline;margin:0px 0px">'; _e('Type an adress to autocomplete','GMW'); echo '</h4>';
    echo	'<input type="text" id="wppl-addresspicker" size="80" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][14]['id'],true) , '" />';
    
    echo 	'<div class="clear"></div>';
    echo	'<h4>'; _e('Use the map to drag and drop the marker to the correct location to get the latitude / longitude and click on the "Get Address" button below','GMW'); echo '</h4>';
	echo	'<div class="wppl-admin-map-holder">';
    echo		'<div id="map"></div>';
	echo	'</div>';
	
	echo 	'<div class="clear"></div>';

   	echo	'<div class="metabox-tabs-div">';
	echo		'<ul class="metabox-tabs" id="metabox-tabs">';
	echo			'<li class="active address-tab"><a class="active" href="javascript:void(null);">'; _e('Address','GMW'); echo '</a></li>';
	echo			'<li class="lat-long-tab"><a href="javascript:void(null);">'; _e('Latitude / Longitude'); echo '</a></li>';
	echo			'<li class="extra-info-tab"><a href="javascript:void(null);">'; _e('Additional Information','GMW'); echo '</a></li>';
	echo			'<li class="days-hours-tab"><a href="javascript:void(null);">'; _e('Days & Hours','GMW'); echo '</a></li>';
	echo			'<li id="ajax-loader" style="visibility:hidden; background:none; border:0px;"><img src="'. plugins_url('images/wpspin_light.gif', __FILE__) . '" id="ajax-loader-image" alt="" ">'; _e('Loading...','GMW'); echo '</li>';
	echo		'</ul>';
	
	echo 		'<div class="address-tab">';
 	echo 		'<h4 class="heading">'; _e('Address','GMW'); echo '</h4>';
 	echo			'<h3>';  _e('Enter Address - Click on the "address" tab below, fill up the address fields and click "Get Lat/Long".','GMW'); echo '</h3>';
  	echo 			'<table class="form-table">';			
 	echo 				'<tr>';
 	echo 					'<th ><label for="', $wppl_meta_box['fields'][0]['id'], '">', $wppl_meta_box['fields'][0]['name'], '</label></th>';
    echo					'<td><input type="text" name="',$wppl_meta_box['fields'][0]['id'], '" id="', $wppl_meta_box['fields'][0]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][0]['id'],true), '" size="30" style="width:97%" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th ><label for="', $wppl_meta_box['fields'][1]['id'], '">', $wppl_meta_box['fields'][1]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][1]['id'], '" id="', $wppl_meta_box['fields'][1]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][1]['id'],true), '" size="30" style="width:97%" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th ><label for="', $wppl_meta_box['fields'][2]['id'], '">', $wppl_meta_box['fields'][2]['name'], '</label></th>';
    echo					'<td><input type="text" name="',$wppl_meta_box['fields'][2]['id'], '" id="', $wppl_meta_box['fields'][2]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][2]['id'],true), '" size="30" style="width:97%" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th ><label for="', $wppl_meta_box['fields'][3]['id'], '">', $wppl_meta_box['fields'][3]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][3]['id'], '" id="', $wppl_meta_box['fields'][3]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][3]['id'],true), '" size="30" style="width:97%" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th ><label for="', $wppl_meta_box['fields'][4]['id'], '">', $wppl_meta_box['fields'][4]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][4]['id'], '" id="', $wppl_meta_box['fields'][4]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][4]['id'],true), '" size="30" style="width:97%" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th ><label for="', $wppl_meta_box['fields'][5]['id'], '">', $wppl_meta_box['fields'][5]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][5]['id'], '" id="', $wppl_meta_box['fields'][5]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][5]['id'],true), '" size="30" style="width:97%" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<td><input type="button"id="gmw-admin-getlatlong-btn" class="button-primary" value="Get Lat/Long" ></td>';
    echo 				'</tr>';
    echo 			'</table>';
    echo 		'</div>'; 
    
    echo 		'<div class="lat-long-tab">';
 	echo 			'<h4 class="heading">'; _e('Latitude / Longitude','GMW'); echo '</h4>';
 	echo 			'<h3>'; _e('- Enter Lat/Long - Click on the "Lat/Long" tab below, fill up the Latitude/Longitude fields and click "Get Address".','GMW'); echo '</h3>';
    echo 			'<table class="form-table">';
    echo 				'<tr>';
 	echo 					'<th ><label for="', $wppl_meta_box['fields'][10]['id'], '">', $wppl_meta_box['fields'][10]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][10]['id'], '" id="', $wppl_meta_box['fields'][10]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][10]['id'],true), '" size="30" style="width:97%" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th ><label for="', $wppl_meta_box['fields'][11]['id'], '">', $wppl_meta_box['fields'][11]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][11]['id'], '" id="', $wppl_meta_box['fields'][11]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][11]['id'],true), '" size="30" style="width:97%" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo					'<td><input type="button" id="gmw-admin-getaddress-btn" class="button-primary" value="Get Address" /></td>';
    echo 				'</tr>'; 
 	echo 			'</table>';
 	echo 		'</div>';
 	
 	echo 		'<div class="extra-info-tab">';
 	echo 			'<h4 class="heading">'; _e('Additional Information','GMW'); echo '</h4>';
    echo 			'<table class="form-table">';    
    echo 				'<tr>';
 	echo 					'<th><label for="', $wppl_meta_box['fields'][6]['id'], '">', $wppl_meta_box['fields'][6]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][6]['id'], '" id="', $wppl_meta_box['fields'][6]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][6]['id'],true), '" size="30" style="width:97%;" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th><label for="', $wppl_meta_box['fields'][7]['id'], '">', $wppl_meta_box['fields'][7]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][7]['id'], '" id="', $wppl_meta_box['fields'][7]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][7]['id'],true), '" size="30" style="width:97%;" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th><label for="', $wppl_meta_box['fields'][8]['id'], '">', $wppl_meta_box['fields'][8]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][8]['id'], '" id="', $wppl_meta_box['fields'][8]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][8]['id'],true), '" size="30" style="width:97%;" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th><label for="', $wppl_meta_box['fields'][9]['id'], '">', $wppl_meta_box['fields'][9]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][9]['id'], '" id="', $wppl_meta_box['fields'][9]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][9]['id'],true), '" size="30" style="width:97%;" />', '<br /></td>';
 	echo 				'</tr>';	
 	echo 			'</table>';
 	echo 		'</div>';
 	
 	$days_hours = get_post_meta($post->ID,$wppl_meta_box['fields'][15]['id'],true);
 	$days_hours = ( isset($days_hours) && is_array($days_hours) && array_filter($days_hours) )  ? get_post_meta($post->ID,$wppl_meta_box['fields'][15]['id'],true) : false;
 	
 	echo 		'<div class="days-hours-tab">';
 	echo 			'<h4 class="heading">'; _e('Days & Hours','GMW'); echo '</h4>';
    echo 			'<table class="form-table">';    
  	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[0][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[0]['days'], '" style="width:150px" />', '<br /></td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[0][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[0]['hours'], '" style="width:150px" />', '<br /></td>';  
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[1][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[1]['days'], '" style="width:150px" />', '<br /></td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[1][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[1]['hours'], '" style="width:150px" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[2][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[2]['days'], '" style="width:150px" />', '<br /></td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[2][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[2]['hours'], '" style="width:150px" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[3][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[3]['days'], '" style="width:150px" />', '<br /></td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[3][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[3]['hours'], '" style="width:150px" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[4][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[4]['days'], '" style="width:150px" />', '<br /></td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[4][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[4]['hours'], '" style="width:150px" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[5][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[5]['days'], '" style="width:150px" />', '<br /></td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[5][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[5]['hours'], '" style="width:150px" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[6][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[6]['days'], '" style="width:150px" />', '<br /></td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[6][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[6]['hours'], '" style="width:150px" />', '<br /></td>';
 	echo 				'</tr>';
 	echo 			'</table>';
 	echo 		'</div>';
 	
 	echo 	'</div>';
 	 	
 	echo 	'<div class="postbox" id="gmw-saved-data" style="margin-top:20px">';
 	echo 		'<h3 style="margin:0px;">'; _e('Saved Data - This table contains the data that is going to be used. it must have an address, latitude and longitude.','GMW'); echo '</h3>';
 	echo 		'<table class="form-table">';
 	echo 			'<tr>';
 	echo 				'<th ><label for="', $wppl_meta_box['fields'][12]['id'], '">', $wppl_meta_box['fields'][12]['name'], '</label></th>';
    echo 				'<td><input type="text" name="',$wppl_meta_box['fields'][12]['id'], '" id="', $wppl_meta_box['fields'][12]['id'], '" class="', $wppl_meta_box['fields'][12]['id'], '" value="', get_post_meta($post->ID,'_wppl_lat',true), '" size="30" style="width:97%;background:lightgray;border:1px solid #aaa" disabled="disabled" />', '<br /></td>';
 	echo				'<input type="hidden" name="gmw_check_lat" id="gmw_check_lat" value"">';
    echo 			'</tr>';
 	echo 			'<tr>';
 	echo 				'<th ><label for="', $wppl_meta_box['fields'][13]['id'], '">', $wppl_meta_box['fields'][13]['name'], '</label></th>';
    echo 				'<td><input type="text" name="',$wppl_meta_box['fields'][13]['id'], '" id="', $wppl_meta_box['fields'][13]['id'], '" class="', $wppl_meta_box['fields'][13]['id'], '" value="', get_post_meta($post->ID,'_wppl_long',true), '" size="30" style="width:97%;background:lightgray;border:1px solid #aaa" disabled="disabled" />', '<br /></td>';
    echo				'<input type="hidden" name="gmw_check_long" id="gmw_check_long" value"">';
    echo 			'</tr>';
 	echo 			'<tr>';
 	echo 				'<th ><label for="', $wppl_meta_box['fields'][14]['id'], '">', $wppl_meta_box['fields'][14]['name'], '</label></th>';
    echo 				'<td><input type="text" name="',$wppl_meta_box['fields'][14]['id'], '" id="', $wppl_meta_box['fields'][14]['id'], '" class="', $wppl_meta_box['fields'][14]['id'], '" value="', get_post_meta($post->ID,'_wppl_address',true),'" size="30" style="width:97%;background:lightgray;border:1px solid #aaa" disabled="disabled" />', '<br /></td>';
    echo				'<input type="hidden" name="gmw_check_address" id="gmw_check_address" value"">';
    echo 			'</tr>';
 	echo 		'</table>';
 	echo 	'</div>';
 	
 	echo 		'<div class="clear"></div>';
 	echo		'<input type="button" style="float:none;" id="gmw-admin-delete-btn" class="button-primary" value="'; _e('Delete Address','GMW'); echo '" />';
 	
 	echo '</td></tr></table>';
 	
}

/* EVERY NEW POST OR WHEN POST IS BEING UPDATED 
 * CREATE MAP, LATITUDE, LONGITUDE AND SAVE DATA INTO OUR LOCATIONS TABLE 
 * DATA SAVED - POST ID, POST TYPE, POST STATUS , POST TITLE , LATITUDE, LONGITUDE AND ADDRESS
 */
add_action( 'save_post' , 'wppl_save_data'); 

function wppl_save_data($post_id) {
    global $wppl_meta_box, $wpdb, $wppl_feature_meta_box, $post;
    $wppl_options = get_option('wppl_fields');
    $wppl_on = get_option('wppl_plugins');
    
    // Return if it's a post revision
    if ( false !== wp_is_post_revision( $post_id ) )
    	return;
    
    if ( !isset( $wppl_options['address_fields'] ) || empty( $wppl_options['address_fields'] ) || !isset($_POST['post_type']) || !in_array( $_POST['post_type'], $wppl_options['address_fields'] ) ) 
    	return;

    // verify nonce //
    if ( !isset(  $_POST['wppl_meta_box_nonce']) || !wp_verify_nonce( $_POST['wppl_meta_box_nonce'] , basename(__FILE__))) {
    	return;
    }
 
    // check autosave //
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if ( ! current_user_can( 'edit_post', $post_id ) )
    	return;
    
    // Check permissions //
    /*if ( isset( $_POST['post_type'] ) && in_array( $_POST['post_type'], $wppl_options['address_fields'] ) ) {
    	if ( !current_user_can('edit_page', $post->ID) ) {
    		return;
    	}
    } else {
    	if ( !current_user_can('edit_post', $post->ID) ) {
    		return;
    	}
    } */
 	
	if ( isset( $wppl_on['featured_posts'] ) ) {
    	update_post_meta($post->ID,'_wppl_featured_post' , $_POST['_wppl_featured_post']);
    }
    
    foreach ( $wppl_meta_box['fields'] as $field ) :
    	
    	if ( isset( $_POST[$field['id']] ) ) :	
	        $old = get_post_meta($post->ID, $field['id'], true);
	        $new = $_POST[$field['id']];
	 
	        if ($new && $new != $old) {
	            update_post_meta($post->ID, $field['id'], $new);
	        } elseif ('' == $new && $old) {
	            delete_post_meta($post->ID, $field['id'], $old);
	        }
		endif;
		
    endforeach;
    
    do_action('gmw_pt_admin_update_location_post_meta',$post->ID, $_POST, $wppl_options );
      
    $getLat = get_post_meta( $post->ID,'_wppl_lat',true );
    $getLong = get_post_meta( $post->ID,'_wppl_long',true );
    
    if ( isset( $_POST[$wppl_meta_box['fields'][12]['id']] ) && $_POST[$wppl_meta_box['fields'][13]['id']] && $_POST[$wppl_meta_box['fields'][14]['id']] ) :
    	
	    $gmwLocationArgs = array(
	    				'postID' 	  		=> $post->ID,
	    				'post_type'			=> $_POST['post_type'],
	    				'post_title'		=> $_POST['post_title'],
	    				'post_status'		=> $_POST['post_status'],
	    				'street'			=> $_POST[$wppl_meta_box['fields'][0]['id']],
	    				'apt'				=> $_POST[$wppl_meta_box['fields'][1]['id']],
	    				'city'				=> $_POST[$wppl_meta_box['fields'][2]['id']],
	    				'state'				=> $_POST[$wppl_meta_box['fields'][3]['id']],
	    				'zipcode'			=> $_POST[$wppl_meta_box['fields'][4]['id']],
	    				'country'			=> $_POST[$wppl_meta_box['fields'][5]['id']],
	    				'full_address'		=> false,
	    				'phone' 			=> $_POST[$wppl_meta_box['fields'][6]['id']],
	    				'fax' 				=> $_POST[$wppl_meta_box['fields'][7]['id']],
	    				'email' 			=> $_POST[$wppl_meta_box['fields'][8]['id']],
	    				'website' 			=> $_POST[$wppl_meta_box['fields'][9]['id']],
	    				'map_icon'  		=> '_default.png',
	    				'auto_components'	=> false
	    			);
	    
	    gmw_pt_update_location( $gmwLocationArgs );
		
	else :
		$wpdb->query(
		$wpdb->prepare( 
			"DELETE FROM " . $wpdb->prefix . "places_locator WHERE post_id=%d",$post->ID
			)
		);
	endif;
}
?>