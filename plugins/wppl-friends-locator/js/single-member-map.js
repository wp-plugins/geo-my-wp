jQuery(window).load(function(){
	var nana = sMArgs;
	jQuery(function() {
		jQuery('.show-directions').click(function(event){
			event.preventDefault();
			jQuery(this).closest("div").find(".wppl-single-member-direction").slideToggle(); 
		}); 
	});
	
	var i; 
	geocoder = new google.maps.Geocoder();
	geocoder.geocode( { 'address': nana.singleMember[1]['address']}, function(results,status) {
		if (status == google.maps.GeocoderStatus.OK) {
			for (i = 1; i <= (nana.memberMapId); i++) {
				var mapSingle = new google.maps.Map(document.getElementById('member-map-'+ i), {
					zoom: parseInt(nana.singleMember[i]['zoom_level']),
					center: new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng()),
					mapTypeId: google.maps.MapTypeId[nana.singleMember[i]['map_type']],
				});	
				var marker; 
				marker = new google.maps.Marker({
					position: new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng()),
					map: mapSingle,
					shadow:'https://chart.googleapis.com/chart?chst=d_map_pin_shadow'       
				});
			}
		}
	});		
});

