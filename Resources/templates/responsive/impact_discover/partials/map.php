<?php
  $map = $this->map;
?>

<div id="impact-discover-map" class="section impact-discover-map">
    <div class="container">

      <div id="map" class="osm-map"
        <?php if ($map->getHeight() && $map->getWidth()): ?>
          style="height:<?= $map->getHeight() ?>px; width:<?= $map->getWidth() ?>;"
        <?php endif; ?>
        data-tile-layer='<?= $this->map->getTileLayer() ?>'
        data-zoom='<?= $this->map->getZoom() ?>'
        data-center='<?= json_encode($this->map->getCenter()) ?>'
        > 
      </div>
    </div>
</div>
