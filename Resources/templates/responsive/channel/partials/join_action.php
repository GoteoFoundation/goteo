<?php

if($this->channel->call_to_action_background_color)
    $background_color=$this->channel->call_to_action_background_color;
elseif($this->main_color)
    $background_color=$this->to_rgba($this->main_color, 0.8);
?>

<div class="matcher-action-section"<?php if($background_color) echo ' style="background-color:' . $background_color . '"'; ?>>
    <div class="container-fluid">
        <div class="row">

            <?php if($this->channel->terms): ?>

                <div class="col-xs-12 col-sm-6">
                    <div class="title">
                        <?= $this->text('channel-join') ?>
                    </div>
                    <div class="description">
                        <?= $this->channel->call_to_action_description ? $this->channel->call_to_action_description  : $this->text('channel-join-desc') ?>
                    </div>
                </div>

                <div class="col-xxs-12 col-xs-6 col-md-3 col-button">
                    <a data-toggle="modal" data-target="#termsModal" href="#"  class="btn btn-transparent"><?= $this->text('matcher-terms') ?></a>
                </div>

                <?php if($this->channel->project_creation_open): ?>
                    <div class="col-xxs-12 col-xs-6 col-md-3 col-button">
                        <a href="/channel/<?= $this->channel->id ?>/create" class="btn btn-white"<?php if($this->main_color) echo ' style="color:' . $this->main_color . '"'; ?>><?= $this->text('regular-create') ?></a>
                    </div>
                <?php endif; ?>

            <?php else: ?>

            <div class="col-xs-12 col-sm-8">
                <div class="title">
                    <?= $this->text('channel-join') ?>
                </div>
                <div class="description">
                    <?= $this->channel->call_to_action_description ? $this->channel->call_to_action_description  : $this->text('channel-join-desc') ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-3 col-sm-offset-1 col-button">
                <a href="/channel/<?= $this->channel->id ?>/create" class="btn btn-white"<?php if($this->main_color) echo ' style="color:' . $this->main_color . '"'; ?>><?= $this->text('regular-create') ?></a>
            </div>

            <?php endif; ?>

        </div>
    </div>
</div>
