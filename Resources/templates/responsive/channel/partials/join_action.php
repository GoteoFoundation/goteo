<div class="matcher-action-section"<?php if($this->color) echo ' style="background-color:' . $this->to_rgba($this->color, 0.8) . '"'; ?>>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-8">
                <div class="title">
                    <?= $this->text('channel-join') ?>
                </div>
                <div class="description">
                    <?= $this->text('channel-join-desc') ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-3 col-sm-offset-1 col-button">
                <a href="/channel/<?= $this->channel->id ?>/create" class="btn btn-white"<?php if($this->color) echo ' style="color:' . $this->color . '"'; ?>><?= $this->text('regular-create') ?></a>
            </div>
        </div>
    </div>
</div>
