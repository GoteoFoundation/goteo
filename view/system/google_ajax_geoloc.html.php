<script type="text/javascript" src="http://www.google.com/jsapi?key=YOURKEY"></script>

<script type="text/javascript">

function geoTest() {

	if (google.loader.ClientLocation) {
		
		var latitude = google.loader.ClientLocation.latitude;
		var longitude = google.loader.ClientLocation.longitude;
		var city = google.loader.ClientLocation.address.city;
		var country = google.loader.ClientLocation.address.country;
		var country_code = google.loader.ClientLocation.address.country_code;
		var region = google.loader.ClientLocation.address.region;
	  
		var text = 'Your Location<br /><br />Latitude: ' + latitude + '<br />Longitude: ' + longitude + '<br />City: ' + city + '<br />Country: ' + country + '<br />Country Code: ' + country_code + '<br />Region: ' + region;
	
	} else {
		
		var text = 'Google was not able to detect your location';
	
	}
	
	document.write(text);

}

geoTest();

</script>
