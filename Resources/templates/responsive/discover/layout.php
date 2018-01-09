<?php

$this->layout('layout', [
    'bodyClass' => 'discover',
    'title' => $this->text('meta-title-discover'),
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

// We include alert messages in this layout, so it will be processed before the
// main layout. Therefore the main layout won't repeat them
?>

<div class="discover">

    <?= $this->supply('discover-content') ?>

</div>

<?php $this->replace() ?>
    
