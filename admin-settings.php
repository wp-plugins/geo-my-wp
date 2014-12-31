<?php

add_action('admin_menu', 'wppl_admin');

function wppl_admin() {
	add_options_page('Places Locator', 'Places Locator', 'manage_options', 'places-locator', 'wppl_plugin_options_page');
}

add_action('admin_init', 'wppl_plugin_admin_init');
	function wppl_plugin_admin_init(){
		register_setting( 'wppl_plugin_options', 'wppl_fields', 'validate_settings' );
		register_setting( 'wppl_plugin_options', 'wppl_shortcode');
		}
		
// display the admin options page
function wppl_plugin_options_page() {

	echo '<div class="wrap">';  
    screen_icon('themes'); 
	echo '<h2>Word Press Places Locator</h2>';
	
	echo '<form action="options.php" method="post">';
	settings_fields('wppl_plugin_options'); 
	$options = get_option('wppl_fields');
	$options_r = get_option('wppl_shortcode');
	$posts = get_post_types();
	
	echo '<table class="widefat fixed" style="margin-bottom:10px;">';
			
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p style="font-size:14px;">I hope you will find this plugin useful! any donation would be much appreciated! thank you :)</p>';
	echo 			'<a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FTXHJQAS523D4" border="0" alt="PayPal - The safer, easier way to pay online!" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" /></a>';
	echo		'</td>';
	echo	'</tr>';
	
	echo '</table>';
	
	echo '<h3> Main Settings </h3>';
	echo '<table class="widefat fixed">';
			
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p>Choose the post types where you want the address fields to appear: </p>';
	echo 		'</td>';		
	echo 		'<td>';			
					foreach ($posts as $post) { 
	echo 				'<input id="address_fields" type="checkbox" name="wppl_fields[address_fields][]" value="' . $post . '" '; if ($options['address_fields']) { echo (in_array($post, $options['address_fields'])) ? "checked=checked" : "";}; echo  ' style="margin-left: 10px;">' . $post;
					}						
	echo 		'</td>';
	echo 	'</tr>';
	
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p>Do you want to make the address fields mandatory? (post will set to draft if no address entered)</p>';
	echo 		'</td>';
	echo		'<td>';
	echo    		'<input name="wppl_fields[mandatory_address] " type="checkbox" value="1"'; if ($options['mandatory_address'] == 1) {echo "checked";}; echo ' /> Yes';
	echo    	'</td>';
	echo	'</tr>';
	
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p>Distance values(comma separated) (ex. 5,10,15,25,50,100): </p>';
	echo 		'</td>';
	echo 		'<td>';
	echo 			'<input id="distance_values" name="wppl_fields[distance_values]" size="40" type="text" value="' . $options[distance_values] .'" />';
	echo 		'</td>';
	echo 	'</tr>';
	/*
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p>Posts type Title: </p>';
	echo 		'</td>';
	echo 		'<td>';
	echo 			'<input id="post_type_title" name="wppl_fields[post_type_title]" size="40" type="text" value="'; if ($options[post_type_title]){ echo $options[post_type_title];} else { echo "What are you looking for"; }; echo '" />'; 
	echo 		'</td>';
	echo 	'</tr>';
		echo '<tr>';
			echo '<td>';
				echo '<p>Categoty Title:';
			echo '</td>';
			echo '<td>';
				echo '<input id="categories_title" name="wppl_fields[categories_title]" size="40" type="text" value="'; if ($options[categories_title]){ echo $options[categories_title];} else { echo "Choose category"; }; echo '" /></p>'; 
			echo '</td>';
		echo '</tr>'; 
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p>Address field title: </p>';
	echo 		'</td>';
	echo 		'<td>';
	echo 			'<input id="address_field_title" name="wppl_fields[address_title]" size="40" type="text" value="'; if ($options[address_title]){ echo $options[address_title];} else { echo "Enter zip code, city or full address"; }; echo '" />'; 
	echo 		'</td>';
	echo 	'</tr>';
	*/
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p>Different styling for each post type in results: </p>';
	echo 		'</td>';
	echo 		'<td>';
 	echo 			'<input name="wppl_fields[different_style] " type="checkbox" value="1"'; if ($options['different_style'] == 1) {echo "checked";}; echo ' /> Yes';
	echo 		'</td>';
	echo 	'</tr>';
	
	echo	'<tr style=" height:40px;">';
	echo		'<td>';
	echo 			'<input name="Submit" type="submit" value="'; esc_attr_e('Save Changes'); echo '" />';
	echo		'</td>';
	echo	'</tr>';
		
	echo '</table>';
	
	echo '<h3>Create Shortcodes</h3>';  		
	echo '<ul id="posts-taxonomies-list">'; 
    
    $yy = 0;
	if (!empty($options_r)) {
		$tt = array_keys($options_r);
		foreach ($options_r as $option) :
		$e_id = $tt[$yy];
		
			echo '<li class="wppl-shortcode-info-holder" id="wppl-shortcode-info-holder-' . $e_id . '">';
			
			echo '<table class="widefat fixed">';
			
			echo '<tr>';
			echo	'<th>Copy the shortcode below and paste it into any page to display the search form.</th>';
			echo '</tr>';
			
			echo '<tr>';
			echo 	'<td style="font-size: 12px; color: brown; padding:8px;">[wppl form="' . $e_id. '"]</td>';
			echo '</tr>';
			echo '<tr>';
			echo	'<td>';
			?>
					<input type="button" onclick="jQuery('.edit-shortcode-<?php echo $e_id; ?>').toggle();" value="Edit">
					<input type="button" onclick="removeElement(jQuery(this).closest('.wppl-shortcode-info-holder'));" value="Remove">
			<?php
			echo	'</td>';
			echo	'<td></td>';
			echo '</table>';
			
			echo '<div class="edit-shortcode-' . $e_id . '" style="display:none">';
			echo '<table class="widefat fixed">';
			
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<label for="label-post-types-' . $e_id . '">Choose the post types to appear in the search form (no categories for multiple post types): </label><br />';
			echo		'</td>';
			echo		'<td id="posts-checkboxes-' . $e_id . '" '; if (!$option['post_types']) { echo 'style="background: #FAA0A0"' ;}; echo '>';
						foreach ($posts as $post) { 
			echo 			'<input type="checkbox" name="wppl_shortcode[' .$e_id .'][post_types][]" value="' . $post . '" '; if ($option['post_types']) { echo (in_array($post, $option['post_types'])) ? "checked=checked" : "";}; echo  ' style="margin-left: 10px;" id="' . $e_id . '" onchange="change_it(this.name,this.id);">' . $post;
						}
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<label for="label-taxonomies-' . $e_id . '">Choose the taxonomies to appear in the search form:</label><br />';
			echo		'</td>';
			
			echo		'<td class="taxes-' .$e_id . '" style=" padding: 8px;">';
								foreach ($posts as $post) {
									$taxes = get_object_taxonomies($post);
			echo					'<div id="' . $post . '_cat_' . $e_id . '" '; echo ((count($option['post_types']) == 1) && (in_array($post, $option['post_types']))) ? 'style="display: block; " ' : 'style="display: none;"'; echo '>';
									foreach ($taxes as $tax) { 
				 						if (is_taxonomy_hierarchical($tax)) { 
			echo 							'<input type="checkbox" name="wppl_shortcode[' .$e_id .'][taxonomies][]" value="' . $tax .'" '; if($option['taxonomies']) { echo (in_array($tax , $option['taxonomies'])) ? "checked=checked" : "";}; echo  ' style="margin-left: 10px; " />' . $tax;
										}
									}
			echo					'</div>';
								}
			echo		'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<label for="label-show-map-' . $e_id . '">Display Google map:</label><br />';
			echo		'</td>';
			echo		'<td>';
			echo    		'Display Map (check box)&nbsp;<input type="checkbox" name="wppl_shortcode[' .$e_id .'][show_map]" '; echo (isset($option['show_map'])) ? "checked=checked" : ""; echo ' value="1">&nbsp;&nbsp;&nbsp;Map Height:&nbsp;&nbsp;<input type="text" name="wppl_shortcode[' .$e_id .'][map_height]" value="' . $option['map_height'] . '" size="5">&nbsp;&nbsp;&nbsp;Map Width:&nbsp;<input type="text" name="wppl_shortcode[' .$e_id .'][map_width]" value="' . $option['map_width'] . '" size="5">';
			echo    	'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<label for="label-per-page-' . $e_id . '">Results per page:</label><br />';
			echo		'</td>';
			echo		'<td>';
			echo    		'<input type="text" name="wppl_shortcode[' .$e_id .'][per_page]" value="' . $option['per_page'] . '" size="5">';
			echo    	'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<label for="label-driving-distance-' . $e_id . '">Display driving distance:</label><br />';
			echo		'</td>';
			echo		'<td>';
			echo    		'<input type="checkbox" name="wppl_shortcode[' .$e_id .'][by_driving]" '; echo (isset($option['by_driving'])) ? "checked=checked" : ""; echo '" value="1">';
			echo    	'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<label for="label-get-directions-' . $e_id . '">Display "get directions" link: </label><br />';
			echo		'</td>';
			echo		'<td>';
			echo    		'<input type="checkbox" name="wppl_shortcode[' .$e_id .'][get_directions]" '; echo (isset($option['get_directions'])) ? "checked=checked" : ""; echo '" value="1" >';
			echo    	'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo 			'<input name="Submit" type="submit" value="'; esc_attr_e('Save Changes'); echo '" />';
			echo		'</td>';
			echo	'</tr>';
			
			echo	'</table>';
			echo	'</div>';

			echo '</li>'; 
			$yy++;
			endforeach;		
			}
    
    echo	'</ul>'; 
    echo 	'<input type="button" id="add-featured-post" value="Create new shortcode">';
    echo 	'<input name="Submit" type="submit" value="'; esc_attr_e('Save Changes'); echo '" />';
    
    
    echo 	'</div>'; // wrap //
    		// number of the last element to pass the to jQuery. //
    		//that is how we know what will be the next rlrmrnt number//
  	echo 	'<input type="hidden" name="element-max-id" value="'; echo (isset($tt)) ? (max($tt) + 1) : "1"; echo '" />';
	
 	echo 	'</form>'; 
 	
	echo 	'<div style="display:none;">';
	echo		'<li class="wppl-post-info-holder" id="wppl-shortcode-info-holder-placeholder">';
	echo 			'<table class="widefat fixed">';
	echo				'<th>Create Shortcode</th>';
	
	echo				'<tr>';
	echo					'<td>';
	echo  						'<label for="element-page-id">Choose the post types to appear in the search form (no categories for multiple post types): </label><br />';
	echo					'</td>';
	echo					'<td>';
							foreach ($posts as $post) { 
	echo 						'<input type="checkbox" name="post_types" value="' . $post . '" id="new" style="margin-left: 10px;" onchange="change_it(this.name,this.id);">'. $post;
							}
	echo    				'</td>';
	echo   				'</tr>';
	
	echo				'<tr>';
	echo					'<td>';
	echo  						'<label for="label-taxonomies">Choose the taxonomies to appear in the search form:</label><br />';
	echo					'</td>';
			
	echo					'<td class="taxes-new">';
								foreach ($posts as $post) {
									$taxes = get_object_taxonomies($post);
	echo							'<div id="' . $post . '_cat_new' . '" style="display: none;">';
									foreach ($taxes as $tax) { 
				 						if (is_taxonomy_hierarchical($tax)) { 
	echo 									'<input type="checkbox" name="taxonomies" value="' . $tax .'" style="margin-left: 10px;" />' . $tax;
										}
									}
	echo							'</div>';
								}
	echo					'</td>';
	echo				'</tr>';
	
	echo				'<tr>';
	echo					'<td>';
	echo  						'<label for="element-page-id">Display Google map:</label><br />';
	echo					'</td>';
	echo					'<td>';
	echo    					'Display Map (check box) <input type="checkbox" name="show_map" value="1" size="45">&nbsp;&nbsp;  height:&nbsp;<input type="text" name="map_height" value="" size="5"> Width:&nbsp;<input type="text" name="map_width" value="" size="5">';
	echo    				'</td>';
	echo   				'</tr>';
	
	echo				'<tr>';
	echo					'<td>';
	echo  						'<label for="element-page-id">Results per page: </label><br />';
	echo					'</td>';
	echo					'<td>';
	echo    					'<input type="text" name="per_page" value="" size="5">';
	echo    				'</td>';
	echo   				'</tr>';
	
	echo				'<tr>';
	echo					'<td>';
	echo  						'<label for="element-page-id">Display driving distance:</label><br />';
	echo					'</td>';
	echo					'<td>';
	echo    					'<input type="checkbox" name="by_driving" value="1">';
	echo    				'</td>';
	echo   				'</tr>';
	
	echo				'<tr>';
	echo					'<td>';
	echo  						'<label for="element-page-id">Display "get directions" link: </label><br />';
	echo					'</td>';
	echo					'<td>';
	echo    					'<input type="checkbox" name="get_directions" value="1" >';
	echo    				'</td>';
	echo   				'</tr>';
	
	echo				'<tr>';
	echo					'<td>';
	echo 						'<input name="Submit" type="submit" value="'; esc_attr_e('Save Changes'); echo '" />';
	echo					'</td>';
	echo				'</tr>';
	
	echo   				'<tr>';
	echo    				'<td>';
	echo   						'<a href="#">Remove</a>'; 
	echo					'</td>';
	echo   				'</tr>';
	
	echo			'</table>';
	echo 		'</li>';	
	echo	'</div>';
	echo 	'<script src="' . plugins_url('js/admin.js', __FILE__) . '" type="text/javascript"></script>';

	 } 

function validate_settings($input) {
	$options = get_option('wppl_fields');
	if (preg_match('/^[0-9,]+$/', $input['distance_values'])) {
		$options['distance_values'] = $input['distance_values'];
	} else {
		$options['distance_values'] = '';
  	}
  	$options['address_fields'] = $input['address_fields'];
  	$options['mandatory_address'] = $input['mandatory_address'];
  	$options['different_style'] = $input['different_style'];

return $options;
}

  	
