<?php

$this->layout('dashboard/layout');

$this->section('dashboard-content');

?>

<div class="container general-dashboard">

    <?= $this->supply('dashboard-project-menu', $this->insert('dashboard/project/partials/menu')) ?>

    <?= $this->supply('dashboard-project-content') ?>

</div>

<?php $this->replace() ?>
