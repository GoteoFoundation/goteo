function send_gl_form(gl_lat, gl_lon, gl_msg) {
    $.ajax({
        type:       'POST',
        url:        '/ws/geologin',
        dataType:   'html',
        data:       ({geologin: 'record', lat: gl_lat, lon: gl_lon, msg:gl_msg})
    });
}

function setPosition(position) {
    send_gl_form(position.coords.latitude, position.coords.longitude, 'OK');
}

function setMsg(error) {
  var msg;
  switch(error.code)
    {
    case error.PERMISSION_DENIED:
      msg = "User denied the request for Geolocation.";
      break;
    case error.POSITION_UNAVAILABLE:
      msg = "Location information is unavailable.";
      break;
    case error.TIMEOUT:
      msg = "The request to get user location timed out.";
      break;
    case error.UNKNOWN_ERROR:
      msg = "An unknown error occurred.";
      break;
    }
    //try the maxmind database
    send_gl_form(null, null, msg);
}

function googleLocator() {
 if (google.loader.ClientLocation) {
    var loc = google.loader.ClientLocation;
        if (loc.address) {
            alert(loc.address.city);
        }
        if (loc.latitude) {
            alert(loc.latitude+'/'+loc.longitude);
        }
    }
    return false;
}
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(setPosition, function(error) {
            //try google locator
            if(!googleLocator)
                setMsg(error);
        });
    } else{
        send_gl_form(null, null, "Geolocation is not supported by the browser.");
    }
}

jQuery(document).ready(function() {
    getLocation();
});
