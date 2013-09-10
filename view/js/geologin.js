function send_gl_form(gl_lat, gl_lon, gl_msg) {
    $.ajax({
        type:       'POST',
        url:        '/ws/geologin',
        dataType:   'html',
        data:       ({geologin: 'record', lat: gl_lat, lon: gl_lon, msg:gl_msg})
    });
}    
    
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(setPosition, setMsg);
    } else{
        send_gl_form(null, null, "Geolocation is not supported by the browser.");
    }
}

function setPosition(position) {
    send_gl_form(position.coords.latitude, position.coords.longitude, 'OK');
}

function setMsg(error) {
  var msg;
  switch(error.code) 
    {
    case error.PERMISSION_DENIED:
      msg = "User denied the request for Geolocation."
      break;
    case error.POSITION_UNAVAILABLE:
      msg = "Location information is unavailable."
      break;
    case error.TIMEOUT:
      msg = "The request to get user location timed out."
      break;
    case error.UNKNOWN_ERROR:
      msg = "An unknown error occurred."
      break;
    }
    send_gl_form(null, null, msg);
}

jQuery(document).ready(function($) {
    getLocation();
});
