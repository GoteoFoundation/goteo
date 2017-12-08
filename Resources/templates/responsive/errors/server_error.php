<?php
//Simple overwrite
$this->layout('errors/layout', ['title' => 'Internal Server Error']);

$page = $this->page('big-error');

?>

<?php $this->section('error-debug') ?>

    <h3>Error <?= $this->code ?></h3>
    <p><?= $this->raw('msg') ?></p>

<?php $this->replace() ?>


<?php $this->section('error-content') ?>

    <h3 class="title"><?=$page->name?></h3>
    <?= $page->parseContent() ?>

<?php $this->replace() ?>
