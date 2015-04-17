<?php
// vista para poner un mapa de google
// El codigo javascript esta en js/geolocation.js

?>
<div id="map-canvas" class="geo-map" data-map-latitude="<?php echo htmlspecialchars($vars['latitude']); ?>"<?php if ($vars['latitude'] && $vars['longitude']) : ?> data-map-longitude="<?php echo htmlspecialchars($vars['longitude']); ?>" data-map-content="<?php echo htmlspecialchars($vars['name']); ?>"<?php endif; ?> style="border: 2px solid grey; height: 500px;"></div>

