<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name=”x-apple-disable-message-reformatting”>
    <title>Goteo Map</title>
    <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/leaflet/dist/leaflet.css"/>
    
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
  
  <?php $this->section('footer') ?>
    <?= $this->insert('partials/footer/javascript') ?>
    <script src="<?= SRC_URL ?>/assets/vendor/leaflet/dist/leaflet.js"></script>
    <script type="text/javascript" src="<?= $this->asset('js/map.js') ?>"></script>
    <?= $this->insert('partials/footer/analytics') ?>

  <?php $this->stop() ?>

</body>
</html>



