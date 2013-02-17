// enable the fields of the new shortcode and save to form to create the shortcode ///
jQuery(document).ready(function() {  
	//jQuery("table tr td.wppl-premium-version-only :input").attr('disabled', 'true'); 
	//jQuery("table tr td span.wppl-premium-message").html('Premium version only'); 
	
    jQuery("#create-new-post-types-shortcode").click(function() {    	
    	jQuery("#wppl-new-shortcode-fields :input").removeAttr('disabled');
    	jQuery("#wppl-new-shortcode-fields #form-type").val('posts'); 
        jQuery("#shortcode-submit").submit();
        return false;
     });
     jQuery("#create-new-friends-shortcode").click(function() {    	
    	jQuery("#wppl-new-shortcode-fields :input").removeAttr('disabled');
    	jQuery("#wppl-new-shortcode-fields #form-type").val('friends'); 
        jQuery("#shortcode-submit").submit();
        return false;
     }); 
     jQuery("#create-new-groups-shortcode").click(function() {    	
    	jQuery("#wppl-new-shortcode-fields :input").removeAttr('disabled');
    	jQuery("#wppl-new-shortcode-fields #form-type").val('groups'); 
        jQuery("#shortcode-submit").submit();
        return false;
     });
     jQuery("#create-new-grand-map-shortcode").click(function() {    	
    	jQuery("#wppl-new-shortcode-fields :input").removeAttr('disabled');
    	jQuery("#wppl-new-shortcode-fields #form-type").val('grand_map'); 
        jQuery("#shortcode-submit").submit();
        return false;
     });
});  
 		
 	function editElement(element) {
    	jQuery(element).toggle();
	}

	function removeElement(element) {  
    	jQuery(element).remove();
    	jQuery("#shortcode-submit").submit();
    } 
    
/////////////////////
jQuery(document).ready(function() { 

	jQuery.each(postsType, function(index, valueNot) {
  		jQuery('table#shortcode-table-' + valueNot + ' tr.friends-yes input').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.friends-yes select').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.friends-yes').hide()
  		jQuery('table#shortcode-table-' + valueNot + ' tr.groups-yes input').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.groups-yes select').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.groups-yes').hide()
  		jQuery('table#shortcode-table-' + valueNot + ' tr.grand-map-yes input').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.grand-map-yes select').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.grand-map-yes').hide()
	});
	
	jQuery.each(friendsType, function(index, valueNot) {
  		jQuery('table#shortcode-table-' + valueNot + ' tr.post-types-yes input').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.post-types-yes select').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.post-types-yes').hide()
  		jQuery('table#shortcode-table-' + valueNot + ' tr.groups-yes input').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.groups-yes select').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.groups-yes').hide()
  		jQuery('table#shortcode-table-' + valueNot + ' tr.grand-map-yes input').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.grand-map-yes select').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.grand-map-yes').hide()	
	});
	
	jQuery.each(groupsType, function(index, valueNot) {
  		jQuery('table#shortcode-table-' + valueNot + ' tr.post-types-yes input').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.post-types-yes select').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.post-types-yes').hide()
  		jQuery('table#shortcode-table-' + valueNot + ' tr.friends-yes input').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.friends-yes select').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.friends-yes').hide()
  		jQuery('table#shortcode-table-' + valueNot + ' tr.grand-map-yes input').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.grand-map-yes select').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.grand-map-yes').hide()
	
	});
})

