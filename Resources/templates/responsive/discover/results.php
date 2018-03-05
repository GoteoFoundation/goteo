<?php $this->layout('discover/layout') ?>

<?php $this->section('discover-content') ?>

<div class="section main-info">
    <div class="container">
        <h2 class="title">
            <?=$this->text('discover-searcher-header')?>
        </h2>
        <?= $this->supply('search-box', $this->insert('discover/partials/search_box')) ?>
    </div>
</div>

<?= $this->insert('discover/partials/projects_list') ?>

<?php $this->replace() ?>
