var formId;
var searchLocator = 0;
jQuery(window).load(function(){ 
	jQuery('.map-loader').fadeOut(1500);
	if (navigator.geolocation) jQuery('.wppl-locate-me-btn').show();
	jQuery('.wppl-locate-me-btn').click(function() {
		searchLocator = 1;
		formId = this.id;
		jQuery('#wppl-search-locator-wrapper').find("#wppl-locator-spinner").show('fast');
		getLocation();
		
	});
});

function showSpinner() {
	jQuery('.wppl-location-form').find("#wppl-locator-spinner").show('fast');
}

//// scroll to top of page ////
jQuery(function() {
	jQuery(window).scroll(function() {
		if(jQuery(this).scrollTop() != 0) {
			jQuery('#wppl-go-top').fadeIn();	
		} else {
			jQuery('#wppl-go-top').fadeOut();
		}
	});
 
	jQuery('#wppl-go-top').click(function() {
		jQuery('body,html').animate({scrollTop:0},800);
	});	
	
});

submitNo = 1;
if (getCookie('wppl_city') == undefined) {
	 if ( (getCookie('wppl_asked_today') != "yes") && (autoLocate == 1) ) {
		submitNo = 0;
		setCookie("wppl_asked_today","yes",1);
		getLocation();
	}
}
	
//// set cookies ////
function setCookie(name,value,exdays) {
	var exdate=new Date();
	exdate.setTime(exdate.getTime() + (exdays*24*60*60*1000));
	var cooki=escape(encodeURIComponent(value)) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=name + "=" + cooki + "; path=/";
}

function getCookie(cookie_name) {
    var results = document.cookie.match ('(^|;) ?' + cookie_name + '=([^;]*)(;|$)');
    return results ? decodeURIComponent(results[2]) : null;
} 

function deleteCookie(c_name) {
    document.cookie = encodeURIComponent(c_name) + "=deleted; expires=" + new Date(0).toUTCString();
}

function removeMessage() {
	if (searchLocator == 1) {
		jQuery('#TB_ajaxContent').find('#wppl-locator-message-wrapper').html('');
		searchLocator = 0;
		tb_remove();
	} else {
		jQuery('#TB_ajaxContent').find('#wppl-locator-message-wrapper').html('');
    	jQuery('.wppl-location-form #wppl-locator-info').show('fast');
    }
}

//// run the user locator and geolocation ////
function getLocation() {
	// if GPS exists locate the user //
	if (navigator.geolocation) {
    	navigator.geolocation.getCurrentPosition(showPosition,showError);	
    	if (searchLocator != 1) {
    		jQuery('.wppl-location-form #wppl-locator-info').hide('fast');
    		jQuery('#TB_ajaxContent').find('#wppl-locator-message-wrapper').html('<div id="wppl-locator-success-message">Getting your current location...</div>');
		}
	} else {
		// if nothing found we cant do much. we cant locate the user :( //
		jQuery('#TB_ajaxContent').find('#wppl-locator-message-wrapper').html('<div id="wppl-locator-success-message">we are sorry! Geolocation is not supported by this browser and we cannot locate you.</div>');
		setTimeout(function() {
      		setCookie("wppl_denied","denied",1);
      		removeMessage();
      	},2500)
	} // end locator function //
	
	// GPS locator function //
	function showPosition(position) {
  		var geocoder = new google.maps.Geocoder();
  		geocoder.geocode({'latLng': new google.maps.LatLng(position.coords.latitude, position.coords.longitude)}, function (results, status) {
        	if (status == google.maps.GeocoderStatus.OK) {
          		getAddressFields(results);
        	} else {
          		alert('Geocoder failed due to: ' + status);
        	}
      	});
  	}

	function showError(error) {
		switch(error.code) {
    		case error.PERMISSION_DENIED:
      			jQuery('#TB_ajaxContent').find('#wppl-locator-message-wrapper').html('<div id="wppl-locator-not-success-message">User denied the request for Geolocation.</div>');
      			jQuery('#TB_ajaxContent').find("#wppl-locator-spinner").hide('fast');		
      			setTimeout(function() {
      				setCookie("wppl_denied","denied",1);
      				removeMessage();
      			},2500)
      		break;
    		case error.POSITION_UNAVAILABLE:
      			jQuery('#TB_ajaxContent').find('#wppl-locator-message-wrapper').html('<div id="wppl-locator-not-success-message">Location information is unavailable</div>');
      			jQuery('#TB_ajaxContent').find("#wppl-locator-spinner").hide('fast');
      			setTimeout(function() {
      				setCookie("wppl_denied","denied",1);
      				removeMessage();
      			},2500)
      		break;
    		case error.TIMEOUT:
      			jQuery('#TB_ajaxContent').find('#wppl-locator-message-wrapper').html('<div id="wppl-locator-not-success-message">The request to get user location timed out</div>');
      			jQuery('#TB_ajaxContent').find("#wppl-locator-spinner").hide();
      			setTimeout(function() {
      				setCookie("wppl_denied","denied",1);
      				removeMessage();
      			},2500)
      		break;
    			case error.UNKNOWN_ERROR:
      			jQuery('#TB_ajaxContent').find('#wppl-locator-message-wrapper').html('<div id="wppl-locator-not-success-message">An unknown error occurred</div>');
      			jQuery('#TB_ajaxContent').find("#wppl-locator-spinner").hide();
      			setTimeout(function() {
      				setCookie("wppl_denied","denied",1);
      				removeMessage();
      			},2500)
      		break;
		}
	}
}

/* get location in user's location widget when manually typing address */	
jQuery('.wppl-location-form').submit(function() {
	showSpinner();
	var retAddress = jQuery(this).find(".wppl-user-address").val();
	
	geocoder = new google.maps.Geocoder();
	locateMessage = 0;
   	geocoder.geocode( { 'address': retAddress}, function(results, status) {
      	if (status == google.maps.GeocoderStatus.OK) {	
      		geocoder.geocode({'latLng': new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng())}, function (results, status) {
        		if (status == google.maps.GeocoderStatus.OK) {
        			submitNo = 0;
          			getAddressFields(results);
        		} else {
          			alert('Geocoder failed due to: ' + status);
        		}
      		});
    	} else {
    		jQuery('.wppl-location-form').find("#wppl-locator-spinner").hide('fast');	
    		jQuery('.wppl-location-form #wppl-locator-info').hide('fast');
    		jQuery('#TB_ajaxContent .wppl-location-form #wppl-locator-message-wrapper').html('<div id="wppl-locator-not-success-message">Geocode was not successful for the following reason: ' + status +'</div>');
      		setTimeout(function() {
      			setCookie("wppl_denied","denied",1);
      			removeMessage();
      		},2500)
    	}
   	}); 
});


/* main function to geocoding from lat/long to address or the other way around when locating the user */
function getAddressFields(results) {
	/* remove all cookies - we gonna get new values */
	deleteCookie('wppl_city');
	deleteCookie('wppl_state');
	deleteCookie('wppl_zipcode');
	deleteCookie('wppl_country');
	deleteCookie('wppl_lat');
	deleteCookie('wppl_long');

	var city = '';
	var state = '';
	var zipcode = '';
	var country = '';
	var cityYes;
    var stateYes;
    var zipcodeYes;
    var countryYes;
    
        	
	var address = results[0].address_components;
	
	gotLat = results[0].geometry.location.lat();
    gotLong = results[0].geometry.location.lng();
    
    setCookie("wppl_lat",gotLat,7);
    setCookie("wppl_long",gotLong,7);
	
	/* check for each of the address components and if exist save it in a cookie */
	for ( x in address ) {
					
		if (address[x].types == 'administrative_area_level_1,political') {
          	state = address[x].short_name;
          	setCookie("wppl_state",state,7);
          	stateYes = 1;
         } 
         
         if(address[x].types == 'locality,political') {
          	city = address[x].long_name;
          	setCookie("wppl_city",city,7);
          	cityYes = 1;
         } 
         
         if (address[x].types == 'postal_code') {
          	zipcode = address[x].long_name;
          	setCookie("wppl_zipcode",zipcode,7);
          	zipcodeYes = 1;
        } 
        
        if (address[x].types == 'country,political') {
          	country = address[x].long_name;
          	setCookie("wppl_country",country,7);
          	countryYes = 1;
         } 
	}
	
	/* if component is not exists clear the cookie */
	if(cityYes != 1) {setCookie("wppl_city",'',7);}
	if(stateYes != 1) {setCookie("wppl_state",'',7);}
	if(zipcodeYes != 1) {setCookie("wppl_zipcode",'',7);}
	if(countryYes != 1) {setCookie("wppl_country",'',7);}
	
	/* check for city and state and if not city and country to display in the message */
	if (cityYes == 1) {
		cityState = city;
		if (stateYes == 1) {
			cityState = city + ', ' + state;
		} else if (countryYes == 1) {
			cityState = city + ', ' + country;
		}
			
	} else {
		if (stateYes == 1) {
			cityState = state;
		} else if (countryYes == 1) {
			cityState = country;
		}	
	}
		
	/* display found you message */		
	jQuery(".wppl-address").val(cityState);
			
	if(searchLocator == 0) {
		jQuery('.wppl-location-form #wppl-locator-info').hide('fast');
		jQuery('#TB_ajaxContent').find('#wppl-locator-message-wrapper').html('<div id="wppl-locator-success-message">We found you at ' + cityState + '</div>');
		setTimeout(function() {
			window.location.reload();	
		},1000);
	} else {
		jQuery('#TB_ajaxContent').find('#wppl-locator-message-wrapper').html('<div id="wppl-locator-success-message">We found you at ' + cityState + '</div>');
		setTimeout(function() {
	 		var btnSubmit = jQuery('#'+formId).closest('form').find('.wppl-search-submit');
			btnSubmit.click();
		},1000);
	} 
}
  
	
