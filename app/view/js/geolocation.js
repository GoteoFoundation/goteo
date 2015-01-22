var locator = {
    trace: goteo.trace,
    map: null,
    autocomplete: null
};

/**
 * Send the data to ws/database
 */
locator.saveGeolocationData = function (type, item, data) {
    this.trace('Saving geolocation data, type:', type, ' item:', item, ' data:', data);
    $.post('/json/geolocate/' + type + (item ? '/' + item : ''), data, function(result){
        locator.trace('Saved gelocation data result:', result);
    });
};

/**
 * Sets the location by asking latitude / longitude to google (ip based location)
 * @param string type location_item (type) field: 'user', ...
 * requires     <script type="text/javascript" src="https://www.google.com/jsapi"></script> to be loaded
 */
// locator.setLocationFromGoogle = function (type, item, iteration) {
//     if(typeof google !== 'undefined' && google.loader.ClientLocation) {
//         var loc = google.loader.ClientLocation;
//         if (loc.latitude) {
//             this.trace('Google ip location:', loc);
//             //save data
//             this.saveGeolocationData(type, item, {
//                 longitude: loc.longitude,
//                 latitude: loc.latitude,
//                 city: google.loader.ClientLocation.address.city,
//                 region: google.loader.ClientLocation.address.region,
//                 country: google.loader.ClientLocation.address.country,
//                 country_code: google.loader.ClientLocation.address.country_code,
//                 method: 'ip'
//             });
//         }
//     }
//     else {
//         if(!(iteration)) iteration = 0;
//         iteration++;
//         this.trace('google client does not exists! [' + type +' '+iteration+ ']');
//         if(iteration > 10) {
//             this.trace('Cancelled');
//         }
//         else {
//             setTimeout(function(){this.setLocationFromGoogle(type, item, iteration);}, 500);
//         }
//     }
// };

locator.setLocationFromFreegeoip = function (type, item) {
    $.getJSON('//freegeoip.net/json', function(data){
        if(data.latitude && data.longitude) {
            locator.trace('geolocated type:', type, ' item:', item, ' data:', data);
           //save data
            locator.saveGeolocationData(type, item, {
                longitude: data.longitude,
                latitude: data.latitude,
                city: data.city,
                region: data.region_name,
                country: data.country_name,
                country_code: data.country_code,
                method: 'ip'
            });
        }
        else {
            locator.trace('Freegeoip error');
        }
    });
};

/**
 * Gets location by asking latitude/longitude to the browser
 *
 * use with callback function as:
 *
 * locator.getLocationFromBrowser(function(success, data) {
 *     locator.trace('success: ' + success, data.city, data.region, data.country, data.country_code, data.latitude, data.longitude);
 * })
 *
 */
locator.getLocationFromBrowser = function (callback, iteration) {
    var success = false;
    var data= {};

    if(typeof google === 'undefined' && !google.maps) {
        if(!(iteration)) iteration = 0;
        iteration++;
        this.trace('google.map client does not exists! ['+iteration+ ']');
        if(iteration > 10) {
            this.trace('Cancelled');
        }
        setTimeout(function(){this.getLocationFromBrowser(callback, iteration);}, 500);
        return;
    }

    if (navigator.geolocation) {

        //Try browser IP locator
        navigator.geolocation.getCurrentPosition(
            function(position) {
                locator.trace('browser info:', position.coords.latitude, position.coords.longitude);
                data = {
                    method: 'browser',
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
                // ask google for address:
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({'latLng': new google.maps.LatLng(position.coords.latitude, position.coords.longitude)}, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            success = true;
                            // locator.trace(results[0]);
                            for(var i in results[0].address_components) {
                                var ob = results[0].address_components[i];
                                // locator.trace(i, ob, "\n");
                                if(ob.types[0] === 'country' && ob.types[1] === 'political') {
                                    data.country = ob.long_name;
                                    data.country_code = ob.short_name;
                                }
                                if(ob.types[0] === 'locality' && ob.types[1] === 'political') {
                                    data.city = ob.long_name;
                                }
                                if((ob.types[0] === 'administrative_area_level_1' || ob.types[0] === 'administrative_area_level_2') && ob.types[1] === 'political') {
                                    data.region = ob.long_name;
                                }
                            }
                            locator.trace('Geocoder data:', data);
                        } else {
                            locator.trace('Geocoder failed due to: ' + status);
                        }
                    }
                    if(typeof callback === 'function') {
                        callback(success, data);
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
                locator.trace('Geocoder error:', error, ' data:', data, ' position:', position);
                if(typeof callback === 'function') {
                    callback(success, data);
                }
            }
        );
    }
};

/**
 * Sets the location by asking latitude / longitude to the browser
 * @param string type location_item (type) field: 'user', ...
 */
locator.setLocationFromBrowser = function (type, item) {
    this.getLocationFromBrowser(function(success, data) {
        locator.saveGeolocationData(type, item, data);
    });
};

/**
 * Loads a google map on a div
 * @param {object} obj       DOM div to create a map on
 * @param {string} desc      description (optional)
 *
 */
locator.setGoogleMapPoint = function (obj, iteration) {
    if(typeof google === 'undefined' || !google.maps) {
        if(!(iteration)) iteration = 0;
        iteration++;
        this.trace('google.maps client does not exists! ['+iteration+ ']');
        if(iteration > 10) {
            this.trace('Cancelled');
        }
        setTimeout(function(){this.setGoogleMapPoint(obj, iteration);}, 500);
        return;
    }

    var mapOptions = {
        // draggable: false,
        // scrollwheel: false,
        center: new google.maps.LatLng(39.5858, 2.6411),
        zoom: 5,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    //look for data-map-* attributes:
    if($(obj).is('[data-map-latitude]') && $(obj).is('[data-map-latitude]')) {
        var id = $(obj).attr('id');
        var lat = $(obj).data('map-latitude');
        var lng = $(obj).data('map-longitude');

        this.trace('Found printable geomap, id: ', id, ' lat,lng: ', lat, lng, ' content', $(obj).data('map-content'));
        if(lat && lng) {
            mapOptions.center = new google.maps.LatLng(lat, lng);
            mapOptions.zoom = 7;
        }
    }
    //draw map
    this.map = new google.maps.Map(obj, mapOptions);
    var marker = new google.maps.Marker({
        position: mapOptions.center
    });
    marker.setMap(this.map);

    //draw info window
    if($(obj).is('[data-map-content]')) {
        var desc = $(obj).data('map-content');
        var coordInfoWindow = new google.maps.InfoWindow();
        coordInfoWindow.setContent(desc);
        coordInfoWindow.setPosition(mapOptions.center);
        coordInfoWindow.open(this.map);

        google.maps.event.addListener(this.map, 'zoom_changed', function() {
            coordInfoWindow.setContent(desc);
            coordInfoWindow.open(this.map);
        });
    }

};

/**
 * updates geolocation from
 * @param {[type]} type          [description]
 * @param {[type]} autocomplete [description]
 */
locator.setGoogleAddressFromAutocomplete = function (type, item) {
    if(!this.autocomplete) {
        this.trace('Geocoder error in setGoogleAddressFromAutocomplete, this.autocomplete not present');
        return;
    }

    //handle auto geolocator if needed
    this.trace('Geocoder by autocomplete, type:', type, ' item:', item, ' autocomplete object:', this.autocomplete);
    var place = this.autocomplete.getPlace();
    if(place && place.geometry && place.address_components) {
        var data = {
            latitude : place.geometry.location.lat(),
            longitude : place.geometry.location.lng(),
            'method' : 'manual'
        };
        for(var i in place.address_components) {
            var ob = place.address_components[i];
            // this.trace(i, ob, "\n");
            if(ob.types[0] === 'country' && ob.types[1] === 'political') {
                data.country = ob.long_name;
                data.country_code = ob.short_name;
            }
            if(ob.types[0] === 'locality' && ob.types[1] === 'political') {
                data.city = ob.long_name;
            }
            if((ob.types[0] === 'administrative_area_level_1' || ob.types[0] === 'administrative_area_level_2') && ob.types[1] === 'political') {
                data.region = ob.long_name;
            }
        }
        this.trace('place:', place, 'location data:', data);
        this.saveGeolocationData(type, item, data);
    }

};

/**
 * Loads a google map on a div
 * @param {object} obj       DOM div to create a map on
 * @param {string} desc      description (optional)
 *
 */
locator.setGoogleAutocomplete = function(id, iteration) {
    if(typeof google === 'undefined' || !google.maps || !google.maps.places) {
        if(!(iteration)) iteration = 0;
        iteration++;
        this.trace('google.maps.places client does not exists! ['+iteration+ ']');
        if(iteration > 10) {
            this.trace('Cancelled');
        }
        setTimeout(function(){this.setGoogleAutocomplete(id, iteration);}, 500);
        return;
    }
    var options = {
        // types: ['(cities)']
    };

    this.trace('Setting autocomplete for id: ', id, ' name: ', $(id).attr('name'), ' element: ', $(id)[0]);
    this.autocomplete = new google.maps.places.Autocomplete($(id)[0], options);

    // When the user selects an address from the dropdown,
    // populate the address fields in the form.
    google.maps.event.addListener(this.autocomplete, 'place_changed', function() {
        if($(id).is('[data-geocoder-type]')) {
            locator.setGoogleAddressFromAutocomplete($(id).data('geocoder-type'), $(id).is('[data-geocoder-item]') ? $(id).data('geocoder-item') : '');
        }
    });
};

/**
 * Document ready
 */
$(function(){

    // get user current location status, geolocate if needed
    $.getJSON('/json/geolocate/user', function(data){
        locator.trace('Current user localization status: ', data);

        //only if user is logged
        if(data.user) {
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
                // locator.setLocationFromGoogle('user');
                locator.setLocationFromFreegeoip('user');
                use_browser = true;
            }

            if(use_browser) {
                locator.trace('Trying browser localization');
                //try the browser for more precision
                locator.setLocationFromBrowser('user');
            }
        }
    });

    //handles all maps and print it
    $('.geo-map').each(function(){
        locator.setGoogleMapPoint(this);
    });

    //handles all autocomplete fields
    $('input.geo-autocomplete').each(function(){
        if(!$(this).is('[id]')) {
            locator.trace('Missing ID html element for input:', this);
        }
        else {
            locator.setGoogleAutocomplete('input#' + $(this).attr('id') + '.geo-autocomplete');
        }
        //bind superforn dom changes
        $(this).closest('li.element').bind('superform.dom.done', function (event, html, new_el) {
            locator.trace('dom update', new_el);
            $(new_el).find('input.geo-autocomplete').each(function(){
                locator.setGoogleAutocomplete('input#' + $(this).attr('id') + '.geo-autocomplete');
            });
        });
    });
});
