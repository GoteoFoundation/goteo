<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => 'Dashboard :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>

  <?= $this->supply('admin-menu', $this->insert('dashboard/partials/menu')) ?>


    <div class="container">
      <?= $this->supply('dashboard-content') ?>
    </div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

    <script type="text/javascript" src="<?= SRC_URL ?>/assets/js/dashboard/ajax-utils.js"></script>

<?php $this->append() ?>
