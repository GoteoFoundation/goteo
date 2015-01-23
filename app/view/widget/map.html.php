<?php
// vista para poner un mapa de google
// El codigo javascript esta en js/geolocation.js

?>
<div id="map-canvas" class="geo-map" data-map-latitude="<?php echo htmlspecialchars($this['latitude']); ?>"<?php if ($this['latitude'] && $this['longitude']) : ?> data-map-longitude="<?php echo htmlspecialchars($this['longitude']); ?>" data-map-content="<?php echo htmlspecialchars($this['name']); ?>"<?php endif; ?> style="border: 2px solid grey; height: 500px;"></div>

