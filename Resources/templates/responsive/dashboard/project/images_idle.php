<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1><?= $this->next ? '3. ' : '' ?><?= $this->text('images-main-header') ?></h1>
    <div class="auto-hide">
        <div class="inner"><?= $this->text('dashboard-project-images-desc') ?></div>
        <!-- <div class="more"><i class="fa fa-info-circle"></i> <?= $this->text('regular-help') ?></div> -->
    </div>

    <blockquote>
        <?= $this->text('dashboard-project-not-alive-yet') ?>
    </blockquote>

    <?php foreach($this->zones as $key => $zone):
        if(!is_array($this->images[$key])) continue;
     ?>
        <h3><?= $zone ?></h3>
        <div class="image-zone" data-section="<?= $key ?>">
            <ul class="list-inline image-list-sortable" id="list-sortable-<?= $key ?>"><?php
            foreach($this->images[$key] as $img) {
                echo trim($this->insert('dashboard/project/partials/image_list_item', [
                        'image_url' => $img->getLink(300, 300, true),
                        'image_name' => $img->getName(),
                        'idle' => true
                    ]));
            }
            ?></ul>
        </div>
    <?php endforeach ?>

    <?php if($this->next): ?>
        <p class="spacer"><a class="btn btn-lg btn-cyan" href="<?= $this->next ?>"><?= $this->text('form-next-button') ?></a></p>
    <?php endif ?>
  </div>
</div>

<?php $this->replace() ?>
