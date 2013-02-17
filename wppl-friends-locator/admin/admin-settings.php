<?php function wppl_fl_basic_admin_settings($wppl_options, $wppl_on, $pages_s) { ?>
<div>
	<table class="widefat">
		<thead>
			<th><h3><input type="button" class="wppl-edit-btn wppl-edit" value="<?php _e('+'); ?>"><?php echo _e('Buddypress - Members Settings','wppl'); ?></h3></th>
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
							<span><?php _e('Choose members (Buddypress) results page :' , 'wppl'); ?></span>
							<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
						</span>
						<div class="clear"></div>
							<span class="wppl-help-message">
								<?php _e('This page will display the search results when using the widget or when you want to have the search form in one page and the results showing in a different page. Choose the page from the dropdown menu and paste the shortcode [wppl_friends_results] into it.', 'wppl'); ?>
							</span>
						</p>
					</div>		
					</td>
					<td>
					<select name="wppl_fields[friends_results_page]" style="float:left" <?php echo(!$wppl_on['friends']) ? "disabled" : "";?>>
						<?php foreach ($pages_s as $page_s) {
						echo '<option value="'.$page_s->post_name.'"'; echo ($wppl_options[friends_results_page] == $page_s->post_name ) ? 'selected="selected"' : "" ; echo '>'. $page_s->post_title . '</option>';
						}
					?>
					</select>
						<p style="float:left;font-size:12px;margin-left:25px"><?php _e('Paste the shortcode', 'wppl'); ?> <code style="color:brown"> [wppl_friends_results] </code><?php echo esc_attr_e('into the results page'); ?></p>
					</td>
				</tr>
				
				<?php do_action('wppl_fl_settings_page', $wppl_options,$wppl_on, $posts, $pages_s); ?>
				
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
<?php } add_action('wppl_main_settings_page', 'wppl_fl_basic_admin_settings', 2,3); ?>