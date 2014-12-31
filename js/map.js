
///// main pam ////
iconFile = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'; 

if (single != 1) {
var map = new google.maps.Map(document.getElementById('map'), {
	zoom: 8,
    center: new google.maps.LatLng(your_location[1],your_location[2]),
    mapTypeId: google.maps.MapTypeId[mapType],
	mapTypeControlOptions: {
		style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
    }
});				
 
var infowindow = new google.maps.InfoWindow();
var marker, i;
    
for (i = 0; i < locations.length; i++) {  
	marker = new google.maps.Marker({
		position: new google.maps.LatLng(locations[i]['lat'], locations[i]['long']),
		map: map,
		icon:'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='+ (page + i + 1) +'|FF776B|000000',
		shadow:'https://chart.googleapis.com/chart?chst=d_map_pin_shadow'       
	});
	
	google.maps.event.addListener(marker, 'click', (function(marker, i) {
		return function() {
			infowindow.setContent(
        	'<div class="wppl-info-window" style="font-size: 13px;color: #555;line-height: 18px;font-family: arial;">' +
        	'<div class="map-info-title" style="color: #457085;text-transform: capitalize;font-size: 16px;margin-bottom: -10px;">' + locations[i]['post_title'] + '</div>' +
        	'<br /> <span style="font-weight: bold;color: #333;">Address: </span>' + locations[i]['address']  + 
        	'<br /> <span style="font-weight: bold;color: #333;">Distance: </span>' + locations[i]['distance'] + 
        	'<br /> <span style="font-weight: bold;color: #333;">Phone: </span>' + locations[i]['phone'] + 
        	'<br /> <span style="font-weight: bold;color: #333;">Fax: </span>' + locations[i]['fax'] + 
        	'<br /> <span style="font-weight: bold;color: #333;">Email Address: </span>' + locations[i]['email'] + 
        	'<br /> <span style="font-weight: bold;color: #333;">Website: </span><a href="http://' + locations[i]['website'] + '" target="_blank">' + locations[i]['website'] + '</a>');
    		infowindow.open(map, marker);
           
		}
	})(marker, i));
    
		marker = new google.maps.Marker({
			position: new google.maps.LatLng(your_location[1],your_location[2]),
			map: map,
    		icon: iconFile,
		});
}

/// if this is a single map page ///
} else {
	//var zoomLevel = zoomLevel;
	var mapSingle = new google.maps.Map(document.getElementById('map_single'), {
	zoom: zoomLevel,
    center: new google.maps.LatLng(singleLocation[0],singleLocation[1]),
    mapTypeId: google.maps.MapTypeId[mapType],
	mapTypeControlOptions: {
		style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
    }
	});				
 
	var marker;   
	marker = new google.maps.Marker({
	position: new google.maps.LatLng(singleLocation[0], singleLocation[1]),
	map: mapSingle,
	shadow:'https://chart.googleapis.com/chart?chst=d_map_pin_shadow'       
	}); 
} 
