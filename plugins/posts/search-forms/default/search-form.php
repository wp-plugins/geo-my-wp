<?php 
/**
 * Default search form for Post, post types and pages.
 * @version 1.0
 * @author Eyal Fitoussi
 */
?>
<div id="gmw-pt-form-wrapper-<? echo $gmw['form_id']; ?>" class="gmw-form-wrapper gmw-pt-form-wrapper gmw-pt-form-wrapper-<? echo $gmw['form_id']; ?>">
	<form id="gmw-pt-form-<? echo $gmw['form_id']; ?>" class="standard-form gmw-pt-form gmw-form" name="wppl_form" action="<?php echo $gmw['results_page']; ?>" method="get">
		<input type="hidden" name="paged" value="1" />
			
		<div class="gmw-post-types-wrapper">
			<!-- post types dropdown -->
			<?php gmw_pt_form_post_types_dropdown($gmw, $title='', $id='gmw-post-type', $class='gmw-post-type', $all= __(' -- Search Site -- ','GMW')); ?>
		</div>
		
		<div class="gmw-taxonomies-wrapper">
			<!-- Display taxonomies/categories --> 
			<?php gmw_pt_form_taxonomies($gmw); ?>
		</div>
		
		<div class="gmw-address-field-wrapper">
			<!-- Address Field -->
			<?php gmw_search_form_address_field($gmw, $label='gmw-address', $id='gmw-address', $class='gmw-address'); ?>
			
			<!--  locator icon -->
			<?php gmw_search_form_locator_icon($gmw, $id="gmw-locate-button", $class="gmw-locate-button"); ?>
		</div>	
		
		<div class="clear"></div>
		
		<div class="gmw-unit-distance-wrapper">
			<!--distance values -->
			<?php gmw_search_form_radius_values($gmw, $id='', $class='', $btitle='', $stitle=''); ?>
			<!--distance units-->
			<?php gmw_search_form_units($gmw, $id='', $class='' ); ?>	
		</div><!-- distance unit wrapper -->
		
		<div class="wppl-submit">
			<input name="submit" type="submit" class="wppl-search-submit" id="wppl-submit-<?php echo $gmw['form_id']; ?>" value="<?php _e('Submit','GMW'); ?>" />
			<input type="hidden" class="wppl-formid" name="wppl_form" value="<?php echo $gmw['form_id']; ?>" />
			<input type="hidden" name="wppl_per_page" value="<?php echo current(explode(",", $gmw['per_page']));?>" />
			<input type="hidden" name="action" value="wppl_post" />
			<input type="hidden" class="gmw-submitted" name="gmw-submitted" value="" />
		</div>	
	</form>
</div><!--form wrapper -->	