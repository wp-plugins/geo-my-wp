jQuery(document).ready(function($) {
	
	function showTab1() {	
		if ( 'active' != jQuery(this).attr('class') ) {
			jQuery('div.lat-long-tab').hide();
			jQuery('div.address-tab').show();
			jQuery('div.extra-info-tab').hide();
			jQuery('div.metabox-tabs-div ul li.active').removeClass('active');
			jQuery('div.metabox-tabs-div ul li.address-tab').addClass('active');
		}
	}
	
	if( $('#_wppl_address').val() == '' ) {
		$('#_wppl_lat').val('');
		$('#_wppl_long').val('');
	}		
	
	$('#publish').click(function () {
	
		if ( mandatoryAddress == true) {
			if( ( $('#_wppl_lat').val() == "" ) || ( $('#_wppl_long').val() == "" ) || ( $('#_wppl_address').val() == "" ) ) {
				$('#publish').disabled = true;
				$('.spinner').hide();
				alert("Post cannot be published. You must enter a vallid address.");
				setTimeout(function() {
					document.getElementById("publish").disabled = false;	
       				jQuery('#publish').removeClass('button-primary-disabled');	
				},2000);
						
			} else {		
  				$('#_wppl_lat').removeAttr('disabled');
  				$('#_wppl_long').removeAttr('disabled');
  				$('#_wppl_address').removeAttr('disabled');
			};
		} else {
			$('#_wppl_lat').removeAttr('disabled');
			$('#_wppl_long').removeAttr('disabled');
			$('#_wppl_address').removeAttr('disabled');
		};
	});
	
	function loaderOff() {
		document.getElementById("ajax-loader").style.visibility = 'hidden';
	    document.getElementById("ajax-loader-image").style.visibility = 'hidden';
	   }
	
	function loaderOn() {
		document.getElementById("ajax-loader").style.visibility = 'visible';
	    document.getElementById("ajax-loader-image").style.visibility = 'visible';
	}
	
	function gmwGetLocation() {
		loaderOn();
		if (navigator.geolocation) {
	    	navigator.geolocation.getCurrentPosition(showPosition,showError);
		} else {
	   	 	alert("Geolocation is not supported by this browser.");
	   	}
	}
	
	$('#gmw-admin-locator-btn').click(function() {
		gmwGetLocation();
	});
	        
	function removefields() {
		$('.address-tab input:text').val('');
      	$('#gmw-saved-data input:text').val('');
      	$('.lat-long-tab input:text').val('');
	}
	
	function showPosition(position) {	
	  	var gotLat = position.coords.latitude;
	   	var gotLong = position.coords.longitude;		
	  	returnAddress(gotLat, gotLong);
	}
	
	function showError(error) {
	  	switch(error.code) {
	    	case error.PERMISSION_DENIED:
	      		alert("User denied the request for Geolocation");
	      		loaderOff();
	     	break;
	    	case error.POSITION_UNAVAILABLE:
	      		alert("Location information is unavailable.");
	      		loaderOff();
	      	break;
	    	case error.TIMEOUT:
	      		alert("The request to get user location timed out.");
	      		loaderOff();
	     	break;
	    	case error.UNKNOWN_ERROR:
	      		alert("An unknown error occurred.");
	      	loaderOff();
	      	break;
		}
	}

	//// conver lat/long to an address //////
	function getAddress() {
	 	loaderOn();
	 	var gotLat = document.getElementById("_wppl_enter_lat").value;
	    var gotLong = document.getElementById("_wppl_enter_long").value;
	    returnAddress(gotLat,gotLong);  
	}
	
	///// main function to conver lat/long to address /////
	function returnAddress(gotLat, gotLong) {
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(gotLat ,gotLong);
		geocoder.geocode({'latLng': latlng, 'region':   'es'}, function(results, status) {
	      	if (status == google.maps.GeocoderStatus.OK) {
	       	 	if (results[0]) {
	         		addressf = results[0].formatted_address;
	         		alert('address successfully returned');
	         		showTab1();
	        		document.getElementById("_wppl_lat").value = gotLat;
	        		$('#gmw_check_lat').val(gotLat);
	        		document.getElementById("_wppl_long").value = gotLong;
	        		$('#gmw_check_long').val(gotLong);
	       			document.getElementById("_wppl_address").value = addressf;
	       			$('#gmw_check_address').val(addressf);
	       			document.getElementById("wppl-addresspicker").value = addressf;
	       	         
	         		var address = results[0].address_components;
	         		//document.getElementById("retAddress").innerHTML = address;
					for ( x in address ) {
						var streetNumber;
						if(address[x].types == 'street_number') {
							if(address[x].long_name != undefined) {
	          					streetNumber = address[x].long_name;
	          					document.getElementById("_wppl_street").value = streetNumber;
	          				}
	          			}
	          				
	          			if (address[x].types == 'route') {
	          				if(address[x].long_name != undefined) {
	          					var streetName = address[x].long_name;
	          					if( streetNumber != undefined ) {
	          						var street = streetNumber + " " + streetName;
	          						document.getElementById("_wppl_street").value = street;
	          					} else {
	          						street = streetName;
	          						document.getElementById("_wppl_street").value = street;
	          					}
	          				}		
	          			}
	          				
	          			if(address[x].types == 'locality,political') {
	          				if(address[x].long_name != undefined) {
	          					var city = address[x].long_name;
	          					document.getElementById("_wppl_city").value = city;
	          				}
	          			}
	          			
	          			if (address[x].types == 'administrative_area_level_1,political') {
	          				if(address[x].long_name != undefined) {
	          					var state = address[x].short_name;
	          					document.getElementById("_wppl_state").value = state;
	          				}
	          					
	          			}
	          			
	          			if (address[x].types == 'postal_code') {
	          				if(address[x].long_name != undefined) {
	          					var zipcode = address[x].long_name;
	          					document.getElementById("_wppl_zipcode").value = zipcode;
	          				}	
	          			}
	          			
	          			if (address[x].types == 'country,political') {
	          				if(address[x].short_name != undefined) {
	          					var country = address[x].short_name;
	          					document.getElementById("_wppl_country").value = country;
	          				}		
	          			}
					}
					
				document.getElementById("_wppl_enter_lat").value = "";
				document.getElementById("_wppl_enter_long").value = "";
	        	}
	        	loaderOff();
	      	} else {
	        	alert("Geocoder failed due to: " + status);
	        	removefields();
	        	loaderOff();
	        
	      	}
	    });
	} 

	/// convert address to lat/long ////////
	function getLatLong() {
		loaderOn();
	    var street = $("#_wppl_street").val();
	    var apt = $("#_wppl_apt").val();
	    var city = $("#_wppl_city").val();
	    var state = $("#_wppl_state").val();
	    var zipcode = $("#_wppl_zipcode").val();
	    var country = $("#_wppl_country").val();
	    
	    retAddress = street + ", " + apt + " " + city + ", " + state + " " + zipcode + ", " + country;
	 
	    geocoder = new google.maps.Geocoder();
	    geocoder.geocode( { 'address': retAddress}, function(results, status) {
	      	if (status == google.maps.GeocoderStatus.OK) {
	      		alert('Latitude / Longitude successfully returned');
	        	retLat = results[0].geometry.location.lat();
	        	retLong = results[0].geometry.location.lng();
	     
	       		$("#_wppl_lat").val(retLat);
	        	$("#_wppl_long").val(retLong);
	       		$("#_wppl_address").val(retAddress);
	       		$("#gmw_check_lat").val(retLat);
	        	$("#gmw_check_long").val(retLong);
	       		$("#gmw_check_address").val(retAddress);
	       		loaderOff();
	    	} else {
	        	alert("Geocode was not successful for the following reason: " + status);     
	       		removefields();
	       		loaderOff();
	    	}
	    });
	}

	$('#gmw-admin-delete-btn').click(function() {
		$('.metabox-tabs-div .address-tab input:text').val('');
		$('.metabox-tabs-div .lat-long-tab input:text').val('');
		$('#gmw-saved-data input:text').val('');
	});
	
	$('#gmw-admin-getlatlong-btn').click(function() {
		getLatLong();
	});
	
	$('#gmw-admin-getaddress-btn').click(function() {
		getAddress();
	});
	
	
	// tab between them
	jQuery('.metabox-tabs li a').each(function(i) {
		var thisTab = jQuery(this).parent().attr('class').replace(/active /, '');
		if ( 'active' != jQuery(this).attr('class') )
			jQuery('div.' + thisTab).hide();

		jQuery('div.' + thisTab).addClass('tab-content');
 
		jQuery(this).click(function(){
			// hide all child content
			jQuery(this).parent().parent().parent().children('div').hide();
 
			// remove all active tabs
			jQuery(this).parent().parent('ul').find('li.active').removeClass('active');
 
			// show selected content
			jQuery('div.' + thisTab).show();
			jQuery('li.'+thisTab).addClass('active');
		});
	});

	jQuery('.heading').hide();
	jQuery('.metabox-tabs').show();
});