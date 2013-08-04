<?php 
/**
 * Default search form for Buddypress members.
 * @version 1.0
 * @author Eyal Fitoussi
 */
?>

<div id="gmw-fl-form-wrapper-<?php echo $gmw['form_id']; ?>" class="gmw-fl-form-wrapper gmw-form-wrapper gmw-fl-form-wrapper-<?php echo $gmw['form_id']; ?>">
	<form id="gmw-fl-form-<?php echo $gmw['form_id']; ?>" class="standard-form gmw-form gmw-fl-form" name="wppl_form" action="<?php echo $gmw['results_page']; ?>" method="get">
		<!-- <input type="hidden" name="page" value="1" /> -->
		
		<?php if ( ( isset($gmw['profile_fields']) || isset($gmw['profile_fields_date']) ) ) gmw_fl_fields_dropdown($gmw, $id='', $class='' ); ?>
		
		<div class="gmw-address-field-wrapper">
			<!-- Address Field -->
			<?php gmw_search_form_address_field($gmw, $label='gmw-address', $id='gmw-address', $class='gmw-address'); ?>
			
			<!--  locator icon -->
			<?php gmw_search_form_locator_icon($gmw, $id="gmw-locate-button-".$gmw['form_id'], $class="gmw-locate-button"); ?>
		</div>	
				
		<div class="gmw-unit-distance-wrapper">
			<!--distance values -->
			<?php gmw_search_form_radius_values($gmw, $id='', $class='', $btitle='', $stitle=''); ?>
			<!--distance units-->
			<?php gmw_search_form_units($gmw, $id='', $class='' ); ?>	
		</div>
		
		<div class="wppl-submit">
			<input name="submit" type="submit" class="wppl-search-submit" id="wppl-submit-<?php echo $gmw['form_id']; ?>" value="<?php _e('Submit','GMW'); ?>" />
			<input type="hidden" name="wppl_per_page" value="<?php echo current(explode(",", $gmw['per_page']));?>" />
			<input type="hidden" class="wppl-formid" name="wppl_form" value="<?php echo $gmw['form_id']; ?>" />
			<input type="hidden" name="action" value="wppl_post" />
			<input type="hidden" class="gmw-submitted" name="gmw-submitted" value="" />
		</div>	
	</form>
</div><!--form wrapper -->	
