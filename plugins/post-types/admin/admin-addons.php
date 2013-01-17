<?php function wppl_pt_addon_page($wppl_on) { ?>
<tr>
	<td>
	<div class="wppl-settings">
		<p>
		<span>
			<span class="add-on-image"><img src="" /></span><span><?php echo _e('Post Types:','wppl'); ?></span>
			<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
		</span>
		<div class="clear"></div>
		<span class="wppl-help-message">
			<?php _e('This feature will let you and the users of your site add a location to any post type or pages you choose. Also you will be able to create an advance search form to let users 
			to search the site based on post types, categories, distance and units and get detailed results ordered by the distance.', 'wppl'); ?>
		</span>
		</p>
	</div>
	</td>		
	<td>
		<p style="color:brown; font-size:12px;"><input name="wppl_plugins[post_types] " type="checkbox" value="1" <?php if ( isset( $wppl_on['post_types'] ) && $wppl_on['post_types'] == 1 ) echo ' checked="checked"'; ?>/></p>
		<span class="addon-error-message"></span>
	</td>
</tr>
<?php } add_action('wppl_addons_page_plugins', 'wppl_pt_addon_page', 1,1); ?>