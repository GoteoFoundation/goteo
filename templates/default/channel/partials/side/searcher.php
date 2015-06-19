<div class="side_widget">
    <?php foreach ($this->types as $type) : ?>
    <div class="line button rounded-corners<?php if ($type == $this->type) echo ' current' ?>">
        <p><a href="<?= $URL.'/channel/'.$this->channel->id.'/'.$type.'#content' ?>" class="show_cat"><?= $this->text('node-side-searcher-'.$type) ?></a></p>
    </div>
    <?php endforeach; ?>

</div>
