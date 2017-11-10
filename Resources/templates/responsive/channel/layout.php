<?php
$this->layout('layout', [
    'bodyClass' => 'channel',
    'title' =>  $this->text('regular-channel').' '.$this->channel->name,
    'meta_description' => $this->channel->description
    ]);

$this->section('header-navbar-brand');

?>
    <a class="navbar-brand" href="<?= SITE_URL ?>"><img src="<?= $this->asset('img/goteo.svg') ?>" class="logo" alt="Goteo"></a>

<?php

$this->replace();

$this->section('content');

$background = $this->channel->owner_background;

?>

    <div class="heading-section">
        <div class="owner-section"<?php if($background) echo ' style="background-color:' . $background . '"'; ?>>
            <?= $this->insert("channel/partials/owner_info") ?>
        </div>
        <?php $this->section('channel-header') ?>
        <div class="matcher-action-section"<?php if($background) echo ' style="background-color:' . $this->to_rgba($background, 0.8) . '"'; ?>>
            <?= $this->insert("channel/partials/join_action", ['color' => $background]) ?>
        </div>
        <?php $this->stop() ?>
    </div>

    <div class="projects-section">
        <div class="container-fluid">
            <div id="content">
                <?= $this->supply('channel-content') ?>
            </div>

        </div>
    </div>

    <?= $this->supply('channel-footer', $this->insert("channel/partials/summary_section")) ?>

<?php $this->replace() ?>
