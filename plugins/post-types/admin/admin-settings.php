<?php function wppl_pt_admin_settings($wppl_options,$wppl_on, $pages_s) {
	$posts 	 = get_post_types();
	wp_enqueue_style( 'farbtastic' );
    wp_enqueue_script( 'farbtastic' ); 
 ?>
<script type="text/javascript"> jQuery(document).ready(function($){ $('#wppl-theme-color-picker').farbtastic('#wppl-theme-color'); }); </script>	
<div>
	<table class="widefat">
		<thead>
			<th><h3><input type="button" class="wppl-edit-btn wppl-edit" value="<?php _e('+'); ?>"><?php echo _e('Post types Settings','wppl'); ?></h3></th>
			<th></th>
		</thead>
	</table>

	<div class="open-settings" style="float:left;display:none;width:100%">
		<table class="widefat fixed">
			<tbody>
				<tr>
					<td>
					<div class="wppl-settings">
						<p>
						<span>
							<span><?php _e('Post types:', 'wppl'); ?></span>
							<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
						</span>
						<div class="clear"></div>
							<span class="wppl-help-message">
								<?php _e('Choose the post types where you want the address fields to appear. choose only the post types which you want to add a location too.', 'wppl'); ?>
							</span>
						</p>
					</div>	
					</td>
					<td>			
					<?php foreach ($posts as $post) { 
						echo '<input id="address_fields" type="checkbox" name="wppl_fields[address_fields][]" value="' . $post . '" '; if ($wppl_options['address_fields']) { if (in_array($post, $wppl_options['address_fields'])) echo  ' checked="checked"';} echo  ' style="margin-left: 10px;">' .get_post_type_object($post)->labels->name ;
							}	
					?>
					</td>
				</tr>
				
				<tr>
					<td>
					<div class="wppl-settings">
						<p>
						<span>
							<span><?php _e('Mandatory address fields: ', 'wppl'); ?></span>
							<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
						</span>
						<div class="clear"></div>
							<span class="wppl-help-message">
								<?php _e('Check this box if you want to make sure that users add location to a new post. it will prevent them from saving a post that do not have a location. Otherwise, users will be able to save a post even without a location. This way the post will be published and would show up in Wordpress search results but not in the Proximity search results.  ', 'wppl'); ?>
							</span>
						</p>
					</div>		
					</td>
					<td>
						<p>
						<input name="wppl_fields[mandatory_address] " type="checkbox" value="1" <?php if ($wppl_options['mandatory_address'] == 1) {echo "checked";}; ?>/>
						<?php echo _e('Yes','wppl'); ?>
						</p>
					</td>
				</tr>
				
				<tr>
					<td>
					<div class="wppl-settings">
						<p>
						<span>
							<span><?php _e('Choose results page :' , 'wppl'); ?></span>
							<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
						</span>
						<div class="clear"></div>
							<span class="wppl-help-message">
								<?php _e('This page will display the search results when using the widget or when you want to have the search form in one page and the results showing in a different page. Choose the page from the dropdown menu and paste the shortcode [wppl_results] into it.', 'wppl'); ?>
							</span>
						</p>
					</div>		
					</td>
					<td>
					<select name="wppl_fields[results_page]" style="float:left">
						<?php foreach ($pages_s as $page_s) {
						echo '<option value="'.$page_s->post_name.'"'; echo ($wppl_options[results_page] == $page_s->post_name ) ? 'selected="selected"' : "" ; echo '>'. $page_s->post_title . '</option>';
						}
					?>
					</select>
						<p style="float:left;font-size:12px;margin-left:25px"><?php _e('Paste the shortcode', 'wppl'); ?> <code style="color:brown"> [wppl_results] </code><?php echo esc_attr_e('into the results page'); ?></p>
					</td>
				</tr>
				
				<tr>
					<td>
						<p><?php _e('To display the search form in one page and the results in the "results" page use', 'wppl'); ?><code style="color:brown"> form_only="y" </code><?php _e('in your shortcode:', 'wppl')?></p>
					</td>
					<td>
						<p style="float:left;font-size:12px;"><?php _e('For example you can use:', 'wppl'); ?> <code style="color:brown"> [wppl form="1" form_only="y"] </code><?php _e('in any page to display the search form and the results will show in the results page', 'wppl'); ?></p>
					</td>
				</tr>
				
				<tr>
					<td>
					<div class="wppl-settings">
						<p>
						<span>
							<span><?php _e('Theme color:', 'wppl'); ?></span>
							<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
						</span>
						<div class="clear"></div>
							<span class="wppl-help-message">
								<?php _e('Check the checkbox if you want to use the theme color and choose the color you want. This feature controls the color of the links, address, and the title in the search results. If the checkbox left empty the colors that will be used will be from the stylesheet.', 'wppl'); ?>
							</span>
						</p>
					</div>		
					</td>
					<td>
						<p>
						<input type="checkbox" name="wppl_fields[use_theme_color]" value="1" <?php echo ($wppl_options['use_theme_color']) ? 'checked="checked"' : ''; ?>" />
						<?php echo _e('Yes','wppl'); ?>
						&nbsp;&nbsp;&#124;&nbsp;&nbsp;
						<input type="text" id="wppl-theme-color" name="wppl_fields[theme_color]" value="<?php echo ($wppl_options['theme_color']) ? $wppl_options['theme_color'] : "#2E738F"; ?>" />
						<div id="wppl-theme-color-picker"></div></p>
					</td>
				</tr>
				
				<tr>
					<td>
					<div class="wppl-settings">
						<p>
						<span>
							<span><?php _e('"No Results" title:', 'wppl'); ?></span>
							<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
						</span>
						<div class="clear"></div>
							<span class="wppl-help-message">
								<?php _e('Message to display when no results were found.', 'wppl'); ?>
							</span>
						</p>
					</div>	
					</td>
					<td>
						<p><input  type="text" id="api_key" name="wppl_fields[no_results]" size="40" value="<?php echo ($wppl_options[no_results]) ? $wppl_options[no_results] : "No results were found."; ?>" /></p>
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
<?php } add_action('wppl_main_settings_page', 'wppl_pt_admin_settings', 1,3); ?>