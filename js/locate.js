jQuery(document).ready(function($){ 
	//GmwGetLocation();
	var lWidget =0;
	$('.gmw-map-loader').fadeOut(1500);
	
	if (navigator.geolocation) $('.gmw-locate-me-btn').show();
	
	// When click on locator button in a form
	$('.gmw-locate-me-btn').click(function() {
		
		$(this).closest('form').find('.gmw-submitted').val('1');
	
		$(this).closest('form').find(".gmw-locator-spinner").show('fast');
		setTimeout(function() {
			GmwGetLocation();	
		},500);
		
	});

	/**
	 * GMW JavaScript - Set Cookie
	 * @version 1.0
	 * @author Eyal Fitoussi
	 */
	function setCookie(name,value,exdays) {
		var exdate=new Date();
		exdate.setTime(exdate.getTime() + (exdays*24*60*60*1000));
		var cooki=escape(encodeURIComponent(value)) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
		document.cookie=name + "=" + cooki + "; path=/";
	}
	
	/**
	 * GMW JavaScript - Get Cookie
	 * @version 1.0
	 * @author Eyal Fitoussi
	 */
	function getCookie(cookie_name) {
	    var results = document.cookie.match ('(^|;) ?' + cookie_name + '=([^;]*)(;|$)');
	    return results ? decodeURIComponent(results[2]) : null;
	}
	
	/**
	 * GMW JavaScript - Delete Cookie
	 * @version 1.0
	 * @author Eyal Fitoussi
	 */
	function deleteCookie(c_name) {
	    document.cookie = encodeURIComponent(c_name) + "=deleted; expires=" + new Date(0).toUTCString();
	}
	
	submitNo = 1;
	if (getCookie('wppl_lat') == undefined) {
		 if ( (getCookie('wppl_asked_today') != "yes") && (autoLocate == 1) ) {
			submitNo = 0;
			$('body').prepend('<div id="gmw-auto-locate-hidden" style="display:none"><div id="message"><div id="gmw-locator-success-message">Getting your current location...</div></div></div>').ready(function() {
				tb_show("Your Current Location","#TB_inline?#bb&width=250&height=130&inlineId=gmw-auto-locate-hidden",null);
			});
			
			setCookie("wppl_asked_today","yes",1);
			GmwGetLocation();
		}
	}
		
	function removeMessage() {
		$('.gmw-submitted').each(function() {
			if( $(this).val() == 1 ) {
				$('#TB_ajaxContent').find('#gmw-cl-message-wrapper').html('');
				tb_remove();
				return;
			}
		});
		$('#TB_ajaxContent').find('#gmw-cl-message-wrapper').html('');
    	$('.gmw-cl-form #gmw-cl-info').show('fast');
	}
	
	
	$('.gmw-cl-trigger').click(function() {
		lWidget = 1;
		$(this).closest('.gmw-cl-form').find(".gmw-locator-spinner").show('fast');
		GmwGetLocation();
	});
	
	/**
	 * GMW JavaScript - Get the user's curent location
	 * @version 1.0
	 * @author Eyal Fitoussi
	 */
	function GmwGetLocation() {
		// if GPS exists locate the user //
		if (navigator.geolocation) {
	    	navigator.geolocation.getCurrentPosition(showPosition,showError);
	    	if (autoLocate != 1) {
	    		$('.gmw-cl-form #gmw-cl-info').hide('fast');
	    		$('#TB_ajaxContent').find('#gmw-cl-message-wrapper').html('<div id="gmw-locator-success-message">Getting your current location...</div>');
			}
		} else {
			// if nothing found we cant do much. we cant locate the user :( //
			$('#TB_ajaxContent').find('#gmw-cl-message-wrapper').html('<div id="gmw-locator-success-message">we are sorry! Geolocation is not supported by this browser and we cannot locate you.</div>');
			setTimeout(function() {
	      		setCookie("wppl_denied","denied",1);
	      		removeMessage();
	      	},2500);
		} 
		
		// GPS locator function //
		function showPosition(position) {
			
			var geocoder = new google.maps.Geocoder();
	  		geocoder.geocode({'latLng': new google.maps.LatLng(position.coords.latitude, position.coords.longitude)}, function (results, status) {
	        	if ( status == google.maps.GeocoderStatus.OK ) {
	          		getAddressFields(results);
	        	} else {
	          		alert('Geocoder failed due to: ' + status);
	        	}
	      	});
	  	}
	
		function showError(error) {
			
			switch(error.code) {
	    		case error.PERMISSION_DENIED:
	    			if ( autoLocate == 1) {
	    				$('#TB_ajaxContent').find('#message').html('<div id="gmw-locator-error-message">User denied the request for Geolocation.</div>');
	    				setTimeout(function() {
		      				setCookie("wppl_denied","denied",1);
		      				tb_remove();
		      			},1500);
	    			}
	      			$('#TB_ajaxContent').find('#gmw-cl-message-wrapper').html('<div id="gmw-locator-error-message">User denied the request for Geolocation.</div>');
	      			$(".gmw-locator-spinner").hide('fast');		
	      			setTimeout(function() {
	      				setCookie("wppl_denied","denied",1);
	      				removeMessage();
	      			},1500);
	      		break;
	    		case error.POSITION_UNAVAILABLE:
	      			$('#TB_ajaxContent').find('#gmw-cl-message-wrapper').html('<div id="gmw-locator-error-message">Location information is unavailable</div>');
	      			$(".gmw-locator-spinner").hide('fast');		
	      			setTimeout(function() {
	      				setCookie("wppl_denied","denied",1);
	      				removeMessage();
	      			},1500);
	      		break;
	    		case error.TIMEOUT:
	      			$('#TB_ajaxContent').find('#gmw-cl-message-wrapper').html('<div id="gmw-locator-error-message">The request to get user location timed out</div>');
	      			$(".gmw-locator-spinner").hide('fast');		
	      			setTimeout(function() {
	      				setCookie("wppl_denied","denied",1);
	      				removeMessage();
	      			},1500);
	      		break;
	    			case error.UNKNOWN_ERROR:
	      			$('#TB_ajaxContent').find('#gmw-cl-message-wrapper').html('<div id="gmw-locator-error-message">An unknown error occurred</div>');
	      			$(".gmw-locator-spinner").hide('fast');		
	      			setTimeout(function() {
	      				setCookie("wppl_denied","denied",1);
	      				removeMessage();
	      			},1500);
	      		break;
			}
		}
	}
	
	/* get location in user's location widget when manually typing address */	
	$('.gmw-cl-form').submit(function() {
		lWidget = 1;
		$(this).closest('.gmw-cl-form').find(".gmw-locator-spinner").show('fast');
		
		var retAddress = $(this).find(".gmw-cl-address").val();
		
		geocoder = new google.maps.Geocoder();
		//locateMessage = 0;
	   	geocoder.geocode( { 'address': retAddress}, function(results, status) {
	      	if (status == google.maps.GeocoderStatus.OK) {	
	      		geocoder.geocode({'latLng': new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng())}, function (results, status) {
	        		if (status == google.maps.GeocoderStatus.OK) {
	        			//submitNo = 0;
	          			getAddressFields(results);
	        		} else {
	          			alert('Geocoder failed due to: ' + status);
	        		}
	      		});
	    	} else {
	    		$(".gmw-locator-spinner").hide('fast');
	    		$('.gmw-cl-form #gmw-cl-info').hide('fast');
	    		$('#TB_ajaxContent .gmw-cl-form #gmw-cl-message-wrapper').html('<div id="gmw-locator-error-message">Geocode was not successful for the following reason: ' + status +'</div>');
	      		setTimeout(function() {
	      			setCookie("wppl_denied","denied",1);
	      			removeMessage();
	      		},2500);
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
	
		var street_number = false;
		var street = false;
		var city = false;
		var state = false;
		var zipcode = false;
		var country = false;
		var cityYes = false;
	    var stateYes = false;
	    var zipcodeYes = false;
	    var countryYes = false;
	    var streetYes = false;
	           	
		var address = results[0].address_components;
		
		gotLat = results[0].geometry.location.lat();
	    gotLong = results[0].geometry.location.lng();
	    
	    setCookie("wppl_lat",gotLat,7);
	    setCookie("wppl_long",gotLong,7);
		
		/* check for each of the address components and if exist save it in a cookie */
		for ( x in address ) {
			
			if ( address[x].types == 'street_number' ) {
				street_number = address[x].long_name; 
			}
			
			if ( address[x].types == 'route') {
				street = address[x].long_name;  
				if ( street_number != false ) {
				
					street = street_number + ' ' + street;
				} else {
					street = street;
				}
				setCookie("wppl_street", street, 7);
				streetYes = 1;
			}
	
			if ( address[x].types == 'administrative_area_level_1,political' ) {
	          	state = address[x].short_name;
	          	setCookie("wppl_state", state, 7);
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
		if( streetYes != 1 ) deleteCookie('wppl_street');
		if( cityYes != 1 ) deleteCookie('wppl_city');
		if( stateYes != 1 ) deleteCookie('wppl_state');
		if( zipcodeYes != 1 ) deleteCookie('wppl_zipcode');
		if( countryYes != 1 ) deleteCookie('wppl_country');
		
		/* check for city and state and if not city and country to display in the message */
		if (cityYes == 1) {
			addressOut = city;
			if (stateYes == 1) {
				addressOut = city + ' ' + state;
			} else if (countryYes == 1) {
				addressOut = city + ' ' + country;
			}
				
		} else {
			
			if (stateYes == 1) {
				addressOut = state;
			} else if (countryYes == 1) {
				addressOut = country;
			}	
		}
			
		/* display found you message */		
		//$(".wppl-address").val(addressOut);
				
		if ( autoLocate == 1) {
			//$(".gmw-locator-spinner").hide('fast');	
    		//$('.gmw-cl-form #gmw-cl-info').hide('fast');
			$('#TB_ajaxContent').find('#message').html('<div id="gmw-locator-success-message">We found you at ' + addressOut + '</div>');
			setTimeout(function() {
				location.reload();	
			},1500);
		}
		
		/* if a form was submitted */
		$('.gmw-submitted').each(function() {
			
			if( $(this).val() == 1 ) {
				
				$(".gmw-locator-spinner").hide('fast');	
	    		$('.gmw-cl-form #gmw-cl-info').hide('fast');
				$('#TB_ajaxContent').find('#gmw-cl-message-wrapper').html('<div id="gmw-locator-success-message">We found you at ' + addressOut + '</div>');
				
				gForm = $(this).closest('form');
				
				$('.gmw-address-field').attr('disabled','disabled');
				
				$('.gmw-address').val('');
				
				$('<input>').attr({
				    type: 'hidden',
				    //id: 'foo',
				    name: 'wppl_address[]',
				    value: addressOut
				}).appendTo(gForm);
				
				var btnSubmit = $(this).closest('form').find('.wppl-search-submit');
				setTimeout(function() {
					btnSubmit.click();	
				},1500);
			}
		});
		
		if ( lWidget == 1 ) {
			
			$(".gmw-locator-spinner").hide('fast');	
    		$('.gmw-cl-form #gmw-cl-info').hide('fast');
			$('#TB_ajaxContent').find('#gmw-cl-message-wrapper').html('<div id="gmw-locator-success-message">We found you at ' + addressOut + '</div>');
			setTimeout(function() {
				location.reload();
			},1500);
		} 
	}
  
});	
