<?php $this->layout('admin/layout') ?>


<?php $this->section('admin-content') ?>

<div class="admin-content">
    <div class="inner-container">
        <h2><?= $this->text('admin-home-title') ?></h2>

        <?= $this->insert('admin/partials/typeahead') ?>
        <hr>
        
        <div class="row">
            <?php foreach($this->raw('links') as $link => $text): ?>
            <div class="col-xs-4 col-sm-3"><a href="<?= $link ?>"><?= $text ?></a></div>
            <?php endforeach ?>
        </div>

        <hr>

        <div class="row">
            <?php foreach($this->legacy as $link => $text): ?>
            <div class="col-xs-4 col-sm-3"><a href="<?= $link ?>"><?= $text ?></a></div>
            <?php endforeach ?>
        </div>

    </div>
</div>

<?php $this->replace() ?>

