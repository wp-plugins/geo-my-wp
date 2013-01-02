jQuery(window).load(function(){ 
	for (var i = 0; i < mainMapArgs.locations.length; i++) {
		var directionsDisplay;
		var directionsService = new google.maps.DirectionsService();
	
		var latlngbounds = new google.maps.LatLngBounds( );
		var latitude = mainMapArgs.locations[i]['lat'];
		var longitude = mainMapArgs.locations[i]['long'];
		
		if (mainMapArgs.yLocation != "0") {
			var yourLocation = new google.maps.LatLng(mainMapArgs.yLocation[1],mainMapArgs.yLocation[2]);
			latlngbounds.extend(yourLocation);
		} 
		
        var myOptions = {
          zoom: 8,
          center: new google.maps.LatLng(latitude, longitude),
          mapTypeId: google.maps.MapTypeId[mainMapArgs.sMapType]   
        };

        var map = new google.maps.Map(document.getElementById("wppl-single-map-" + (i + 1)), myOptions);
        directionsDisplay = new google.maps.DirectionsRenderer();
		//directionsDisplay.setMap(map);
		
		var infowindow = new google.maps.InfoWindow();
		var marker, i;
	
		var point = new google.maps.LatLng(latitude, longitude);
		latlngbounds.extend(point);
		
		if(mainMapArgs.mapIconUsage == undefined || mainMapArgs.mapIconUsage== '') {
			mapIcon = mainMapArgs.mIFolder + '/main-icons/_default.png';
		}else if(mainMapArgs.mapIconUsage == 'per_post') {
			if (mainMapArgs.locations[i]['map_icon'] == "_default.png") {
				mapIcon = 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='+ ((mainMapArgs.page * 1) + i + 1) +'|FF776B|000000';
			} else {
				if (mainMapArgs.locations[i]['map_icon'] == '') {
					mapIcon = mainMapArgs.mIFolder + '/main-icons/' + mainMapArgs.mainIcon;
				} else {
					mapIcon = mainMapArgs.mIFolder + '/main-icons/' + mainMapArgs.locations[i]['map_icon'];
				}
			}
		} else if(mainMapArgs.mapIconUsage == 'same') {
			if (mainMapArgs.mainIcon == "_default.png" || mainMapArgs.mainIcon == undefined) {
				mapIcon = 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='+ ((mainMapArgs.page * 1) + i + 1) +'|FF776B|000000';
			} else {
				mapIcon = mainMapArgs.mIFolder + '/main-icons/' + mainMapArgs.mainIcon;
			}
		
		} else if(mainMapArgs.mapIconUsage == 'per_post_type') {
			//if (mainMapArgs.locations[i]['post_type'] == "_default.png") {
			//	mapIcon = 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='+ ((mainMapArgs.page * 1) + i + 1) +'|FF776B|000000';
			//} else {
				mapIcon = mainMapArgs.mIFolder + '/main-icons/' +  mainMapArgs.postTypeIcons[mainMapArgs.locations[i]['post_type']];
			//}
		}
		
		marker = new google.maps.Marker({
			position: new google.maps.LatLng(latitude, longitude),
			map: map,
			icon:mapIcon,
			shadow:'https://chart.googleapis.com/chart?chst=d_map_pin_shadow'       
		});

		map.fitBounds(latlngbounds);
	
		marker = new google.maps.Marker({
			position: new google.maps.LatLng(mainMapArgs.yLocation[1],mainMapArgs.yLocation[2]),
			map: map,
    		icon: mainMapArgs.mIFolder + '/your-location-icons/' + mainMapArgs.yLIcon,
		}); 
		
		/*
		});
		//function calcRoute() {
  		var start = new google.maps.LatLng(latitude,longitude);
  		var end = new google.maps.LatLng(your_location[1],your_location[2]);
  		var request = {
    		origin:start,
    		destination:end,
    		travelMode: google.maps.TravelMode.DRIVING
 		};
 		
  		directionsService.route(request, function(result, status) {
    		if (status == google.maps.DirectionsStatus.OK) {
      			directionsDisplay.setDirections(result);
      			//alert((result.routes[0].legs[0].distance.value * 0.001));
      			
      			
    		}
 		 });
 		 jQuery('.wppl-driving-distance-' + i).text(result.routes[0].legs[0].distance.value * 0.001)
 		 directionsDisplay.setMap(map);
 		 //directionsDisplay.setPanel(document.getElementById("wppl-directions-panel-" + (i + 1) ));    
 		 */
//}	
	}
});
