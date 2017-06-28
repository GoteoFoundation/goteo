<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => 'Dashboard :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>

<div class="dashboard">

    <?= $this->supply('dashboard-sections', $this->insert('dashboard/partials/sections')) ?>

    <?= $this->supply('dashboard-content') ?>

</div>

<?php $this->replace() ?>

<?php $this->section('head') ?>
    <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/dropzone/dist/min/dropzone.min.css" />
    <link rel="stylesheet" href="<?= SRC_URL ?>/assets/vendor/simplemde/dist/simplemde.min.css" type="text/css" />
<?php $this->append() ?>

<?php $this->section('footer') ?>

    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/Sortable/Sortable.min.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/dropzone/dist/min/dropzone.min.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/js/forms.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/js/dashboard/ajax-utils.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/simplemde/dist/simplemde.min.js"></script>
    <script type="text/javascript">
        // Disable dropzone auto discover
        Dropzone.autoDiscover = false;

        // Some used vars
        goteo.texts = goteo.texts || [];
        goteo.texts['dashboard-project-dnd-image'] = '<i style="font-size:2em" class="fa fa-plus"></i><br><br><?= $this->ee($this->text('dashboard-project-dnd-image'), 'js') ?>';
    </script>
<?php $this->append() ?>
