<div id="wppl-bp-wrapper">
	<div id="bp-location-form-wrapper">
		<form name="addLocation" type="post" action="" id="wppl-location-form">
			
			<div id="saved-data">
				<div class="single-input-fields">
					<label for="address">Your Location:</label>
					<input name="wppl_address" id="wppl_address" value="<?php echo $member_loc_tab[0]['address']; ?>"  type="text" size="40" disabled/>
					<input type="hidden" name="wppl_address" id="wppl_formatted_address" value="<?php echo $member_loc_tab[0]['formatted_address']; ?>"  type="text" size="40" disabled />
 				</div>
 				
 				<div class="single-input-fields" hidden >
					<label for="address">Latitude:</label>
					<input name="wppl_lat" id="wppl_lat" value="<?php echo $member_loc_tab[0]['lat']; ?>" type="text" disabled />
 				</div>
 				<div class="single-input-fields" hidden >
					<label for="address">Longitude:</label>
					<input name="wppl_long" id="wppl_long" value="<?php echo $member_loc_tab[0]['long']; ?>" type="text" disabled />
				</div>
				
				<div class="single-input-fields">
					<input type="button" id="edit-user-location-btn" class="bp-btn" style="float:left" value="Edit Location" />
					<input type="button" id="remove-address-btn" style="float:left;" class="bp-btn" value="Delete Location" />	
					<div id="wppl-ajax-loader-bp" style="float:left; display:none"><img src="<?php echo plugins_url('images/ajax-loader.gif', __FILE__); ?>" id="wppl-loader-image" alt="" /> Loading</div>
					<div id="wppl-bp-feedback"></div>						
 				</div>
 				
 			</div><!-- saved data -->
 			<div id="edit-user-location">
 				<div class="single-input-fields">
					<label for="street">Get your current location:</label>
 					<input type="button" class="bp-btn" id="locate-me-bp" value="Locate Me" style="float:left;"/><div id="wppl-ajax-loader-locate" style="float:left; display:none"><img src="<?php echo plugins_url('images/ajax-loader.gif', __FILE__); ?>" id="wppl-loader-image" alt="" /> Loading</div>
 				</div>
				<div id="autocomplete-wrapper">
					<label>Type an address for autocomplete:</label>
					<input type="text" id="wppl-addresspicker" value="<?php echo $member_loc_tab[0]['address']; ?>" style="width:450px !important" />
				</div>
				<div class="metabox-tabs-div">
					<label for="street">Enter your location manually:</label>
					<ul class="metabox-tabs" id="metabox-tabs">
						<li class="active address-tab" style="width:70px"><input type="button" id="wppl-address-tab" value="Address" /></li>
						<li class="lat-long-tab" style="width:120px"><input type="button" id="wppl-latlong-tab" value="Latitude / Longitude" /></li>
					</ul>
					
					<div class="address-tab" id="address-tab-wrapper">	
 						<div class="single-input-fields">
							<label for="street">Street:</label>
							<input name="wppl_street" id="wppl_street" type="text" value="<?php echo $member_loc_tab[0]['street']; ?>" />
						</div>
						<div class="single-input-fields">
							<label for="apt">Apt/Suit:</label>
							<input name="wppl_apt" id="wppl_apt" type="text" value="<?php echo $member_loc_tab[0]['apt']; ?>"/>
 						</div>
 				
 						<div class="single-input-fields">
							<label for="city">City:</label>
							<input name="wppl_city" id="wppl_city" type="text" value="<?php echo $member_loc_tab[0]['city']; ?>" />
 						</div>
 						
 						<div class="single-input-fields">
							<label for="state">State:</label>
							<input name="wppl_state" id="wppl_state" type="text" value="<?php echo $member_loc_tab[0]['state']; ?>" />
							<input name="wppl_state_short" id="wppl_state_short" type="text" value="<?php echo $member_loc_tab[0]['state_short']; ?>" hidden />
 						</div>
 				
 						<div class="single-input-fields">
							<label for="zipcode">Zipcode:</label>
							<input name="wppl_zipcode" id="wppl_zipcode" type="text" value="<?php echo $member_loc_tab[0]['zipcode']; ?>" />
						</div>
				
						<div class="single-input-fields">
							<label for="country">Country:</label>
							<input name="wppl_country" id="wppl_country" type="text" value="<?php echo $member_loc_tab[0]['country']; ?>" />
							<input name="wppl_country_short" id="wppl_country_short" type="text" value="<?php echo $member_loc_tab[0]['country_short']; ?>" hidden />
 						</div>
 					</div><!-- address tab -->
 			
 					<div class="lat-long-tab" id="latlong-tab-wrapper">	
 						<div class="single-input-fields">
							<label for="address">Latitude:</label>
							<input name="wppl_enter_lat" id="wppl_enter_lat" value="<?php echo $member_loc_tab[0]['lat']; ?>" type="text" />
 						</div>
 				
 						<div class="single-input-fields">
							<label for="address">Longitude:</label>
							<input name="wppl_enter_long" id="wppl_enter_long" value="<?php echo $member_loc_tab[0]['long']; ?>" type="text" />
 						</div>	
 						<div><input type="button" class="member-get-address" class="bp-btn" value="Get Address"></div>		
 					</div><!-- lat/long tab -->
 					
 					<input type="hidden" name="action" value="addLocation"/>
				
				</div><!-- meta tabs wrapper -->
				<div class="single-map-field">
					<label>Drag and drop the marker on the map:</label>
					<div id="wppl-bp-map-holder">	
    					<div id="bp-map" style="height:210px;width:210px"></div>
					</div><!-- map holder -->	
				</div>
				
				<?php if ( isset($wppl_options['per_member_icon']) ) { ?>
					<div>
						<label>Choose map's icon:</label>
						<div id="member-icons-wrapper">
							<?php $map_icons = glob(GMW_FL_PATH . 'map-icons/*.png');
								$display_icon = GMW_FL_URL. 'map-icons/';
								foreach ($map_icons as $map_icon) {
									echo '<span style="float:left"><input type="radio" name="map_icon" value="'.basename($map_icon).'"'; echo ( $member_loc_tab[0]['map_icon'] == basename($map_icon) ) ? "checked" : ""; echo ' />
									<img src="'.$display_icon.basename($map_icon).'" height="40px" width="35px" /></span>';
								} ?>
						</div>
					</div>
					<?php } ?>
				<div class="single-input-fields">	
					<input type="button" id="member-save-location" class="bp-btn" value="Save Location">
				</div>
			</div><!-- edit users location -->
		</form>
	</div><!-- location form wrapper -->

</div><!-- wppl bp wrapper -->
