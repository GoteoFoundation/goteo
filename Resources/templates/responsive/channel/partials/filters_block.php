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
