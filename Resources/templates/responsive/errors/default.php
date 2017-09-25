<?php
$this->layout('errors/layout', ['title' => 'Unexpected Error']);

$this->section('error-debug')
?>

    <h3>Error <?= $this->code ?></h3>
    <p><?= $this->raw('msg') ?></p>

<?php $this->replace() ?>
