jQuery(function() {
	//// show hide map ///
    jQuery('.wppl-help-btn').click(function(event){
    	event.preventDefault();
    	jQuery(this).closest("div").find(".wppl-help-message").slideToggle();
    
    });    
    jQuery('.wppl-edit').click(function(){
    	jQuery(this).closest('div').find('.open-settings').slideToggle('slow');
	});
	jQuery('.wppl-shortcodes-help-btn').click(function(){
    	jQuery(this).closest('.wppl-shortcodes-help').find('.open-settings').slideToggle('slow');
	});
	
});

jQuery('.wppl-icons-list-button').click(function(){
	jQuery(this).closest('div').find('.wppl-icons-list').slideToggle('slow');
});
