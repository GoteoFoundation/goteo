<?php $channel=$this->channel; ?>

<div class="section projects">
  <div class="container">
    <h2 class="title"><span class="icon icon-desktop icon-3x"></span>Algunos proyectos en marcha</h2>
    <?= $this->insert('channel/call/partials/filters_block') ?>

        <?php if ($this->type !== 'available' && $this->type !== 'popular' && $this->type !== 'success') : ?>
            <?= $this->insert('channel/partials/projects') ?>
        <?php endif ?>
  </div>

  <div class="container">
    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10]) ?>
  </div>

</div>