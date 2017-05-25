<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-project-content') ?>

    <h1><?= $this->text('images-main-header') ?></h1>
    <blockquote><?= $this->text('dashboard-project-images-desc') ?></blockquote>

    <?php foreach($this->zones as $key => $zone): ?>
        <h4><?= $zone ?></h4>
        <?php foreach($this->images[$key] as $i => $img): ?>
            <img class="img-responsive" src="<?= $img->getLink(200, 200, true) ?>">
        <?php endforeach ?>
    <?php endforeach ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
// @license-end
</script>

<?php $this->append() ?>
