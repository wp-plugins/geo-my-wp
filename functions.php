<?php

///// CONVERT ADDRESS TO COORDINATES //////
function ConvertToCoords($address) {	
 	$returned_address = array();
	$ch = curl_init();	
    $rip_it = array( " " => "+", "," => "", "?" => "", "&" => "", "=" => "" , "#" => "");
    
    //// MAKE SURE ADDRES DOENST HAVE ANY CHARACTERS THAT GOOGLE CANNOT READ //
    $address = str_replace(array_keys($rip_it), array_values($rip_it), $address);
    
    //// GET THE XML FILE WITH RESUALTS
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/2.0 (compatible; MSIE 3.02; Update a; AK; Windows 95)");
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	curl_setopt($ch, CURLOPT_URL, "http://maps.googleapis.com/maps/api/geocode/xml?address=". $address."&sensor=false"  );
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	$got_xml = curl_exec($ch);
  
    //// PARSE THE XML FILE //////////////
    $xml = false;

	$xml = new SimpleXMLElement($got_xml);
	
	//// GET THE LATITUDE/LONGITUDE FROM THE XML FILE ///////////////
	$returned_address['lat']  = esc_attr($xml->result->geometry->location->lat);
	$returned_address['long'] = esc_attr($xml->result->geometry->location->lng);
	
	$returned_address['formatted_address'] = esc_attr($xml->result->formatted_address);
	
	$address_array = $xml->result->address_component;
	
	if ( isset($address_array) && !empty($address_array) ) {
		foreach ($address_array as $ac) {
			if ($ac->type == 'street_number') {
				$street_number = esc_attr($ac->long_name); 
			}
			if ($ac->type == 'route') {
				$street_f = esc_attr($ac->long_name); 
				if (isset($street_number)  && !empty($street_number) )	
					$returned_address['street'] = $street_number . ' ' . $street_f;
				else
					$returned_address['street'] = $street_f;
			}
			if ($ac->type == 'subpremise') {
				$returned_address['apt'] = esc_attr($ac->long_name); 
			}
			if ($ac->type == 'locality') {
				$returned_address['city'] = esc_attr($ac->long_name); 
			}
			
			if ($ac->type == 'administrative_area_level_1') {
				$returned_address['state_short'] = esc_attr($ac->short_name); 
				$returned_address['state_long'] = esc_attr($ac->long_name);
			}
			if ($ac->type == 'postal_code') {
				$returned_address['zipcode'] = esc_attr($ac->long_name); 
			}
			if ($ac->type == 'country') {
				$returned_address['country_short'] = esc_attr($ac->short_name); 
				$returned_address['country_long'] = esc_attr($ac->long_name);
			}	
		}
	}
	return $returned_address;
}

?>
