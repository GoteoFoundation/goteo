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
      data-projects="<?= $this->ee(json_encode($this->map->getProjects())) ?>"
      data-workshops="<?= $this->ee(json_encode($this->map->getWorkshops())) ?>"
      data-donations="<?= $this->ee(json_encode($this->map->getDonations())) ?>"
  ></div>
  
  <template id = "template-project">
    <div class="gm-style-iw gm-style-iw-c" style="max-width: 400px; max-height: 279px; min-width: 0px;">
      <div class="gm-style-iw-d" style="max-height: 243px;">
        <div>
          <div class="infowindow prj">
            <div class="left">
              <img src="https://goteo.org/img/medium/img-6667.jpg">
            </div>
            <div class="right">
              <h3>Float Studio Donosti</h3>
              <h2>De: Uzuri Guridi</h2>
            </div>
          </div>
        </div>
      </div>
      <button style="background: rgba(0, 0, 0, 0) none repeat scroll 0% 0%; display: block; border: 0px none; margin: 0px; padding: 0px; position: absolute; cursor: pointer; user-select: none; top: -6px; right: -6px; width: 30px; height: 30px;" draggable="false" title="Close" aria-label="Close" type="button" class="gm-ui-hover-effect">
        <img src="data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224px%22%20height%3D%2224px%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22%23000000%22%3E%0A%20%20%20%20%3Cpath%20d%3D%22M19%206.41L17.59%205%2012%2010.59%206.41%205%205%206.41%2010.59%2012%205%2017.59%206.41%2019%2012%2013.41%2017.59%2019%2019%2017.59%2013.41%2012z%22%2F%3E%0A%20%20%20%20%3Cpath%20d%3D%22M0%200h24v24H0z%22%20fill%3D%22none%22%2F%3E%0A%3C%2Fsvg%3E%0A" style="pointer-events: none; display: block; width: 14px; height: 14px; margin: 8px;">
      </button>
    </div>
  </template>

  <?php $this->section('footer') ?>
    <?= $this->insert('partials/footer/javascript') ?>
    <script src="<?= SRC_URL ?>/assets/vendor/leaflet/dist/leaflet.js"></script>
    <script type="text/javascript" src="<?= $this->asset('js/map.js') ?>"></script>
    <?= $this->insert('partials/footer/analytics') ?>

  <?php $this->stop() ?>

</body>
</html>



