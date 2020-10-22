<?php

$this->layout('layout', [
    'bodyClass' => 'faq',
    'title' => $this->text('faq-meta-title'),
    'meta_description' => $this->text('faq-meta-description')
    ]);

$this->section('content');

?>

<div class="faq">

    <?= $this->supply('faq-content') ?>

</div>

<?php $this->replace() ?>
