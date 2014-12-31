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
	echo 			'<p style="font-size:14px;">'; _e('I hope you will find this plugin useful! any donation would be much appreciated! thank you :)', 'places-locator'); echo '</p>';
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
	echo 				'<input id="address_fields" type="checkbox" name="wppl_fields[address_fields][]" value="' . $post . '" '; if ($options['address_fields']) { echo (in_array($post, $options['address_fields'])) ? "checked=checked" : "";}; echo  ' style="margin-left: 10px;">' .get_post_type_object($post)->labels->name ;
					}						
	echo 		'</td>';
	echo 	'</tr>';
	
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p>Do you want to make the address fields mandatory? (post will set to draft if no address entered) </p>';
	echo 		'</td>';
	echo		'<td>';
	echo    		'<p><input name="wppl_fields[mandatory_address] " type="checkbox" value="1"'; if ($options['mandatory_address'] == 1) {echo "checked";}; echo ' /> Yes</p>';
	echo    	'</td>';
	echo	'</tr>';
	
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p>Distance values(comma separated) (ex. 5,10,15,25,50,100): </p>';
	echo 		'</td>';
	echo 		'<td>';
	echo 			'<p><input id="distance_values" name="wppl_fields[distance_values]" size="40" type="text" value="' . $options[distance_values] .'" /></p>';
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
 	echo 			'<p><input name="wppl_fields[different_style] " type="checkbox" value="1"'; if ($options['different_style'] == 1) {echo "checked";}; echo ' /> Yes</p>';
	echo 		'</td>';
	echo 	'</tr>';
	
	echo	'<tr style=" height:40px;">';
	echo		'<td>';
	echo 			'<p><input name="Submit" type="submit" value="'; esc_attr_e('Save Changes'); echo '" /></p>';
	echo		'</td>';
	echo	'</tr>';
		
	echo '</table>';
	
	echo '<h3> Shortcode For Single Location Map </h3>';
	echo '<table class="widefat fixed">';
			
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p>Paste this code into any single page template to display map of a single location : </p>';
	echo 		'</td>';
	echo		'<td>';
	echo    		'<p style="color:brown; font-size:13px;"> &#60;?php do_shortcode(&#39;[swppl]&#39;); ?&#62;</p>';
	echo    	'</td>';
	echo	'</tr>';
	
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p >Map height in pixels (250px by default)</p>';
	echo 		'</td>';
	echo 		'<td>';
	echo 			'<p style="color:brown; font-size:13px;">map_height=" "</p>';
	echo 		'</td>';
	echo 	'</tr>';
	
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p>Map Width in pixels (250px by default)</p>';
	echo 		'</td>';
	echo 		'<td>';
	echo 			'<p style="color:brown; font-size:13px;">map_width=" "</p>';
	echo 		'</td>';
	echo 	'</tr>';
	
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p>Map type - ROADMAP,SATELLITE,HYBRID,TERRAIN (ROADMAP by default)</p>';
	echo 		'</td>';
	echo 		'<td>';
	echo 			'<p style="color:brown; font-size:13px;">map_type=" "</p>';
	echo 		'</td>';
	echo 	'</tr>';
	
	echo 	'<tr>';
	echo 		'<td>';
	echo 			'<p>Shortcode example: </p>';
	echo 		'</td>';
	echo 		'<td>';
	echo 			'<p style="color:brown;"> &#60;?php do_shortcode(&#39;[swppl map_height="300" map_width="300" map_type="SATELLITE"]&#39;); ?&#62;</p>';
	echo 		'</td>';
	echo 	'</tr>';
	
	echo '</table>';
	
	echo '<h3>Create Search Form Shortcodes</h3>';  		
	echo '<ul id="posts-taxonomies-list">'; 
    
    $yy = 0;
	if (!empty($options_r)) {
		$tt = array_keys($options_r);
		foreach ($options_r as $option) :
		$e_id = $tt[$yy];
		
			echo '<li class="wppl-shortcode-info-holder" id="wppl-shortcode-info-holder-' . $e_id . '">';
			
			echo '<table class="widefat fixed" style="margin-bottom: 1px;">';
			
			echo '<tr>';
			echo	'<th>Copy the shortcode and paste it into any page to display the search form.</th>';		
			echo 	'<p><td style="font-size: 12px; color: brown; padding:8px;">[wppl form="' . $e_id. '"]</td></p>';
			echo '</tr>';
			echo '<tr>';
			echo	'<td>';
			?>
					<p><input type="button" onclick="jQuery('.edit-shortcode-<?php echo $e_id; ?>').toggle();" value="Edit">
					<input type="button" onclick="removeElement(jQuery(this).closest('.wppl-shortcode-info-holder'));" value="Remove"></p>
			<?php
			echo	'</td>';
			echo	'<td></td>';
			echo '</table>';
			
			echo '<div class="edit-shortcode-' . $e_id . '" style="display:none">';
			echo '<table class="widefat fixed">';
						
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<p><label for="label-post-types-' . $e_id . '">Choose the post types to appear in the search form (no categories for multiple post types): </label></p>';
			echo		'</td>';
			echo		'<td id="posts-checkboxes-' . $e_id . '" '; if (!$option['post_types']) { echo 'style="background: #FAA0A0"' ;}; echo '>';
						foreach ($posts as $post) { 
			echo 			'<p><input type="checkbox" name="wppl_shortcode[' .$e_id .'][post_types][]" value="' . $post . '" '; if ($option['post_types']) { echo (in_array($post, $option['post_types'])) ? "checked=checked" : "";}; echo  ' style="margin-left: 10px;" id="' . $e_id . '" onchange="change_it(this.name,this.id);">' . get_post_type_object($post)->labels->name . '</p>';
						}
			echo		'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<p><label for="label-taxonomies-' . $e_id . '">Choose the taxonomies to appear in the search form:</label></p>';
			echo		'</td>';
			
			echo		'<td class="taxes-' .$e_id . '" style=" padding: 8px;">';
								foreach ($posts as $post) {
									$taxes = get_object_taxonomies($post);
			echo					'<div id="' . $post . '_cat_' . $e_id . '" '; echo ((count($option['post_types']) == 1) && (in_array($post, $option['post_types']))) ? 'style="display: block; " ' : 'style="display: none;"'; echo '>';
									foreach ($taxes as $tax) { 
				 						if (is_taxonomy_hierarchical($tax)) { 
			echo 							'<p><input type="checkbox" name="wppl_shortcode[' .$e_id .'][taxonomies][]" value="' . $tax .'" '; if($option['taxonomies']) { echo (in_array($tax , $option['taxonomies'])) ? "checked=checked" : "";}; echo  ' style="margin-left: 10px; " />' . get_taxonomy($tax)->labels->singular_name . '</p>';
										}
									}
			echo					'</div>';
								}
			echo		'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<p><label for="label-show-map-' . $e_id . '">Show Google map:</label></p>';
			echo		'</td>';
			echo		'<td>';
			echo    		'<p>Show Map&nbsp;<input type="checkbox" name="wppl_shortcode[' .$e_id .'][show_map]" '; echo (isset($option['show_map'])) ? "checked=checked" : ""; echo ' value="1">
							&nbsp;&nbsp;Height:&nbsp;<input type="text" name="wppl_shortcode[' .$e_id .'][map_height]" value="' . $option['map_height'] . '" size="2">px
							&nbsp;&nbsp;&nbsp;Width:&nbsp;<input type="text" name="wppl_shortcode[' .$e_id .'][map_width]" value="' . $option['map_width'] . '" size="2">px
							&nbsp;&nbsp;&nbsp;Type:&nbsp;
							<select name="wppl_shortcode[' .$e_id .'][map_type]">
								<option value="ROADMAP" '; echo ($option['map_type'] == "ROADMAP" ) ?'selected="selected"' : ""; echo '>ROADMAP</option>
								<option value="SATELLITE" '; echo ($option['map_type'] == "SATELLITE" ) ?'selected="selected"' : ""; echo '>SATELLITE</option>
								<option value="HYBRID" '; echo ($option['map_type'] == "HYBRID" ) ?'selected="selected"' : ""; echo '>HYBRID</option>
								<option value="TERRAIN" '; echo ($option['map_type'] == "TERRAIN" ) ?'selected="selected"' : ""; echo '>TERRAIN</option>
							</select></p>';
			echo    	'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<p><label for="label-per-page-' . $e_id . '">Results per page:</label></p>';
			echo		'</td>';
			echo		'<td>';
			echo    		'<p><input type="text" name="wppl_shortcode[' .$e_id .'][per_page]" value="' . $option['per_page'] . '" size="3"></p>';
			echo    	'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<p><label for="label-driving-distance-' . $e_id . '">Show driving distance:</label></p>';
			echo		'</td>';
			echo		'<td>';
			echo    		'<p><input type="checkbox" name="wppl_shortcode[' .$e_id .'][by_driving]" '; echo (isset($option['by_driving'])) ? "checked=checked" : ""; echo '" value="1"></p>';
			echo    	'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<p><label for="label-get-directions-' . $e_id . '">Show "get directions" link: </label></p>';
			echo		'</td>';
			echo		'<td>';
			echo    		'<p><input type="checkbox" name="wppl_shortcode[' .$e_id .'][get_directions]" '; echo (isset($option['get_directions'])) ? "checked=checked" : ""; echo '" value="1" ></p>';
			echo    	'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo 			'<p><input name="Submit" type="submit" value="'; esc_attr_e('Save Changes'); echo '" /></p>';
			echo		'</td>';
			echo	'</tr>';
			
			echo	'</table>';
			echo	'</div>';

			echo '</li>'; 
			$yy++;
			endforeach;		
			}
    
    echo	'</ul>'; 
    echo 	'<p><input type="button" id="add-featured-post" value="Create new shortcode">';
    echo 	'<input name="Submit" type="submit" value="'; esc_attr_e('Save Changes'); echo '" /></p>';
    
    
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
	echo  						'<p><label for="element-page-id">Choose the post types to appear in the search form (no categories for multiple post types): </label></p>';
	echo					'</td>';
	echo					'<td>';
							foreach ($posts as $post) { 
	echo 						'<p><input type="checkbox" name="post_types" value="' . $post . '" id="new" style="margin-left: 10px;" onchange="change_it(this.name,this.id);">'. get_post_type_object($post)->labels->name . '</p>';
							}
	echo    				'</td>';
	echo   				'</tr>';
	
	echo				'<tr>';
	echo					'<td>';
	echo  						'<p><label for="label-taxonomies">Choose the taxonomies to appear in the search form:</label></p>';
	echo					'</td>';
			
	echo					'<td class="taxes-new">';
								foreach ($posts as $post) {
									$taxes = get_object_taxonomies($post);
	echo							'<div id="' . $post . '_cat_new' . '" style="display: none;">';
									foreach ($taxes as $tax) { 
				 						if (is_taxonomy_hierarchical($tax)) { 
	echo 									'<p><input type="checkbox" name="taxonomies" value="' . $tax .'" style="margin-left: 10px;" />' . get_taxonomy($tax)->labels->singular_name . '</p>';
										}
									}
	echo							'</div>';
								}
	echo					'</td>';
	echo				'</tr>';
	
	echo				'<tr>';
	echo					'<td>';
	echo  						'<label for="element-page-id">Show Google map:</label><br />';
	echo					'</td>';
	echo					'<td>';
	echo    					'<p>';
	echo						'Show Map <input type="checkbox" name="show_map" value="1" size="45">&nbsp;&nbsp;height:&nbsp;<input type="text" name="map_height" value="" size="5">px &nbsp;Width:&nbsp;<input type="text" name="map_width" value="" size="5">px
								&nbsp;&nbsp;&nbsp;Type:&nbsp;
								<select name="map_type">
									<option value="ROADMAP">ROADMAP</option>
									<option value="SATELLITE">SATELLITE</option>
									<option value="HYBRID">HYBRID</option>
									<option value="TERRAIN">TERRAIN</option>
								</select>
								</p>';
	echo    				'</td>';
	echo   				'</tr>';
	
	echo				'<tr>';
	echo					'<td>';
	echo  						'<p><label for="element-page-id">Results per page: </label></p>';
	echo					'</td>';
	echo					'<td>';
	echo    					'<p><input type="text" name="per_page" value="" size="2"></p>';
	echo    				'</td>';
	echo   				'</tr>';
	
	echo				'<tr>';
	echo					'<td>';
	echo  						'<p><label for="element-page-id">Show driving distance:</label></p>';
	echo					'</td>';
	echo					'<td>';
	echo    					'<p><input type="checkbox" name="by_driving" value="1"></p>';
	echo    				'</td>';
	echo   				'</tr>';
	
	echo				'<tr>';
	echo					'<td>';
	echo  						'<p><label for="element-page-id">Show "get directions" link: </label></p>';
	echo					'</td>';
	echo					'<td>';
	echo    					'<p><input type="checkbox" name="get_directions" value="1" ></p>';
	echo    				'</td>';
	echo   				'</tr>';
	
	echo				'<tr>';
	echo					'<td>';
	echo 						'<p><input name="Submit" type="submit" value="'; esc_attr_e('Save Changes'); echo '" /></p>';
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

  	
