<?php

$this->layout('dashboard/layout');

$this->section('dashboard-content');

?>

<div class="container general-dashboard">

<div class="row">
    <div class="col-xs-4 col-sm-3 col-xxs-12 dashboard-sidebar">
        <?= $this->supply('dashboard-project-menu', $this->insert('dashboard/project/partials/menu')) ?>
    </div>
    <div class="col-xs-8 col-sm-9 col-xxs-12">
        <?= $this->supply('dashboard-project-content') ?>
    </div>
</div>
</div>

<?php $this->replace() ?>
