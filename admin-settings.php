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
	$pages_s = get_pages();	

?>
	
	<table class="widefat fixed" style="margin-bottom:10px;">
		<tr>
			<td>
			<p style="font-size:14px;"><?php _e('I hope you will find this plugin useful! any donation would be much appreciated! thank you :)', 'places-locator'); ?></p>
			<a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FTXHJQAS523D4" border="0" alt="PayPal - The safer, easier way to pay online!" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" /></a>
			</td>
		</tr>
	</table>
	
	<h3> Main Settings </h3>
	<table class="widefat fixed">
		<tr>
			<td>
			<p><?php _e('Choose the post types where you want the address fields to appear:'); ?> </p>
			</td>
			<td>			
			<?php foreach ($posts as $post) { 
				echo '<input id="address_fields" type="checkbox" name="wppl_fields[address_fields][]" value="' . $post . '" '; if ($options['address_fields']) { echo (in_array($post, $options['address_fields'])) ? "checked=checked" : "";}; echo  ' style="margin-left: 10px;">' .get_post_type_object($post)->labels->name ;
					}	
			?>
			</td>
		</tr>
		<tr>
			<td>
				<p><?php _e('Do you want to make the address fields mandatory? (post will set to draft if no address entered)'); ?> </p>
			</td>
			<td>
				<p><input name="wppl_fields[mandatory_address] " type="checkbox" value="1" <?php if ($options['mandatory_address'] == 1) {echo "checked";}; ?>/> Yes</p>
			</td>
		</tr>
		<tr>
			<td>
				<p><?php _e('Distance values(comma separated) (ex. 5,10,15,25,50,100):'); ?></p>
			</td>
			<td>
				<p><input id="distance_values" name="wppl_fields[distance_values]" size="40" type="text" value="<?php echo $options[distance_values]; ?>" /></p>
			</td>
		</tr>
		<tr style=" height:40px;">
			<td>
				<p><?php _e('Choose the page where you want to display the results (when not in the same page or when using the widget ):'); ?> </p>
			</td>
			<td>
			<select name="wppl_fields[results_page]" style="float:left">
				<?php foreach ($pages_s as $page_s) {
				echo '<option value="'.$page_s->post_name.'"'; echo ($options[results_page] == $page_s->post_name ) ? 'selected="selected"' : "" ; echo '>'. $page_s->post_title . '</option>';
				}
			?>
			</select>
				<p style="float:left;font-size:12px;margin-left:25px"><?php _e('Paste the shortcode'); ?> <span style="color:brown"> [wpplresults] </span><?php _e('into the results page'); ?></p>
			</td>
		</tr>
		<tr style=" height:40px;">
			<td>
				<p><?php _e('To display the search form in one page and the results in the "results" page use')?><span style="color:brown"> form_only="y" </span><?php _e('in your shortcode:')?></p>
			</td>
			<td>
				<p style="float:left;font-size:12px;"><?php _e('For example you can use:'); ?> <span style="color:brown"> [wppl form="1" form_only="y"] </span><?php _e('in any page to display the search form and the results will show in the results page'); ?></p>
			</td>
		</tr>
		<tr style=" height:40px;">
			<td>
				<p><input name="Submit" type="submit" value="<?php echo esc_attr_e('Save Changes'); ?>" /></p>
			</td>
		</tr>
	<?php /* ?>	<tr>
			<td>
				<p><?php _e('Enter your Google Maps API Key:'); ?></p>
			</td>
			<td>
				<p><input id="api_key" name="wppl_fields[google_api]" size="40" type="text" value="<?php echo $options[google_api]; ?>" /></p>
			</td>
		</tr> <?php */ ?>
		
		</table>
		
		
	
		<h3> Shortcode - Map For Single Location</h3>
		<table class="widefat fixed">
			<tr>
				<td>
					<p>Paste this code into any single page template to display map of a single location : </p>
				</td>
				<td>
					<p style="color:brown; font-size:12px;"> &#60;?php echo do_shortcode(&#39;[swppl]&#39;); ?&#62;</p>
				</td>
				</tr>
				<tr>
				<td>
					<p>Shortcode Attributes: (for more information visit the <a href="http://www.wpplaceslocator.com/installation" target="_blank">Installation</a> page)</p>
				</td>
				<td>
					<p style="color:brown; font-size:13px;">map_height=" " , map_width=" " , map_type=" "</p>
		 		</td>
	 		</tr>
		</table>
		<h3>Create Search Form Shortcodes</h3>		
		<ul id="posts-taxonomies-list">

<?php	$yy = 0;
		if (!empty($options_r)) {
		$tt = array_keys($options_r);
		foreach ($options_r as $option) :
		$e_id = $tt[$yy];
		
			echo '<li class="wppl-shortcode-info-holder" id="wppl-shortcode-info-holder-' . $e_id . '">';
			
			echo '<table class="widefat fixed" style="margin-bottom: 1px;">';
			echo '<input type="hidden" name="wppl_shortcode[' .$e_id .'][form_id]" value="' . $e_id . '">';
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
			echo  			'<p><label for="label-styling-' . $e_id . '">Results Styling:</label></p>';
			echo		'</td>';
			echo		'<td>';
			echo    		'<p>
							<select name="wppl_shortcode[' .$e_id .'][result_style]">
								<option value="default" '; echo ($option['result_style'] == "default" ) ?'selected="selected"' : ""; echo '>Default</option>
								<option value="blue" '; echo ($option['result_style'] == "blue" ) ?'selected="selected"' : ""; echo '>Blue</option>
							</select>
							</p>';
			echo    	'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<p><label for="label-show-map-' . $e_id . '">Google Map:</label></p>';
			echo		'</td>';
			echo		'<td>';
			echo    		'<p style="line-height: 40px;">Show Map&nbsp;<input type="checkbox" name="wppl_shortcode[' .$e_id .'][show_map]" '; echo (isset($option['show_map'])) ? "checked=checked" : ""; echo ' value="1">
							&nbsp;&nbsp;Height:&nbsp;<input type="text" name="wppl_shortcode[' .$e_id .'][map_height]" value="' . $option['map_height'] . '" size="2">px
							&nbsp;&nbsp;&nbsp;Width:&nbsp;<input type="text" name="wppl_shortcode[' .$e_id .'][map_width]" value="' . $option['map_width'] . '" size="2">px
							&nbsp;&nbsp;&nbsp;
							<br />
							Map Type:&nbsp;				
							<select name="wppl_shortcode[' .$e_id .'][map_type]">
								<option value="ROADMAP" '; echo ($option['map_type'] == "ROADMAP" ) ?'selected="selected"' : ""; echo '>ROADMAP</option>
								<option value="SATELLITE" '; echo ($option['map_type'] == "SATELLITE" ) ?'selected="selected"' : ""; echo '>SATELLITE</option>
								<option value="HYBRID" '; echo ($option['map_type'] == "HYBRID" ) ?'selected="selected"' : ""; echo '>HYBRID</option>
								<option value="TERRAIN" '; echo ($option['map_type'] == "TERRAIN" ) ?'selected="selected"' : ""; echo '>TERRAIN</option>
							</select>
							&nbsp;&nbsp;&nbsp;Auto Zoom Map:&nbsp;
							<input type="checkbox" name="wppl_shortcode[' .$e_id .'][auto_zoom]" '; echo (isset($option['auto_zoom'])) ? "checked=checked" : ""; echo ' value="1">
							&nbsp;or &nbsp;&nbsp;Zoom Level:&nbsp; 
							<select name="wppl_shortcode[' .$e_id .'][zoom_level]">';
							for ($r=1; $r< 18 ; $r++) { 			
								echo '<option value="' .$r. '"'; echo ($option['zoom_level'] == $r ) ? 'selected="selected"' : ""; echo '>'.$r.'</option>';
								}						
			echo			'</select>(will not count if auto zoom is checked)</p>';
			echo    	'</td>';
			echo	'</tr>';
			
			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<p><label for="label-show-thumb-' . $e_id . '">Feature Imgae Settings:</label></p>';
			echo		'</td>';
			echo		'<td>';
			echo    		'<p>Show Feature Image&nbsp;<input type="checkbox" name="wppl_shortcode[' .$e_id .'][show_thumb]" '; echo (isset($option['show_thumb'])) ? "checked=checked" : ""; echo ' value="1">';
			echo				'&nbsp;&nbsp;Height:&nbsp;<input type="text" name="wppl_shortcode[' .$e_id .'][thumb_height]" '; echo ($option['thumb_height']) ? 'value="' . $option['thumb_height'] . '"' : 'value="200"'; echo '" size="2">px';
			echo				'&nbsp;&nbsp;&nbsp;Width:&nbsp;<input type="text" name="wppl_shortcode[' .$e_id .'][thumb_width]" '; echo ($option['thumb_width']) ? 'value="' . $option['thumb_width'] . '"' : 'value="200"'; echo '" size="2">px';
			echo			'</p>';
			echo    	'</td>';
			echo	'</tr>';

			echo	'<tr style=" height:40px;">';
			echo		'<td>';
			echo  			'<p><label for="label-excerpt-' . $e_id . '">Show excerpt:</label></p>';
			echo		'</td>';
			echo		'<td>';
			echo    		'<p><input type="checkbox" name="wppl_shortcode[' .$e_id .'][show_excerpt]" '; echo (isset($option['show_excerpt'])) ? "checked=checked" : ""; echo '" value="1"></p>';
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
 	?>
 		<a href="http://www.wpplaceslocator.com" target="_blank">Wordpress Places locator </a> Developed by Eyal Fitoussi.</p>
 		
 		<div style="display:none;">
			<li class="wppl-post-info-holder" id="wppl-shortcode-info-holder-placeholder">
				<table class="widefat fixed">
					<th>Create Shortcode</th>
					<tr>
						<td>
							<p><label for="element-page-id">Choose the post types to appear in the search form (no categories for multiple post types): </label></p>
							<input type="hidden" name="form_id" value="<?php echo (isset($tt)) ? (max($tt) + 1) : "1"; ?>" />
						</td>
						<td>
						<?php   foreach ($posts as $post) {  ?>
							<p><input type="checkbox" name="post_types" value="<?php echo $post; ?>" id="new" style="margin-left: 10px;" onchange="change_it(this.name,this.id);"><?php echo get_post_type_object($post)->labels->name; ?></p>
						<?php	} ?>
						</td>
					</tr>
					<tr>
						<td>
							<p><label for="label-taxonomies">Choose the taxonomies to appear in the search form:</label></p>
						</td>
						<td class="taxes-new">
						<?php	foreach ($posts as $post) {
									$taxes = get_object_taxonomies($post);
									echo	'<div id="' . $post . '_cat_new' . '" style="display: none;">';
									foreach ($taxes as $tax) { 
				 						if (is_taxonomy_hierarchical($tax)) { 
										echo '<p><input type="checkbox" name="taxonomies" value="' . $tax .'" style="margin-left: 10px;" />' . get_taxonomy($tax)->labels->singular_name . '</p>';
										}
									}
									echo	'</div>';
								} ?>
						</td>
					</tr>
					<tr style=" height:40px;">
						<td>
							<p><label for="element-page-id">Results Styling:</label></p>
						</td>
						<td>
							<p>
							<select name="result_style">
								<option value="default">Default</option>
								<option value="blue">Blue</option>
							</select>
							</p>
						</td>
					</tr>
					<tr>
						<td>
							<label for="element-page-id">Google Map:</label><br />
						</td>
						<td>
							<p style="line-height:40px;">
							Show Map <input type="checkbox" name="show_map" value="1" size="45">&nbsp;&nbsp;height:&nbsp;<input type="text" name="map_height" value="" size="5">px &nbsp;Width:&nbsp;<input type="text" name="map_width" value="" size="5">px
							&nbsp;&nbsp;&nbsp;
							<br />
							Map Type:&nbsp;
							<select name="map_type">
								<option value="ROADMAP">ROADMAP</option>
								<option value="SATELLITE">SATELLITE</option>
								<option value="HYBRID">HYBRID</option>
								<option value="TERRAIN">TERRAIN</option>
							</select>
							&nbsp;&nbsp;&nbsp;Auto Zoom Map:&nbsp;
							<input type="checkbox" name="auto_zoom" value="1">
							&nbsp;or &nbsp;&nbsp;Zoom Level:&nbsp;
							<select name="zoom_level">
							<?php	for ($i=1; $i< 18 ; $i++) { 			
								echo '<option value="' .$i. '">'.$i.'</option>';
								} ?>
							</select>(will not count if auto zoom is checked).
							</p>
						</td>
					</tr>
					<tr style=" height:40px;">
						<td>
							<p><label for="element-page-id">Feature Imgae Settings:</label></p>
						</td>
						<td>
							<p>Show Feature Image&nbsp;<input type="checkbox" name="show_thumb" value="1">
							&nbsp;&nbsp;Height:&nbsp;<input type="text" name="thumb_height" value="" size="2">px
							&nbsp;&nbsp;&nbsp;Width:&nbsp;<input type="text" name="thumb_width" value="" size="2">px
							</p>
						</td>
					</tr>
					<tr style=" height:40px;">
						<td>
							<p><label for="element-page-id">Show excerpt:</label></p>
						</td>
						<td>
							<p><input type="checkbox" name="show_excerpt" value="1"></p>
						</td>
					</tr>
					<tr>
						<td>
							<p><label for="element-page-id">Results per page: </label></p>
						</td>
						<td>
							<p><input type="text" name="per_page" value="" size="2"></p>
						</td>
					</tr>
					<tr>
						<td>
							<p><label for="element-page-id">Show driving distance:</label></p>
						</td>
						<td>
							<p><input type="checkbox" name="by_driving" value="1"></p>
						</td>
					</tr>
					<tr>
						<td>
							<p><label for="element-page-id">Show "get directions" link: </label></p>
						</td>
						<td>
							<p><input type="checkbox" name="get_directions" value="1" ></p>
						</td>
					</tr>
					<tr>
						<td>
							<p><input name="Submit" type="submit" value="<?php echo esc_attr_e('Save Changes'); ?>" /></p>
						</td>
					</tr>
					<tr>
						<td>
							<a href="#">Remove</a>
						</td>
					</tr>
				</table>
			</li>	
		</div>
<?php
		echo '<script src="'. plugins_url('js/admin.js', __FILE__) . '" type="text/javascript"></script>';

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
  	$options['results_page'] = $input['results_page'];
  	$options['google_api'] = $input['google_api'];

return $options;
}
?>