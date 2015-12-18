<?php
// vista para poner un mapa de google
// El codigo javascript esta en js/geolocation.js

?>
<div id="map-canvas" class="geo-map"<?php
    if ($this->latitude && $this->longitude): ?> data-map-latitude="<?= $this->latitude ?>" data-map-longitude="<?= $this->longitude ?>"<?php endif;
    if($this->address): ?> data-map-address="<?= $this->address ?>"<?php endif;
    if($this->content): ?> data-map-content="<?= $this->content ?>"<?php endif
    ?> style="border: 2px solid grey; height: 500px;"></div>
