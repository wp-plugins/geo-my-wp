<?php
global $wppl_on, $wppl_exist;
	$wppl_on = array();
	$wppl_exist = array();

	$plugins_options = get_option('wppl_plugins');
	
	if ( class_exists( 'BuddyPress' ) )  { 
		$wppl_exist['flo'] = 1;
		if ($plugins_options['friends_locator_on']) $wppl_on['friends'] = 1;
	}
	
	if ( is_file(plugin_dir_path(dirname(__FILE__)).'plugins/wppl-near-locations-widget/wppl-near-locations-widget.php' ) ) { 
		$wppl_exist['nlo'] = 1;
		if ($plugins_options['near_location_on']) $wppl_on['near_location'] = 1;	
	}
	
	if ( is_file(plugin_dir_path(dirname(__FILE__)).'plugins/featured-posts/featured-posts-admin.php' ) ) { 
		$wppl_exist['fpo'] = 1;
		if ($plugins_options['featured_posts_on']) $wppl_on['featured_posts'] = 1;
	}
	
	if ( is_file(plugin_dir_path(dirname(__FILE__)).'plugins/featured-map-icon/featured-map-icons.php' ) ) { 
		$wppl_exist['mio'] = 1;
		if ($plugins_options['map_icons_on']) $wppl_on['per_post_icon'] = 1;
	}
	
	if ( is_file(plugin_dir_path(dirname(__FILE__)).'plugins/featured-posts-scroller/results.php' ) ) { 
		$wppl_exist['fps'] = 1;
		if ($plugins_options['posts_scroller_on']) $wppl_on['scroller'] = 1;
	}
	
	if ( is_file(plugin_dir_path(dirname(__FILE__)).'themes/restaurants/results.php' ) ) { 
		$wppl_exist['rto'] = 1;
		if ($plugins_options['restaurant_on']) $wppl_on['restaurant'] = 1;
	}
	
	if ( is_file(plugin_dir_path(dirname(__FILE__)).'themes/real-estate/results.php' ) ) { 
		$wppl_exist['eto'] = 1;
		if ($plugins_options['estate_on']) $wppl_on['estate'] = 1; 
	}
		
function premium_only() {
	echo ' <span style="color:brown; fornt-weight:bold"> (Premium version only! Get your copy now.) </span> ';
	}
	
/////////////////////
function wppl_admin() {
	//global $wppl_settings_page;
	add_menu_page('GEO my WP', 'GEO my WP', 'manage_options', 'geo-my-wp', 'wppl_plugin_options_page',plugins_url('images/locate-me-menu.png', __FILE__),66);
	$sub_main		= add_submenu_page( 'geo-my-wp', 'General Settings', 'Settings', 'manage_options', 'geo-my-wp', 'wppl_plugin_options_page');
	$sub_addons		= add_submenu_page( 'geo-my-wp', 'Add-ons', 'Add-ons', 'manage_options', 'wppl-add-ons', 'wppl_plugins_page');
	$sub_shortcodes	= add_submenu_page( 'geo-my-wp', 'Shortcodes', 'Shortcodes', 'manage_options', 'wppl-shortcodes', 'wppl_shortcodes_page');
	add_action('admin_print_styles-' . $sub_main, 'wppl_javascript');
	add_action('admin_print_styles-' . $sub_shortcodes, 'wppl_javascript');
	add_action('admin_print_styles-' . $sub_addons, 'wppl_javascript');
	//add_action('admin_print_styles-' . $sub_plugins, 'wppl_javascript');
}
add_action('admin_menu', 'wppl_admin');
/////////////////////

function wppl_plugin_admin_init(){
	register_setting( 'wppl_plugin_options', 'wppl_fields', 'validate_settings' );
	register_setting( 'wppl_plugin_options_1', 'wppl_shortcode');
	register_setting( 'wppl_plugin_plugins_options', 'wppl_plugins', 'validate_plugins_settings');
	}
add_action('admin_init', 'wppl_plugin_admin_init');
///////////////////

function wppl_javascript() {
	wp_enqueue_script( 'wppl-admin',  plugins_url('js/admin-settings.js', __FILE__),array(),false,true );
	wp_enqueue_style( 'settings-style', plugins_url('css/settings-pages.css', __FILE__),array(),false,false);
}
		
//// MAIN SETTINGS PAGE ///////////
function wppl_plugin_options_page() {
	global $wppl_on; ?>
	<div class="wrap">  
    <?php screen_icon('wppl'); ?>
	<h2><?php _e('GEO my Wp - Main Settings', 'wppl'); ?></h2>
	
	<form action="options.php" method="post">
    
		<?php	settings_fields('wppl_plugin_options');
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' ); 
    	$plugins_options = get_option('wppl_plugins');
		$options 		 = get_option('wppl_fields');
		$posts 			 = get_post_types();
		$pages_s 		 = get_pages();	?>
    
   	 	<script type="text/javascript"> jQuery(document).ready(function($){ $('#wppl-theme-color-picker').farbtastic('#wppl-theme-color'); }); </script>
	
		<?php include_once 'main-settings.php'; ?>
	
	</form>
	<table class="widefat fixed" style="margin-bottom:0;margin-top:-6px;">
		<thead>
			<th><p><a href="http://www.geomywp.com" target="_blank">GEO my WP </a> Developed by Eyal Fitoussi.</p></th>
			<th></th>
		</thead>
	</table>
	
<?php }

//// ADD-ONS PAGE ///////////
function wppl_plugins_page() {	
	global $wppl_exist; ?>
	
	<div class="wrap"> 
	
	<?php screen_icon('wppl'); ?>
	
	<h2><?php echo _e('GEO my WP - Add-ons','wppl'); ?></h2>
	
	<form action="options.php" method="post">
		<?php settings_fields('wppl_plugin_plugins_options'); 
		$plugins_options = get_option('wppl_plugins');		
		$pro_message = _('Premium version only'); 
		
		if (is_multisite()) $site_url = network_site_url('/wp-admin/network/plugin-install.php?tab=search&s=buddypress&plugin-search-input=Search+Plugins');
		else  $site_url = site_url('/wp-admin/plugin-install.php?tab=search&s=buddypress&plugin-search-input=Search+Plugins');
		
		include_once 'admin-addons.php'; ?>
		
	</form>
	<table class="widefat fixed" style="margin-bottom:0;margin-top:-6px;">
		<thead>
			<th><p><a href="http://www.geomywp.com" target="_blank">GEO my WP </a> Developed by Eyal Fitoussi.</p></th>
			<th></th>
		</thead>
	</table>

<?php }

function wppl_shortcodes_page() {
	global $wppl_on, $plugins_options;
	
	$posts = get_post_types(); $pages_s = get_pages();		
	echo '<div class="wrap">';  
    screen_icon('wppl'); ?>
	
	<h2><?php echo _e('GEO my WP - Shortcodes','wppl'); ?></h2>
		
		<table class="widefat fixed">
			<thead>
				<th><h3><?php echo _e('Map for Single Location','wppl'); ?></h3></th>
				<th></th>
			</thead>
			<tr>
				<td>
					<p class="wppl-bold"><?php echo esc_attr_e('Copy and paste this code into the content area of a post to display the map of the location :','wppl'); ?></p>
				</td>
				<td>
					<p class="wppl-write-shortcode"> [wppl_single] </p>
				</td>
			</tr>
			<tr>
				<td>
					<p class="wppl-bold"><?php echo esc_attr_e('copy and paste this code into any single page template to display the map of a single location :','wppl'); ?></p>
				</td>
				<td>
					<p class="wppl-write-shortcode"> &#60;?php echo do_shortcode(&#39;[wppl_single]&#39;); ?&#62;</p>
				</td>
				</tr>
				<tr>
				<td>
					<p class="wppl-bold"><?php echo esc_attr_e('Shortcode Attributes: (for more information visit the','wppl'); ?> <a href="http://www.wpplaceslocator.com/installation" target="_blank">Installation</a><?php echo esc_attr_e(' page)'); ?>)</p>
				</td>
				<td>
					<p class="wppl-write-shortcode">map_height=" " , map_width=" " , map_type=" ", show_info=" " (1 to display)</p>
		 		</td>
	 		</tr>
		</table>
		
		<table class="widefat fixed">
			<thead>
				<th><h3><?php echo _e('User Location','wppl'); ?></h3></th>
				<th></th>
			</thead>
			<tr>
				<td>
					<p class="wppl-bold"><?php echo esc_attr_e('Paste this code anywhere in the template (ex. header.php) to display the user&#39;s location. :','wppl'); ?></p>
				</td>
				<td>
					<p class="wppl-write-shortcode"> &#60;?php echo do_shortcode(&#39;[wppl_location]&#39;); ?&#62;</p>
				</td>
				</tr>
				<tr>
				<td>
					<p class="wppl-bold"><?php echo esc_attr_e('Shortcode Attributes: (for more information visit the','wppl'); ?> <a href="http://www.wpplaceslocator.com/installation" target="_blank">Installation</a><?php echo esc_attr_e(' page)'); ?>)</p>
				</td>
				<td>
					<p class="wppl-write-shortcode">display_by=" "(zipcode or city) , show_name=" "(o or 1) , title=" "(ex. your location:)</p>
		 		</td>
	 		</tr>
		</table>
		
		<table class="widefat fixed" style="margin-bottom:0">
			<thead>
				<th><h3><?php echo _e('Create Search Form Shortcodes','wppl'); ?></h3></th>
				<th></th>
			</thead>
		</table>
		
		<form class="wppl-shortcode-submit" id="shortcode-submit" action="options.php" method="post">
			
			<?php settings_fields('wppl_plugin_options_1'); $options_r = get_option('wppl_shortcode'); ?>
			<?php include_once 'admin-shortcodes.php'; ?>
	
		</form>
		<table class="widefat fixed" style="margin-bottom:0;margin-top:-6px;">
		<thead>
			<th><p><a href="http://www.geomywp.com" target="_blank">GEO my WP </a> Developed by Eyal Fitoussi.</p></th>
			<th></th>
		</thead>
	</table>
	
<?php }

function validate_settings($input) {
	$options = get_option('wppl_fields');

  	$options['address_fields'] 			= $input['address_fields'];
  	$options['mandatory_address'] 		= $input['mandatory_address'];
  	$options['different_style'] 		= $input['different_style'];
  	$options['results_page'] 			= $input['results_page'];
  	$options['friends_results_page'] 	= $input['friends_results_page'];
  	$options['google_api'] 				= $input['google_api'];
  	$options['show_locator_icon'] 		= $input['show_locator_icon'];
  	$options['locator_icon']			= $input['locator_icon'];
  	$options['front_autocomplete']		= $input['front_autocomplete'];
  	$options['country_code'] 			= $input['country_code'];
  	$options['no_results'] 				= $input['no_results'];
  	$options['words_excerpt'] 			= $input['words_excerpt'];
  	$options['java_conflict'] 			= $input['java_conflict'];
  	$options['use_theme_color'] 		= $input['use_theme_color'];
  	$options['theme_color'] 			= $input['theme_color'];
  	$options['auto_search'] 			= $input['auto_search'];
  	$options['auto_radius']				= $input['auto_radius'];
  	$options['auto_units'] 				= $input['auto_units'];
  	$options['auto_locate'] 			= $input['auto_locate'];
  	$options['locate_message'] 			= $input['locate_message'];
  	$options['per_post_icon'] 			= $input['per_post_icon'];
  	$options['wider_search'] 			= $input['wider_search'];
  	$options['wider_search_value'] 		= $input['wider_search_value'];
  	$options['per_member_icon'] 		= $input['per_member_icon'];

return $options;
}

function validate_plugins_settings($input) {
	$plugins_options = get_option('wppl_plugins');

  	$plugins_options['friends_locator_on'] 		= $input['friends_locator_on'];
  	$plugins_options['near_location_on'] 	= $input['near_location_on'];
  	$plugins_options['restaurant_on'] 		= $input['restaurant_on'];
  	$plugins_options['estate_on'] 			= $input['estate_on'];
  	$plugins_options['featured_posts_on'] 	= $input['featured_posts_on'];
  	$plugins_options['map_icons_on'] 		= $input['map_icons_on'];
  	$plugins_options['posts_scroller_on'] 	= $input['posts_scroller_on'];
  	 	
return $plugins_options;
}
?>
