/**
 * Send the data to ws/database
 */
function save_geolocation_data(type, data) {
    $.post('/ws/geolocate/' + type, data, function(result){
        console.log(result);
    });
}
/**
 * Sets the location by asking latitude / longitude to google (ip based location)
 * @param string type location_item (type) field: 'user', ...
 */
function set_location_from_google(type, iteration) {
    if(typeof google !== 'undefined' && google.loader.ClientLocation) {
        var loc = google.loader.ClientLocation;
        if (loc.latitude) {
            console.log('Google ip location:', loc);
            //save data
            save_geolocation_data(type, {
                lng: loc.longitude,
                lat: loc.latitude,
                city: google.loader.ClientLocation.address.city,
                region: google.loader.ClientLocation.address.region,
                country: google.loader.ClientLocation.address.country,
                country_code: google.loader.ClientLocation.address.country_code,
                method: 'ip'
            });
        }
    }
    else {
        if(!(iteration)) iteration = 0;
        iteration++;
        console.log('google client does not exists! [' + type +' '+iteration+ ']');
        if(iteration > 10) {
            console.log('Cancelled');
        }
        else {
            setTimeout(function(){set_location_from_google(type, iteration);}, 500);
        }
    }
}

/**
 * Gets location by asking latitude/longitude to the browser
 *
 * use with callback function as:
 *
 * get_location_from_browser(function(success, data) {
 *     console.log('success: ' + success, data.city, data.region, data.country, data.country_code, data.latitude, data.longitude);
 * })
 *
 */
function get_location_from_browser(callback, iteration) {
    var success = false;
    var data= {};

    if(typeof google === 'undefined' && !google.maps) {
        if(!(iteration)) iteration = 0;
        iteration++;
        console.log('google.map client does not exists! ['+iteration+ ']');
        if(iteration > 10) {
            console.log('Cancelled');
        }
        setTimeout(function(){get_location_from_browser(callback, iteration);}, 500);
        return;
    }

    if (navigator.geolocation) {

        //Try browser IP locator
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // console.log('browser info:', position.coords.latitude, position.coords.longitude);
                data = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
                // ask google for address:
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({'latLng': new google.maps.LatLng(position.coords.latitude, position.coords.longitude)}, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            success = true;
                            // console.log(results[0]);
                            for(var i in results[0].address_components) {
                                var ob = results[0].address_components[i];
                                // console.log(i, ob, "\n");
                                if(ob.types[0] === 'country' && ob.types[1] === 'political') {
                                    data.country = ob.long_name;
                                    data.country_code = ob.short_name;
                                }
                                if(ob.types[0] === 'locality' && ob.types[1] === 'political') {
                                    data.city = ob.long_name;
                                }
                                if((ob.types[0] === 'administrative_area_level_1' || ob.types[0] === 'administrative_area_level_2') && !(data.region) && ob.types[1] === 'political') {
                                    data.region = ob.long_name;
                                }
                            }
                            console.log('DATA:', data);
                        } else {
                            console.log('Geocoder failed due to: ' + status);
                        }
                    }
                });
            },
            function(error) {
                data = {
                    locable : 1,
                    info : ''
                };
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                      //set the unlocable status for the user
                      data.locable = 0;
                      data.info = "User denied the request for Geolocation.";
                      break;
                    case error.POSITION_UNAVAILABLE:
                      data.info = "Location information is unavailable.";
                      break;
                    case error.TIMEOUT:
                      data.info = "The request to get user location timed out.";
                      break;
                    case error.UNKNOWN_ERROR:
                      data.info = "An unknown error occurred.";
                      break;
                }
                console.log(error, data);

            }
        );
    }
    if(typeof callback === 'function') {
        callback(success, data);
    }
}

/**
 * Sets the location by asking latitude / longitude to the browser
 * @param string type location_item (type) field: 'user', ...
 */
function set_location_from_browser(type) {
    get_location_from_browser(function(success, data) {
        save_geolocation_data(type, data);
    });
}

jQuery(document).ready(function() {
    //asyncronious loading of maps V3
    if(typeof google !== 'undefined') {
        console.log(typeof google);
        google.load("maps", "3", {other_params:'sensor=false'});
    }

    // get user current location status
    $.get('/ws/geolocate/user', function(data){
        console.log(data);
        var use_browser = false;

        if(data.success) {
            //Is located, if method is IP, Try to override by browser coordinates
            if(data.location.method === 'ip' && data.location.locable) {
                use_browser = true;
            }
            //if method is browser or manual, no further actions are required
        }
        else {
            //try google IP locator
            set_location_from_google('user');
        }

        if(use_browser) {
            console.log('Trying browser localization');
            //try the browser for more precision
            set_location_from_browser('user');
        }
    });


});
