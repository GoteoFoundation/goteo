<?php
$this->layout('layout', [
    'bodyClass' => 'channel',
    'title' =>  $this->text('regular-channel').' '.$this->channel->name,
    'meta_description' => $this->channel->description,
    'tw_image' =>  $this->channel->logo ? $this->channel->logo->getlink(400,0) : ''
    ]);

$this->section('header-navbar-brand');

?>
    <a class="navbar-brand" href="<?= $this->get_config('url.main') ?>"><img src="<?= $this->asset('img/goteo.svg') ?>" class="logo" alt="Goteo"></a>

<?php

$this->replace();

$this->section('content');

$background = $this->channel->owner_background;

?>

    <div class="heading-section">
        <div class="owner-section"<?php if($background) echo ' style="background-color:' . $background . '"'; ?>>
            <?= $this->insert("channel/partials/owner_info") ?>
        </div>

        <?= $this->supply('channel-header', $this->insert("channel/partials/join_action", ['color' => $background])) ?>

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
