<?php

$this->layout('channel/layout', [
    'title' => $this->title_text.' :: '.$this->channel->name,
    'meta_description' => $this->title_text.'. '.$this->channel->description
    ]);

$this->section('channel-content');

?>
    <div class="content_widget channel-projects rounded-corners">
        <h2 class="section-title"><?= $this->text('node-projects-title') ?>
        </h2>
        <ul class="filters list-inline center-block text-center">
            <a href="<?= '/channel/' . $this->channel->id ?>">
                <li <?= ''==$this->type ? 'class="active"' : '' ?> >
                    <?= $this->text('node-side-searcher-promote') ?>        
                </li>
            </a>
            <a href="<?= '/channel/' . $this->channel->id . '/available' ?>">
                <li <?= 'available' == $this->type ? 'class="active"' : '' ?> >
                    <?= $this->text('regular-see_all') ?>     
                </li>
            </a>
            <?php foreach ($this->types as $type) : ?>
                <a href="<?= '/channel/' . $this->channel->id . '/' . $type ?>" >
                    <li class="<?php if ($type == $this->type) echo 'active' ?>">
                        <?= $this->text('node-side-searcher-' . $type) ?>
                    </li>
                </a>
            <?php endforeach; ?>
        </ul>
        <?php if ($this->projects) : ?>
            <?php foreach ($this->projects as $project): ?>
                <div class="col-sm-6 col-md-4 col-xs-12 spacer widget-element">
                    <?= $this->insert('project/widgets/normal', [
                        'project' => $project,
                        // 'admin' => $project->userCanEdit($this->get_user())
                        'admin' => (bool)$this->admin
                    ]) ?>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <?= $this->text('discover-results-empty') ?>
        <?php endif ?>
    </div>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10]) ?>


<?php $this->replace() ?>

