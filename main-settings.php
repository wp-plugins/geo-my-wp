<div>
	<table class="widefat">
		<thead>
			<th><h3><input type="button" class="wppl-edit-btn wppl-edit" value="<?php _e('+'); ?>"><?php echo _e('General Settings','wppl'); ?></h3></th>
			<th></th>
		</thead>
	</table>

	<div class="open-settings" style="float:left;display:none;width:100%">
		<table class="widefat">
			<tbody>
				<tr>
					<td>
						<div class="wppl-settings">
							<p>
							<span>
								<label><?php _e('Google Maps API V3 Key:', 'wppl'); ?></label>
								<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
							</span>
							<div class="clear"></div>
							<span class="wppl-help-message"><?php _e('This is optional but will let you track your API requests. you can obtain your free Goole API key <a href="https://code.google.com/apis/console/" target="_blank">here</a>.', 'wppl'); ?></span>
							</p>
						</div>
					</td>
					<td>
						<p><input id="api_key" name="wppl_fields[google_api]" size="40" type="text" value="<?php echo $wppl_options[google_api]; ?>" /></p>
					</td>
				</tr> 
				<tr>
					<td>
						<div class="wppl-settings">
							<p>
							<span>
								<span><?php _e('Country code:', 'wppl'); ?></span>
								<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
							</span>
							<div class="clear"></div>
							<span class="wppl-help-message"><?php _e('Enter you country code. For example for United States enter US. you can find your country code <a href="http://www.wpplaceslocator.com/wp-content/country-code.php" target="blank">here</a>', 'wppl'); ?></span>
							</p>
						</div>
					</td>
					<td>
						<p><input id="country-code" name="wppl_fields[country_code]" size="5" type="text" value="<?php echo $wppl_options[country_code]; ?>" /></p>
					</td>
				</tr> 
				
				<?php do_action('wppl_main_settings_page_main', $wppl_on, $wppl_options, $posts, $pages); ?>
				
				<tr>
					<td>
					<div class="wppl-settings">
						<p>
						<span>
							<span><?php _e('Auto Locator:', 'wppl'); ?> </span>
							<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
						</span>
						<div class="clear"></div>
							<span class="wppl-help-message"><?php _e('This feature will automatically try to get the user\'s current location when first visiting the website.', 'wppl'); ?></span>
						</p>
					</div>
					</td>
					<td>
						<p>
							<input id="wppl-locate-message" name="wppl_fields[auto_locate] " type="checkbox" value="1" <?php if ($wppl_options['auto_locate'] == 1) {echo "checked";}; ?>/>
						</p>
					</td>
				</tr>
				
				<?php if ( isset($wppl_exist['shortcode']) && $wppl_exist['shortcode'] == 1) include GMW_PATH .'/plugins/shortcode-addons/post-types-icons.php'; ?>
				
				<tr>
					<td>
					<div class="wppl-settings">
						<p>
						<span>
							<span><?php _e('Auto Results:', 'wppl'); ?> </span>
							<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
						</span>
						<div class="clear"></div>
							<span class="wppl-help-message"><?php _e('Will automatically run initial search and display results based on the user\'s current location when first goes to a search page. You need to define the radius and the units for this initial search .', 'wppl'); ?></span>
						</p>
					</div>
					</td>
					<td>
						<p>
						<input id="wppl-auto-search" name="wppl_fields[auto_search] " type="checkbox" value="1" <?php if ($wppl_options['auto_search'] == 1) {echo "checked";}; ?>/>
						<?php echo _e('Yes','wppl'); ?>
						&nbsp;&nbsp;&#124;&nbsp;&nbsp;
						<?php echo _e('Radius','wppl'); ?>		
						<input type="text" id="wppl-auto-radius" name="wppl_fields[auto_radius]" SIZE="1" value="<?php echo ($wppl_options['auto_radius']) ? $wppl_options['auto_radius'] : "50"; ?>" />	
						&nbsp;&nbsp;&#124;&nbsp;&nbsp;
						<select id="wppl-auto-units" name="wppl_fields[auto_units]">
							<option value="imperial" <?php echo ($wppl_options['auto_units'] == "imperial") ? "selected" : ""; ?>>Miles</option>
							<option value="metric" <?php echo ($wppl_options['auto_units'] == "metric") ? "selected" : ""; ?>>Kilometers</option>
						</select>
						</p>
					</td>
				</tr>
				
				<tr>
					<td>
					<div class="wppl-settings">
						<p>
						<span>
							<span><?php echo _e('"Greater range search" Link: ','wppl'); ?></span>
							<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
						</span>
						<div class="clear"></div>
							<span class="wppl-help-message">
								<?php _e('When no results found a message with two links will be displayed. One will display all existing locations and another 
								link that will run a search within a greater radius. You need to define the radius in the "Distance value" input box.', 'wppl'); ?>
							</span>
						</p>
					</div>
					</td>
					<td>
						<p>
						<input name="wppl_fields[wider_search] " type="checkbox" value="1" <?php if ($wppl_options['wider_search'] == 1) {echo "checked";}; ?>/>
						<?php echo _e('Yes','wppl'); ?>
						&nbsp;&nbsp;&#124;&nbsp;&nbsp;
						<?php echo _e('Distance value: ','wppl'); ?>
						<input id="wider-search" name="wppl_fields[wider_search_value]" size="5" type="text" value="<?php echo $wppl_options[wider_search_value]; ?>" /></p>
					</td>
				</tr>
				
				<tr>
					<td>
						<p><input name="Submit" class="wppl-save-btn" type="submit" value="<?php echo esc_attr_e('Save Changes'); ?>" /></p>
					</td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>


<?php do_action('wppl_main_settings_page', $wppl_options, $wppl_on, $pages_s, $wppl_site_options); ?> 

