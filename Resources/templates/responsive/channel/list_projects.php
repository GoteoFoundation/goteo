<?php

$this->layout('channel/layout', [
    'title' => $this->title_text.' :: '.$this->channel->name,
    'meta_description' => $this->title_text.'. '.$this->channel->description
    ]);

$this->section('channel-content');

?>
    <div class="content_widget channel-projects rounded-corners">
        <h2 class="title-section"><?= $this->text('node-projects-title') ?></h2>

        <?= $this->insert('channel/partials/filters_block') ?>

        <?= $this->insert('channel/partials/projects_block') ?>

    </div>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10]) ?>


<?php $this->replace() ?>

