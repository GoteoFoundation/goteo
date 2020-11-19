<?php
  $map = $this->map;
?>

<div id="map" class="osm-map" 
    <?php if ($map->getHeight() && $map->getWidth()): ?>
      style="height:<?= $map->getHeight() ?>px; width:<?= $map->getWidth() ?>px;"
    <?php endif; ?>
    data-tile-layer="<?= $this->map->getTileLayer() ?>"
    data-projects="<?= $this->map->getProjects() ?>"
    data-workshops="<?= $this->map->getWorkshops() ?>"
    data-donations="<?= $this->map->getDonations() ?>"
    data-geojson="<?= $this->map->getGeoJSON() ?>"
></div>
