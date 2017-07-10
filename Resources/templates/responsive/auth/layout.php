<?php

// var to overrite the login layout
$alt_layout = $this->get_session('alt_layout');
if($alt_layout && is_file($alt_layout)) {
    require($alt_layout);
    return;
} else {
    $this->layout('layout', [
        'bodyClass' => '',
        'title' => $this->alt_title ? $this->alt_title : $this->text('meta-title-register'),
        'meta_description' => $this->alt_description ? $this->alt_description : $this->text('meta-description-register')
        ]);
}

$this->section('content');
?>

    <?= $this->supply('main-content') ?>

<?php $this->replace() ?>
