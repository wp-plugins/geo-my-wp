<ul id="wppl-members-list" class="wppl-item-list" role="main">
	<?php foreach($user_ids as $single_user) { 
		
		$user_data = new BP_Core_User($single_user->member_id);				
		/* get the avatar url into array - later we will use that to output the avatars on the map */
		$single_user->avatar_icon 	= bp_core_fetch_avatar(array('object' => 'user','item_id' => $single_user->member_id,'type' => 'thumb','html' => '$as_html'));
		$single_user->full_name = $user_data->fullname;
		$single_user->user_permalink = $user_data->user_url;
		$single_user->avatar = $user_data->avatar;
		?>
	<!-- members loop starts here -->
	<li <?php echo 'style="width:'.$single_result_width.'px; height:'.$single_result_height.'px"'; ?>>
		
		<?php wppl_bp_thumbnail($wppl, $single_user); ?>
			
		<div class="wppl-item">
			<?php wppl_bp_by_radius($returned_address, $single_user, $wppl); ?>
			
			<?php wppl_bp_title($single_user); ?>
			
			<?php wppl_bp_last_active($user_data); ?>
			
		</div>
		
		<?php wppl_bp_user_profile_fields($wppl, $single_user, $total_results_fields); ?>
		
		<?php wppl_bp_action_btn($single_user); ?>
		
		<div class="bp-distance-wrapper">
				<?php wppl_bp_address($wppl, $single_user); ?>
				<?php wppl_bp_directions($wppl, $single_user); ?>
				<?php bp_distance_between($wppl, $single_user, $returned_address, $pc); ?>
		</div>
		<div class="clear"></div>
	</li>
	<!-- end members loop -->
<?php $pc++;  } ?>
</ul>
<?php bp_member_hidden_fields(); ?>
	
