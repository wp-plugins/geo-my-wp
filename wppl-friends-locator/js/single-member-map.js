//var zoomLevel = zoomLevel;
	var mapSingle = new google.maps.Map(document.getElementById('member_map'), {
		zoom: 14,
    	center: new google.maps.LatLng(singleLocation[0],singleLocation[1]),
    	mapTypeId: google.maps.MapTypeId.ROADMAP,
		mapTypeControlOptions: {
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
    	}
	});	
	
	var markern, i;   
	var infowindow = new google.maps.InfoWindow();
	
	marker = new google.maps.Marker({
		position: new google.maps.LatLng(singleLocation[0], singleLocation[1]),
		map: mapSingle,
		shadow:'https://chart.googleapis.com/chart?chst=d_map_pin_shadow'       
	});
 
	/*google.maps.event.addListener(marker, 'click', (function(marker, i) {
		return function() {
			infowindow.setContent(
        		'<div class="wppl-info-window" style="font-size: 13px;color: #555;line-height: 18px;font-family: arial;">' +
        		'<div class="map-info-title" style="color: #457085;text-transform: capitalize;font-size: 16px;margin-bottom: -10px;">' + singleLocation[2] + '</div>' +
        		'<br /> <span style="font-weight: bold;color: #333;">Address: </span>'   + 
        		'<br /> <span style="font-weight: bold;color: #333;">Phone: </span>' + 
        		'<br /> <span style="font-weight: bold;color: #333;">Fax: </span>'  + 
        		'<br /> <span style="font-weight: bold;color: #333;">Email: </span>'  + 
        		'<br /> <span style="font-weight: bold;color: #333;">Website: </span>' + '</a>');
    			infowindow.open(mapSingle, marker);    
		}
	})(marker, i)); */
		