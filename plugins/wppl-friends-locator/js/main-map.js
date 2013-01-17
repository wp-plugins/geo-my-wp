///// friends map ////
jQuery(window).load(function(){ 
	jQuery(function() {
		//// show hide map ///
		jQuery('.map-show-hide-btn').click(function(event){
			event.preventDefault();
			jQuery("#wppl-hide-map").slideToggle();
		
		}); 
	});
		
	var latlngbounds = new google.maps.LatLngBounds();
	if (fMapArgs.your_location != "0") {
		var yourLocation  = new google.maps.LatLng(fMapArgs.your_location[1],fMapArgs.your_location[2]);
		latlngbounds.extend(yourLocation);
	}

	var membersMap = new google.maps.Map(document.getElementById('wppl-members-map'), {
		center: new google.maps.LatLng(fMapArgs.your_location[1],fMapArgs.your_location[2]),
		mapTypeId: google.maps.MapTypeId[fMapArgs.mapType],
	});				
	
	var marker, i, fliw;
	mMarkers = [];
	
	for (i = 0; i < fMapArgs.locations.length; i++) {  
		var memberLocation = new google.maps.LatLng(fMapArgs.locations[i]['lat'], fMapArgs.locations[i]['long']);
		latlngbounds.extend(memberLocation);
			
		if(fMapArgs.perMemberIcon == 1) {
			if (fMapArgs.locations[i]['map_icon'] == "_default.png") {
				mapIcon = 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='+ ((fMapArgs.page * 1) + i + 1) +'|FF776B|000000';
				shadow = 'https://chart.googleapis.com/chart?chst=d_map_pin_shadow';
			} else if (fMapArgs.locations[i]['map_icon'] == "_avatar.png") {
				//mapIcon = avatar[locations[i]['member_id']][2] + 'jpg';	
				var mapIcon = new google.maps.MarkerImage(
					fMapArgs.locations[i]['avatar_icon'],
					new google.maps.Size(30, 30),
					new google.maps.Point(0,0),
					new google.maps.Point(9.5, 29),
					new google.maps.Size(28,27)
				);
					var shadow = new google.maps.MarkerImage(
					fMapArgs.bpIconsFolder + '_avatar.png',
					new google.maps.Size(40, 44),
					new google.maps.Point(0,0),
					new google.maps.Point(15, 35)
				);
			} else {
				if (fMapArgs.locations[i]['map_icon'] == '') {
					mapIcon = fMapArgs.bpIconsFolder + fMapArgs.mainIcon;
					shadow = 'https://chart.googleapis.com/chart?chst=d_map_pin_shadow';
				} else {
					mapIcon = fMapArgs.bpIconsFolder + fMapArgs.locations[i]['map_icon'];
					shadow = 'https://chart.googleapis.com/chart?chst=d_map_pin_shadow';		
				}
			}
		} else {
			if (fMapArgs.mainIcon == "_default.png" || fMapArgs.mainIcon == undefined ) {
				mapIcon = 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='+ ((fMapArgs.page * 1) + i + 1) +'|FF776B|000000';
				shadow = 'https://chart.googleapis.com/chart?chst=d_map_pin_shadow';
			} else if (fMapArgs.mainIcon == "_avatar.png") {
				//mapIcon = avatar[locations[i]['member_id']][2] + 'jpg';	
				var mapIcon = new google.maps.MarkerImage(
					fMapArgs.locations[i]['avatar_icon'],
					new google.maps.Size(30, 30),
					new google.maps.Point(0,0),
					new google.maps.Point(9.5, 29),
					new google.maps.Size(28,27)
				);
					var shadow = new google.maps.MarkerImage(
					fMapArgs.bpIconsFolder + '_avatar.png',
					new google.maps.Size(40, 44),
					new google.maps.Point(0,0),
					new google.maps.Point(15, 35)
				);
					
			} else {
				mapIcon = fMapArgs.bpIconsFolder + fMapArgs.mainIcon;
				shadow = 'https://chart.googleapis.com/chart?chst=d_map_pin_shadow';
			}
		}
			
		mMarkers[i] = new google.maps.Marker({
			position: memberLocation,
			icon:mapIcon,
			animation: google.maps.Animation.DROP,
			shadow: shadow,
			id:i   
		});
		
		with ({ mMarker: mMarkers[i] }) {
			google.maps.event.addListener(mMarker, 'click', function() {
				if (fliw) {
					fliw.close();
					fliw = null;
				}
				fliw = new google.maps.InfoWindow({
					content: getFLIWContent(mMarker.id),
				});
				fliw.open(membersMap, mMarker); 		
			});
		}
		
		setTimeout(dropMarker(i), i * 150);	
		if (fMapArgs.autoZoom == 1) membersMap.fitBounds(latlngbounds);
	}
	
	var yLMemberIcon;
	if (fMapArgs.ylIcon) yLMemberIcon = fMapArgs.mainIconsFolder + '/your-location-icons/' + fMapArgs.ylIcon; else yLMemberIcon = 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png';
	
	marker = new google.maps.Marker({
		position: new google.maps.LatLng(fMapArgs.your_location[1],fMapArgs.your_location[2]),
		map: membersMap,
		icon: yLMemberIcon,
	});
		
	// drop marker 								
	function dropMarker(i) {
		return function() {
			mMarkers[i].setMap(membersMap);
		}
	}
	
	function getFLIWContent(i) {
		
		/*if (jQuery('.wppl-gm-wrapper .wppl-gm-units').val() == 'metric') {
			var distance = Math.round( (markers[i]['distance'] * 1.6) * 10) / 10;
		} else {
			var distance = Math.round( markers[i]['distance'] * 10) / 10;
		}*/
		var content = "";
		content +=	'<div class="wppl-fl-info-window">';
		content +=  	'<div class="wppl-info-window-thumb">' + fMapArgs.locations[i]['avatar'] + '</div>';
		content +=		'<div class="wppl-info-window-info">';
		content +=			'<table>';
		content +=				'<tr><td><div class="wppl-info-window-permalink"><a href="' + fMapArgs.locations[i]['user_permalink'] + '">' + fMapArgs.locations[i]['full_name'] + '</a></div></td></tr>';
		content +=				'<tr><td><span>Address: </span>' + fMapArgs.locations[i]['address'] + '</td></tr>';
		content +=				'<tr><td><span>Distance: </span>' + fMapArgs.locations[i]['distance'] + ' ' + fMapArgs.units['name'] + '</td></tr>';
		content +=			'</table>';
		content +=		'</div>';
		content +=  '</div>';
		return content;
	}  
});


				  
		