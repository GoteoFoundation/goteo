<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name=”x-apple-disable-message-reformatting”>
    <title>Goteo Map</title>
    <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/leaflet/dist/leaflet.css"/>
    <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/leaflet.markercluster/dist/MarkerCluster.css"/>
    <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/leaflet.markercluster/dist/MarkerCluster.Default.css"/>
    <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/leaflet.fullscreen/Control.FullScreen.css"/>
    
    <?= $this->insert('partials/header/styles') ?>
    <?= $this->insert('map//partials/styles') ?>

</head>

<body>
  <?php
    $map = $this->map;
  ?>

  <div id="map" class="osm-map" 
      <?php if ($map->getHeight() && $map->getWidth()): ?>
        style="height:<?= $map->getHeight() ?>px; width:<?= $map->getWidth() ?>;"
      <?php endif; ?>
      data-tile-layer='<?= $this->map->getTileLayer() ?>'
      <?php if ($this->map->getChannel()): ?>
        data-channel='<?= $this->map->getChannel() ?>'
      <?php endif; ?>
  ></div>

  <div class="map-layouts spacer-20">
		<div id="button-projects-activate" class="btn btn-cyan btn-lg hidden" sel-label=""><img src="/assets/img/map/pin-project.svg" alt="RECEIVED PROJECTS" title="RECEIVED PROJECTS"> <div class="title">SHOW RECEIVED PROJECTS</div></div>
		<div id="button-projects-hide" class="btn btn-cyan btn-lg" sel-label=""><img src="/assets/img/map/pin-project.svg" alt="RECEIVED PROJECTS" title="RECEIVED PROJECTS"> <div class="title">HIDE PROJECTS</div></div>
		<div id="button-workshops-activate" class="btn btn-lilac btn-lg hidden" sel-label=""><img src="/assets/img/map/pin-workshop.svg" alt="WORKSHOPS" title="WORKSHOPS"> <div class="title">SHOW WORKSHOPS</div></div>
		<div id="button-workshops-hide" class="btn btn-lilac btn-lg" sel-label=""><img src="/assets/img/map/pin-workshop.svg" alt="WORKSHOPS" title="WORKSHOPS"> <div class="title">HIDE WORKSHOPS</div></div>
	</div>
  
  <?php $this->section('footer') ?>
    <?= $this->insert('partials/footer/javascript') ?>
    <?= $this->insert('map/partials/javascript') ?>
    <?= $this->insert('partials/footer/analytics') ?>

  <?php $this->stop() ?>

</body>
</html>



