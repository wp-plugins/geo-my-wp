<?php
function lala() {// the option name
	global $post, $tax_array;
	$tax_array = array();
	
	define('WPPL_CATEGORY_FIELDS', 'wppl_category_fields_option');
	global $wppl_category_fields, $tag, $wppl_options;
	
	foreach ($wppl_options['address_fields'] as $pt) {
		 $taxes = get_object_taxonomies($pt);
		 $tax_array = array_merge($tax_array, $taxes);
	}
	
	$current_tax = $_GET['taxonomy'];
	
	// your fields (the form)
	if (in_array($current_tax, $tax_array)) add_filter($current_tax.'_edit_form_fields', 'wppl_category_fields');
	if (in_array($current_tax, $tax_array)) add_filter($current_tax.'_add_form_fields', 'wppl_category_fields');
	function wppl_category_fields($tag) {
		$wppl_category_fields = get_option(WPPL_CATEGORY_FIELDS);
		$posts = get_post_types();
	
		?>
		<table class="form-table">
			<tr class="form-field">
				<th scope="row" valign="top"><label for="wppl-category-icon">Map's Icon:</label></th>
				<td>
					<?php 	
					$map_icons = glob(plugin_dir_path(dirname(__FILE__)) . 'map-icons/main-icons/*.png');
					$display_icon = plugins_url('/geo-my-wp/map-icons/main-icons/');
					foreach ($map_icons as $map_icon) {
						echo '<span style="float:left;"><input type="radio" name="wppl_category_icon" value="'.basename($map_icon).'"'; echo ($wppl_category_fields[$tag->term_id]['category_icon'] == basename($map_icon) ) ? "checked" : ""; echo ' />
						<img src="'.$display_icon.basename($map_icon).'" height="40px" width="35px"/></span>';
					} 
					?>
			 </tr>
		</table>
		<?php
	}



// when the form gets submitted, and the category gets updated (in your case the option will get updated with the values of your custom fields above
add_filter('edited_terms', 'update_wppl_category_fields');
add_filter('created_term', 'update_wppl_category_fields');

function update_wppl_category_fields($term_id) {
	global $tax_array;
	
	if( in_array($_POST['taxonomy'], $tax_array) ):
		$wppl_category_fields = get_option(WPPL_CATEGORY_FIELDS);
		$wppl_category_fields[$term_id]['category_icon'] = strip_tags($_POST['wppl_category_icon']);
		update_option(WPPL_CATEGORY_FIELDS, $wppl_category_fields);
	endif;
}

// when a category is removed
add_filter('deleted_term_taxonomy', 'remove_wppl_category_fields');

function remove_wppl_category_fields($term_id) {
	global $tax_array;
	
	if( in_array($_POST['taxonomy'], $tax_array) ):
   	 	$wppl_category_fields = get_option(WPPL_CATEGORY_FIELDS);
    	unset($wppl_category_fields[$term_id]);
    	update_option(WPPL_CATEGORY_FIELDS, $wppl_category_fields);
  endif;
}

function add_post_tag_columns($columns){
    $columns['wppl_icon'] = 'Icon';
    return $columns;
}
if( in_array($_GET['taxonomy'], $tax_array) ) add_filter('manage_edit-'.$_GET['taxonomy'].'_columns', 'add_post_tag_columns');

function add_post_tag_column_content($content, $column_name, $term_id){
	$wppl_category_fields = get_option(WPPL_CATEGORY_FIELDS);
    if( isset($wppl_category_fields[$term_id]['category_icon']) ) 
    	$content .=  '<img src="'.plugins_url('/geo-my-wp/map-icons/main-icons/') . $wppl_category_fields[$term_id]['category_icon'] . '" />';
    else 
    	$content .= 'N/A';
    return $content;
}
if( in_array($_GET['taxonomy'], $tax_array) ) add_filter('manage_'.$_GET['taxonomy'].'_custom_column', 'add_post_tag_column_content',10,3);

}
add_action('init','lala');