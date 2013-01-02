<?php

function wppl_admin() {
	add_menu_page('GEO my WP', 'GEO my WP', 'manage_options', 'geo-my-wp', 'wppl_plugin_options_page',GMW_URL. '/admin/images/locate-me-menu.png',66);
	$sub_main			= add_submenu_page( 'geo-my-wp', 'General Settings', '+ Settings', 'manage_options', 'geo-my-wp', 'wppl_plugin_options_page');
	$sub_addons			= add_submenu_page( 'geo-my-wp', 'Add-ons', '+ Add-ons', 'manage_options', 'wppl-add-ons', 'wppl_plugins_page');
	$sub_shortcodes		= add_submenu_page( 'geo-my-wp', 'Search form shortcodes', '+ Search form shortcodes', 'manage_options', 'wppl-shortcodes', 'wppl_shortcodes_page');
	$sub_helpshortcodes = add_submenu_page( 'geo-my-wp', 'Shortcodes', '+ Shortcodes', 'manage_options', 'wppl-help-shortcodes', 'wppl_help_shortcodes_page');
	add_action('admin_print_styles-' . $sub_main, 'wppl_javascript');
	add_action('admin_print_styles-' . $sub_shortcodes, 'wppl_javascript');
	add_action('admin_print_styles-' . $sub_addons, 'wppl_javascript');
	add_action('admin_print_styles-' . $sub_helpshortcodes, 'wppl_javascript');
}
add_action('admin_menu', 'wppl_admin');
/////////////////////

function wppl_multisite_menu() {
	$site_settings = add_menu_page('GEO my WP', 'GEO my WP', 'manage_options', 'geo-my-wp', 'wppl_site_admin_options_page',plugins_url('images/locate-me-menu.png', __FILE__),66);
	add_action('admin_print_styles-' . $site_settings, 'wppl_javascript');
}
if( is_multisite() ) add_action( 'network_admin_menu', 'wppl_multisite_menu', 21);

function wppl_plugin_admin_init(){
	register_setting( 'wppl_plugin_options', 'wppl_fields', 'validate_settings' );
	register_setting( 'wppl_plugin_options_1', 'wppl_shortcode');
	register_setting( 'wppl_pb_options', 'wppl_pb_shortcode');
	register_setting( 'wppl_plugin_plugins_options', 'wppl_plugins', 'validate_plugins_settings');
	}
add_action('admin_init', 'wppl_plugin_admin_init');
///////////////////

if ( is_multisite() && isset( $_POST['wppl_admin_submit'] )  ) {   
	  $wppl_site_options = $_POST['wppl_site_options'];
	  $wppl_site_options['xprofile_address'] = preg_replace('/[^0-9]+/', '', $_POST['wppl_site_options']['xprofile_address']);
	  //if(!current_user_can('manage_network_options')) wp_die('FU');
	 
	  update_site_option( 'wppl_site_options', $wppl_site_options );
	  //wp_redirect(admin_url('network/settings.php?page=my-netw-settings'));
}

function wppl_javascript() {
	wp_register_script( 'wppl-admin',  GMW_URL . '/admin/js/admin-settings.js', array(),false,true );
	wp_enqueue_style( 'settings-style', GMW_URL . '/admin/css/settings-pages.css', array(),false,false);
	wp_enqueue_script('wppl-toggle', GMW_URL . '/admin/js/toggle.js', array(),false,true);
	wp_register_script('wppl-addon', GMW_URL . '/admin/js/addon.js', array(),false,true);
}

//// SITE ADMIN - MAIN SETTINGS PAGE ///////////
function wppl_site_admin_options_page() {
	$wppl_on = get_option('wppl_plugins');
	$wppl_options = get_option('wppl_fields'); 
	$wppl_site_options = get_site_option('wppl_site_options');
	?>
	
	<div class="wrap"> 
	<br />
	<table class="widefat fixed" style="margin-bottom:0;margin-top:-6px;">
		<thead>		
			<th>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:left;height: 28px;">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form><span>Every donation is much appreciated. Thank you!</span>
			</th>	
		</thead>
		<thead>		
			<th>
				<?php screen_icon('wppl'); ?><h2><?php _e('GEO my Wp - Main Settings', 'wppl'); ?></h2>
			</th>
		</thead>
		<tbody>
			<!-- <tr style="height: 35px !important;background: #efefef;">
				<td style="padding:10px;"><a href="http://geomywp.com" target="_blank">GEO my WP</a> | documentations | Forum | Hire Me |</td>
			</tr> -->
			<tr>
				<td>
					<form action="" method="post">
						<table>
							<tbody>
								<?php settings_fields('wppl_plugin_options'); ?>
								<?php include_once 'site-admin-settings.php'; ?>	
							</tbody>
						</table>
					</form>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="widefat fixed" style="margin-bottom:0;margin-top:-2px;">
		<thead>
			<th><p><a href="http://www.geomywp.com" target="_blank">GEO my WP </a> Developed by Eyal Fitoussi.</p></th>
			<th></th>
		</thead>
	</table>

<?php }
	
//// MAIN SETTINGS PAGE ///////////
function wppl_plugin_options_page() { 
	if( is_multisite() ) $wppl_site_options = get_site_option('wppl_site_options');
	$wppl_on      = get_option('wppl_plugins');
	$wppl_options = get_option('wppl_fields'); 
	$posts 		  = get_post_types();
	$pages_s 	  = get_pages();
	?>
	<div class="wrap">  
	<br />
	<table class="widefat fixed" style="margin-bottom:0;margin-top:-6px;">
		<thead>		
			<th>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:left;height: 28px;">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form><span>Every donation is much appreciated. Thank you!</span>
			</th>	
		</thead>
		<thead>		
			<th>
				<?php screen_icon('wppl'); ?><h2><?php _e('GEO my Wp - Main Settings', 'wppl'); ?>
			</th>
		</thead>
		<tbody>
			<!-- <tr style="height: 35px !important;background: #efefef;">
				<td style="padding:10px;"><a href="http://geomywp.com" target="_blank">GEO my WP</a> | documentations | Forum | Hire Me |</td>
			</tr> -->
			<tr>
				<td>
					<form action="options.php" method="post">
						<?php settings_fields('wppl_plugin_options'); ?>
						<?php include_once 'main-settings.php'; ?>
					</form>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="widefat fixed" style="margin-bottom:0;margin-top:-2px;">
		<thead>
			<th><p><a href="http://www.geomywp.com" target="_blank">GEO my WP </a> Developed by Eyal Fitoussi.</p></th>
			<th></th>
		</thead>
	</table>
<?php }

//// ADD-ONS PAGE ///////////
function wppl_plugins_page() {  
	$wppl_on = get_option('wppl_plugins');
	?>
	<div class="wrap"> 
	<br />
	<table class="widefat fixed" style="margin-bottom:0;margin-top:-6px;">
		<thead>		
			<th>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:left;height: 28px;">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form><span>Every donation is much appreciated. Thank you!</span>
			</th>	
		</thead>
		<thead>		
				<th><?php screen_icon('wppl'); ?><h2><?php echo _e('GEO my WP - Add-ons','wppl'); ?></h2></th>
		</thead>
		<tbody>
			<!-- <tr style="height: 35px !important;background: #efefef;">
				<td style="padding:10px;"><a href="http://geomywp.com" target="_blank">GEO my WP</a> | documentations | Forum | Hire Me |</td>
			</tr> -->
			<tr>
				<td>
					<form action="options.php" method="post">
						<?php 
						settings_fields('wppl_plugin_plugins_options'); 		
						if (is_multisite()) $site_url = network_site_url('/wp-admin/network/plugin-install.php?tab=search&s=buddypress&plugin-search-input=Search+Plugins');
						else  $site_url = site_url('/wp-admin/plugin-install.php?tab=search&s=buddypress&plugin-search-input=Search+Plugins');
						include_once 'admin-addons.php';
						wp_enqueue_script('wppl-addon');
						wp_localize_script('wppl-toggle','imgUrl', GMW_URL. '/admin/images/');
						?>
					</form>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="widefat fixed" style="margin-bottom:0;margin-top:-2px;">
		<thead>
			<th><p><a href="http://www.geomywp.com" target="_blank">GEO my WP </a> Developed by Eyal Fitoussi.</p></th>
			<th></th>
		</thead>
	</table>

<?php }


// SHORTCODES PAGE 
function wppl_shortcodes_page() {
	$wppl_on = get_option('wppl_plugins');
	$options_r = get_option('wppl_shortcode');
	$wppl_options = get_option('wppl_fields');
	
	$posts = get_post_types(); 
	$pages_s = get_pages();	?>	
	<div class="wrap">	
	<br />
	<table class="widefat fixed" style="margin-bottom:0;margin-top:-6px;">
		<thead>		
			<th>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:left;height: 28px;">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form><span>Every donation is much appreciated. Thank you!</span>
			</th>	
		</thead>
		<thead>
			<th>
				<div class="wppl-settings">
					<span  style="width:100%">
						<span  style="width:325px"><?php screen_icon('wppl'); ?><h2><?php echo _e('Search Form - Shortcodes','wppl'); ?></h2></span>
						<span style="padding: 8px 0px;"><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
					</span>
					<div class="clear"></div>
					<span class="wppl-help-message">
					<p style="font-size: 13px;font-weight: normal;"><?php _e('Below you can create the shortcodes that will display the search form and the results. Click on the button of the shortcode that you want to create in order to create a new shortcode.
							the page will reload and you will see your new shortcode.
							Click on the "Edit" button to choose the setting of the shortcode and when you done click "Save". 
							copy the shortcode you have just created and paste in the page where you want the search form and the results to be displayed. 
							you can also choose this shortcode using the search form widget to display the search form in the sidebar.
							if you want to display only the search form in one page and have the results being displayed in a different page 
							you need to use the shortcode like <code>[wppl form="1" form_only="y"]</code> and you need to choose the results page in the main setting page.', 'wppl'); ?>
					</p></span>
				</div>	
			</th>	
		</thead>
		<tbody>
			<!-- <tr style="height: 35px !important;background: #efefef;">
				<td style="padding:10px;"><a href="http://geomywp.com" target="_blank">GEO my WP</a> | documentations | Forum | Hire Me |</td>
			</tr> -->
			<tr>
				<td>
					<form class="wppl-shortcode-submit" id="shortcode-submit" action="options.php" method="post">		
						<?php settings_fields('wppl_plugin_options_1');  ?>
						<?php include_once 'admin-shortcodes.php'; ?>
					</form>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="widefat fixed" style="margin-bottom:0;margin-top:-2px;">
		<thead>
			<th><p><a href="http://www.geomywp.com" target="_blank">GEO my WP </a> Developed by Eyal Fitoussi.</p></th>
			<th></th>
		</thead>
	</table>
		
<?php }

// SHORTCODES HELP PAGE 
function wppl_help_shortcodes_page() {
	$wppl_on = get_option('wppl_plugins');	
	echo '<div class="wrap">';  
    //screen_icon('wppl'); 
    ?>
	
	<!-- <h2><?php echo _e('Shortcodes','wppl'); ?></h2> -->
	<br />
	<table class="widefat fixed" style="margin-bottom:0;margin-top:-6px;">
		<thead>		
			<th>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:left;height: 28px;">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form><span>Every donation is much appreciated. Thank you!</span>
			</th>	
		</thead>
		<thead>		
			<th><?php screen_icon('wppl'); ?><h2><?php echo _e('Geo My WP - Shortcodes','wppl'); ?></h2></th>
		</thead>
		<tbody>
			<!-- <tr style="height: 35px !important;background: #efefef;">
				<td style="padding:10px;"><a href="http://geomywp.com" target="_blank">GEO my WP</a> | documentations | Forum | Hire Me |</td>
			</tr> -->
			<tr>
				<td>		
					<form class="wppl-shortcode-submit" id="shortcode-submit">
						<?php include_once 'help-shortcodes.php'; ?>
					</form>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="widefat fixed" style="margin-bottom:0;margin-top:-2px;">
		<thead>
			<th><p><a href="http://www.geomywp.com" target="_blank">GEO my WP </a> Developed by Eyal Fitoussi.</p></th>
			<th></th>
		</thead>
	</table>
		
<?php }

function validate_settings($input) {
	$wppl_options = get_option('wppl_fields');
	
	$wppl_options['gmw_license_key'] 		= $input['gmw_license_key'];
  	$wppl_options['address_fields'] 		= $input['address_fields'];
  	$wppl_options['mandatory_address'] 		= $input['mandatory_address'];
  	$wppl_options['different_style'] 		= $input['different_style'];
  	$wppl_options['results_page'] 			= $input['results_page'];
  	$wppl_options['friends_results_page'] 	= $input['friends_results_page'];
  	$wppl_options['groups_results_page'] 	= $input['groups_results_page'];
  	$wppl_options['google_api'] 			= $input['google_api'];
  	$wppl_options['show_locator_icon'] 		= $input['show_locator_icon'];
  	$wppl_options['locator_icon']			= $input['locator_icon'];
  	$wppl_options['front_autocomplete']		= $input['front_autocomplete'];
  	$wppl_options['country_code'] 			= $input['country_code'];
  	$wppl_options['no_results'] 			= $input['no_results'];
  	$wppl_options['words_excerpt'] 			= $input['words_excerpt'];
  	$wppl_options['java_conflict'] 			= $input['java_conflict'];
  	$wppl_options['use_theme_color'] 		= $input['use_theme_color'];
  	$wppl_options['theme_color'] 			= $input['theme_color'];
  	$wppl_options['auto_search'] 			= $input['auto_search'];
  	$wppl_options['auto_radius']			= preg_replace('/[^0-9]+/', '', $input['auto_radius']);
  	$wppl_options['auto_units'] 			= $input['auto_units'];
  	$wppl_options['auto_locate'] 			= $input['auto_locate'];
  	$wppl_options['locate_message'] 		= $input['locate_message'];
  	$wppl_options['per_post_icon'] 			= $input['per_post_icon'];
  	$wppl_options['wider_search'] 			= $input['wider_search'];
  	$wppl_options['wider_search_value'] 	= preg_replace('/[^0-9]+/', '', $input['wider_search_value']);
  	$wppl_options['per_member_icon'] 		= $input['per_member_icon'];
  	$wppl_options['per_group_icon'] 		= $input['per_group_icon'];
  	$wppl_options['xprofile_address']		= preg_replace('/[^0-9]+/', '', $input['xprofile_address']);
  	$wppl_options['user_location_tab']		= preg_replace('/[^0-9]+/', '', $input['user_location_tab']);
  	$wppl_options['gravity_fields']			= str_replace(" ", "", $input['gravity_fields']);
  	$wppl_options['checkin_toolbar']		= $input['checkin_toolbar'];
  	$wppl_options['checkin_float']			= $input['checkin_float'];
  	$wppl_options['checkin_tab']			= $input['checkin_tab'];
  	$wppl_options['checkin_activity']		= $input['checkin_activity'];
  	$wppl_options['post_types_icons']		= $input['post_types_icons'];
  	$wppl_options['friends']				= $input['friends'];
  	
  
return $wppl_options;
}

function validate_plugins_settings($input) {
	$wppl_on = get_option('wppl_plugins');
	$wppl_on = $input;
return $wppl_on;
}
?>
