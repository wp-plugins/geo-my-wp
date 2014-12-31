  	iconFile = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'; 
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 8,
      center: new google.maps.LatLng(your_location[1],your_location[2]),
      mapTypeId: google.maps.MapTypeId.ROADMAP
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
          infowindow.setContent(locations[i]['post_title'] + "<br /> Address: " + locations[i]['address'] + "<br /> Distance: " + locations[i]['distance']);
          infowindow.open(map, marker);
           
        }
      })(marker, i));
      
      	marker = new google.maps.Marker({
      	position: new google.maps.LatLng(your_location[1],your_location[2]),
		map: map,
        icon: iconFile,
     });
     	
    }
    
