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
    $.getJSON('//freegeoip.net/json/?callback=?', function(data){
        if(data.latitude && data.longitude) {
            locator.trace('Freegeoip geolocated type:', type, ' item:', item, ' data:', data);
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
                locator.trace('Geocoder error:', error, ' data:', data);
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
locator.setLocationFromBrowser = function (type, item, onFail) {
    this.getLocationFromBrowser(function(success, data) {
        locator.trace('Browser geolocation result:', success, data);
        if(success) {
            locator.saveGeolocationData(type, item, data);
        }
        else if(typeof onFail === 'function') {
            onFail(type, item);
        }
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
    //draw map
    this.map = new google.maps.Map(obj, mapOptions);
    // marker
    this.marker = new google.maps.Marker();
    this.marker.setMap(this.map);

    //draw info window
    if($(obj).is('[data-map-content]')) {
        var desc = $(obj).data('map-content');
        this.marker = new google.maps.InfoWindow();
        this.marker.setContent(desc);
        this.marker.setPosition(mapOptions.center);
        this.marker.open(this.map);

        google.maps.event.addListener(this.map, 'zoom_changed', function() {
            locator.marker.setContent(desc);
            locator.marker.open(locator.map);
        });
    }

    //look for data-map-* attributes:
    var id = $(obj).attr('id');
    if(!id) id = 'map';
    if($(obj).is('[data-map-latitude]') && $(obj).is('[data-map-latitude]')) {
        var lat = $(obj).data('map-latitude');
        var lng = $(obj).data('map-longitude');

        this.trace('Found printable geomap, id: ', id, ' lat,lng: ', lat, lng, ' content', $(obj).data('map-content'));
        if(lat && lng) {
            var center = new google.maps.LatLng(lat, lng);
            this.marker.setPosition(center);
            this.map.setCenter(center);
            this.map.setZoom(7);
        }
    }
    else if($(obj).is('[data-map-address]')) {
        // Geocoding
        var address = $(obj).data('map-address');
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({'address': address}, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            locator.map.setCenter(results[0].geometry.location);
            locator.marker.setPosition(results[0].geometry.location);
        }
      });
    }
};

/**
 * updates geolocation from
 * @param {[type]} type          [description]
 * @param {[type]} autocomplete [description]
 */
locator.getGoogleAddressFromAutocomplete = function (autocomplete) {
    if(!autocomplete) {
        this.trace('Geocoder error in getGoogleAddressFromAutocomplete, autocomplete not present');
        return;
    }

    //handle auto geolocator if needed
    this.trace('Geocoder by autocomplete, autocomplete object:', autocomplete);
    var place = autocomplete.getPlace();
    this.trace('place:', place);
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
            if(ob.types[0] === 'route') {
                data.route = ob.long_name;
            }
            if(ob.types[0] === 'street_number') {
                data.number = ob.long_name;
            }
            if(ob.types[0] === 'locality' && ob.types[1] === 'political') {
                data.city = ob.long_name;
            }
            if(ob.types[0] === 'postal_code') {
                data.zipcode = ob.long_name;
            }
            if(ob.types[0] === 'administrative_area_level_1' && ob.types[1] === 'political' && !data.region) {
                data.region = ob.long_name;
            }
            if(ob.types[0] === 'administrative_area_level_2' && ob.types[1] === 'political') {
                data.region = ob.long_name;
            }
        }
        if(data.route) {
            data.address = data.route;
        }
        if(data.number && data.route) {
            data.address = data.route + ', ' + data.number;
        }
        this.trace('location data:', data);
        return data;
    }
    return [];
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

    //https://developers.google.com/places/documentation/autocomplete?hl=es#place_types
    if($(id).is('[data-geocoder-filter]')) {
        options.types = [$(id).data('geocoder-filter')];
    }

    this.trace('Setting autocomplete for id: ', id, ' name: ', $(id).attr('name'), ' element: ', $(id)[0]);
    if(!this.autocomplete) this.autocomplete = [];
    this.autocomplete[id] = new google.maps.places.Autocomplete($(id)[0], options);

    // When the user selects an address from the dropdown,
    google.maps.event.addListener(this.autocomplete[id], 'place_changed', function() {
        var data = locator.getGoogleAddressFromAutocomplete(locator.autocomplete[id]);
        // Save address field via ajax if required.
        if($(id).is('[data-geocoder-type]')) {
            locator.saveGeolocationData($(id).data('geocoder-type'), $(id).is('[data-geocoder-item]') ? $(id).data('geocoder-item') : '', data);
        }
        // populate the address fields in the form if available.
        var fields = ['address', 'city', 'region', 'zipcode', 'country_code', 'country', 'latitude', 'longitude'];
        for(var i in fields) {
            var f = fields[i];
            var el = $(id).data('geocoder-populate-' + f);
            if($(id).is('[data-geocoder-populate-' + f + ']')) {
                // if($(el).val() == '') $(el).val(data[f]);
                // alert(el+': '+f+'['+data[f]+'] / '+$(el).val());
                if(data[f]) {
                    $(el).val(data[f]);
                }
            }
        }
        // Update marker if map present
        if(locator.map && locator.marker) {
            locator.map.setCenter(locator.autocomplete[id].getPlace().geometry.location);
            locator.marker.setPosition(locator.autocomplete[id].getPlace().geometry.location);
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
            var use_browser = true;
            var use_ip = true;

            if(data.success) {
                //Is located, if method is IP, Try to override by browser coordinates
                use_ip = false;
                //Don't annoy if unlocable or method is 'browser' or 'manual', no further actions are required
                if(!data.location.locable || data.location.method !== 'ip') {
                    use_browser = false;
                }
            }

            if(use_browser) {
                locator.trace('Trying browser localization');
                //try the browser for more precision
                locator.setLocationFromBrowser('user', null, function() {
                    locator.setLocationFromFreegeoip('user');
                });
            }
            else if(use_ip) {
                //try google IP locator
                // locator.setLocationFromGoogle('user');
                locator.setLocationFromFreegeoip('user');
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
            locator.setGoogleAutocomplete('#' + $(this).attr('id'));
        }
        //bind superforn dom changes
        $(this).closest('li.element').bind('superform.dom.done', function (event, html, new_el) {
            locator.trace('dom update', new_el);
            $(new_el).find('input.geo-autocomplete').each(function(){
                locator.setGoogleAutocomplete('#' + $(this).attr('id'));
            });
        });
    });
});
