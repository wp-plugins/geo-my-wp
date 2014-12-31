<?php
/////// SHORTCODES AND WIDGETS REGISTRATION PAGE //////////
//////////////////////////////////////////////////////////

////// SHORTCODE FOR SINGLE LOCATION MAP///////
function wppl_shortcode_single($single) {
	extract(shortcode_atts(array(
		'map_height' => '250',
		'map_width' => '250',
		'map_type' => 'ROADMAP',
		'zoom_level' => 13,
		'show_info' => 1	
	),$single));
	
	global $post, $wpdb;		
    $get_loc = array(
    	get_post_meta($post->ID,'_wppl_lat',true),
    	get_post_meta($post->ID,'_wppl_long',true),
    	get_the_title($post->ID),
    	get_post_meta($post->ID,'_wppl_address',true),
    	get_post_meta($post->ID,'_wppl_phone',true),
    	get_post_meta($post->ID,'_wppl_fax',true),
    	get_post_meta($post->ID,'_wppl_email',true),
    	get_post_meta($post->ID,'_wppl_website',true),
    	$map_type,
    );
    wp_enqueue_script( 'wppl-map' ,true);
    echo '<script type="text/javascript">'; 
	echo 	'singleLocation= '.json_encode($get_loc),';'; 
	echo 	'single="1";';
	echo	'zoomLevel= '. $zoom_level,';';
	echo  '</script>';
	
 	$single_map .='';
 	$single_map .=	'<div class="wppl-single-wrapper">';
	$single_map .=		'<div class="wppl-single-map-wrapper">';
	$single_map .=			'<div id="map_single" style="width:' . $map_width .'px; height:' . $map_height . 'px;"></div>';
	$single_map .=		'</div>';// map wrapper //
	
	if ($show_info == 1) {
		$single_map .= '<div class="wppl-info-left">';
    	$single_map .=	'<div class="wppl-phone"><span>Phone: </span>' . get_post_meta($post->ID,'_wppl_phone',true). '</div>';
    	$single_map .=	'<div class="wppl-fax"><span>Fax: </span>' . get_post_meta($post->ID,'_wppl_fax',true). '</div>';
    	$single_map .=	'<div class="wppl-email"><span>Email: </span>' .get_post_meta($post->ID,'_wppl_email',true). '</div>';
   	 	$single_map .=	'<div class="wppl-website"><span>Website: </span><a href="http://' . get_post_meta($post->ID,'_wppl_website',true). '" target="_blank">' .get_post_meta($post->ID,'_wppl_website',true). '</a></div>';
    	$single_map .= '</div>';
    } 
    
    $single_map .= '</div>';
    return $single_map;	
}
add_shortcode( 'wppl_single' , 'wppl_shortcode_single' );

////// SHORTCODE DISPLAY USER'S LOCATION   ///////
function wppl_location_shortcode($locate) {
	extract(shortcode_atts(array(
		'display_by' 	=> 'city',
		'title' 		=> 'Your Location:',
		'show_name' 	=> 0
	),$locate));
	$options = get_option('wppl_fields');
	
    
	
	$location .= '';
	$location .=	'<div class="wppl-your-location-wrapper">';
	$location .= 		'<button type="button"  onClick="getLocationNoSubmit();" class="wppl-locate-me-btn"><img src="' . plugins_url('images/locate-me-'.$options['locator_icon'].'.png', __FILE__) . '" style="height:23px;"></button>';
	
	if ($show_name == 1) {
		global $current_user;
		get_currentuserinfo();
		if($current_user->user_login) {
			$user_name = ', '.$current_user->user_login;
		} else if($current_user->user_firstname) {	
			$user_name = ', '.$current_user->user_firstname;
		} else {
			$user_name = ', '.$current_user->user_lastname;
		}
		if(!is_user_logged_in()) { 
			$name_is = 'Hello, Guest!';
		}else {
			$name_is = 'Hello'.$user_name.'!';
		}
		$location .=		'<span class="wppl-hello-user">'.$name_is.'</span>';
	}
	if($_COOKIE['wppl_' . $display_by]) {
		$location .= '<span class="wppl-your-location-title">' . $title . '</span>';
	} else {
		$location .= '<span class="wppl-your-location-title">Get your current location</span>';
	}
	$location .=		'<span class="wppl-your-location-location">' . $_COOKIE['wppl_' . $display_by]. '</span>';
	$location .=	'</div>';
	
	return $location;
	
}
add_shortcode( 'wppl_location' , 'wppl_location_shortcode' );

//// Register Search Form widget ////
class wppl_widget extends WP_Widget {
	// Constructor //
		function wppl_widget() {
			$widget_ops = array( 'classname' => 'wppl_widget', 'description' => 'Displays Places Locator Search form in your sidebar' ); // Widget Settings
			$control_ops = array( 'id_base' => 'wppl_widget' ); // Widget Control Settings
			$this->WP_Widget( 'wppl_widget', 'WPPL Search Form', $widget_ops, $control_ops ); // Create the widget
		}
	// Extract Args //
		function widget($args, $instance) {
			extract( $args );
			$title 		= apply_filters('widget_title', $instance['title']); // the widget title
			$short_code	= $instance['short_code'];

			echo $before_widget;

			if ( $title ) { echo $before_title . $title . $after_title; }

        	echo do_shortcode('[wppl form="'.$short_code.'" form_only="1"]');

			echo $after_widget;
		}

	// Update Settings //
		function update($new_instance, $old_instance) {
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['short_code'] = $new_instance['short_code'];
			//$instance['pages'] = $new_instance['pages'];
			return $instance;
		}

	// Widget Control Panel //
		function form($instance) {
			$w_posts = get_post_types();
			$defaults = array( 'title' => 'Search Places');
			$instance = wp_parse_args( (array) $instance, $defaults ); 
			$shortcodes = get_option('wppl_shortcode');
			?>
  			<p style="margin-bottom:10px; float:left;">
		    	<lable><?php echo  esc_attr( __( 'Title:' ) ); ?></lable>
				<input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" width="25" style="float: left;width: 100%;"/>
			</p>
			<p>
			<lable><?php echo esc_attr( __( 'Choose shortcode to use in the sidebar:' ) ); ?></lable>
				<select name="<?php echo $this->get_field_name('short_code'); ?>" style="float: left;width: 100%;">
				<?php foreach ($shortcodes as $shortcode) {
					echo '<option value="' . $shortcode[form_id] . '"'; echo ($instance['short_code'] == $shortcode[form_id] ) ?'selected="selected"' : ""; echo '>wppl form="' . $shortcode[form_id] . '"</options>';
						} ?>
				</select>
			</p>
	<?php } 
	 }
add_action('widgets_init', create_function('', 'return register_widget("wppl_widget");'));

//// Register User Location widget ////
class wppl_widget_location extends WP_Widget {
	// Constructor //
		function wppl_widget_location() {
			$widget_location_ops = array( 'classname' => 'wppl_widget_location', 'description' => 'Displays user Location' ); // Widget Settings
			$control_location_ops = array( 'id_base' => 'wppl_widget_location' ); // Widget Control Settings
			$this->WP_Widget( 'wppl_widget_location', 'WPPL User Location', $widget_location_ops, $control_location_ops ); // Create the widget
		}
	// Extract Args //
		function widget($args, $instance) {
			extract( $args );
			$title_location 		= $instance['title_location']; // the widget title
			$short_code_location	= $instance['short_code_location'];
			$display_by 			= $instance['display_by'];
			$name_guest 			= $instance['name_guest'];
			
			echo $before_widget;

			//if ( $title_location ) { echo $before_title . $title_location . $after_title; }

        	echo do_shortcode('[wppl_location show_name="'.$name_guest.'" display_by="'.$display_by.'" title="'.$title_location.'"]');

			echo $after_widget;
		}

	// Update Settings //
		function update($new_instance, $old_instance) {
			$instance['title_location'] 		= strip_tags($new_instance['title_location']);
			$instance['short_code_location'] 	= $new_instance['short_code_location'];
			$instance['display_by'] 			= $new_instance['display_by'];
			$instance['name_guest'] 			= $new_instance['name_guest'];
			
			return $instance;
		}

	// Widget Control Panel //
		function form($instance) {
			$defaults = array( 'title' => 'WPPL User Location');
			$instance = wp_parse_args( (array) $instance, $defaults ); 
			?>
  			<p style="margin-bottom:10px; float:left;">
		    	<lable><?php echo  esc_attr( __( 'Title: (ex:"Your Location")' ) ); ?></lable>
				<input type="text" name="<?php echo $this->get_field_name('title_location'); ?>" value="<?php echo $instance['title_location']; ?>" width="25" style="float: left;width: 100%;"/>
			</p>
			<p style="margin-bottom:10px; float:left;width:100%">
		    	<?php echo '<input type="checkbox" value="1" name="'. $this->get_field_name('name_guest').'"'; echo ($instance["name_guest"] == "1" ) ? "checked=checked" : ""; echo 'width="25" style="float: left;margin-right:10px;"/>'; ?>
		    	<lable><?php echo  esc_attr( __( 'Display User Name.' ) ); ?></lable>
			</p>
			<p>
			<lable><?php echo esc_attr( __( 'Display location by:' ) ); ?></lable>
				<select name="<?php echo $this->get_field_name('display_by'); ?>" style="float: left;width: 100%;">
					<?php echo '<option value="zipcode"'; echo ($instance['display_by'] == "zipcode" ) ?'selected="selected"' : ""; echo '>Zipcode</options>'; ?>
					<?php echo '<option value="city"'; echo ($instance['display_by'] == "city" ) ?'selected="selected"' : ""; echo '>City, State</options>'; ?>
				</select>
			</p>
	<?php } 
	 }
add_action('widgets_init', create_function('', 'return register_widget("wppl_widget_location");'));
