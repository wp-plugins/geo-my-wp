<div class="wppl-shortcodes-help">
	<table class="widefat">
		<thead>
			<th><h3><input type="button" class="wppl-edit-btn wppl-shortcodes-help-btn" value="<?php _e('+'); ?>"><?php echo _e("User's Current Location","wppl"); ?></h3></th>
			<th></th>
		</thead>
	</table>
	<div class="open-settings" style="float:left;display:none;width:100%">
		<table class="widefat">
			<tbody>
				<tr>
					<td>
						<p><?php echo esc_attr_e('Paste this code anywhere in the template (ex. header.php) to display the user&#39;s location. :','wppl'); ?></p>
					</td>
					<td>
						<code class="wppl-write-shortcode"> &#60;?php echo do_shortcode(&#39;[wppl_location]&#39;); ?&#62;</code>
					</td>
				</tr>
				<tr>
					<td>
						<p><?php echo esc_attr_e('Display location by city or by zipcode','wppl'); ?> </p>
					</td>
					<td>
						<code class="wppl-write-shortcode">display_by="zipcode|city"</code>
					</td>
				</tr>
				<tr>
					<td>
						<p><?php echo esc_attr_e('Display user name: (1 for yes)','wppl'); ?> </p>
					</td>
					<td>
						<code class="wppl-write-shortcode">show_name="0|1"</code>
					</td>
				</tr>
				<tr>
					<td>
						<p><?php echo esc_attr_e('Show title:','wppl'); ?> </p>
					</td>
					<td>
						<code class="wppl-write-shortcode">title="your title"</code>
					</td>
				</tr>
				<tr>
					<td>
						<p><?php echo esc_attr_e('Shortcode Example:','wppl'); ?> </p>
					</td>
					<td>
						<code> &#60;?php echo do_shortcode(&#39;[wppl_location display_by="zipcode" show_name="1" title="your location"]&#39;); ?&#62; </code>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php do_action('wppl_shortcodes_help_page', $wppl_on); ?>

