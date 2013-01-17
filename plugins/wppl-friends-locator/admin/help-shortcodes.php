<?php function wppl_fl_shortcodes_help($wppl_on) { ?>
<div class="wppl-shortcodes-help">
	<table class="widefat">
		<thead>
			<th>
			  <div class="wppl-settings">
					<span>
						<span><h3><input type="button" class="wppl-edit-btn wppl-shortcodes-help-btn" value="<?php _e('+'); ?>"><?php echo _e("Buddypress Member's Location","wppl"); ?></h3></span>
						<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
					</span>
					<div class="clear"></div>
					<span class="wppl-help-message">
					<p style="font-size: 13px;font-weight: normal;">
						<?php echo esc_attr_e('You can user this shortcode to display map of Member&#39;s location anywhere on a page. for example you can paste the shortcode in the "member-header.php" to display the map in next to the members avatar 
							when viewing his profile. you can also  use the widget to display it in the side bar.
							The shortcode/widget will display map of a memebrs only when viewing his profile.  :','wppl'); ?>
					</p></span>
				</div>
			</th>
			<th></th>
		</thead>
	</table>
	<div class="open-settings" style="float:left;display:none;width:100%">
		<table class="widefat">
			<tbody>
				<tr>
					<td>
						<p><?php echo esc_attr_e("Copy and paste this code into any template page to display Member's location map :","wppl"); ?></p>
					</td>
					<td>
						<code>&#60;?php echo do_shortcode(&#39;[wppl_member_location]&#39;); ?&#62;</code>
					</td>
				</tr>
				<tr>
					<td>
						<p><?php echo esc_attr_e('Map height (in pixels)','wppl'); ?></p>
					</td>
					<td>
						<code>map_height=" "</code>
					</td>
				</tr>
				<tr>
					<td>
						<p><?php echo esc_attr_e('Map width (in pixels)','wppl'); ?></p>
					</td>
					<td>
						<code>map_width=" "</code>
					</td>
				</tr>
				<tr>
					<td>
						<p><?php echo esc_attr_e('Display address:','wppl'); ?></p>
					</td>
					<td>
						<code>address="0|1"</code>
					</td>
				</tr>
				<tr>
					<td>
						<p><?php echo esc_attr_e('Show directions link:','wppl'); ?></p>
					</td>
					<td>
						<code>Directions="0|1"</code>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php } add_action('wppl_shortcodes_help_page','wppl_fl_shortcodes_help',2,1); ?>