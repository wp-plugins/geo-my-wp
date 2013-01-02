<ul class="wppl-shortcodes-list" style="margin:0;">
	<?php $yy = 0; $hide_not = array(); $hide_yes = array(); $posts_type = array(); $friends_type = array(); $groups_type = array();$grand_map = array();
		
			if (!empty($options_r)) {
				$tt = array_keys($options_r);
				foreach ($options_r as $option) :
					$e_id = $tt[$yy]; // element id //
					
					if(!$option['form_type']) {
						if ($option['friends_search']) { $option['form_type'] = 'friends' ;} else  { $option['form_type'] = 'posts' ;}
					}
					
					if ($option['form_type'] == 'posts') {
						array_push($posts_type, $e_id);
					} elseif ($option['form_type'] == 'friends') {
						array_push($friends_type, $e_id);
					} elseif ($option['form_type'] == 'groups') {
						array_push($groups_type, $e_id);
					} elseif ($option['form_type'] == 'grand_map') {
						array_push($grand_map, $e_id);
					}
					
					echo '<script type="text/javascript">'; 	
						echo 	'postsType= '.json_encode($posts_type),';'; 
						echo 	'friendsType= '.json_encode($friends_type),';'; 
						echo 	'groupsType= '.json_encode($groups_type),';';
						echo 	'grandMap= '.json_encode($grand_map),';'; 
					echo '</script>';		
					
					 ?>
			
			<li class="wppl-shortcode-info-holder" id="wppl-shortcode-info-holder-<?php echo $e_id; ?>">
			
			<table class="widefat" style="margin-bottom: -2px;" id="shortcode-header-table-<?php echo $e_id; ?>">
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$e_id.'][form_type]'; ?>" value="<?php echo $option[form_type];?>">
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$e_id.'][form_id]'; ?>" value="<?php echo $e_id;?>">
				<thead>
					<th>
						<?php if ($option['form_type'] == 'posts') { ?>
							<img src="<?php echo plugins_url('/geo-my-wp/admin/images/wp-icon.png'); ?>" width="40px" height="40px" style="float:left;" /> <span style="fonr-weight:bold;font-size:14px;float:left;padding: 10px;">Post types search form </span>
						<?php } elseif ($option['form_type'] == 'friends') { ?>
							<img src="<?php echo plugins_url('/geo-my-wp/admin/images/bp-members-icon.png'); ?>" width="40px" height="40px" style="float:left;" /> <span style="fonr-weight:bold;font-size:14px;float:left;padding: 10px;">BP Friends search form</span>
						<?php } elseif ($option['form_type'] == 'groups') { ?>
							<img src="<?php echo plugins_url('/geo-my-wp/admin/images/bp-groups-icon.png'); ?>" width="40px" height="40px" style="float:left;" /> <span style="fonr-weight:bold;font-size:14px;float:left;padding: 10px;">BP Groups search form</span>
						<?php }?>
						<p style="float:left;margin-top:10px;margin-left:10px;color: #21759B;">[wppl form="<?php echo $e_id; ?>"]</p>
					</th>
					<th>
						<h4><?php echo _e('Copy this shortcode <code style="font-size:12px;color: #21759B;">[wppl form="'.$e_id.'"]</code>and paste it into any page to display the search form and results','wppl'); ?></h4>
					</th>
				</thead>
				<tr>
					<td>
						<p><input type="button" class="wppl-edit-btn" onclick="jQuery('.edit-shortcode-<?php echo $e_id; ?>').slideToggle('slow');" value="<?php _e('Edit'); ?>">
						<input type="button" class="wppl-remove-btn" onclick="removeElement(jQuery(this).closest('.wppl-shortcode-info-holder'));" value="<?php _e('Remove'); ?>"></p>
			
					</td>
					<td></td>
				</tr>
			</table>
			
			<!-- shortcode starts here -->
			
			<div class="edit-shortcode-<?php echo $e_id; ?>" style="display:none">
				<table class="widefat" id="shortcode-table-<?php echo $e_id; ?>">
					<th><?php echo _e('Search form:' , 'wppl'); ?></th>
					<th></th>		

					<tr style="height:40px;" class="post-types-yes">
						<td>
							<p><label for="label-post-types-<?php echo $e_id; ?>"><?php echo _e('Post types:','wppl'); ?></label></p>
						</td>
						<td id="posts-checkboxes-<?php echo $e_id; ?>" <?php if (!$option['post_types']) { echo 'style="background: #FAA0A0"' ;}; ?>>
							<?php foreach ($posts as $post) { ?>
							<p><input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][post_types][]'; ?>" value="<?php echo $post; ?>" style="margin-left: 10px;" id="<?php echo $e_id; ?>" onchange="change_it(this.name,this.id);" <?php if ($option['post_types']) { echo (in_array($post, $option['post_types'])) ? ' checked=checked' : '';} ?>>&nbsp;&nbsp;<?php echo get_post_type_object($post)->labels->name; ?></p>
							<?php } ?>
						</td>
					</tr>
					
					<tr style="height:40px;" class="grand-map-yes">
						<td>
							<p><label for="label-post-types-<?php echo $e_id; ?>"><?php echo _e('Post types:','wppl'); ?></label></p>
						</td>
						<td id="posts-checkboxes-<?php echo $e_id; ?>" <?php if (!$option['post_types']) { echo 'style="background: #FAA0A0"' ;}; ?>>
							<?php foreach ($posts as $post) { ?>
							<p><input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][post_types][]'; ?>" value="<?php echo $post; ?>" style="margin-left: 10px;" id="<?php echo $e_id; ?>" onchange="change_it(this.name,this.id);" <?php if ($option['post_types']) { echo (in_array($post, $option['post_types'])) ? ' checked=checked' : '';} ?>>&nbsp;&nbsp;<?php echo get_post_type_object($post)->labels->name; ?></p>
							<?php } ?>
						</td>
					</tr>
			
					<tr style=" height:40px;" class="post-types-yes groups-not friends-not" >
					 	<td>
							<p><label for="label-taxonomies-<?php echo $e_id; ?>"><?php echo _e('Taxonomies: (no taxonomies for multiple post types):','wppl'); ?></label></p>
						</td>
						<td class="taxes-<?php echo $e_id; ?>" style=" padding: 8px;">
							<?php foreach ($posts as $post) {
									$taxes = get_object_taxonomies($post);
									echo '<div id="' . $post . '_cat_' . $e_id . '" '; echo ((count($option['post_types']) == 1) && (in_array($post, $option['post_types']))) ? 'style="display: block; " ' : 'style="display: none;"'; echo '>';
									foreach ($taxes as $tax) { 
				 						if (is_taxonomy_hierarchical($tax)) { 
											echo '<p><input type="checkbox" name="wppl_shortcode[' .$e_id .'][taxonomies][]" value="' . $tax .'" '; if($option['taxonomies']) { echo (in_array($tax , $option['taxonomies'])) ? "checked=checked" : "";}; echo  ' style="margin-left: 10px; " />&nbsp;&nbsp;' . get_taxonomy($tax)->labels->singular_name . '</p>';
										}
									}
									echo '</div>';
								} ?>
						</td>
					</tr>
					
					<?php if ($wppl_on['friends']) { ?>
					<tr style="height:40px;" class="post-types-not groups-not friends-yes">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<span><?php echo _e('Profile fields:','wppl'); ?></span>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('The profile fields you choose here will be displayed in the search form. They will be used to filter results based on the profile fields the user chooses. Only checkbox, select, multiselect, and radio buttons types fields will show here - no text input type fields. Also you can choose one of your date fields to work as an "Age range" field.', 'wppl'); ?>
								</span>
							</p>
						</div>		
						</td>
						<td id="profile-fields-checkboxes-<?php echo $e_id; ?>">
							<p><?php global $bp; wppl_bp_admin_profile_fields($e_id, $option, 'profile_fields'); ?></p>	
						</td>
					</tr>
					<?php } ?>
					
					<?php do_action('wppl_sf_shortcodes_before_address_field' , $wppl_on, $wppl_options, $option, $e_id); ?>
					
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-address-title-<?php echo $e_id; ?>"><?php echo  _e("Address Fields Title"); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Type the title for the address field of the search form. for example "Enter your address". this title wll be displayed either next to the address input field or within if you check the checkbox for it. ', 'wppl'); ?>
								</span>
							</p>
						</div>		
						
						</td>
						<td>
							<p>
								<?php echo _e('Field title:','wppl'); ?>
								<input name="<?php echo 'wppl_shortcode[' .$e_id .'][address_title]'; ?>" size="40" type="text" value="<?php echo ($option[address_title]) ? $option[address_title] : 'zipcode, city & state or full address...' ; ?>" />
								<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][address_title_within]'; ?>" <?php echo (isset($option['address_title_within'])) ? " checked=checked " : ""; ?>>	
								<?php echo _e('Within the input field','wppl'); ?>
							</p>
						</td>
					</tr>
			
					<tr style=" height:40px;">
						<td>
							<div class="wppl-settings">
							<p>
								<span>
									<label for="label-distance-<?php echo $e_id; ?>"><?php echo _e('Radius values:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Check the "multiple" checkbox if you want to have a select dropdown menu of multiple radius values in the search form. then enter the values you in the input box comma separated. If the checkbox in unchecked the last value in the values list will be the default radius value.', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p>
							<input type="checkbox"  value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][display_radius]'; ?>" <?php echo (isset($option['display_radius'])) ? " checked=checked " : ""; ?>>
							<?php echo _e('Multiple:','wppl'); ?>&nbsp;
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('Values (comma separated):','wppl'); ?>&nbsp;
							<input type="text" name="<?php echo 'wppl_shortcode[' .$e_id .'][distance_values]'; ?>" size="20" <?php echo ($option['distance_values']) ? ' value="' . $option['distance_values'] . '"' : ' value="5,10,25,50"'; ?>></p>				
						</td>
					</tr>
			
					<tr style=" height:40px;">
						<td> 
							<div class="wppl-settings">
							<p>
								<span>
									<label for="label-distance-<?php $e_id; ?>"><?php echo _e('Units:' , 'wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Check the "both" checkbox to have a select dropdown list of both Miles and Kilometer in the search form. Otherwise, leave the box unchecked and choose the default units you want to use.', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p>
							<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][display_units]'; ?>" <?php echo (isset($option['display_units'])) ? "checked=checked" : ""; ?>> 
							<?php echo _e('Both','wppl'); ?>
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('or choose one:','wppl'); ?>
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][units_name]'; ?>">
								<option value="imperial" <?php echo ($option['units_name'] == "imperial" ) ?'selected="selected"' : ""; ?>>Miles</option>
								<option value="metric" <?php echo ($option['units_name'] == "metric" ) ?'selected="selected"' : ""; ?>>Kilometers</option>
							</select>			
						</td>
					</tr>
					
					<tr>
						<td>
						<div class="wppl-settings">
							<p>
							<span>
								<span><?php _e('Locator icon:', 'wppl'); ?></span>
								<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
							</span>
							<div class="clear"></div>
								<span class="wppl-help-message"><?php _e('Choose if to display the locator button in the search form. This button will get the user&#39;s current location and submit the search form based of that. you can choose one of the default icons or you can add icon of your own. ', 'wppl'); ?></span>
							</p>
						</div>
						</td>
						<td>
							<p>
							<span style="float:left"><input name="<?php echo 'wppl_shortcode[' .$e_id .'][locator_icon][show]'; ?>" type="checkbox" value="1" <?php if ($option['locator_icon'][show] == 1) echo ' checked="checked"'; ?>/>
								<?php echo _e('Yes','wppl'); ?>
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							</span>
							<span style="width:365px;float:left;margin-left:10px;">	
								<?php $locator_icons = glob(GMW_PATH . '/images/locator-images/*.png');
								$display_icon = GMW_URL. '/images/locator-images/';
								foreach ($locator_icons as $locator_icon) { ?>
								<span style="float:left;">
									<input type="radio" name="<?php echo 'wppl_shortcode[' .$e_id .'][locator_icon][icon]'; ?>" value="<?php echo basename($locator_icon); ?>" <?php if ($option['locator_icon']['icon'] == basename($locator_icon) ) echo  ' checked="checked"'; ?> />
									<img src="<?php echo $display_icon.basename($locator_icon); ?>" height="30px" width="30px"/>
									&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								</span>
								<?php } ?>
							</span>
							</p>	
						</td>
					</tr>
			
					<th><?php echo _e('Results','wppl'); ?></th>
					<th></th>
					<tr style=" height:40px;" class="post-types-yes groups-not friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-styling-<?php echo $e_id; ?>"><?php echo _e('Results template:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Choose the look of the results page. you can always modify the themes by adding your own styling. "custom" theme is a very plain and simple theme for you to modify it the way you want. ', 'wppl'); ?>
								</span>
							</p>
						</div>	
						
						</td>
						<td>
							<p> 
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][results_template]'; ?>">
								<?php foreach ( glob(GMW_PT_PATH .'themes/*', GLOB_ONLYDIR) as $dir ) { ?>
									<option value="<?php echo basename($dir); ?>" <?php if ($option['results_template'] == basename($dir) ) echo 'selected="selected"'; ?>><?php echo basename($dir); ?></option>
								<?php } ?>
								
							</select>
							</p>
						</td>
					</tr>
					
					<?php if ($wppl_on['friends']) { ?>
					<tr style=" height:40px;" class="post-types-not groups-not friends-yes">
						<td>
							<p><label for="label-styling-<?php echo $e_id; ?>"><?php echo _e('Results template:','wppl'); ?></label></p>
						</td>
						<td>
							<p> 
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][results_template]'; ?>">
								<option value="default" <?php echo ($option['results_template'] == "default" ) ? 'selected="selected"' : ""; ?>>Default</option>						
							</select>
							</p>
						</td>
					</tr>
					<?php } ?>
					
					<?php if ($wppl_on['groups']) { ?>
					<tr style=" height:40px;" class="post-types-not groups-yes friends-not">
						<td>
							<p><label for="label-styling-<?php echo $e_id; ?>"><?php echo _e('Results template:','wppl'); ?></label></p>
						</td>
						<td>
							<p> 
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][results_template]'; ?>">
								<option value="default" <?php echo ($option['results_template'] == "default" ) ? 'selected="selected"' : ""; ?>>Default</option>						
							</select>
							</p>
						</td>
					</tr>
					<?php } ?>
			
					<tr style=" height:40px;" class="grand-map-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-styling-<?php echo $e_id; ?>"><?php echo _e('Main wrapper width:','wppl');?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('This is the DIV tag that wraps the map and the results. you can choose the exact size for it or leave it empty for 100% to cover the content area.. ', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p>
								<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][main_wrapper_width]'; ?>" <?php echo ($option['main_wrapper_width']) ? 'value="' . $option['main_wrapper_width'] . '"' : 'value=""' ?>>px&nbsp;&nbsp;<?php echo _e('(100% if left empty)','wppl'); ?>
							</p>
						</td>
					</tr>
			
					<tr style=" height:40px;" class="grand-map-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-display-results-<?php echo $e_id; ?>"><?php echo _e('Results output:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Choose if you want to show the results using the map only , posts only or both map and the results below it.', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p><input type="radio" name="<?php echo 'wppl_shortcode[' .$e_id .'][results_type]'; ?>" value="both" <?php echo ($option['results_type'] == "both") ? "checked=checked" : ""; ?> />&nbsp;&nbsp;<?php echo _e('Both posts and map','wppl'); ?>
							<p><input type="radio" name="<?php echo 'wppl_shortcode[' .$e_id .'][results_type]'; ?>"  value="posts"  <?php echo ($option['results_type'] == "posts") ? "checked=checked" : ""; ?>/>&nbsp;&nbsp;<?php echo _e('Posts only','wppl'); ?>
							<p><input type="radio" name="<?php echo 'wppl_shortcode[' .$e_id .'][results_type]'; ?>" value="map"  <?php echo ($option['results_type'] == "map") ? "checked=checked" : ""; ?>/>&nbsp;&nbsp;<?php echo _e('Map only','wppl'); ?>
						</td>
					</tr>
					
					<tr style=" height:40px;" class="grand-map-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-display-results-<?php echo $e_id; ?>"><?php echo _e('Google Map:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Settings for the main google map. Define its height and width in PX otherwise it will by 500 X 500. Choose the map type from the dropdown menu . check the "auto zoom" checkbox if you want all the markers to fit in the map or you can choose a custom zoom level from the dropdown menu.', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p style="line-height: 40px;">
							<?php echo _e('Width:','wppl'); ?>
							&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" name="<?php echo 'wppl_shortcode[' .$e_id .'][map_width]'; ?>" value="<?php echo $option['map_width']; ?>" size="2">px
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('height:','wppl'); ?>
							&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" name="<?php echo 'wppl_shortcode[' .$e_id .'][map_height]'; ?>" value="<?php echo $option['map_height']; ?>" size="2">px
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('Map Type:','wppl'); ?>
							<?php echo 				
							'<select name="wppl_shortcode[' .$e_id .'][map_type]">
								<option value="ROADMAP" '; echo ($option['map_type'] == "ROADMAP" ) ?'selected="selected"' : ""; echo '>ROADMAP</option>
								<option value="SATELLITE" '; echo ($option['map_type'] == "SATELLITE" ) ?'selected="selected"' : ""; echo '>SATELLITE</option>
								<option value="HYBRID" '; echo ($option['map_type'] == "HYBRID" ) ?'selected="selected"' : ""; echo '>HYBRID</option>
								<option value="TERRAIN" '; echo ($option['map_type'] == "TERRAIN" ) ?'selected="selected"' : ""; echo '>TERRAIN</option>
							</select>'
							?>
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][auto_zoom]'; ?>" <?php echo (isset($option['auto_zoom'])) ? "checked=checked" : ""; ?>>
							<?php echo _e('Auto zoom:','wppl'); ?>&nbsp;
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('Or zoom lever:','wppl'); ?>&nbsp; 
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][zoom_level]'; ?>">
							<?php for ($r=1; $r< 18 ; $r++) { 			
								echo '<option value="' .$r. '"'; echo ($option['zoom_level'] == $r ) ? 'selected="selected"' : ""; echo '>'.$r.'</option>';
								} ?>						
							</select><?php echo _e('(will not count if auto zoom is checked)','wppl'); ?></p>
						</td>
					</tr>
					
					<tr style=" height:40px;" class="grand-map-yes">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-display-results-<?php echo $e_id; ?>"><?php echo _e('Google Map:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Settings for the main google map. Define its height and width in PX otherwise it will by 500 X 500. Choose the map type from the dropdown menu . check the "auto zoom" checkbox if you want all the markers to fit in the map or you can choose a custom zoom level from the dropdown menu.', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p style="line-height: 40px;">
							<?php echo _e('Width:','wppl'); ?>
							&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" name="<?php echo 'wppl_shortcode[' .$e_id .'][map_width][value]'; ?>" value="<?php echo $option[map_width][value]; ?>" size="2">
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][map_width][units]'; ?>">
								<option value="px" <?php if ($option[map_width][units] == 'px') echo 'selected="selected"'; ?>>Px</option>
								<option value="%" <?php if ($option[map_width][units] == '%') echo 'selected="selected"'; ?>>%</option>
							</select>
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('height:','wppl'); ?>
							&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" name="<?php echo 'wppl_shortcode[' .$e_id .'][map_height][value]'; ?>" value="<?php echo $option['map_height'][value]; ?>" size="2">
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][map_height][units]'; ?>">
								<option value="px" <?php if ($option[map_height][units] == 'px') echo 'selected="selected"'; ?>>Px</option>
								<option value="%" <?php if ($option[map_height][units] == '%') echo 'selected="selected"'; ?>>%</option>
							</select>
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('Map Type:','wppl'); ?>
							<?php echo 				
							'<select name="wppl_shortcode[' .$e_id .'][map_type]">
								<option value="ROADMAP" '; echo ($option['map_type'] == "ROADMAP" ) ?'selected="selected"' : ""; echo '>ROADMAP</option>
								<option value="SATELLITE" '; echo ($option['map_type'] == "SATELLITE" ) ?'selected="selected"' : ""; echo '>SATELLITE</option>
								<option value="HYBRID" '; echo ($option['map_type'] == "HYBRID" ) ?'selected="selected"' : ""; echo '>HYBRID</option>
								<option value="TERRAIN" '; echo ($option['map_type'] == "TERRAIN" ) ?'selected="selected"' : ""; echo '>TERRAIN</option>
							</select>'
							?>
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][auto_zoom]'; ?>" <?php echo (isset($option['auto_zoom'])) ? "checked=checked" : ""; ?>>
							<?php echo _e('Auto zoom:','wppl'); ?>&nbsp;
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('Or zoom lever:','wppl'); ?>&nbsp; 
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][zoom_level]'; ?>">
							<?php for ($r=1; $r< 18 ; $r++) { 			
								echo '<option value="' .$r. '"'; echo ($option['zoom_level'] == $r ) ? 'selected="selected"' : ""; echo '>'.$r.'</option>';
								} ?>						
							</select><?php echo _e('(will not count if auto zoom is checked)','wppl'); ?></p>
						</td>
					</tr>
							
					<?php if ($wppl_on['groups']) { ?>
					<tr style="height:40px;" class="post-types-not groups-yes friends-no">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-per-post-icon-<?php echo $e_id; ?>"><?php echo _e('Per group map&#39;s icon:', 'wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
									<span class="wppl-premium-message"></span>
									<?php echo (!$wppl_options['per_group_icon']) ? '<span class="wppl-turnon-message">(Check the "Allow groups admin to choose map icon" in the main settings page in order to use this feature.)</span>' : ''; ?>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Check this checkbox if you want each group to use its own google map icon. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td class="wppl-premium-version-only">
							<p>
								<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][per_group_icon]'; ?>" <?php echo ( isset($option['per_group_icon']) ) ? " checked=checked " : ""; echo ' value="1" '; echo((!$wppl_on['groups']) ||  (!$wppl_options['per_group_icon']) ) ? "disabled" : ""; ?>>
								<?php echo _e('Yes','wppl'); ?>
							</p>
						</td>
					</tr>
					<?php } ?>
					
					<?php do_action('wppl_sf_shortcodes_after_map' , $wppl_on, $wppl_options, $option, $e_id); ?>
					
					<tr style=" height:40px;" class="grand-map-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-styling-<?php echo $e_id; ?>"><?php echo _e('Single result styling:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('This control the height and width of each result within the results page. Define its width and height in PX otherwise it will be uses based on the stylesheet.', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p> 
								<?php echo _e('Width:','wppl'); ?>&nbsp;&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][single_result_width]'; ?>" <?php echo ($option['single_result_width']) ? 'value="' . $option['single_result_width'] . '"' : 'value=""'; ?>>px
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Height:','wppl'); ?>&nbsp;&nbsp;<input type="text" size="2" onkeyup="this.value=this.value.replace(/[^\d]/,'')" name="<?php echo 'wppl_shortcode[' .$e_id .'][single_result_height]'; ?>" <?php echo ($option['single_result_height']) ? 'value="' . $option['single_result_height'] . '"' : 'value=""'; ?>>px
								&nbsp;&nbsp;<?php echo _e('(stylesheet if left empty)','wppl'); ?>
							</p>
						</td>
					</tr>
				
					<tr style=" height:40px;" class="post-types-yes groups-not friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-show-thumb-<?php echo $e_id; ?>"><?php echo _e('Featured image:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Display featured image and define its width and height in PX. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
								<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][show_thumb]'; ?>" <?php echo (isset($option['show_thumb'])) ? "checked=checked" : ""; ?>>
								<?php echo _e('Yes','wppl'); ?>
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Height:','wppl'); ?>
								&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')"  size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][thumb_height]'; ?>" <?php echo ($option['thumb_height']) ? 'value="' . $option['thumb_height'] . '"' : 'value="200"'; ?>>px
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Width:','wppl'); ?>
								&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')"  size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][thumb_width]'; ?>" <?php echo ($option['thumb_width']) ? 'value="' . $option['thumb_width'] . '"' : 'value="200"'; ?>>px
							</p>
						</td>
					</tr>
					
					<tr style=" height:40px;" class="post-types-not groups-no friends-yes">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-show-thumb-<?php echo $e_id; ?>"><?php echo _e('Avatar:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Display members avatar and define its width and height in PX. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
								<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][show_thumb]'; ?>" <?php echo (isset($option['show_thumb'])) ? "checked=checked" : ""; ?>>
								<?php echo _e('Yes','wppl'); ?>
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Height:','wppl'); ?>
								&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')"  size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][thumb_height]'; ?>" <?php echo ($option['thumb_height']) ? 'value="' . $option['thumb_height'] . '"' : 'value="200"'; ?>>px
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Width:','wppl'); ?>
								&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')"  size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][thumb_width]'; ?>" <?php echo ($option['thumb_width']) ? 'value="' . $option['thumb_width'] . '"' : 'value="200"'; ?>>px
							</p>
						</td>
					</tr>
					<tr style=" height:40px;" class="post-types-not groups-yes friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-show-thumb-<?php echo $e_id; ?>"><?php echo _e('Avatar:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Display groups avatar and define its width and height in PX. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
								<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][show_thumb]'; ?>" <?php echo (isset($option['show_thumb'])) ? "checked=checked" : ""; ?>>
								<?php echo _e('Yes','wppl'); ?>
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Height:','wppl'); ?>
								&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')"  size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][thumb_height]'; ?>" <?php echo ($option['thumb_height']) ? 'value="' . $option['thumb_height'] . '"' : 'value="200"'; ?>>px
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Width:','wppl'); ?>
								&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')"  size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][thumb_width]'; ?>" <?php echo ($option['thumb_width']) ? 'value="' . $option['thumb_width'] . '"' : 'value="200"'; ?>>px
							</p>
						</td>
					</tr>
					<tr style=" height:40px;" class="post-types-yes groups-not friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-show-thumb-<?php echo $e_id; ?>"><?php echo _e('Additional information:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Show the fields of the additional information you want to display for each result. ', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p>
								<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][additional_info][phone]'; ?>" value="1" <?php echo (isset($option['additional_info']['phone'])) ? "checked=checked" : ""; ?>>&nbsp;<?php echo _e('Phone','wppl'); ?>&nbsp;&nbsp;
								<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][additional_info][fax]'; ?>" value="1" <?php echo (isset($option['additional_info']['fax'])) ? "checked=checked" : ""; ?>>&nbsp;<?php echo _e('Fax','wppl'); ?>&nbsp;&nbsp;
								<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][additional_info][email]'; ?>" value="1" <?php echo (isset($option['additional_info']['email'])) ? "checked=checked" : ""; ?>>&nbsp;<?php echo _e('Email','wppl'); ?>&nbsp;&nbsp;
								<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][additional_info][website]'; ?>" value="1" <?php echo (isset($option['additional_info']['website'])) ? "checked=checked" : ""; ?>>&nbsp;<?php echo _e('Website','wppl'); ?>&nbsp;&nbsp;
							</p>
						</td>
					</tr>
			
					<tr style=" height:40px;" class="post-types-yes groups-not friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-excerpt-<?php echo $e_id; ?>"><?php echo _e('Excerpt:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('This featured will grab the number of words that you choose from the post content and display it in the resut. You can give it a very high number (ex. 99999) to display the entire content. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
								<input type="checkbox"  value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][show_excerpt]'; ?>" <?php echo (isset($option['show_excerpt'])) ? "checked=checked" : ""; ?>>
								<?php echo _e('Yes','wppl'); ?>&nbsp;
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Words count:','wppl'); ?>
								<input type="text" name="<?php echo 'wppl_shortcode[' .$e_id .'][words_excerpt]'; ?>" value="<?php echo $option['words_excerpt']; ?>" size="5"></p>
						</td>
					</tr>
					
					<tr style=" height:40px;" class="post-types-yes groups-not friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-extra-info-<?php echo $e_id; ?>"><?php echo _e('Show Categories','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Using this feature you can display in each result the categories attached to its post.', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
							<input type="checkbox"  value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][custom_taxes]'; ?>" <?php echo (isset($option['custom_taxes'])) ? "checked=checked" : ""; ?>>
							<?php echo _e('Yes','wppl'); ?>
							</p>
						</td>
					</tr>
				
				<!--	
					<tr style=" height:40px;" class="friends-not">
						<td>
							<p><label for="label-extra-info-<?php echo $e_id; ?>"><?php echo _e('"Extra Information" link:','wppl'); ?></label></p>
						</td>
						<td>
							<p>
							<input type="checkbox"  value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][extra_info]'; ?>" <?php echo (isset($option['extra_info'])) ? "checked=checked" : ""; ?>>
							<?php echo _e('Yes','wppl'); ?>
							</p>
						</td>
					</tr>
			-->
					<tr style=" height:40px;" class="grand-map-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-per-page-<?php echo $e_id; ?>"><?php echo _e('Results per page:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Choose the number of results per page.', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p><input type="text" name="<?php echo 'wppl_shortcode[' .$e_id .'][per_page]'; ?>" value="<?php echo ($option['per_page']) ? $option['per_page'] : '5'; ?>" size="3"></p>
						</td>
					</tr>
			
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-driving-distance-<?php echo $e_id; ?>"><?php echo _e('Driving distance:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('While the results showing the radius distance from the user to the location this feature let you display the exact driving distance. Please note that each driving distance request counts with google API when you can have 2500 requests per day (without a Google API key). also this featured could cause the results being displayed a little slower.', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
							<input type="checkbox"  value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][by_driving]'; ?>" <?php echo (isset($option['by_driving'])) ? "checked=checked" : ""; ?>>
							<?php echo _e('Yes','wppl'); ?>
							</p>
						</td>
					</tr>
			
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-get-directions-<?php echo $e_id; ?>"><?php echo _e('Show "get directions" link:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Display "get directions" link that will open a new window with google map that show the exact driving direction from the user to the location..', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
							<input type="checkbox"  value="1"  name="<?php echo 'wppl_shortcode[' .$e_id .'][get_directions]'; ?>" <?php echo (isset($option['get_directions'])) ? "checked=checked" : ""; ?>>
							<?php echo _e('Yes','wppl'); ?>
							</p>
						</td>
					</tr>
			
					<tr style=" height:40px;">
						<td>
							<p><input type="submit" name="Submit" class="wppl-save-btn" value="<?php echo _e('Save Changes','wppl'); ?>" /></p>
						</td>
						<td></td>
					</tr>
			
				</table>
			</div>

			</li>
			<?php $yy++;
			endforeach;		
			} ?>
			
			<li class="wppl-shortcode-info-holder">
    			<table class="widefat" style="margin-bottom: -2px;">
    				<th>
						<div class="wppl-settings">
							<span>
								<span><h4><?php echo _e('Create new shortcode','wppl'); ?></h4></span>
								<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
							</span>
							<div class="clear"></div>
							<span class="wppl-help-message">
							<p style="font-size: 13px;font-weight: normal;"><?php _e('Create a new shortcode. Click on the button of the shortcode that you want to create, wait for the page to reload then click on "edit" to choose the setting of the shortcode. ', 'wppl'); ?>
							</p></span>
						</div>	
					</th>
    				<tr>
    					<td>
    						<p>	
    							<?php if ( isset( $wppl_on['post_types'] ) && $wppl_on['post_types'] == 1) { ?><input type="button" class="wppl-edit-btn" id="create-new-post-types-shortcode" value="<?php echo _e('Post Types'); ?>" /><?php } ?>
    							<?php if ( isset( $wppl_on['friends'] ) && $wppl_on['friends'] == 1) { ?><input type="button" class="wppl-edit-btn" id="create-new-friends-shortcode" value="Buddypress Members" /><?php } ?>
    							<?php if ( isset( $wppl_on['groups'] ) && $wppl_on['groups'] == 1 ) { ?><input type="button" class="wppl-edit-btn" id="create-new-groups-shortcode" value="Buddypress Groups" /><?php } ?>
   							</p>
   						</td>
   					</tr>
   				</table>
   			</li>
    
    		<?php $next_id = (isset($tt)) ? (max($tt) + 1) : "1"; ?>
  			<input type="hidden" name="element-id" value="<?php echo next_id; ?>" />
  			
 			<div style="display:none;" id="wppl-new-shortcode-fields">
 				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][form_type]'; ?>" id="form-type" value="" disabled/>
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][form_id]'; ?>" value="<?php echo $next_id; ?>" disabled/>
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][address_title]'; ?>" value="Zipcode or full address..." disabled />
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][display_radius]'; ?>" value="1" disabled />
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][results_type]'; ?>" value="both" disabled />
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][auto_zoom]'; ?>" value="1" disabled />
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][map_icon]'; ?>" value="_default.png" disabled />
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][your_location_icon]'; ?>" value="blue-dot.png" disabled />
			</div>
		</ul>
		<?php wp_enqueue_script('wppl-admin'); ?>