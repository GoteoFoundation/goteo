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
<?php $this->append() ?>
