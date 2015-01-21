<?php
// vista para poner un mapa de google
// El codigo javascript esta en js/geolocation.js
// recibimos las coordenadas
if ($this['latitude'] && $this['longitude']) :

?>
<div id="map-canvas" class="geomap" data-map-latitude="<?php echo htmlspecialchars($this['latitude']); ?>" data-map-longitude="<?php echo htmlspecialchars($this['longitude']); ?>" data-map-content="<?php echo htmlspecialchars($this['name']); ?>" style="border: 2px solid grey; height: 500px;"></div>
<?php

endif;
