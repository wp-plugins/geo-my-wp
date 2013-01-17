<?php
session_start();
 //// SHORTCODE TO DISPLAY FORM AND RESULTS ////
function wppl_shortcode($params) {
	global $wppl;
	$wppl_on = get_option('wppl_plugins');
	$wppl_options = get_option('wppl_fields');
	
	$post_types = array();
	$ptc = false;
	$keywords_field = 0;
	$display_units = 0;
	$get_results = 0;

	// get and extract shortcode settings //
	$options_r = get_option('wppl_shortcode'); 
	$wppl = $options_r[$params['form']];
    extract(shortcode_atts($wppl, $params)); 
    
    ob_start(); 
    
    /* posts types drop down */
    if($form_type == 'posts') {
   		$ptc = count($post_types);
    	$pti = implode( " ",$post_types); 
		($_GET["wppl_post"] == $pti ) ? $pti_all = 'selected="selected"' : $pti_all = "";
	}
	
	$miles = explode(",", $distance_values);

	/* define the "results page" */
	if ($form_type == 'posts') 		 $results_page = $wppl_options['results_page'] ;
	elseif ($form_type == 'friends') $results_page = $wppl_options['friends_results_page'];
	elseif ($form_type == 'groups')  $results_page = $wppl_options['groups_results_page'];
	
	/* check if this is a widget or if the search form is different from the results */
	if(isset($params['form_only']) && $params['form_only']  == 1) { 	
		$widget = "widget-"; $faction = get_bloginfo('wpurl') . "/" . $results_page . "/";
	} elseif (isset($params['form_only']) && $params['form_only']  == "y") { 
		$faction = get_bloginfo('wpurl') . "/" . $results_page . "/"; $widget = "";
	} elseif ( (empty($params['form_only'])) ){ 
		$faction = ""; $widget = "";
	} 
	
	?>
	<!-- DISPLAY SEARCH FORM -->	
	<div class="wppl-<?php echo $widget; ?>form-wrapper">
		<form class="wppl-<?php echo $widget; ?>form" name="wppl_form" action="<?php echo $faction; ?>" method="get">
			<input type="hidden" name="pagen" value="0" />
				
				<?php if ( ( isset($wppl_on['friends']) && $wppl_on['friends'] == 1 && $form_type == 'friends') && ( isset($profile_fields) || isset($profile_fields_date) ) )  bp_fields_dropdown($wppl); ?>
				
				<?php if ($form_type == 'posts') { ?>
				
					<!-- only if more then one post type entered display them in dropdown menu otherwise no dropdown needed -->
					<?php if($ptc>1) { ?>
						<div class="wppl-post-type-wrapper">
							<label for="wppl-post-type">What are you looking for: </label>				
								<!-- post types dropdown -->
							<select name="wppl_post" id="wppl-post-type">
								<option value="<?php echo implode(' ', $post_types); ?>"> -- Search Site -- </option>
								<?php foreach ($post_types as $post_type) { ?>
								<?php if( $_GET["wppl_post"] == $post_type ) {$pti_post = 'selected="selected"';} else {$pti_post = "";} ?>
								<option value=<?php echo '"' .$post_type .'"' .$pti_post; ?>><?php echo get_post_type_object($post_type)->labels->name; ?></option>			
								<?php } ?>
							</select>
						</div> <!-- post type wrapper --> 
					<?php } else { ?>
						<input type="hidden" name="wppl_post" value="<?php echo implode(' ',$post_types);  ?>" />
					<?php } 
					
					/* post category disply categories only when one post type chooses 
				 	we cant switch categories between the post types if more then one */ 
					if ($ptc<2 && (isset($taxonomies))) { ?>	
						<div class="wppl-category-wrapper">			
							<?php // output dropdown for each taxonomy //
							foreach ($taxonomies as $tax) { ?>
								<div id="<?php echo $tax . '_cat'; ?>">
									<label for="wppl-category-id"><?php echo get_taxonomy($tax)->labels->singular_name; ?>: </label>
									<?php custom_taxonomy_dropdown($tax); ?>				
								</div>
							<?php } /* end foreach */ ?>			
						</div><!-- category-wrapper -->
					<?php } /* end taxonomies dropdown */ ?>
				
				<?php } ?>
				
				<?php if ( ($form_type == 'posts') && isset($keywords_field) && $keywords_field == 1 ) { ?>
					<div class="wppl-keywords-field-wrapper">        		
						<input type="text" name="wppl_keywords" class="wppl-keywords" value="<?php echo $_GET['wppl_keywords']; ?>" size="35" placeholder="Enter key words" />
					</div><!-- end address wrapper -->
				<?php } ?>
				<div class="wppl-address-wrapper">        		
					<?php if( !isset($address_title_within) || empty($address_title_within) ) echo '<label for="wppl-address">' .$address_title. '</label>'; ?>
					<div class="wppl-address-field-wrapper">
						<input type="text" name="wppl_address" class="wppl-address" value="<?php echo (isset($_GET['wppl_address'])) ? $_GET['wppl_address'] : '' ; ?>" size="35" <?php echo($address_title_within) ? 'placeholder="'. $address_title . '"' : ""; ?> />
						<?php if( isset($locator_icon['show']) && $locator_icon['show'] == 1 ) { ?>
							<div class="wppl-locate-button-wrapper">
								<a href="#TB_inline?#bb&width=250&height=130&inlineId=wppl-locator-thickbox" id="wsf-<?php echo $params['form'];?>" class="thickbox wppl-locate-me-btn" style="display:none;"><img class="wppl-locator-img" src="<?php echo GMW_URL . '/images/locator-images/'.$locator_icon['icon']; ?>" /></a>
							</div>
							<div id="wppl-locator-thickbox" style="display:none;">
								<div id="wppl-search-locator-wrapper">
									<div id="wppl-locator-message-wrapper"><div id="wppl-locator-success-message">Getting your current location...</div></div>
									<span><div id="wppl-locator-spinner" style="display:none;"><img src="<?php echo GMW_URL .'/images/wpspin_light.gif'; ?>" /></div></span>
								</div>
							</div>
						<?php } ?>
					</div>
				</div><!-- end address wrapper -->
		
				<!--distance values dropdown	-->
				<div class="wppl-distance-units-wrapper">
					<?php if ( isset($display_radius) && $display_radius == 1 ) { ?>
						<div class="wppl-distance-wrapper">
							<select name="wppl_distance" class="wppl-distance" id="wppl-distance">
								<option value="<?php echo end($miles); ?>"><?php if ( isset($display_units) && !empty($display_units) ) echo  '- Within -'; else echo ($units_name == "imperial") ? "- Miles -" : "- Kilometers -"; ?> </option>
								<?php foreach ($miles as $mile) { ?>
									<?php if( $_GET["wppl_distance"] == $mile ) $mile_s = 'selected="selected"'; else $mile_s = ""; ?>
									<option value=<?php echo '"' .$mile .'"' .$mile_s; ?>><?php echo $mile; ?></option>
								<?php } ?>
							</select>
						</div>
					<?php } else { ?>
						<div class="wppl-distance-wrapper">
							<input type="hidden" name="wppl_distance" value="<?php echo end($miles); ?>" />
						</div>
					<?php } ?>
				
					<?php if ( isset($display_units) && $display_units == 1 ) { ?>
						<div class="wppl-units-wrapper">			
							<?php if (isset($_GET['wppl_units']) && $_GET['wppl_units'] == 'metric')  $unit_m = 'selected="selected"'; else $unit_i = 'selected="selected"'; ?>
							<select name="wppl_units" class="wppl-units">
								<option value="imperial" <?php echo $unit_i; ?>>Miles</option>
								<option value="metric" <?php echo $unit_m; ?>>Kilometers</option>
							</select>
						</div>
					<?php } else { ?>
						<div class="wppl-units-wrapper">	
							<input type="hidden" name="wppl_units" value="<?php echo $units_name; ?>" />
						</div>
					<?php } ?>
				</div><!-- distance unit wrapper -->
			<div class="wppl-submit">
				<input name="submit" type="submit" class="wppl-search-submit" id="wppl-submit-<?php echo $params['form']; ?>" value="Submit" />
				<input type="hidden" class="wppl-formid" name="wppl_form" value="<?php echo $params['form']; ?>" />
				<input type="hidden" name="action" value="wppl_post" />
			</div>	
		</form>
	</div><!--form wrapper -->	
<?php 	
	/* run the result function */
	if (empty($params['form_only'])) {
		if ( isset($wppl_on['post_types']) && $wppl_on['post_types'] == 1 && $form_type == 'posts') 
			wppl_get_results($params);
		elseif ( isset($wppl_on['friends']) && $wppl_on['friends'] && $form_type == 'friends') 
			wppl_bp_query_results ($params);
		elseif (isset( $wppl_on['groups'] ) && $wppl_on['groups'] && $form_type == 'groups') 
			wppl_bp_query_groups_results ($params, $wppl, $wppl_options);	
    }
    
    $output_string=ob_get_contents();
	ob_end_clean();

	return $output_string;
} //end of shortcode
add_shortcode( 'wppl' , 'wppl_shortcode' );

////// SHORTCODE DISPLAY USER'S LOCATION   ///////
function wppl_location_shortcode($locate) {
	global $wppl_options;
	extract(shortcode_atts(array(
		'display_by' 	=> 'city',
		'title' 		=> 'Your Location:',
		'show_name' 	=> 0
	),$locate));
	
	//$location .= '';
	$location .=	'<div class="wppl-your-location-wrapper">';
	if ($show_name) {
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
	if( isset($_COOKIE['wppl_city']) || isset($_COOKIE['wppl_state']) || isset($_COOKIE['wppl_country']) || isset($_COOKIE['wppl_zipcode']) ) {
		$location .= '<span class="wppl-your-location-title">' . $title . '</span>';
	} else {
		$location .= '<span class="wppl-your-location-title"><a href="#TB_inline?#bb&width=250&height=130&inlineId=wppl-locator-hidden" class="thickbox" title="Your Current Location">Get your current location</a></span>';
	}
	$location .=		'<span class="wppl-your-location-location"><a href="#TB_inline?#bb&width=250&height=130&inlineId=wppl-locator-hidden" class="thickbox" title="Your Current Location">' . urldecode($_COOKIE['wppl_' . $display_by]); if($display_by == 'city') { if(urldecode($_COOKIE['wppl_country']) == 'United States') { $location .= ' ' . urldecode($_COOKIE['wppl_state']);} else { $location .= ' ' . urldecode($_COOKIE['wppl_country']);} } $location .='</a></span>';
	$location .=    	'<div class="wppl-show-location-form" id="wppl-locator-hidden" style="display:none">';
	$location .=			'<form class="wppl-location-form" name="wppl_location_form" onsubmit="return false" action="" method="post">';
	$location .=				'<div>';
	$location .=					'<div id="wppl-locator-info">';
	$location .=						'<span> - Enter Your Location Manually - </span>';
	$location .=						'<span><input type="text" name="wppl_user_address" class="wppl-user-address" value="" placeholder="zipcode or full address..." /><input type="submit" value="go" /></span>';
	$location .=						'<span> - or - </span>';
	$location .= 						'<span><a href="#" onClick="showSpinner();getLocation();" ><div id="wppl-locator-message">Get your current Location</a></div></span>';
	$location .=					'</div>';
	$location .=					'<div id="wppl-locator-message-wrapper"></div>';
	$location .= 					'<span><div id="wppl-locator-spinner" style="display:none;"><img src="'. GMW_URL .'/images/wpspin_light.gif" /></div></span>';
	$location .=				'</div>';
	$location .=				'<input type="hidden" name="action" value="wppl_user_location" />';
	$location .=			'</form>';
	$location .= 		'</div>';	
	$location .=	'</div>';
	
	return $location;
	
}
add_shortcode( 'wppl_current_location' , 'wppl_location_shortcode' );
?>