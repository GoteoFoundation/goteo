<?php

$this->layout('layout', [
    'bodyClass' => 'impact_discover',
    'title' => 'Descubre proyectos por ODS y Huella',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>

<div class="impact-discover">

    <?= $this->supply('impact-discover-content') ?>

</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>
    <?= $this->insert('impact_discover/partials/javascript') ?>

    <?php if ($this->view = 'map'): ?>
        <?= $this->insert('map/partials/javascript'); ?>

        <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/leaflet/dist/leaflet.css"/>
        <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/leaflet.markercluster/dist/MarkerCluster.css"/>
        <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/leaflet.markercluster/dist/MarkerCluster.Default.css"/>
        <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/leaflet.fullscreen/Control.FullScreen.css"/>
    <?php endif; ?>

    <?= $this->insert('partials/footer/analytics'); ?>
<?php $this->append() ?>
