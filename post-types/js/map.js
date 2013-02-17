///// main map ////
jQuery(window).load(function(){ 
	jQuery(function() {
		//// show hide map ///
		jQuery('.map-show-hide-btn').click(function(event){
			event.preventDefault();
			jQuery("#wppl-hide-map").slideToggle();
		
		}); 
	});
	//var point = new google.maps.LatLng(0,0);
	var latlngbounds = new google.maps.LatLngBounds( );
	if (mainMapArgs.yLocation != "0") {
		var yourLocation  = new google.maps.LatLng(mainMapArgs.yLocation[1],mainMapArgs.yLocation[2]);
		latlngbounds.extend(yourLocation);
	}
	
	if (mainMapArgs.gmwVersion == 'premium') {
		var ptMap = new google.maps.Map(document.getElementById('wppl-pt-map'), {
			zoom: parseInt(mainMapArgs.zoomLevel),
			panControl: Boolean(mainMapArgs.mapControls.pan),
			zoomControl: Boolean(mainMapArgs.mapControls.zoom),
			mapTypeControl: Boolean(mainMapArgs.mapControls.map_type),
			scaleControl: Boolean(mainMapArgs.mapControls.scale),
			streetViewControl: Boolean(mainMapArgs.mapControls.street_view),
			overviewMapControl: Boolean(mainMapArgs.mapControls.overview),
			center: new google.maps.LatLng(mainMapArgs.yLocation[1],mainMapArgs.yLocation[2]),
			mapTypeId: google.maps.MapTypeId[mainMapArgs.mapType],
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
			}
		});		
	} else {
		var ptMap = new google.maps.Map(document.getElementById('wppl-pt-map'), {
			zoom: parseInt(mainMapArgs.zoomLevel),
			center: new google.maps.LatLng(mainMapArgs.yLocation[1],mainMapArgs.yLocation[2]),
			mapTypeId: google.maps.MapTypeId[mainMapArgs.mapType],
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
			}
		});		
	}	
	
	var i, ptiw;
	ptMarkers = [];
				
	for (i = 0; i < mainMapArgs.locations.length; i++) {  
		var ptLocation = new google.maps.LatLng(mainMapArgs.locations[i]['lat'], mainMapArgs.locations[i]['long']);
		latlngbounds.extend(ptLocation);
		
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
			
		ptMarkers[i] = new google.maps.Marker({
			position: ptLocation,
			icon:mapIcon,
			animation: google.maps.Animation.DROP,
			shadow: 'https://chart.googleapis.com/chart?chst=d_map_pin_shadow',
			id:i 
		});
		
		with ({ ptMarker: ptMarkers[i] }) {
			google.maps.event.addListener(ptMarker, 'click', function() {
				if (ptiw) {
					ptiw.close();
					ptiw = null;
				}
				ptiw = new google.maps.InfoWindow({
					content: getPTIWContent(ptMarker.id),
				});
				ptiw.open(ptMap, ptMarker); 		
			});
		}
		
		setTimeout(dropMarker(i), i * 150);	
	}
	
	
	var yLMapIcon;
	
	if (mainMapArgs.yLIcon) yLMapIcon = mainMapArgs.mIFolder + '/your-location-icons/' + mainMapArgs.yLIcon; else yLMapIcon = 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png';
	marker = new google.maps.Marker({
		position: new google.maps.LatLng(mainMapArgs.yLocation[1],mainMapArgs.yLocation[2]),
		map: ptMap,
		icon:yLMapIcon
	});
	
	if (mainMapArgs.autoZoom == 1) ptMap.fitBounds(latlngbounds);
	
	// drop marker 								
	function dropMarker(i) {
		return function() {
			ptMarkers[i].setMap(ptMap);
		}
	}
		
	function getPTIWContent(i) {
		
		/*if (jQuery('.wppl-gm-wrapper .wppl-gm-units').val() == 'metric') {
			var distance = Math.round( (markers[i]['distance'] * 1.6) * 10) / 10;
		} else {
			var distance = Math.round( markers[i]['distance'] * 10) / 10;
		}*/
		var content = "";
		content +=	'<div class="wppl-pt-info-window">';
		content +=  	'<div class="wppl-info-window-thumb">' + mainMapArgs.locations[i]['post_thumbnail'] + '</div>';
		content +=		'<div class="wppl-info-window-info">';
		content +=			'<table>';
		content +=				'<tr><td><div class="wppl-info-window-permalink"><a href="' + mainMapArgs.locations[i]['post_permalink'] + '">' + mainMapArgs.locations[i]['post_title'] + '</a></div></td></tr>';
		content +=				'<tr><td><span>Address: </span>' + mainMapArgs.locations[i]['address'] + '</td></tr>';
		content +=				'<tr><td><span>Distance: </span>' + mainMapArgs.locations[i]['distance'] + ' ' + mainMapArgs.units['name'] + '</td></tr>';
		content +=				'<tr><td><span>Phone: </span>' + mainMapArgs.locations[i]['phone'] + '</td></tr>';
		content +=				'<tr><td><span>Fax: </span>' + mainMapArgs.locations[i]['fax'] + '</td></tr>';
		content +=				'<tr><td><span>Email Address: </span>' + mainMapArgs.locations[i]['email'] + '</td></tr>';
		content +=				'<tr><td><span>Website: </span><a href="http://' + mainMapArgs.locations[i]['website'] + '" target="_blank">' + mainMapArgs.locations[i]['website'] + '</a>' + '</td></tr>';
		content +=			'</table>';
		content +=		'</div>';
		content +=  '</div>';
		return content;
	}
});
	
