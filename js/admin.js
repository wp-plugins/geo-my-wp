// we retrive it from the admin settings //
var elementCounter = jQuery("input[name=element-max-id]").val();
   
    jQuery(document).ready(function() {  
        jQuery("#add-featured-post").click(function() {  
            var elementRow = jQuery("#wppl-shortcode-info-holder-placeholder").clone();  
            var newId = "wppl-shortcode-info-holder-" + elementCounter;  
  
            elementRow.attr("id", newId);  
            elementRow.show();  
  
            var inputPostTypes = jQuery("input[name=post_types]", elementRow);
            inputPostTypes.attr("name", "wppl_shortcode["+ elementCounter +"][post_types][]"); 
            
            var inputPostTax = jQuery("input[name=taxonomies]", elementRow);
            inputPostTax.attr("name", "wppl_shortcode["+ elementCounter +"][taxonomies][]"); 
            
            var inputShowMap = jQuery("input[name=show_map]", elementRow);
            inputShowMap.attr("name", "wppl_shortcode["+ elementCounter +"][show_map]");
            
            var inputMapHeight = jQuery("input[name=map_height]", elementRow);
            inputMapHeight.attr("name", "wppl_shortcode["+ elementCounter +"][map_height]");  
            
            var inputMapWidth = jQuery("input[name=map_width]", elementRow);
            inputMapWidth.attr("name", "wppl_shortcode["+ elementCounter +"][map_width]");
            
            var inputPerPage = jQuery("input[name=per_page]", elementRow);
            inputPerPage.attr("name", "wppl_shortcode["+ elementCounter +"][per_page]");
            
            var inputByDriving = jQuery("input[name=by_driving]", elementRow);
            inputByDriving.attr("name", "wppl_shortcode["+ elementCounter +"][by_driving]");
            
             var inputGetDirections = jQuery("input[name=get_directions]", elementRow);
            inputGetDirections.attr("name", "wppl_shortcode["+ elementCounter +"][get_directions]");
  
            var labelField = jQuery("label", elementRow);  
            labelField.attr("for", "post-types-" + elementCounter);   
  			
  			var removeLink = jQuery("a", elementRow).click(function() {  
    		removeElement(elementRow);  
    		return false;  
			});  


            elementCounter++;  
            jQuery("input[name=element-max-id]").val(elementCounter);  
  
            jQuery("#posts-taxonomies-list").append(elementRow);  
  
            return false;  
        });  
    });  
 		
 	function editElement(element) {
    jQuery(element).toggle();
	}

	function removeElement(element) {  
    jQuery(element).remove();
    } 
    
////////////////////////////

function change_it(nam,i_d) {
	n = jQuery("input[name='" + nam + "']:checked").length;
	if(n == 1) { 
    	var sel = jQuery("input[name='" + nam + "']:checked").val(); 
    	jQuery('#' + sel + '_cat_' + i_d).css('display','block');
    	//jQuery('#' + sel + '_cat_' + i_d).animate({backgroundColor: '#F0DADA'}, 'fast');
    	jQuery('#posts-checkboxes-' + i_d).css('background','#F9F9F9');
    } else {
    	jQuery('#posts-checkboxes-' + i_d).css('background','#F9F9F9');
    	jQuery('.taxes-' + i_d + ' div').css('display','none');
    	jQuery('.taxes-' + i_d + ' input').attr('checked',false);
		
    }    
    if(n == 0) {
    	jQuery('#posts-checkboxes-' + i_d).animate({backgroundColor: '#FAA0A0'}, 'fast');
    } 	
};


