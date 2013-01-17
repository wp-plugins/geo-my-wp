<table class="widefat fixed">
	<thead>
		<th>
			<div class="wppl-settings">
				<span>
					<span><h3><?php echo _e('Add-ons','wppl'); ?></h3></span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
				</span>
				<div class="clear"></div>
				<span class="wppl-help-message">
				<p style="font-size: 13px;font-weight: normal;">
					<?php _e('The add-ons will add extra features to GEO my WP. You can choose to turn on or off any of them. 
					Turn on only the add-ons that you are going to use for a better performance. You need to check the checkboxes for the add-ons you wish to use
					and click on "Save Changes" in order to see the settings for those add-ons in the "Settings" page.', 'wppl'); ?>
				</p></span>
			</div>
		</th>	
	<th></th>
	</thead>
			
	<?php do_action('wppl_addons_page_plugins', $wppl_on); ?>
		
	<tr>
		<td>
			<p><input name="Submit" class="wppl-save-btn" type="submit" value="<?php _e('Save Changes'); ?>" /></p>
		</td>
		<td></td>
	</tr>
	
	<thead>
		<th>
			<div class="wppl-settings">
				<span>
					<span><h3><?php echo _e('Available Post Types Themes','wppl'); ?></h3></span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
				</span>
				<div class="clear"></div>
				<span class="wppl-help-message">
				<p style="font-size: 13px;font-weight: normal;">
					<?php _e('The featured themes will let you choose different styling for the results pages.', 'wppl'); ?>
				</p></span>
			</div>
		</th>	
		<th></th>
	</thead>
	
	<?php if (isset($wppl_on['post_types']) && $wppl_on['post_types'] == 1) { ?>
		<?php do_action('wppl_addons_page_themes', $wppl_on); ?>
		
		<?php foreach ( glob(GMW_PT_PATH .'themes/*', GLOB_ONLYDIR) as $dir ) { ?>
			<tr>
				<td>
					<p style="text-transform: capitalize"><span class="add-on-image"><img src="" /></span><?php echo basename($dir) ?></p>
				</td>
				<td>
				</td>
			</tr>
		<?php } ?>
	<?php } ?>
</table>
</table>