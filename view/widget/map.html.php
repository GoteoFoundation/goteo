<?php
//vista para poner un mapa de google
// recibimos las coordenadas
if (empty($this['lat']) && empty($this['lon'])) return '';
?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript">
var map;
function setMap() {
  var centerPoint = new google.maps.LatLng(<?php echo $this['lat'] ?>, <?php echo $this['lon']?>); 
    
  var mapOptions = {
    draggable: false,
    scrollwheel: false,
    zoom: 7,
    center: centerPoint,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
  
  var coordInfoWindow = new google.maps.InfoWindow();
  coordInfoWindow.setContent('<?php echo $this['name']?>');
  coordInfoWindow.setPosition(centerPoint);
  coordInfoWindow.open(map);
  
  google.maps.event.addListener(map, 'zoom_changed', function() {
    coordInfoWindow.setContent('<?php echo $this['name']?>');
    coordInfoWindow.open(map);
  });
}

google.maps.event.addDomListener(window, 'load', setMap);
</script>
<div id="map-canvas" style="border: 2px solid grey; height: 500px;"></div>
