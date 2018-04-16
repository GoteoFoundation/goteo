<?php

$this->layout('layout', [
    'bodyClass' => 'discover',
    'title' => $this->text('meta-title-discover'),
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>

<div class="discover">

    <?= $this->supply('discover-content') ?>

</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>
    <?= $this->insert('discover/partials/javascript') ?>
<?php $this->append() ?>
