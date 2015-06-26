<?php

$this->layout('channel/layout', [
    'title' => $this->title_text.' :: '.$this->channel->name,
    'meta_description' => $this->title_text.'. '.$this->channel->description
    ]);

$this->section('channel-content');

?>
    <div id="channel-projects-promote" class="content_widget channel-projects rounded-corners">
        <h2 class="title"><?= $this->title_text ?>
        <span class="line"></span>
        </h2>
        <?php if ($this->projects) : ?>
            <?php foreach ($this->projects as $project) {
                echo $this->insert('project/widget/horizontal_project', ['project' => $project]);
            }?>
        <?php else : ?>
            <?= $this->text('discover-results-empty') ?>
        <?php endif ?>
    </div>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10]) ?>


<?php $this->replace() ?>

