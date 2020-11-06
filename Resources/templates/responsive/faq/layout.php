<?php

$this->layout('layout', [
    'bodyClass' => 'faq',
    'title' => $this->meta_title,
    'meta_description' => $this->meta_description
    ]);

$this->section('content');

?>

<div class="faq">

    <?= $this->supply('faq-content') ?>

</div>

<?php $this->replace() ?>
