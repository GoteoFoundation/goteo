<?php
/*
 * //@TODO: Pasar esto a una vista de usuario para incluirlo facilmente si al loguear no tiene geoloc asignada
 * //@TODO: que el envio de datos a /ws/geologin sea con ajax
 * 
 * Cambio temporalmente a /system/whereami para reverse geolocate
 * 
 * 
 */
?>
<form action="/ws/geologin" method="POST" id="gl_form">
    <input type="hidden" name="geologin" value="record" />
    <input type="text" id="gl_msg" name="msg" value="" style="width: 500px;" /><br />
    <input type="text" id="gl_lon" name="lon" value="" style="width: 300px;" /><br />
    <input type="text" id="gl_lat" name="lat" value="" style="width: 300px;" /><br />
</form>
<script type="text/javascript">
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(setPosition, setMsg);
    } else{
        document.getElementById("gl_msg").value = "Geolocation is not supported by the browser.";
        document.getElementById("gl_form").submit();
    }
}

function setPosition(position) {
    document.getElementById("gl_lon").value = position.coords.longitude;
    document.getElementById("gl_lat").value = position.coords.latitude; 
    document.getElementById("gl_form").submit();
}

function setMsg(error) {
  switch(error.code) 
    {
    case error.PERMISSION_DENIED:
      document.getElementById("gl_msg").value = "User denied the request for Geolocation."
      break;
    case error.POSITION_UNAVAILABLE:
      document.getElementById("gl_msg").value = "Location information is unavailable."
      break;
    case error.TIMEOUT:
      document.getElementById("gl_msg").value = "The request to get user location timed out."
      break;
    case error.UNKNOWN_ERROR:
      document.getElementById("gl_msg").value = "An unknown error occurred."
      break;
    }
  document.getElementById("gl_form").submit();
}

jQuery(document).ready(function($) {
    getLocation();
});
</script>
