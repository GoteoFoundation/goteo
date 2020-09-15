<?php

$this->layout('layout', [
    'bodyClass' => 'faq',
    'title' => $this->text('meta-title-faq'),
    'meta_description' => $this->text('meta-description-faq')
    ]);

$this->section('content');

?>

<div class="faq">

    <?= $this->supply('faq-content') ?>

</div>

<?php $this->replace() ?>
