<?php
// vista para poner un mapa de google
// El codigo javascript esta en js/geolocation.js

?>
<div id="map-canvas" class="geo-map"<?php
    if ($this->latitude && $this->longitude): ?> data-map-latitude="<?= $this->latitude ?>" data-map-longitude="<?= $this->longitude ?>"<?php endif;
    if($this->address): ?> data-map-address="<?= $this->address ?>"<?php endif;
    if($this->content): ?> data-map-content="<?= $this->content ?>"<?php endif;
    if($this->geoType): ?> data-geocoder-type="<?= $this->geoType ?>"<?php endif;
    if($this->geoItem): ?> data-geocoder-item="<?= $this->geoItem ?>"<?php endif;
    if($this->radius): ?> data-map-radius="<?= $this->radius ?>"<?php endif;
    if($this->coords): ?> data-map-coords="<?= $this->escape(json_encode($this->raw('coords'))) ?>"<?php endif;
    ?> style="height: 250px;"></div>
