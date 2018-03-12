/*
@licstart  The following is the entire license notice for the
JavaScript code in this page.

Copyright (C) 2010  Goteo Foundation

The JavaScript code in this page is free software: you can
redistribute it and/or modify it under the terms of the GNU
General Public License (GNU GPL) as published by the Free Software
Foundation, either version 3 of the License, or (at your option)
any later version.  The code is distributed WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

As additional permission under GNU GPL version 3 section 7, you
may distribute non-source (e.g., minimized or compacted) forms of
that code without the copy of the GNU GPL normally required by
section 4, provided you include this license notice and a URL
through which recipients can access the Corresponding Source.


@licend  The above is the entire license notice
for the JavaScript code in this page.
*/

var locator = {
    trace: goteo.trace,
    map: null,
    autocomplete: null
};


locator.getUserLocation = function (callback) {
    if(typeof callback !== 'function') {
        callback = function() {};
    }
    if(goteo.user_location) {
        locator.trace('Location already defined', goteo.user_location);
        callback(goteo.user_location);
    } else {
        // get from ip
        locator.setLocationFromFreegeoip('user', null, function(data, error) {
            locator.trace('Location from freegeoip', data);
            if(data) {
                goteo.user_location = data;
                callback(data);
            } else {
                callback(null, error);
            }
        });
    }
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

locator.setLocationFromFreegeoip = function (type, item, callback) {
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
            if(typeof callback === 'function') {
                callback(data);
            }
        } else {
            locator.trace('Freegeoip error');
            if(typeof callback === 'function') {
                callback(null, 'Freegeoip error');
            }
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

locator.geoCode = function(obj, callback, error_callback) {
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode(obj, function(results, status) {
    if (status === google.maps.GeocoderStatus.OK) {
        locator.trace('Got coordinates for', obj, results[0].geometry.location);
        locator.map.setCenter(results[0].geometry.location);
        locator.marker.setPosition(results[0].geometry.location);
        locator.map.setZoom(12);
        if(callback) {
            callback(results[0]);
        }
    } else {
        locator.trace('No address found for', obj);
        if(error_callback) {
            error_callback();
        }
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
    var map_id = $(obj).attr('id');
    if(!map_id) map_id = 'map';
    var autocomplete = $(obj).data('autocomplete-target');
    var $ko_msg = $($(obj).data('autocomplete-error')).hide();
    var $ok_msg = $($(obj).data('autocomplete-success')).hide();

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
    this.marker = new google.maps.Marker({
            map: this.map,
            draggable: true,
            animation: google.maps.Animation.DROP
        });
    google.maps.event.addListener(this.marker, 'dragstart', function() {
        locator.trace('drag start');
        $ko_msg.hide();
        $ok_msg.hide();
    });
    google.maps.event.addListener(this.marker, 'dragend', function() {
        locator.trace('dragged marker', locator.marker.getPosition());
        locator.geoCode({latLng: locator.marker.getPosition()}, function(place) {
            locator.trace('triggering changePlace', autocomplete);
            if(autocomplete) {
                $(autocomplete).val(place.formatted_address);
                locator.changePlace(autocomplete, place);
                $ok_msg.show();
            }
        }, function() {
            $ko_msg.show();
        });
    });
    // array of points
    this.markers = [];
    if($(obj).is('[data-map-coords]')) {
        var coords = $(obj).data('map-coords');
        if($.isArray(coords)) {
            //  Create a new viewpoint bound
            var bounds = new google.maps.LatLngBounds();
            var geocoder = new google.maps.Geocoder();
            for(var i in coords) {
                if(coords[i].lat && coords[i].lng) {
                    var m = new google.maps.Marker();
                    var pos = new google.maps.LatLng(coords[i].lat, coords[i].lng);
                    m.setMap(this.map);
                    m.setPosition(pos);
                    bounds.extend(pos);
                    if(coords[i].title) m.setTitle(coords[i].title);
                    this.markers[this.markers.length] = m;
                }
                else if(coords[i].address) {
                    // geolocate this address
                    geocoder.geocode({'address': coords[i].address}, function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            var m = new google.maps.Marker();
                            var pos = results[0].geometry.location;
                            m.setMap(locator.map);
                            m.setPosition(pos);
                            bounds.extend(pos);
                            if(coords[i].title) m.setTitle(coords[i].title);
                            locator.markers[locator.markers.length] = m;
                            //  Fit these bounds to the map
                            // this.map.fitBounds(bounds);
                        }
                    });
                }
            }
            //  Fit these bounds to the map
            this.map.fitBounds(bounds);
        }
    }

    //draw info window
    if($(obj).is('[data-map-content]')) {
        var desc = $(obj).data('map-content');
        this.marker = new google.maps.InfoWindow();
        this.marker.setContent(desc);
        this.marker.setPosition(mapOptions.center);
        this.marker.open(this.map);
        if(!(this.circle)) {
            this.circle = new google.maps.Circle({
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.35,
                map: this.map,
                center: mapOptions.center,
                radius: 0
              });

        }
        google.maps.event.addListener(this.map, 'zoom_changed', function() {
            locator.marker.setContent(desc);
            locator.marker.open(locator.map);
        });
    }

    //look for data-map-* attributes:
    var lat = parseFloat($(obj).data('map-latitude')) || 0;
    var lng = parseFloat($(obj).data('map-longitude')) || 0;
    var radius = parseInt($(obj).data('map-radius'), 10) || 0;
    var address = $(obj).data('map-address');
    if(lat && lng) {
        this.trace('Found printable geomap, map_id: ', map_id, ' lat,lng: ', lat, lng, ' radius;', radius, ' content', $(obj).data('map-content'));
        if(lat && lng) {
            var center = new google.maps.LatLng(lat, lng);
            this.marker.setPosition(center);
            this.map.setCenter(center);
            this.map.setZoom(12);
        }
    }
    else if(address) {
        // Geocoding
        locator.geoCode({'address': address}, function(place) {
            lat = place.geometry.location.lat();
            lng = place.geometry.location.lng();
            var data = locator.getGoogleAddressFromAutocomplete(place);
            // Save address field via ajax if required.
            if($(obj).is('[data-geocoder-type]')) {
                locator.saveGeolocationData($(obj).data('geocoder-type'), $(obj).is('[data-geocoder-item]') ? $(obj).data('geocoder-item') : '', data);
            }
            locator.trace('triggering changePlace', autocomplete);
            if(autocomplete) {
                locator.changePlace(autocomplete, place);
            }
        });
    }
    if(radius && lat && lng) {
      this.circle = new google.maps.Circle({
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35,
        map: this.map,
        center: new google.maps.LatLng(lat, lng),
        radius: radius * 1000 // in KM
      });
    }

};

/**
 * updates geolocation from
 * @param {[type]} type          [description]
 * @param {[type]} autocomplete [description]
 */
locator.getGoogleAddressFromAutocomplete = function (place) {
    if(!place) {
        this.trace('Geocoder error in getGoogleAddressFromplace, place not present');
        return;
    }

    //handle auto geolocator if needed
    this.trace('Geocoder by place, place object:', place);
    this.trace('place:', place);
    if(place && place.geometry && place.address_components) {
        var data = {
            latitude : place.geometry.location.lat(),
            longitude : place.geometry.location.lng(),
            'method' : 'manual',
            formatted_address: place.formatted_address
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

// When the user selects an address from the dropdown,
locator.changePlace = function(id, place) {
    var data = locator.getGoogleAddressFromAutocomplete(place);

    if(!(data)) {
        locator.trace('No data to populate for place', place);
        return;
    }
    locator.trace('Populating new place', place, data);
    // Save address field via ajax if required.
    if($(id).is('[data-geocoder-type]')) {
        locator.saveGeolocationData($(id).data('geocoder-type'), $(id).is('[data-geocoder-item]') ? $(id).data('geocoder-item') : '', data);
    }
    // populate the address fields in the form if available.
    var fields = ['address', 'city', 'region', 'zipcode', 'country_code', 'country', 'latitude', 'longitude', 'formatted_address', 'radius'];

    // Do not update fields if already filled
    if($(id).data('geocoder-skip-population')) {
        var $lat = $($(id).data('geocoder-populate-latitude'));
        var $lng = $($(id).data('geocoder-populate-longitude'));
        var v_lat = $lat.is(':input') ? $lat.val() : $lat.text();
        var v_lng = $lng.is(':input') ? $lng.val() : $lng.text();
        if(v_lat && v_lng) {
            locator.trace('Skipping population. Already populated to', v_lat, v_lng);
            return;
        }
    }
    // Update fields
    for(var i in fields) {
        var f = fields[i];
        var el = $(id).data('geocoder-populate-' + f);
        locator.trace('populate element', id, el, $(el).val());
        if(el) {
            var val = $(el).text();
            if($(el).is(':input')) {
                val = $(el).val();
            }
            locator.trace(el+': '+f+'['+data[f]+'] / '+val);
            if(data[f]) {
                if($(el).is(':input'))
                    $(el).val(data[f]);
                else
                    $(el).text(data[f]);
            }
        }
    }

    // Update marker if map present
    if(locator.map && locator.marker) {
        locator.map.setCenter(place.geometry.location);
        locator.marker.setPosition(place.geometry.location);
        if(locator.circle) {
            locator.circle.setCenter(place.geometry.location);
        }

        if(data.formatted_address) {
            try{
                locator.marker.setContent(data.formatted_address);
            } catch(e){}
        }
    }
};
/**
 * Autocompletes from google places
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

    if(!this.autocomplete) this.autocomplete = [];

    if(!this.autocomplete[id]) {
        this.trace('Setting autocomplete for id: ', id, ' name: ', $(id).attr('name'), ' element: ', $(id)[0]);
        this.autocomplete[id] = new google.maps.places.Autocomplete($(id)[0], options);
        google.maps.event.addListener(this.autocomplete[id], 'place_changed', function(){
            locator.changePlace(id, locator.autocomplete[id].getPlace());
        });
    }
};

/**
 * Document ready
 */
$(function(){
    // get user current location status, geolocate if needed
    locator.getUserLocation();

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

    //handles all autocomplete radius fields
    $('input.geo-autocomplete-radius').change(function(){
        if(locator.map && locator.circle) {
            locator.circle.setRadius($(this).val() * 1000);
            var radius = $(this).data('geocoder-populate-radius');
            locator.trace('set radius', radius, $(this).val());
            if(radius) {
                $(radius).val($(this).val());
            }
        }
    });
});

