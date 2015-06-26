<div class="side_widget">
    <div class="line button rounded-corners<?php if ('' == $this->type) echo ' current' ?>">
        <p><a href="<?= '/channel/' . $this->channel->id ?>" class="show_cat"><?= $this->text('node-side-searcher-promote') ?></a></p>
    </div>
    <div class="line button rounded-corners<?php if ('available' == $this->type) echo ' current' ?>">
        <p><a href="<?= '/channel/' . $this->channel->id . '/available' ?>" class="show_cat"><?= $this->text('regular-see_all') ?></a></p>
    </div>

    <?php foreach ($this->types as $type) : ?>
    <div class="line button rounded-corners<?php if ($type == $this->type) echo ' current' ?>">
        <p><a href="<?= '/channel/' . $this->channel->id . '/' . $type ?>" class="show_cat"><?= $this->text('node-side-searcher-' . $type) ?></a></p>
    </div>
    <?php endforeach; ?>

</div>
