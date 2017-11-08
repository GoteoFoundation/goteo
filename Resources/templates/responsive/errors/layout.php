<?php
//Simple overwrite
$this->layout('layout', ['title' => $this->title ? $this->title : 'Access Error']);

$this->section('content');
?>
<div class="container spacer-20">

    <div class="panel panel-danger">
        <div class="panel-heading"><h3 class="panel-title"><?= $this->raw('title') ?></h3></div>
        <div class="panel-body">
            <?= $this->supply('error-debug') ?>
            <p class="text-muted"><?= $this->get_uri() ?></p>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <?= $this->supply('error-content') ?>
        </div>
    </div>
</div>

<?php $this->stop() ?>
