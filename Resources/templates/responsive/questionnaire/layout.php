<?php

$this->layout('layout', [
    'bodyClass' => 'questionnaire',
    'title' =>  $this->meta_title,
    'meta_description' => $this->meta_description
    ]);

$this->section('content');

?>

<?php $this->stop() ?>